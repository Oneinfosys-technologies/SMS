<?php

namespace App\Http\Requests\Inventory;

use App\Models\Inventory\StockCategory;
use App\Models\Inventory\StockItem;
use Illuminate\Foundation\Http\FormRequest;

class StockItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:2', 'max:100'],
            'code' => ['nullable', 'min:2', 'max:100'],
            'category' => ['required', 'uuid'],
            'unit' => ['required', 'min:2', 'max:10'],
            'description' => ['nullable', 'min:2', 'max:100'],
        ];
    }

    public function withValidator($validator)
    {
        if (! $validator->passes()) {
            return;
        }

        $validator->after(function ($validator) {

            $stockItemUuid = $this->route('stock_item');

            $stockCategory = StockCategory::query()
                ->byTeam()
                ->filterAccessible()
                ->whereUuid($this->category)
                ->getOrFail(__('inventory.stock_category.stock_category'), 'category');

            $existingRecord = StockItem::query()
                ->when($stockItemUuid, function ($q, $stockItemUuid) {
                    $q->where('uuid', '!=', $stockItemUuid);
                })
                ->whereStockCategoryId($stockCategory->id)
                ->whereName($this->name)
                ->exists();

            if ($existingRecord) {
                $validator->errors()->add('name', __('inventory.stock_item.duplicate_item'));
            }

            if ($this->code) {
                $existingRecord = StockItem::query()
                    ->when($stockItemUuid, function ($q, $stockItemUuid) {
                        $q->where('uuid', '!=', $stockItemUuid);
                    })
                    ->whereStockCategoryId($stockCategory->id)
                    ->whereCode($this->code)
                    ->exists();

                if ($existingRecord) {
                    $validator->errors()->add('code', __('inventory.stock_item.duplicate_item'));
                }
            }

            $this->merge([
                'stock_category_id' => $stockCategory->id,
            ]);
        });
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => __('inventory.stock_item.props.name'),
            'code' => __('inventory.stock_item.props.code'),
            'category' => __('inventory.stock_category.stock_category'),
            'unit' => __('inventory.stock_item.props.unit'),
            'description' => __('inventory.stock_item.props.description'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [];
    }
}
