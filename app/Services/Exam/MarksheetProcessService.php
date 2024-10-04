<?php

namespace App\Services\Exam;

use App\Actions\Exam\ProcessCreditBasedMarksheet;
use App\Actions\Exam\ProcessCumulativeMarksheet;
use App\Actions\Exam\ProcessExamWiseCameroonMarksheet;
use App\Actions\Exam\ProcessExamWiseMarksheet;
use App\Actions\Exam\ProcessTermWiseCameroonMarksheet;
use App\Actions\Exam\ProcessTermWiseMarksheet;
use App\Actions\Student\FetchBatchWiseStudent;
use App\Enums\Exam\AssessmentAttempt;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\Exam\TermResource;
use App\Models\Academic\Batch;
use App\Models\Exam\Exam;
use App\Models\Exam\Term;
use App\Support\HasGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarksheetProcessService
{
    use HasGrade;

    private function getMarksheetTypes()
    {
        $types = [];

        if (config('config.exam.marksheet_format') == 'India') {
            $types = [
                ['label' => trans('exam.marksheet.exam_wise_credit_based'), 'value' => 'exam_wise_credit_based', 'requires_exam' => true],
                ['label' => trans('exam.marksheet.exam_wise'), 'value' => 'exam_wise', 'requires_exam' => true],
                ['label' => trans('exam.marksheet.term_wise'), 'value' => 'term_wise', 'requires_term' => true],
                ['label' => trans('exam.marksheet.cumulative'), 'value' => 'cumulative'],
            ];
        } elseif (config('config.exam.marksheet_format') == 'Cameroon') {
            $types = [
                ['label' => trans('exam.marksheet.exam_wise'), 'value' => 'exam_wise_cameroon', 'requires_exam' => true],
                ['label' => trans('exam.marksheet.term_wise'), 'value' => 'term_wise_cameroon', 'requires_term' => true],
            ];
        }

        return $types;
    }

    public function preRequisite(Request $request)
    {
        $types = $this->getMarksheetTypes();

        $terms = TermResource::collection(Term::query()
            ->with('division')
            ->byPeriod()
            ->get());

        $exams = ExamResource::collection(Exam::query()
            ->with('term.division')
            ->byPeriod()
            ->get());

        $attempts = AssessmentAttempt::getOptions();

        $templates = $this->getTemplates();

        return compact('types', 'terms', 'exams', 'attempts', 'templates');
    }

    private function getTemplates()
    {
        $predefinedTemplates = collect(glob(resource_path('views/print/exam/marksheet/*.blade.php')))
            ->filter(function ($template) {
                return ! in_array(basename($template), ['header.blade.php', 'sub-header.blade.php']);
            })
            ->map(function ($template) {
                return basename($template, '.blade.php');
            });

        $customTemplates = collect(glob(resource_path('views/print/custom/exam/marksheet/*.blade.php')))
            ->filter(function ($template) {
                return ! in_array(basename($template), ['header.blade.php', 'sub-header.blade.php']);
            })
            ->map(function ($template) {
                return basename($template, '.blade.php');
            });

        $templates = collect($predefinedTemplates->merge($customTemplates))
            ->unique()
            ->filter(function ($template) {
                if (config('config.exam.marksheet_format') == 'India') {
                    return ! Str::contains($template, ['cameroon', 'ghana']);
                } elseif (config('config.exam.marksheet_format') == 'Cameroon') {
                    return Str::endsWith($template, '-cameroon');
                }

                return true;
            })
            ->map(function ($template) {
                return [
                    'label' => Str::toWord($template),
                    'value' => $template,
                ];
            })
            ->values();

        return $templates;
    }

    public function process(Request $request)
    {
        $types = $this->getMarksheetTypes();
        $types = collect($types)->pluck('value')->implode(',');

        $request->validate([
            'type' => 'required|in:'.$types,
            'term' => 'uuid|required_if:type,term_wise',
            'batch' => 'required|uuid',
            'result_date' => 'required|date_format:Y-m-d',
            'template' => 'required|string',
        ], [
            'term.required_if' => trans('validation.required', ['attribute' => trans('exam.term.term')]),
        ]);

        if (Str::startsWith($request->type, 'exam_wise')) {
            $request->validate([
                'exam' => 'required|uuid',
            ]);
        }

        if (Str::startsWith($request->type, 'term_wise')) {
            $request->validate([
                'term' => 'required|uuid',
            ]);
        }

        $batch = Batch::query()
            ->byPeriod()
            ->filterAccessible()
            ->whereUuid($request->batch)
            ->getOrFail(trans('academic.batch.batch'), 'batch');

        $exam = $request->exam ? Exam::query()
            ->with('term.division')
            ->byPeriod()
            ->whereUuid($request->exam)
            ->getOrFail(trans('exam.exam'), 'exam') : null;

        $params = $request->all();
        $params['status'] = 'all';
        $params['select_all'] = true;

        $students = (new FetchBatchWiseStudent)->execute($params);

        if (in_array($request->type, ['cumulative'])) {
            (new ProcessCumulativeMarksheet)->execute($batch, $students, $params);
        } elseif (in_array($request->type, ['term_wise'])) {
            (new ProcessTermWiseMarksheet)->execute($batch, $students, $params);
        } elseif (in_array($request->type, ['exam_wise_cameroon'])) {
            (new ProcessExamWiseCameroonMarksheet)->execute($batch, $students, $params);
        } elseif (in_array($request->type, ['term_wise_cameroon'])) {
            (new ProcessTermWiseCameroonMarksheet)->execute($batch, $students, $params);
        } elseif (in_array($request->type, ['exam_wise_credit_based'])) {
            (new ProcessCreditBasedMarksheet)->execute($batch, $students, $params);
        } else {
            (new ProcessExamWiseMarksheet)->execute($batch, $students, $params);
        }
    }
}
