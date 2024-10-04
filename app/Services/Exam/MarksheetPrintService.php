<?php

namespace App\Services\Exam;

use App\Actions\Student\FetchBatchWiseStudent;
use App\Enums\Exam\AssessmentAttempt;
use App\Enums\Exam\Result as ExamResult;
use App\Http\Resources\Exam\ExamResource;
use App\Http\Resources\Exam\TermResource;
use App\Models\Academic\Batch;
use App\Models\Academic\Period;
use App\Models\Exam\Exam;
use App\Models\Exam\Form;
use App\Models\Exam\Result;
use App\Models\Exam\Schedule;
use App\Models\Exam\Term;
use App\Support\HasGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\ValidationException;

class MarksheetPrintService
{
    use HasGrade;

    public function preRequisite(Request $request)
    {
        $types = [
            ['label' => trans('exam.marksheet.exam_wise'), 'value' => 'exam_wise', 'requires_exam' => true],
            ['label' => trans('exam.marksheet.term_wise'), 'value' => 'term_wise', 'requires_term' => true],
            ['label' => trans('exam.marksheet.cumulative'), 'value' => 'cumulative'],
        ];

        $terms = TermResource::collection(Term::query()
            ->with('division')
            ->byPeriod()
            ->get());

        $exams = ExamResource::collection(Exam::query()
            ->with('term.division')
            ->byPeriod()
            ->get());

        $attempts = AssessmentAttempt::getOptions();

        return compact('types', 'terms', 'exams', 'attempts');
    }

