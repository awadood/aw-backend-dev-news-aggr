<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('articles:fetch-and-store')->cron(env('EXPR_PULL_ARTICLES', '0 * * * *'));
