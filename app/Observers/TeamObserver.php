<?php

namespace App\Observers;

use App\Actions\Finance\CreateDefaultLedgerType;
use App\Helpers\SysHelper;
use App\Models\Team;
use App\Models\User;

class TeamObserver
{
    /**
     * Handle the Team "created" event.
     *
     * @return void
     */
    public function created(Team $team)
    {
        $user = User::where('meta->is_default', true)->first();

        if ($user) {
            SysHelper::setTeam($team->id);
            $user->assignRole('admin');
            SysHelper::setTeam(auth()->user()?->current_team_id);
        }

        (new CreateDefaultLedgerType)->execute($team->id);
    }

    /**
     * Handle the Team "updated" event.
     *
     * @return void
     */
    public function updated(Team $team)
    {
        //
    }

    /**
     * Handle the Team "deleted" event.
     *
     * @return void
     */
    public function deleted(Team $team)
    {
        \DB::table('roles')->whereTeamId($team->id)->delete();
        \DB::table('model_has_roles')->whereTeamId($team->id)->delete();
    }

    /**
     * Handle the Team "restored" event.
     *
     * @return void
     */
    public function restored(Team $team)
    {
        //
    }

    /**
     * Handle the Team "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Team $team)
    {
        //
    }
}
