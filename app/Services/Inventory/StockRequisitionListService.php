<?php

namespace App\Services\Inventory;

use App\Contracts\ListGenerator;
use App\Http\Resources\Inventory\StockRequisitionResource;
use App\Models\Asset\Building\Room;
use App\Models\Inventory\StockRequisition;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class StockRequisitionListService extends ListGenerator
{
    protected $allowedSorts = ['created_at', 'date'];

    protected $defaultSort = 'date';

    protected $defaultOrder = 'desc';

    public function getHeaders(): array
    {
        $headers = [
            [
                'key' => 'codeNumber',
                'label' => trans('inventory.stock_requisition.props.code_number'),
                'print_label' => 'code_number',
                'print_sub_label' => 'inventory.name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'vendor',
                'label' => trans('inventory.vendor'),
                'print_label' => 'vendor.name',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'date',
                'label' => trans('inventory.stock_requisition.props.date'),
                'print_label' => 'date.formatted',
                'sortable' => true,
                'visibility' => true,
            ],
            // [
            //     'key' => 'total',
            //     'label' => trans('inventory.stock_requisition.props.total'),
            //     'print_label' => 'total.formatted',
            //     'sortable' => false,
            //     'visibility' => true,
            // ],
            [
                'key' => 'place',
                'label' => trans('inventory.place'),
                'print_label' => 'place.name',
                'print_sub_label' => 'place.floor.name_with_block',
                'sortable' => false,
                'visibility' => true,
            ],
            [
                'key' => 'employee',
                'label' => trans('inventory.stock_requisition.props.requested_by'),
                'print_label' => 'employee.name',
                'print_sub_label' => 'employee.designation',
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
        $inventory = $request->query('inventory');
        $vendors = Str::toArray($request->query('vendors'));
        $places = Str::toArray($request->query('places'));

        return StockRequisition::query()
            ->filterAccessible()
            ->with(['inventory', 'vendor', 'place' => fn ($q) => $q->withFloorAndBlock(), 'employee' => fn ($q) => $q->summary()])
            ->when($inventory, function ($q, $inventory) {
                return $q->whereHas('inventory', function ($q) use ($inventory) {
                    $q->where('uuid', $inventory);
                });
            })
            ->when($vendors, function ($q, $vendors) {
                $q->whereHas('vendor', function ($q) use ($vendors) {
                    $q->whereIn('uuid', $vendors);
                });
            })
            ->when($places, function ($q, $places) {
                $q->whereHasMorph(
                    'place',
                    [Room::class],
                    function (Builder $query) use ($places) {
                        $query->whereIn('uuid', $places);
                    }
                );
            })
            ->filter([
                'App\QueryFilters\LikeMatch:code_number',
                'App\QueryFilters\DateBetween:start_date,end_date,date',
                'App\QueryFilters\UuidMatch',
            ]);
    }

    public function paginate(Request $request): AnonymousResourceCollection
    {
        return StockRequisitionResource::collection($this->filter($request)
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
