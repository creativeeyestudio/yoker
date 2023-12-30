<?php

namespace App\Schedule;

use Zenstruck\ScheduleBundle\Schedule;
use Zenstruck\ScheduleBundle\Schedule\ScheduleBuilder;

class BackupDatabaseSchedule implements ScheduleBuilder
{
    public function buildSchedule(Schedule $schedule): void
    {
        $schedule
            ->timezone('UTC')
            ->environments('dev')
        ;

        $schedule
            ->addCommand('backup-database')
            ->description('Sauvegarde les données du site Internet')
            ->daily()
            ->at(9)
        ;
    }
}
