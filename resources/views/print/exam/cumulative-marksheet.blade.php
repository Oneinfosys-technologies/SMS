<x-print.layout type="{{ Arr::get($layout, 'column', 1) == 1 ? 'centered' : 'full-page' }}" :spacing="false">
    @foreach ($students->chunk(Arr::get($layout, 'column', 1)) as $studentPair)
        <div style="margin-top: {{ Arr::get($layout, 'margin_top', 0) }}mm; page-break-after: always;">
            <div style="display: flex; justify-content: space-between;">
                @foreach ($studentPair as $student)
                    <div style="width: {{ Arr::get($layout, 'box_width') }}; border: 1px solid black;">

                        <div class="{{ Arr::get($layout, 'watermark') ? 'watermark-container' : '' }}">
                            @if (Arr::get($layout, 'watermark'))
                                <img class="watermark-image" src="{{ url(config('config.assets.logo')) }}">
                            @endif

                            @includeFirst([
                                config('config.print.custom_path') . 'exam.header',
                                'print.exam.header',
                            ])

                            @if (view()->exists(config('config.print.custom_path') . 'exam.marksheet-subheader'))
                                @include(config('config.print.custom_path') . 'exam.marksheet-subheader', [
                                    'titles' => $titles,
                                ])
                            @else
                                <div style="padding: 10px 15px;">
                                    <table width="100%" border="0">
                                        <tr>
                                            <td colspan="2">
                                                @foreach ($titles as $title)
                                                    @if (Arr::get($title, 'label'))
                                                        <div class="{{ Arr::get($title, 'class') }}">
                                                            {{ Arr::get($title, 'label') }}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endif

                            <table class="mt-2 inner-table cellpadding" width="100%">
                                <tr>
                                    <td>{{ trans('student.props.name') }}</td>
                                    <td class="text-right">{{ $student->name }}</td>
                                    <td>{{ trans('contact.props.birth_date') }}</td>
                                    <td class="text-right">{{ \Cal::date($student->birth_date)->formatted }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('student.admission.props.code_number') }}</td>
                                    <td class="text-right">{{ $student->code_number }}</td>
                                    <td>{{ trans('student.roll_number.roll_number') }}</td>
                                    <td class="text-right">{{ $student->roll_number }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('contact.props.father_name') }}</td>
                                    <td class="text-right">{{ $student->father_name }}</td>
                                    <td>{{ trans('contact.props.mother_name') }}</td>
                                    <td class="text-right">{{ $student->mother_name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('academic.course.course') }}</td>
                                    <td class="text-right">{{ $student->course_name . ' ' . $student->batch_name }}
                                    </td>
                                    <td>{{ trans('contact.props.contact_number') }}</td>
                                    <td class="text-right">{{ $student->contact_number }}</td>
                                </tr>
                                <tr>
                                    <td>{{ trans('student.health_record.props.height') }} /
                                        {{ trans('student.health_record.props.weight') }}</td>
                                    <td class="text-right">{{ Arr::get($student->health_record, 'general.height') }}cm
                                        / {{ Arr::get($student->health_record, 'general.weight') }}kg
                                    </td>
                                    <td>{{ trans('student.attendance.attendance') }}</td>
                                    <td class="text-right">{{ Arr::get($student->attendance, 'present') }} /
                                        {{ Arr::get($student->attendance, 'working_days') }}</td>
                                </tr>
                            </table>

                            <table class="mt-4 inner-table font-85pc" width="100%">
                                @foreach ($student->rows as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td colspan="{{ Arr::get($cell, 'colspan', 1) }}"
                                                @class([
                                                    'text-center' => Arr::get($cell, 'align') == 'center',
                                                    'font-110pc' => Arr::get($cell, 'font-size') == 'lg',
                                                    'font-120pc' => Arr::get($cell, 'font-size') == 'xl',
                                                    'py-2' => Arr::get($cell, 'font-size') == 'xl',
                                                    'font-weight-bold' => Arr::get($cell, 'bold'),
                                                ])
                                                rowspan="{{ Arr::get($cell, 'rowspan', 1) }}">
                                                {{ Arr::get($cell, 'label') }}

                                                @if (Arr::get($cell, 'blank'))
                                                    &nbsp;
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach

                                @foreach ($student->gradingRows as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td colspan="{{ Arr::get($cell, 'colspan', 1) }}"
                                                @class([
                                                    'text-center' => Arr::get($cell, 'align') == 'center',
                                                    'font-weight-bold' => Arr::get($cell, 'bold'),
                                                ])
                                                rowspan="{{ Arr::get($cell, 'rowspan', 1) }}">
                                                {{ Arr::get($cell, 'label') }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>

                            <table class="mt-4 inner-table font-80pc" width="100%">
                                @foreach ($student->observationRows as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td colspan="{{ Arr::get($cell, 'colspan', 1) }}"
                                                @class([
                                                    'text-center' => Arr::get($cell, 'align') == 'center',
                                                    'font-weight-bold' => Arr::get($cell, 'bold'),
                                                ])
                                                rowspan="{{ Arr::get($cell, 'rowspan', 1) }}">
                                                {{ Arr::get($cell, 'label') }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </table>

                            <table class="mt-4" width="100%">
                                <tr>
                                    <td style="width:50%; padding-left:10px;" valign="top">
                                        <div>
                                            <span class="font-weight-bold">{{ trans('exam.result') }}:</span>
                                            {{ Arr::get($student->comment, 'result') }}
                                        </div>
                                        <div class="mt-2">
                                            <span class="font-weight-bold">{{ trans('exam.comment') }}:</span>
                                            {{ Arr::get($student->comment, 'comment') }}
                                        </div>
                                    </td>
                                    <td style="width:50%;">
                                        @if (Arr::get($params, 'show_grade_detail'))
                                            <h2 class="sub-heading">{{ trans('exam.grade.grade') }}</h2>
                                            <table border="1" class="mt-4 table font-90pc">
                                                @foreach ($grade->records as $record)
                                                    <tr>
                                                        <td>{{ Arr::get($record, 'code') }}</td>
                                                        <td>{{ Arr::get($record, 'min_score') }} -
                                                            {{ Arr::get($record, 'max_score') }}</td>
                                                        <td>{{ Arr::get($record, 'label') }}</td>
                                                @endforeach
                                            </table>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            @includeFirst(
                                [config('config.print.custom_path') . 'exam.signatory', 'print.exam.signatory'],
                                ['layout' => $layout, 'margin' => 'mt-32']
                            )

                            @if (Arr::get($layout, 'show_print_date_time'))
                                <div class="mt-4" style="padding-left: 10px; padding-right: 10px;">
                                    <p>{{ trans('general.printed_at') }}: {{ \Cal::dateTime(now())->formatted }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</x-print.layout>
