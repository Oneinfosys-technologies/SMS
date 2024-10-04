<?php

namespace App\Actions\Exam;

use App\Enums\Exam\AssessmentAttempt;
use App\Models\Academic\Batch;
use App\Models\Academic\Subject;
use App\Models\Exam\Exam;
use App\Models\Exam\Schedule;
use App\Models\Exam\Term;
use App\Models\Student\SubjectWiseStudent;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class ProcessTermWiseMarksheet
{
    public function execute(Batch $batch, Collection $students, array $params)
    {
        throw ValidationException::withMessages(['message' => trans('general.errors.feature_under_development')]);
        $params['subject_absent_criteria'] = 'all'; // all | any
        $params['cumulative_assessment'] = false;

        $allStudents = $students;

        $term = Term::query()
            ->byPeriod()
            ->where('uuid', Arr::get($params, 'term'))
            ->firstOrFail();

        $exams = Exam::query()
            ->byPeriod()
            ->where('term_id', $term->id)
            ->get();

        $schedules = Schedule::query()
            ->with('records', 'assessment', 'grade')
            ->whereIn('exam_id', $exams->pluck('id')->all())
            ->whereBatchId($batch->id)
            ->where('is_reassessment', false)
            ->where('attempt', AssessmentAttempt::FIRST->value)
            ->get();

        $lastSchedule = $schedules->last();

        $examGrade = $lastSchedule->grade;
        $failGrades = collect($examGrade->records)->where('is_fail_grade', true)->pluck('code')->all();

        $subjects = Subject::query()
            ->withSubjectRecord($batch->id, $batch->course_id)
            ->orderBy('subjects.position', 'asc')
            ->get();

        $examRecords = [];
        foreach ($schedules as $schedule) {
            $scheduleAssessmentRecords = collect($schedule->assessment->records ?? []);
            foreach ($schedule->records as $record) {
                $subject = $subjects->firstWhere('id', $record->subject_id);

                if (! $subject) {
                    continue;
                }

                $hasExam = $record->getConfig('has_exam');

                if (! $hasExam) {
                    continue;
                }

                $recordMarks = $record->marks;
                $recordAssessments = $record->getConfig('assessments', []);

                $assessments = [];
                foreach ($recordAssessments as $recordAssessment) {
                    $code = Arr::get($recordAssessment, 'code');

                    $assessmentMaxMark = Arr::get($recordAssessment, 'max_mark', 0);
                    $originalAssessmentMaxMark = $assessmentMaxMark;

                    $scheduleAssessmentRecord = $scheduleAssessmentRecords->firstWhere('code', $code);
                    $assessments[] = [
                        'code' => $code,
                        'name' => Arr::get($scheduleAssessmentRecord, 'name'),
                        'position' => Arr::get($scheduleAssessmentRecord, 'position', 0),
                        'max_mark' => $assessmentMaxMark,
                        'original_max_mark' => $originalAssessmentMaxMark,
                    ];
                }

                $assessments = collect($assessments)->sortBy('position')->values()->all();

                $examRecords[] = [
                    'exam_id' => $schedule->exam_id,
                    'schedule_id' => $schedule->id,
                    'subject_id' => $record->subject_id,
                    'has_grading' => $subject->has_grading,
                    'is_elective' => $subject->is_elective,
                    'assessments' => $assessments,
                    'not_applicable_students' => $record->getConfig('not_applicable_students', []),
                    'marks' => $recordMarks,
                ];
            }
        }

        $subjectWiseStudents = SubjectWiseStudent::query()
            ->whereBatchId($batch->id)
            ->whereIn('student_id', $students->pluck('id')->all())
            ->get();
    }
}
