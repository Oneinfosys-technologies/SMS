<?php

namespace App\Services\Student;

use App\Actions\Student\GetFeeInstallments;
use App\Actions\Student\GetHeadWiseFee;
use App\Enums\Finance\PaymentStatus;
use App\Helpers\CalHelper;
use App\Http\Resources\Student\FeeListResource;
use App\Http\Resources\Student\TransactionListForGuestResource;
use App\Http\Resources\Student\TransactionListResource;
use App\Models\Finance\FeeGroup;
use App\Models\Finance\Transaction;
use App\Models\Finance\TransactionPayment;
use App\Models\Finance\TransactionRecord;
use App\Models\Student\Fee;
use App\Models\Student\FeeRecord;
use App\Models\Student\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FeeListService
{
    public function fetchFee(Request $request, Student $student): array
    {
        $fees = Fee::query()
            ->with('installment', 'concession', 'transportCircle', 'records', 'records.head')
            ->whereStudentId($student->id)
            ->get();

        $studentFees = (new GetFeeInstallments)->execute($student, $fees);

        $feeGroups = FeeGroup::query()
            ->select('name', 'uuid', 'id')
            ->byPeriod($student->period_id)
            ->where(function ($q) {
                $q->whereNull('meta->is_custom')
                    ->orWhere('meta->is_custom', '!=', true);
            })
            ->get()
            ->transform(function ($feeGroup) use ($studentFees) {
                $installments = collect($studentFees)->filter(function ($studentFee) use ($feeGroup) {
                    return $studentFee['fee_group_uuid'] == $feeGroup->uuid;
                })->values();

                return [
                    'name' => $feeGroup->name,
                    'uuid' => $feeGroup->uuid,
                    'fees' => $installments,
                ];
            })
            ->filter(function ($feeGroup) {
                return count($feeGroup['fees']) > 0;
            })
            ->values();

        return compact('feeGroups');
    }

    public function listFee(Request $request, Student $student): array
    {
        if ($request->query('type', 'group') == 'head') {
            return $this->headWiseFee($request, $student);
        }

        return $this->groupWiseFee($request, $student);
    }

    public function groupWiseFee(Request $request, Student $student): array
    {
        $date = $request->query('date');

        if (! CalHelper::validateDate($date)) {
            $date = Carbon::parse(CalHelper::toDateTime(now()->toDateTimeString()))->toDateString();
        }

        $currentDate = Carbon::parse(CalHelper::toDateTime(now()->toDateTimeString()))->toDateString();

        if (auth()->user()->hasAnyRole(['student', 'guardian']) && $date != $currentDate) {
            $date = $currentDate;
        }

        $fees = Fee::query()
            ->with('installment', 'installment.group', 'concession', 'transportCircle', 'records', 'records.head')
            ->whereStudentId($student->id)
            ->get();

        if (! $fees->count()) {
            throw ValidationException::withMessages(['message' => trans('student.fee.set_fee_info')]);
        }

        $transactions = Transaction::query()
            ->select('transactions.*', 'users.name as user_name')
            ->leftJoin('users', 'transactions.user_id', '=', 'users.id')
            ->where('transactions.head', '=', 'student_fee')
            ->where('transactions.transactionable_type', '=', 'Student')
            ->where('transactions.transactionable_id', '=', $student->id)
            ->get();

        $transactionRecords = TransactionRecord::query()
            ->select('transaction_records.*', 'student_fees.uuid as student_fee_uuid')
            ->join('student_fees', 'transaction_records.model_id', '=', 'student_fees.id')
            ->whereIn('transaction_records.transaction_id', $transactions->pluck('id')->all())
            ->get();

        $payments = TransactionPayment::query()
            ->select('transaction_payments.*', 'ledgers.name as ledger_name', 'ledgers.uuid as ledger_uuid')
            ->with('method')
            ->leftJoin('ledgers', 'transaction_payments.ledger_id', '=', 'ledgers.id')
            ->whereIn('transaction_id', $transactions->pluck('id')->all())
            ->get();

        $transactions = $transactions->map(function ($transaction) use ($payments, $transactionRecords) {
            $transaction->records = $transactionRecords->where('transaction_id', $transaction->id);
            $transaction->payments = $payments->where('transaction_id', $transaction->id);

            return $transaction;
        });

        $feeGroups = FeeGroup::query()
            ->byPeriod($student->period_id)
            ->select('name', 'uuid', 'id')
            ->get()
            ->transform(function ($feeGroup) use ($fees, $date) {
                $total = $fees->where('installment.fee_group_id', $feeGroup->id)->sum(function ($fee) use ($date) {
                    return $fee->getTotal($date)->value;
                });

                $lateFee = $fees->where('installment.fee_group_id', $feeGroup->id)->sum(function ($fee) use ($date) {
                    return $fee->calculateLateFeeAmount($date)->value;
                });

                $paid = $fees->where('installment.fee_group_id', $feeGroup->id)->sum(function ($fee) {
                    return $fee->getPaid()->value;
                });

                $concession = $fees->where('installment.fee_group_id', $feeGroup->id)->sum(function ($fee) {
                    return $fee->records->sum('concession.value');
                });

                $balance = $total - $paid;

                return [
                    'name' => $feeGroup->name,
                    'uuid' => $feeGroup->uuid,
                    'late_fee' => \Price::from($lateFee),
                    'total' => \Price::from($total),
                    'paid' => \Price::from($paid),
                    'concession' => \Price::from($concession),
                    'balance' => \Price::from($balance),
                    'status' => PaymentStatus::getDetail(PaymentStatus::status($total, $paid)),
                ];
            })
            ->filter(function ($feeGroup) {
                return $feeGroup['total']->value > 0 || $feeGroup['concession']->value > 0;
            })
            ->all();

        $grandTotal = collect($feeGroups)->sum('total.value');
        $grandTotalPaid = collect($feeGroups)->sum('paid.value');
        $grandTotalBalance = collect($feeGroups)->sum('balance.value');

        if (auth()->user()) {
            $transactionResource = TransactionListResource::collection($transactions);
        } else {
            $transactionResource = TransactionListForGuestResource::collection($transactions);
        }

        return [
            'student' => [
                'name' => $student->name,
                'uuid' => $student->uuid,
                'batch_name' => $student->batch_name,
                'course_name' => $student->course_name,
                'code_number' => $student->code_number,
            ],
            'feeGroups' => array_values($feeGroups),
            'fees' => FeeListResource::collection($fees),
            'summary' => [
                'grandTotal' => \Price::from($grandTotal),
                'grandTotalPaid' => \Price::from($grandTotalPaid),
                'grandTotalBalance' => \Price::from($grandTotalBalance),
            ],
            'transactions' => $transactionResource,
            'date' => \Cal::date($date),
        ];
    }

    private function headWiseFee(Request $request, Student $student): array
    {
        $date = $request->date ?? today()->toDateString();

        $fees = Fee::query()
            ->whereStudentId($student->id)
            ->get();

        $feeRecords = FeeRecord::query()
            ->with('head')
            ->whereIn('student_fee_id', $fees->pluck('id')->all())
            ->get();

        $feeHeads = (new GetHeadWiseFee)->execute(
            student: $student,
            fees: $fees,
            feeRecords: $feeRecords,
            date: $date,
        );

        $transactionRecords = TransactionRecord::query()
            ->select('transaction_records.*', 'student_fees.uuid as student_fee_uuid')
            ->join('student_fees', 'transaction_records.model_id', '=', 'student_fees.id')
            ->join('transactions', 'transaction_records.transaction_id', '=', 'transactions.id')
            ->whereNull('transactions.cancelled_at')
            ->whereNull('transactions.rejected_at')
            ->where('transactions.head', '=', 'student_fee')
            ->where('transactions.transactionable_type', '=', 'Student')
            ->where('transactions.transactionable_id', '=', $student->id)
            ->get();

        $charges = [];
        $discounts = [];
        foreach ($transactionRecords as $transactionRecord) {
            $additionalCharges = $transactionRecord->getMeta('additional_charges') ?? [];
            $additionalDiscounts = $transactionRecord->getMeta('additional_discounts') ?? [];

            foreach ($additionalCharges as $additionalCharge) {
                $charges[] = $additionalCharge;
            }

            foreach ($additionalDiscounts as $additionalDiscount) {
                $discounts[] = $additionalDiscount;
            }
        }

        $charges = collect($charges)->groupBy('label')->map(function ($items) {
            return $items->sum('amount');
        })->toArray();

        foreach ($charges as $name => $amount) {
            $feeHeads[] = [
                'name' => $name,
                'uuid' => (string) Str::uuid(),
                'amount' => \Price::from($amount),
                'concession' => \Price::from(0),
                'total' => \Price::from($amount),
                'paid' => \Price::from($amount),
                'balance' => \Price::from(0),
            ];
        }

        $discounts = collect($discounts)->groupBy('label')->map(function ($items) {
            return $items->sum('amount');
        });

        foreach ($discounts as $name => $amount) {
            $feeHeads[] = [
                'name' => $name,
                'is_deduction' => true,
                'uuid' => (string) Str::uuid(),
                'amount' => \Price::from($amount),
                'concession' => \Price::from(0),
                'total' => \Price::from($amount),
                'paid' => \Price::from($amount),
                'balance' => \Price::from(0),
            ];
        }

        $additionFeeHeads = collect($feeHeads)->filter(function ($feeHead) {
            return ! Arr::get($feeHead, 'is_deduction');
        });

        $deductionFeeHeads = collect($feeHeads)->filter(function ($feeHead) {
            return Arr::get($feeHead, 'is_deduction');
        });

        $grandTotalAmount = $additionFeeHeads->sum('amount.value') - $deductionFeeHeads->sum('amount.value');
        $grandTotalConcession = $additionFeeHeads->sum('concession.value') - $deductionFeeHeads->sum('concession.value');
        $grandTotal = $additionFeeHeads->sum('total.value') - $deductionFeeHeads->sum('total.value');
        $grandTotalPaid = $additionFeeHeads->sum('paid.value') - $deductionFeeHeads->sum('paid.value');
        $grandTotalBalance = $additionFeeHeads->sum('balance.value') - $deductionFeeHeads->sum('balance.value');

        return [
            'feeHeads' => $feeHeads,
            'fees' => $fees->pluck('uuid')->all(),
            'date' => \Cal::date($date),
            'summary' => [
                'grandTotalAmount' => \Price::from($grandTotalAmount),
                'grandTotalConcession' => \Price::from($grandTotalConcession),
                'grandTotal' => \Price::from($grandTotal),
                'grandTotalPaid' => \Price::from($grandTotalPaid),
                'grandTotalBalance' => \Price::from($grandTotalBalance),
            ],
        ];
    }
}
