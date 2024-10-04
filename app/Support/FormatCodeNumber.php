<?php

namespace App\Support;

use App\Models\Academic\Batch;
use App\Models\Academic\Course;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait FormatCodeNumber
{
    public function preFormatForAcademic(string $string, array $shortcode)
    {
        if (Str::of($string)->contains('%PERIOD%')) {
            $string = str_replace('%PERIOD%', Arr::get($shortcode, 'period_shortcode'), $string);
        }

        if (Str::of($string)->contains('%PROGRAM%')) {
            $string = str_replace('%PROGRAM%', Arr::get($shortcode, 'program_shortcode'), $string);
        }

        if (Str::of($string)->contains('%DIVISION%')) {
            $string = str_replace('%DIVISION%', Arr::get($shortcode, 'division_shortcode'), $string);
        }

        if (Str::of($string)->contains('%COURSE%')) {
            $string = str_replace('%COURSE%', Arr::get($shortcode, 'course_shortcode'), $string);
        }

        return $string;
    }

    public function preFormatForAcademicCourse(int $courseId, string $string)
    {
        $shortcode = Course::query()
            ->select(
                'courses.shortcode as course_shortcode',
                'divisions.shortcode as division_shortcode',
                'periods.shortcode as period_shortcode',
                'programs.shortcode as program_shortcode'
            )
            ->where('courses.id', $courseId)
            ->join('divisions', 'courses.division_id', '=', 'divisions.id')
            ->join('periods', 'divisions.period_id', '=', 'periods.id')
            ->join('programs', 'divisions.program_id', '=', 'programs.id')
            ->first()
            ?->toArray() ?? [];

        return $this->preFormatForAcademic($string, $shortcode);
    }

    public function preFormatForAcademicBatch(int $batchId, string $string)
    {
        $shortcode = Batch::query()
            ->select(
                'courses.shortcode as course_shortcode',
                'divisions.shortcode as division_shortcode',
                'periods.shortcode as period_shortcode',
                'programs.shortcode as program_shortcode'
            )
            ->where('batches.id', $batchId)
            ->join('courses', 'batches.course_id', '=', 'courses.id')
            ->join('divisions', 'courses.division_id', '=', 'divisions.id')
            ->join('periods', 'divisions.period_id', '=', 'periods.id')
            ->join('programs', 'divisions.program_id', '=', 'programs.id')
            ->first()
            ?->toArray() ?? [];

        return $this->preFormatForAcademic($string, $shortcode);
    }

    public function getCodeNumber(int $number = 0, int $digit = 0, string $format = '', string $date = ''): array
    {
        if (! $date) {
            $date = today()->toDateString();
        }

        $date = strtotime($date);

        $numberFormat = $format;

        $string = $format;

        $string = str_replace('%YEAR%', date('Y', $date), $string);
        $string = str_replace('%YEAR_SHORT%', date('y', $date), $string);
        $string = str_replace('%MONTH%', date('F', $date), $string);
        $string = str_replace('%MONTH_SHORT%', date('M', $date), $string);
        $string = str_replace('%MONTH_NUMBER%', date('m', $date), $string);
        $string = str_replace('%MONTH_NUMBER_SHORT%', date('n', $date), $string);
        $string = str_replace('%DAY%', date('d', $date), $string);
        $string = str_replace('%DAY_SHORT%', date('j', $date), $string);

        return [
            'code_number' => str_replace('%NUMBER%', str_pad($number, $digit, '0', STR_PAD_LEFT), $string),
            'number_format' => $numberFormat,
            'number' => $number,
        ];
    }
}
