<?php

namespace App\Services\Student;

use App\Actions\CreateTag;
use App\Models\Student\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class StudentActionService
{
    public function setDefaultPeriod(Request $request, Student $student)
    {
        $contact = $student->contact;

        $user = $contact->user;

        if (! $user) {
            throw ValidationException::withMessages([
                'message' => trans('global.could_not_find', ['attribute' => trans('user.user')]),
            ]);
        }

        $preference = $user->user_preference;
        $preference['academic'] = Arr::get($preference, 'academic', []);
        $preference['academic']['period_id'] = $student->period_id;

        $user->preference = $preference;
        $user->save();
    }

    public function updateTags(Request $request, Student $student)
    {
        $request->validate([
            'tags' => 'array',
            'tags.*' => 'required|string|distinct',
        ]);

        $tags = (new CreateTag)->execute($request->input('tags', []));

        $student->tags()->sync($tags);
    }
}
