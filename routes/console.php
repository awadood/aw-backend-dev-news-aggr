<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('pull:articles')->cron(env('EXPR_PULL_ARTICLES', '0 * * * *'));
