<?php

namespace App\Services\Exam;

use App\Http\Resources\Academic\DivisionResource;
use App\Models\Academic\Division;
use App\Models\Exam\Term;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TermService
{
    public function preRequisite(Request $request)
    {
        $divisions = DivisionResource::collection(Division::query()
            ->byPeriod()
            ->get());

        return compact('divisions');
    }

    public function create(Request $request): Term
    {
        \DB::beginTransaction();

        $term = Term::forceCreate($this->formatParams($request));

        \DB::commit();

        return $term;
    }

    private function formatParams(Request $request, ?Term $term = null): array
    {
        $formatted = [
            'name' => $request->name,
            'division_id' => $request->division_id,
            'display_name' => $request->display_name,
            'position' => $request->position,
            'description' => $request->description,
        ];

        if (! $term) {
            $formatted['period_id'] = auth()->user()->current_period_id;
        }

        return $formatted;
    }

    public function update(Request $request, Term $term): void
    {
        \DB::beginTransaction();

        $term->forceFill($this->formatParams($request, $term))->save();

        \DB::commit();
    }

    public function deletable(Term $term): bool
    {
        $examExists = \DB::table('exams')
            ->whereTermId($term->id)
            ->exists();

        if ($examExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('exam.term.term'), 'dependency' => trans('exam.exam')])]);
        }

        return true;
    }
}
