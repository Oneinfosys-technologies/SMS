<?php

namespace App\Actions\Student;

use App\Models\Finance\Transaction;
use App\Models\Student\Fee;
use App\Models\Student\Registration;
use App\Models\Student\Student;
use App\Support\FormatCodeNumber;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PayOnlineFee
{
    use FormatCodeNumber;

    private function codeNumber(int $batchId, int $teamId): array
    {
        $numberPrefix = config('config.finance.receipt_number_prefix');
        $numberSuffix = config('config.finance.receipt_number_suffix');
        $digit = config('config.finance.receipt_number_digit');

        if (config('config.finance.enable_online_transaction_number')) {
            $numberPrefix = config('config.finance.online_transaction_number_prefix');
            $numberSuffix = config('config.finance.online_transaction_number_suffix');
            $digit = config('config.finance.online_transaction_number_digit');
        }

        $numberFormat = $numberPrefix.'%NUMBER%'.$numberSuffix;

        $string = $this->preFormatForAcademicBatch($batchId, $numberFormat);

        $codeNumber = (int) Transaction::query()
            ->join('periods', 'periods.id', '=', 'transactions.period_id')
            ->where('periods.team_id', $teamId)
            ->whereNumberFormat($string)
            ->max('number') + 1;

        return $this->getCodeNumber(number: $codeNumber, digit: $digit, format: $string);
    }

    public function studentFeePayment(Student $student, Transaction $transaction): void
    {
        $teamId = auth()->user()?->current_team_id ?? $transaction->period->team_id;

        $studentFees = Fee::query()
            ->where('student_id', $student->id)
            ->whereIn('id', $transaction->getMeta('student_fee_ids'))
            ->get();

        $this->dualBalanceValidation($studentFees, $transaction);

        $payableAmount = $transaction->amount->value;

        foreach ($studentFees as $studentFee) {
            $payableAmount = (new PayFeeInstallment)->execute($studentFee, $transaction, $payableAmount);
        }

        $codeNumberDetails = $this->codeNumber($student->batch_id, $teamId);

        $transaction->number_format = Arr::get($codeNumberDetails, 'number_format');
        $transaction->number = Arr::get($codeNumberDetails, 'number');
        $transaction->code_number = Arr::get($codeNumberDetails, 'code_number');
        $transaction->processed_at = now()->toDateTimeString();
        $transaction->save();
    }

    private function dualBalanceValidation(Collection $studentFees, Transaction $transaction)
    {
        $balance = 0;
        foreach ($studentFees as $studentFee) {
            $balance += $studentFee->getBalance($transaction->date->value)->value;
        }

        if ($balance < $transaction->amount->value) {
            $referenceNumber = Arr::get($transaction->payment_gateway, 'reference_number');

            throw ValidationException::withMessages(['message' => trans('student.fee.no_payable_balance', ['attribute' => $referenceNumber])]);
        }
    }

    public function registrationFeePayment(Registration $registration, Transaction $transaction): void
    {
        $teamId = $registration->period->team_id;

        $batchId = $registration->course->batches->first()->id;

        $codeNumberDetails = $this->codeNumber($batchId, $teamId);

        $transaction->number_format = Arr::get($codeNumberDetails, 'number_format');
        $transaction->number = Arr::get($codeNumberDetails, 'number');
        $transaction->code_number = Arr::get($codeNumberDetails, 'code_number');
        $transaction->processed_at = now()->toDateTimeString();
        $transaction->save();
    }
}
