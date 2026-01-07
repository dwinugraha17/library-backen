<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-whatsapp-reminders')->dailyAt('08:00');