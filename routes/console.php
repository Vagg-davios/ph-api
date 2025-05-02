<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('download:pornstars-feed')
    ->daily()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/feed-download.log'));
