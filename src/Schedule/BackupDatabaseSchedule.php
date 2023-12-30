<?php

namespace App\Schedule;

use Zenstruck\ScheduleBundle\Schedule;
use Zenstruck\ScheduleBundle\Schedule\ScheduleBuilder;

class BackupDatabaseSchedule implements ScheduleBuilder
{
    public function buildSchedule(Schedule $schedule): void
    {
        $schedule
            ->timezone('Europe/Paris')
            ->environments('prod')
        ;

        $schedule
            ->addCommand('backup-database')
            ->description('Sauvegarde les données du site Internet')
            ->monthly()
            ->at(1)
        ;
    }
}
