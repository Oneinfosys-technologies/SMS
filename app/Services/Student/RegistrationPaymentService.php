<?php

namespace App\Services\Student;

use App\Actions\Finance\CancelTransaction;
use App\Actions\Finance\CreateTransaction;
use App\Enums\Finance\PaymentStatus;
use App\Enums\Finance\TransactionType;
use App\Enums\Student\RegistrationStatus;
use App\Http\Resources\Finance\LedgerResource;
use App\Http\Resources\Finance\PaymentMethodResource;
use App\Models\Finance\Ledger;
use App\Models\Finance\PaymentMethod;
use App\Models\Student\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegistrationPaymentService
{
    public function preRequisite(Request $request): array
    {
        $paymentMethods = PaymentMethodResource::collection(PaymentMethod::query()
            ->byTeam()
            ->where('is_payment_gateway', false)
            ->get());

        $ledgers = LedgerResource::collection(Ledger::query()
            ->byTeam()
            ->subType('primary')
            ->get()
        );

        return compact('paymentMethods', 'ledgers');
    }

    public function skipPayment(Request $request, Registration $registration)
    {
        if ($registration->fee->value <= 0) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_input')]);
        }

        if ($registration->payment_status != PaymentStatus::UNPAID) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_action')]);
        }

        \DB::beginTransaction();

        $registration->fee = 0;
        $registration->payment_status = PaymentStatus::NA;
        $registration->save();

        \DB::commit();
    }

    public function payment(Request $request, Registration $registration)
    {
        $request->merge([
            'period_id' => $registration->period_id,
            'transactionable_type' => 'Registration',
            'transactionable_id' => $registration->id,
            'head' => 'registration_fee',
            'type' => TransactionType::RECEIPT->value,
        ]);

        \DB::beginTransaction();

        $params = $request->all();
        $params['course_id'] = $registration->course_id;
        $params['payments'] = [
            [
                'ledger_id' => $request->ledger?->id,
                'amount' => $request->amount,
                'payment_method_id' => $request->payment_method_id,
                'payment_method_details' => $request->payment_method_details,
            ],
        ];

        (new CreateTransaction)->execute($params);

        $registration->payment_status = PaymentStatus::PAID;
        $registration->save();

        \DB::commit();
    }

    public function cancelPayment(Request $request, Registration $registration, $uuid)
    {
        if ($registration->status != RegistrationStatus::PENDING) {
            throw ValidationException::withMessages(['message' => trans('student.registration.could_not_delete_transaction_if_processed')]);
        }

        if ($registration->payment_status != PaymentStatus::PAID) {
            throw ValidationException::withMessages(['message' => trans('finance.fee.not_paid')]);
        }

        $transaction = $registration->transactions->firstWhere('uuid', $uuid);

        if (! $transaction) {
            throw ValidationException::withMessages(['message' => trans('general.errors.invalid_input')]);
        }

        \DB::beginTransaction();

        (new CancelTransaction)->execute($request, $transaction);

        $registration->payment_status = PaymentStatus::UNPAID;
        $registration->save();

        \DB::commit();
    }
}
