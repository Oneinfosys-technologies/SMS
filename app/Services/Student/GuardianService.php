<?php

namespace App\Services\Student;

use App\Actions\CreateContact;
use App\Enums\FamilyRelation;
use App\Models\Guardian;
use App\Models\Student\Student;
use Illuminate\Http\Request;

class GuardianService
{
    public function preRequisite(Request $request): array
    {
        $relations = FamilyRelation::getOptions();

        return compact('relations');
    }

    public function create(Request $request, Student $student): Guardian
    {
        \DB::beginTransaction();

        $params = $request->all();
        $params['source'] = 'guardian';

        $contact = (new CreateContact)->execute($params);

        $guardian = Guardian::firstOrCreate([
            'contact_id' => $contact->id,
            'primary_contact_id' => $student->contact_id,
        ]);

        $guardian->relation = $request->relation;
        $guardian->save();

        \DB::commit();

        return $guardian;
    }

    public function update(Request $request, Student $student, Guardian $guardian): void
    {
        \DB::beginTransaction();

        $guardian->relation = $request->relation;
        $guardian->save();

        \DB::commit();
    }

    public function deletable(Student $student, Guardian $guardian): void
    {
        //
    }
}
