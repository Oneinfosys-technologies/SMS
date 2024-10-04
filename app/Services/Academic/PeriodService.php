<?php

namespace App\Services\Academic;

use App\Http\Resources\Academic\SessionResource;
use App\Models\Academic\Period;
use App\Models\Academic\Session;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PeriodService
{
    public function preRequisite(): array
    {
        $sessions = SessionResource::collection(Session::query()
            ->byTeam()
            ->get());

        return compact('sessions');
    }

    public function findByUuidOrFail(string $uuid): Period
    {
        return Period::query()
            ->byTeam()
            ->findByUuidOrFail($uuid, trans('academic.period.period'), 'message');
    }

    public function create(Request $request): Period
    {
        \DB::beginTransaction();

        $period = Period::forceCreate($this->formatParams($request));

        \DB::commit();

        return $period;
    }

    private function formatParams(Request $request, ?Period $period = null): array
    {
        $formatted = [
            'name' => $request->name,
            'code' => $request->code,
            'shortcode' => $request->shortcode,
            'alias' => $request->alias,
            'session_id' => $request->session_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
        ];

        $config = $period?->config ?? [];

        $config['enable_registration'] = $request->boolean('enable_registration');

        $formatted['config'] = $config;

        if (! $period) {
            $formatted['is_default'] = $request->boolean('is_default');
            $formatted['team_id'] = auth()->user()?->current_team_id;
        }

        return $formatted;
    }

    public function update(Request $request, Period $period): void
    {
        \DB::beginTransaction();

        $period->forceFill($this->formatParams($request, $period))->save();

        \DB::commit();
    }

    public function deletable(Period $period, $validate = false): ?bool
    {
        $divisionExists = \DB::table('divisions')
            ->wherePeriodId($period->id)
            ->exists();

        if ($divisionExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.period.period'), 'dependency' => trans('academic.division.division')])]);
        }

        $subjectExists = \DB::table('subjects')
            ->wherePeriodId($period->id)
            ->exists();

        if ($subjectExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.period.period'), 'dependency' => trans('academic.subject.subject')])]);
        }

        $transactionExists = \DB::table('transactions')
            ->wherePeriodId($period->id)
            ->exists();

        if ($transactionExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.period.period'), 'dependency' => trans('finance.transaction.transaction')])]);
        }

        $feeGroupExists = \DB::table('fee_groups')
            ->wherePeriodId($period->id)
            ->exists();

        if ($feeGroupExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.period.period'), 'dependency' => trans('finance.fee_group.fee_group')])]);
        }

        $feeHeadExists = \DB::table('fee_heads')
            ->wherePeriodId($period->id)
            ->exists();

        if ($feeHeadExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.period.period'), 'dependency' => trans('finance.fee_head.fee_head')])]);
        }

        $transportCircleExists = \DB::table('transport_circles')
            ->wherePeriodId($period->id)
            ->exists();

        if ($transportCircleExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.period.period'), 'dependency' => trans('transport.circle.circle')])]);
        }

        return true;
    }
}
