<x-print.layout type="full-page">

    <h2 class="heading">Student Profile (Missing)</h2>

    {{-- <table width="100%">
        <tr>
            <td>Total Days: {{ $totalDays }}</td>
            <td class="text-center">Holidays: {{ $holidayCount }}</td>
            <td class="text-right">Working Days: {{ $workingDays }}</td>
        </tr>
    </table> --}}

    <table border="1" class="border-dark mt-2 table" width="100%" border="0" cellspacing="4" cellpadding="0">
        <thead>
            <th>{{ trans('academic.batch.batch') }}</th>
            <th>{{ trans('academic.batch_incharge.batch_incharge') }}</th>
            <th>Total</th>
            <th>Missing Alternate Number</th>
            <th>Missing Photo</th>
            <th>Missing Caste</th>
            <th>Missing Category</th>
            <th>Missing Religion</th>
            <th>Missing Aadhar</th>
        </thead>
        @foreach ($data as $item)
            <tr>
                <td>{{ Arr::get($item, 'batch') }}</td>
                <td>{{ Arr::get($item, 'incharge') }}</td>
                <td>{{ Arr::get($item, 'total') }}</td>
                <td>{{ Arr::get($item, 'missing_alternate_number') }}</td>
                <td>{{ Arr::get($item, 'missing_photo') }}</td>
                <td>{{ Arr::get($item, 'missing_caste') }}</td>
                <td>{{ Arr::get($item, 'missing_category') }}</td>
                <td>{{ Arr::get($item, 'missing_religion') }}</td>
                <td>{{ Arr::get($item, 'missing_aadhar') }}</td>
            </tr>
        @endforeach
    </table>
</x-print.layout>
