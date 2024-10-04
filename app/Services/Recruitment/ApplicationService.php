<?php

namespace App\Services\Recruitment;

use App\Models\Recruitment\Application;
use Illuminate\Http\Request;

class ApplicationService
{
    public function preRequisite(Request $request): array
    {
        return [];
    }

    public function create(Request $request): Application
    {
        \DB::beginTransaction();

        $vacancy = Application::forceCreate($this->formatParams($request));

        $vacancy->addMedia($request);

        \DB::commit();

        return $vacancy;
    }

    private function formatParams(Request $request, ?Application $vacancy = null): array
    {
        $formatted = [
            //
        ];

        if (! $vacancy) {
            //
        }

        return $formatted;
    }

    public function update(Request $request, Application $vacancy): void
    {
        \DB::beginTransaction();

        $vacancy->forceFill($this->formatParams($request, $vacancy))->save();

        $vacancy->updateMedia($request);

        \DB::commit();
    }

    public function deletable(Application $vacancy): void
    {
        //
    }
}