    public function print(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:exam_wise,term_wise,cumulative'],
            'exam' => 'uuid|required_if:type,exam_wise',
            'term' => 'uuid|required_if:type,term_wise',
            'attempt' => ['required', new Enum(AssessmentAttempt::class)],
            'batch' => 'required|uuid',
            'students' => 'nullable',
        ]);

        $batch = Batch::query()
            ->byPeriod()
            ->filterAccessible()
            ->whereUuid($request->batch)
            ->getOrFail(trans('academic.batch.batch'), 'batch');

        $exam = $request->type == 'exam_wise' ? Exam::query()
            ->with('term.division')
            ->byPeriod()
            ->whereUuid($request->exam)
            ->getOrFail(trans('exam.exam'), 'exam') : null;

        $term = $request->type == 'term_wise' ? Term::query()
            ->byPeriod()
            ->whereUuid($request->term)
            ->getOrFail(trans('exam.term'), 'term') : null;

        if ($request->type == 'cumulative') {
            throw ValidationException::withMessages(['message' => trans('general.errors.feature_under_development')]);
        }

        $schedules = Schedule::query()
            ->when($request->type == 'exam_wise', function ($q) use ($exam) {
                $q->whereExamId($exam->id);
            })
            ->when($request->type == 'term_wise', function ($q) use ($term) {
                $q->whereIn('exam_id', $term->exams->pluck('id')->all());
            })
            ->whereBatchId($batch->id)
            ->where('attempt', $request->attempt)
            ->get();

        if (! $schedules->count()) {
            throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('exam.schedule.schedule')])]);
        }

        $schedule = $schedules->last();

        $grade = $schedule->grade;

        if ($request->type == 'term_wise') {
            $exams = Exam::query()
                ->with('term.division')
                ->whereTermId($term->id)
                ->orderBy('position', 'asc')
                ->get();

            $exam = $exams->last();
        }

        if (auth()->user()->hasAnyRole(['student', 'guardian'])) {
            if ($schedule->getConfig('marksheet_status') != 'processed') {
                throw ValidationException::withMessages(['message' => trans('exam.marksheet.not_generated')]);
            }

            if (! Arr::get($exam->config, $request->attempt.'_attempt.publish_marksheet')) {
                throw ValidationException::withMessages(['message' => trans('exam.marksheet.not_published')]);
            }
        }

        $queryStudents = Str::toArray($request->query('students'));

        $params = $request->all();

        if (count($queryStudents)) {
            $params['students'] = $queryStudents;
            $params['select_all'] = false;
        } else {
            $params['select_all'] = true;
        }

        if ($request->boolean('show_all_student')) {
            $params['status'] = 'all';
        }

        $schedules = collect([$schedule]);

        if ($request->boolean('show_course_wise')) {
            $params['batch'] = $batch->course->batches->pluck('uuid')->all();

            $schedules = Schedule::query()
                ->whereExamId($schedule->exam_id)
                ->whereHas('batch', function ($q) use ($params) {
                    $q->whereIn('uuid', $params['batch']);
                })
                ->where('attempt', $request->attempt)
                ->get();
        }

        $students = (new FetchBatchWiseStudent)->execute($params);

        if ($schedule->getMeta('has_form')) {

            $examForms = Form::query()
                ->whereIn('schedule_id', $schedules->pluck('id')->all())
                ->whereNotNull('submitted_at')
                ->whereNotNull('approved_at')
                ->get();

            $students = $students->filter(function ($student) use ($examForms) {
                return $examForms->contains('student_id', $student->id);
            })->values();
        }

        $examResults = Result::query()
            ->when($request->type == 'exam_wise', function ($q) use ($exam) {
                $q->whereExamId($exam->id);
            })
            ->when($request->type == 'term_wise', function ($q) use ($term) {
                $q->whereTermId($term->id);
            })
            ->whereIn('student_id', $students->pluck('id')->all())
            ->where('attempt', $request->attempt)
            ->get();

        if (! $examResults->count()) {
            throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('exam.result')])]);
        }

        $students = $students->filter(function ($student) use ($examResults) {
            return $examResults->contains('student_id', $student->id);
        })->values();

        if (! $students->count()) {
            throw ValidationException::withMessages(['message' => trans('global.could_not_find', ['attribute' => trans('exam.result')])]);
        }

        foreach ($students as $student) {
            $examResult = $examResults->where('student_id', $student->id)->first();

            $student->marks = $examResult?->marks ?? [];
            $student->grading_marks = $examResult?->getMeta('grading_marks') ?? [];
            $student->observation_marks = $examResult?->getMeta('observation_marks') ?? [];

            $total = $examResult?->total_marks ?? 0;
            $obtained = $examResult?->obtained_marks ?? 0;

            $student->summary = [
                'total' => $total,
                'obtained' => $obtained,
                'grade' => $this->getGrade($grade, $total, $obtained, 'code'),
                'percentage' => $examResult?->percentage ?? 0,
                'attempt' => AssessmentAttempt::getDetail($schedule->attempt),
                'result_date' => \Cal::date($examResult?->result_date),
                'result' => ExamResult::getDetail($examResult?->result ?? ''),
                'reassessment_subjects' => implode(', ', Arr::get($examResult?->subjects, 'reassessment', [])),
                'failed_subjects' => implode(', ', Arr::get($examResult?->subjects, 'failed', [])),
            ];
        }

        $period = Period::find($exam->period_id);
        $title = Arr::get($exam->config_detail, $request->attempt.'_attempt.title', $exam->name);
        $subTitle = Arr::get($exam->config_detail, $request->attempt.'_attempt.sub_title', $period->code);

        $titles = [
            [
                'label' => $title,
                'align' => 'center',
                'class' => 'heading',
            ],
            [
                'label' => $subTitle,
                'align' => 'center',
                'class' => 'mt-2 sub-heading',
            ],
            [
                'label' => $batch->course->name.' '.$batch->name,
                'align' => 'center',
                'class' => 'mt-2 sub-heading',
            ],
        ];

        $boxWidth = match ((int) $request->query('column', 1)) {
            1 => '100%',
            2 => '48%',
            3 => '33%',
            default => '100%',
        };

        $layout = [
            'column' => $request->query('column', 1),
            'margin_top' => $request->query('margin_top', 0),
            'box_width' => $boxWidth,
            'show_print_date_time' => (bool) Arr::get($exam->config_detail, 'show_print_date_time'),
            'show_watermark' => (bool) Arr::get($exam->config_detail, 'show_watermark'),
            'signatory1' => Arr::get($exam->config_detail, 'signatory1'),
            'signatory2' => Arr::get($exam->config_detail, 'signatory2'),
            'signatory3' => Arr::get($exam->config_detail, 'signatory3'),
            'signatory4' => Arr::get($exam->config_detail, 'signatory4'),
        ];

        $template = $examResult->getMeta('template');

        if (! $template) {
            $template = Arr::get($schedule->config, 'marksheet_template', 'default');
        }

        return view()->first([config('config.print.custom_path').'exam.marksheet.'.$template, 'print.exam.marksheet.'.$template], compact('batch', 'exam', 'schedule', 'students', 'titles', 'layout', 'period'))->render();
    }
}
