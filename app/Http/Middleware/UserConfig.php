<?php

namespace App\Http\Middleware;

use App\Helpers\SysHelper;
use App\Models\Academic\Period;
use App\Models\Team;
use Closure;
use Illuminate\Http\Request;

class UserConfig
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (empty(auth()->check())) {
            return $next($request);
        }

        config([
            'config.display_timezone' => \Auth::user()->timezone ?? config('config.system.timezone'),
            'config.system.locale' => \Auth::user()->getPreference('system.locale') ?? config('config.system.locale'),
        ]);

        $allowedTeamIds = \Auth::user()->getAllowedTeamIds();

        config([
            'config.teams_set' => true,
            'config.teams' => $allowedTeamIds,
        ]);

        $userCurrentTeamId = auth()->user()?->current_team_id;

        if (in_array($userCurrentTeamId, $allowedTeamIds)) {
            SysHelper::setTeam($userCurrentTeamId);
        } elseif (! $request->route()->named('teams.select')) {
            \Auth::guard('web')->logout();

            return response()->json(['message' => __('team.could_not_find_selected_team')], 422);
        }

        $team = Team::find($userCurrentTeamId);
        $periods = Period::byTeam()->get();

        config([
            'config.team' => $team,
            'config.academic.periods' => $periods->pluck('id')->all(),
            'config.academic.period' => $periods->firstWhere('is_default', true),
            'config.academic.default_period_id' => optional($periods->firstWhere('is_default', true))->id,
        ]);

        return $next($request);
    }
}
