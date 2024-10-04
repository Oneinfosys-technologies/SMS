<?php

namespace App\Services\Library;

use App\Enums\Library\IssueTo;
use App\Models\Library\Transaction;
use App\Models\Library\TransactionRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    public function preRequisite(Request $request)
    {
        $to = IssueTo::getOptions();

        return compact('to');
    }

    public function create(Request $request): Transaction
    {
        \DB::beginTransaction();

        $transaction = Transaction::forceCreate($this->formatParams($request));

        $this->updateRecords($request, $transaction);

        \DB::commit();

        return $transaction;
    }

    private function updateRecords(Request $request, Transaction $transaction): void
    {
        $bookCopyIds = [];
        foreach ($request->records as $record) {
            $bookCopyIds[] = Arr::get($record, 'copy.id');

            $transactionRecord = TransactionRecord::firstOrCreate([
                'book_transaction_id' => $transaction->id,
                'book_copy_id' => Arr::get($record, 'copy.id'),
            ]);

            $transactionRecord->uuid = Arr::get($record, 'uuid');
            $transactionRecord->save();
        }

        TransactionRecord::query()
            ->whereBookTransactionId($transaction->id)
            ->whereNotIn('book_copy_id', $bookCopyIds)
            ->delete();
    }

    private function formatParams(Request $request, ?Transaction $transaction = null): array
    {
        $formatted = [
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'transactionable_type' => $request->requester_type,
            'transactionable_id' => $request->requester_id,
            'remarks' => $request->remarks,
        ];

        if (! $transaction) {
            $formatted['team_id'] = auth()->user()?->current_team_id;
        }

        return $formatted;
    }

    private function validateBookReturned(Transaction $transaction): void
    {
        $hasBookReturned = $transaction->records->filter(function ($record) {
            return ! empty($record->return_date->value);
        })->count();

        if ($hasBookReturned) {
            throw ValidationException::withMessages(['message' => trans('user.errors.permission_denied')]);
        }
    }

    public function update(Request $request, Transaction $transaction): void
    {
        $this->validateBookReturned($transaction);

        \DB::beginTransaction();

        $transaction->forceFill($this->formatParams($request, $transaction))->save();

        $this->updateRecords($request, $transaction);

        \DB::commit();
    }

    public function deletable(Transaction $transaction): void
    {
        $this->validateBookReturned($transaction);
    }
}
