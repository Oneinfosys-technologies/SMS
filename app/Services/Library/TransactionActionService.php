<?php

namespace App\Services\Library;

use App\Models\Library\Transaction;
use App\Models\Library\TransactionRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TransactionActionService
{
    public function returnBook(Request $request, Transaction $transaction): void
    {
        if ($request->return_date < $transaction->issue_date->value) {
            throw ValidationException::withMessages(['return_date' => trans('validation.after_or_equal', ['attribute' => trans('library.transaction.props.return_date'), 'date' => $transaction->issue_date->formatted])]);
        }

        $transactionRecord = TransactionRecord::query()
            ->whereBookTransactionId($transaction->id)
            ->whereHas('copy', function ($query) use ($request) {
                $query->whereNumber($request->number);
            })
            ->where('return_date', null)
            ->firstOr(function () {
                throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('library.book.props.number')])]);
            });

        \DB::beginTransaction();

        $transactionRecord->return_date = $request->return_date;
        $transactionRecord->remarks = $request->remarks;
        $transactionRecord->save();

        \DB::commit();
    }
}
