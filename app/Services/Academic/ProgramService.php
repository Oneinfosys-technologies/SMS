<?php

namespace App\Services\Academic;

use App\Enums\Academic\ProgramType;
use App\Models\Academic\Program;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProgramService
{
    public function preRequisite(): array
    {
        $types = ProgramType::getOptions();

        return compact('types');
    }

    public function create(Request $request): Program
    {
        \DB::beginTransaction();

        $program = Program::forceCreate($this->formatParams($request));

        \DB::commit();

        return $program;
    }

    private function formatParams(Request $request, ?Program $program = null): array
    {
        $formatted = [
            'name' => $request->name,
            'code' => $request->code,
            'shortcode' => $request->shortcode,
            'type' => $request->type,
            'alias' => $request->alias,
            'description' => $request->description,
        ];

        $config = $program?->config ?? [];

        $config['enable_registration'] = $request->boolean('enable_registration');

        $formatted['config'] = $config;

        if (! $program) {
            $formatted['team_id'] = auth()->user()?->current_team_id;
        }

        return $formatted;
    }

    public function update(Request $request, Program $program): void
    {
        \DB::beginTransaction();

        $program->forceFill($this->formatParams($request, $program))->save();

        \DB::commit();
    }

    public function deletable(Program $program, $validate = false): ?bool
    {
        $divisionExists = \DB::table('divisions')
            ->whereProgramId($program->id)
            ->exists();

        if ($divisionExists) {
            throw ValidationException::withMessages(['message' => trans('global.associated_with_dependency', ['attribute' => trans('academic.program.program'), 'dependency' => trans('academic.division.division')])]);
        }

        return true;
    }
}
