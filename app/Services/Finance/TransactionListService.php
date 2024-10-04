<?php

namespace App\Services\Finance;

use App\Contracts\ListGenerator;
use App\Enums\Finance\TransactionStatus;
use App\Http\Resources\Finance\TransactionResource;
use App\Models\Academic\Period;
use App\Models\Finance\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class TransactionListService extends ListGenerator
{
    protected $allowedSorts = ['created_at', 'date', 'amount'];

    protected $defaultSort = 'date';

    protected $defaultOrder = 'desc';

    public function getHeaders(): array
    {
        $headers = [
            [
                'key' => 'codeNumber',
                'label' => trans('finance.transaction.props.code_number'),
                'print_label' => 'code_number',
                'print_sub_label' => 'reference_number',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'primaryLedger',
                'label' => trans('finance.ledger.ledger'),
                'print_label' => 'payment.ledger.name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'type',
                'label' => trans('finance.transaction.props.type'),
                'print_label' => 'type.label',
                'print_sub_label' => 'payment_method.name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'date',
                'label' => trans('finance.transaction.props.date'),
                'print_label' => 'date.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'amount',
                'label' => trans('finance.transaction.props.amount'),
                'print_label' => 'amount.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
            [
                'key' => 'secondaryLedger',
                'label' => trans('finance.ledger.ledger'),
                'print_label' => 'record.ledger.name',
                'print_sub_label' => 'transactionable.name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'user',
                'label' => trans('user.user'),
                'print_label' => 'user.profile.name',
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
        $types = Str::toArray($request->query('types'));
        $paymentMethods = Str::toArray($request->query('payment_methods'));
        $onlinePaymentMethods = Str::toArray($request->query('online_payment_methods'));
        $ledgers = Str::toArray($request->query('ledgers'));
        $secondaryledgers = Str::toArray($request->query('secondary_ledgers'));

        $head = $request->query('head');

        return Transaction::query()
            ->withRecord()
            ->withPayment()
            ->when($request->period_id, function ($q, $periodId) {
                $q->where('transactions.period_id', $periodId);
            })
            ->with('transactionable.contact', 'user')
            ->when($types, function ($q, $types) {
                return $q->whereIn('type', $types);
            })
            ->when($head, function ($q, $head) {
                $q->where('head', $head);
            })
            ->when($paymentMethods, function ($q, $paymentMethods) {
                return $q->whereHas('payments', function ($q) use ($paymentMethods) {
                    $q->whereHas('method', function ($q) use ($paymentMethods) {
                        $q->whereIn('uuid', $paymentMethods);
                    });
                });
            })
            ->when($onlinePaymentMethods, function ($q, $onlinePaymentMethods) {
                return $q->whereHas('payments', function ($q) use ($onlinePaymentMethods) {
                    $q->whereHas('method', function ($q) use ($onlinePaymentMethods) {
                        $q->whereIn('uuid', $onlinePaymentMethods);
                    });
                });
            })
            ->when($ledgers, function ($q, $ledgers) {
                return $q->whereHas('payments', function ($q) use ($ledgers) {
                    $q->whereHas('ledger', function ($q) use ($ledgers) {
                        $q->whereIn('uuid', $ledgers);
                    });
                });
            })
            ->when($secondaryledgers, function ($q, $secondaryledgers) {
                return $q->whereHas('records', function ($q) use ($secondaryledgers) {
                    $q->whereHas('ledger', function ($q) use ($secondaryledgers) {
                        $q->whereIn('uuid', $secondaryledgers);
                    });
                });
            })
            ->when($request->query('status'), function ($q, $status) {
                if ($status == TransactionStatus::PENDING->value) {
                    $q->where(function ($q) {
                        $q->where('is_online', 1)->whereNull('processed_at')->where(function ($q) {
                            $q->whereNull('payment_gateway->status')->orWhere('payment_gateway->status', '!=', 'updated');
                        });
                    });
                } elseif ($status == TransactionStatus::FAILED->value) {
                    $q->where(function ($q) {
                        $q->where('is_online', 1)->whereNull('processed_at')->where('payment_gateway->status', '=', 'updated');
                    });
                } elseif ($status == TransactionStatus::SUCCEED->value) {
                    $q->where(function ($q) {
                        $q->where('is_online', 0)
                            ->orWhere(function ($q) {
                                $q->where('is_online', 1)->whereNotNull('processed_at');
                            });
                    })->whereNull('transactions.cancelled_at')->whereNull('rejected_at');
                } elseif ($status == TransactionStatus::CANCELLED->value) {
                    $q->whereNotNull('transactions.cancelled_at');
                } elseif ($status == TransactionStatus::REJECTED->value) {
                    $q->whereNotNull('rejected_at');
                }
            })
            ->filter([
                'App\QueryFilters\LikeMatch:code_number',
                'App\QueryFilters\DateBetween:start_date,end_date,date',
            ]);
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        $types = Str::toArray($request->query('types'));
        $paymentMethods = Str::toArray($request->query('payment_methods'));
        $onlinePaymentMethods = Str::toArray($request->query('online_payment_methods'));
        $ledgers = Str::toArray($request->query('ledgers'));
        $secondaryledgers = Str::toArray($request->query('secondary_ledgers'));

        $head = $request->query('head');

        $periodUuid = $request->query('period');
        $period = $periodUuid ? Period::query()
            ->whereUuid($periodUuid)->first() : null;

        $request->merge([
            'period_id' => $period?->id,
        ]);

        $summary = null;

        if (($request->query('types') == 'payment' || $request->query('types') == 'receipt') && ! empty($request->query('status'))) {
            $summary = Transaction::query()
                ->when($request->period_id, function ($q, $periodId) {
                    $q->where('transactions.period_id', $periodId);
                })
                ->when($types, function ($q, $types) {
                    return $q->whereIn('type', $types);
                })
                ->when($head, function ($q, $head) {
                    $q->where('head', $head);
                })
                ->when($paymentMethods, function ($q, $paymentMethods) {
                    return $q->whereHas('payments', function ($q) use ($paymentMethods) {
                        $q->whereHas('method', function ($q) use ($paymentMethods) {
                            $q->whereIn('uuid', $paymentMethods);
                        });
                    });
                })
                ->when($onlinePaymentMethods, function ($q, $onlinePaymentMethods) {
                    return $q->whereHas('payments', function ($q) use ($onlinePaymentMethods) {
                        $q->whereHas('method', function ($q) use ($onlinePaymentMethods) {
                            $q->whereIn('uuid', $onlinePaymentMethods);
                        });
                    });
                })
                ->when($ledgers, function ($q, $ledgers) {
                    return $q->whereHas('payments', function ($q) use ($ledgers) {
                        $q->whereHas('ledger', function ($q) use ($ledgers) {
                            $q->whereIn('uuid', $ledgers);
                        });
                    });
                })
                ->when($secondaryledgers, function ($q, $secondaryledgers) {
                    return $q->whereHas('records', function ($q) use ($secondaryledgers) {
                        $q->whereHas('ledger', function ($q) use ($secondaryledgers) {
                            $q->whereIn('uuid', $secondaryledgers);
                        });
                    });
                })
                ->when($request->query('status'), function ($q, $status) {
                    if ($status == TransactionStatus::PENDING->value) {
                        $q->where(function ($q) {
                            $q->where('is_online', 1)->whereNull('processed_at')->where(function ($q) {
                                $q->whereNull('payment_gateway->status')->orWhere('payment_gateway->status', '!=', 'updated');
                            });
                        });
                    } elseif ($status == TransactionStatus::FAILED->value) {
                        $q->where(function ($q) {
                            $q->where('is_online', 1)->whereNull('processed_at')->where('payment_gateway->status', '=', 'updated');
                        });
                    } elseif ($status == TransactionStatus::SUCCEED->value) {
                        $q->where(function ($q) {
                            $q->where('is_online', 0)
                                ->orWhere(function ($q) {
                                    $q->where('is_online', 1)->whereNotNull('processed_at');
                                });
                        })->whereNull('transactions.cancelled_at')->whereNull('rejected_at');
                    } elseif ($status == TransactionStatus::CANCELLED->value) {
                        $q->whereNotNull('transactions.cancelled_at');
                    } elseif ($status == TransactionStatus::REJECTED->value) {
                        $q->whereNotNull('rejected_at');
                    }
                })
                ->filter([
                    'App\QueryFilters\LikeMatch:code_number',
                    'App\QueryFilters\DateBetween:start_date,end_date,date',
                ])
                ->selectRaw('SUM(transactions.amount) as total_amount')
                ->first();
        }

        return TransactionResource::collection($this->filter($request)
            ->orderBy($this->getSort(), $this->getOrder())
            ->paginate((int) $this->getPageLength(), ['*'], 'current_page'))
            ->additional([
                'headers' => $this->getHeaders(),
                'meta' => [
                    'sno' => $this->getSno(),
                    'allowed_sorts' => $this->allowedSorts,
                    'default_sort' => $this->defaultSort,
                    'default_order' => $this->defaultOrder,
                    'has_footer' => empty($summary) ? false : true,
                ],
                'footers' => [
                    ['key' => 'codeNumber', 'label' => trans('general.total')],
                    ['key' => 'primaryLedger', 'label' => ''],
                    ['key' => 'type', 'label' => ''],
                    ['key' => 'date', 'label' => ''],
                    ['key' => 'amount', 'label' => \Price::from($summary?->total_amount)?->formatted],
                    ['key' => 'secondaryLedger', 'label' => ''],
                    ['key' => 'user', 'label' => ''],
                    ['key' => 'createdAt', 'label' => ''],
                ],
            ]);
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        return $this->paginate($request);
    }
}
