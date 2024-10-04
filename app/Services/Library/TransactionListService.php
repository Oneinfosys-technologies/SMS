<?php

namespace App\Services\Library;

use App\Contracts\ListGenerator;
use App\Http\Resources\Library\TransactionResource;
use App\Models\Library\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TransactionListService extends ListGenerator
{
    protected $allowedSorts = ['created_at'];

    protected $defaultSort = 'created_at';

    protected $defaultOrder = 'desc';

    public function getHeaders(): array
    {
        $headers = [
            [
                'key' => 'to',
                'label' => trans('library.transaction.props.to'),
                'print_label' => 'to.label',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'requester',
                'label' => trans('library.transaction.props.requester'),
                'print_label' => 'requester.name',
                'print_sub_label' => 'requester.contact_number',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'issueDate',
                'label' => trans('library.transaction.props.issue_date'),
                'print_label' => 'issue_date.formatted',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'recordsCount',
                'label' => trans('library.transaction.count'),
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'createdAt',
                'label' => trans('general.created_at'),
                'print_label' => 'created_at.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
        ];

        if (request()->ajax()) {
            $headers[] = $this->actionHeader;
        }

        return $headers;
    }

    public function filter(Request $request): Builder
    {
        return Transaction::query()
            ->byTeam()
            ->with([
                'transactionable.contact',
            ])
            ->withCount([
                'records',
                'records as non_returned_books_count' => function ($query) {
                    $query->whereNull('return_date');
                },
            ])
            ->filter([
                'App\QueryFilters\DateBetween:start_date,end_date,issue_date',
                'App\QueryFilters\UuidMatch',
            ]);
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        return TransactionResource::collection($this->filter($request)
            ->orderBy($this->getSort(), $this->getOrder())
            ->paginate((int) $this->getPageLength(), ['*'], 'current_page'))
            ->additional([
                'headers' => $this->getHeaders(),
                'meta' => [
                    'allowed_sorts' => $this->allowedSorts,
                    'default_sort' => $this->defaultSort,
                    'default_order' => $this->defaultOrder,
                ],
            ]);
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        return $this->paginate($request);
    }
}
