<?php

namespace App\Actions\Student;

use App\Models\Finance\Transaction;
use App\Models\Student\FeePayment;
use App\Models\Student\FeeRecord;

class PayFeeHead
{
    public function execute(FeeRecord $studentFeeRecord, Transaction $transaction, float $amount = 0): float
    {
        if ($amount <= 0) {
            return $amount;
        }

        $balance = $studentFeeRecord->getBalance()->value;

        if ($balance <= 0) {
            return $amount;
        }

        $payableAmount = $balance;

        if ($payableAmount > $amount) {
            $payableAmount = $amount;
        }

        $amount -= $payableAmount;

        $studentFeeRecord->paid = $studentFeeRecord->paid->value + $payableAmount;
        $studentFeeRecord->save();

        if ($payableAmount > 0) {
            FeePayment::forceCreate([
                'student_fee_id' => $studentFeeRecord->student_fee_id,
                'fee_head_id' => $studentFeeRecord->fee_head_id,
                'default_fee_head' => $studentFeeRecord->default_fee_head,
                'transaction_id' => $transaction->id,
                'amount' => $payableAmount,
            ]);
        }

        return $amount;
    }
}
