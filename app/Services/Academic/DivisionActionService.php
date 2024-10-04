<?php

namespace App\Services\Academic;

use App\Models\Academic\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DivisionActionService
{
    public function updateConfig(Request $request, Division $division): void
    {
        //
    }

    public function reorder(Request $request): void
    {
        $divisions = $request->divisions ?? [];

        $allDivisions = Division::query()
            ->byPeriod()
            ->get();

        foreach ($divisions as $index => $divisionItem) {
            $division = $allDivisions->firstWhere('uuid', Arr::get($divisionItem, 'uuid'));

            if (! $division) {
                continue;
            }

            $division->position = $index + 1;
            $division->save();
        }
    }
}
