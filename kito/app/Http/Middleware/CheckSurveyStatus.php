<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Survei;
use Carbon\Carbon;

class CheckSurveyStatus
{
    public function handle($request, Closure $next)
    {
        // Cek dan update status hanya 1x per hari
        if ($this->shouldCheckStatus()) {
            $this->updateSurveyStatus();
            $this->setLastCheckedTime();
        }

        return $next($request);
    }

    protected function shouldCheckStatus()
    {
        $lastChecked = cache('last_survey_status_check');
        return !$lastChecked || now()->diffInHours($lastChecked) >= 24;
    }

    protected function updateSurveyStatus()
    {
        $today = now();
        
        Survei::query()
            ->where('jadwal_kegiatan', '<=', $today)
            ->where('jadwal_berakhir_kegiatan', '>=', $today)
            ->where('status_survei', '!=', 2)
            ->update(['status_survei' => 2]);

        Survei::query()
            ->where('jadwal_berakhir_kegiatan', '<', $today)
            ->where('status_survei', '!=', 3)
            ->update(['status_survei' => 3]);
    }

    protected function setLastCheckedTime()
    {
        cache(['last_survey_status_check' => now()], now()->addDay());
    }
}