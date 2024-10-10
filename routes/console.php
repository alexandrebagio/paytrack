<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command(
    'sanctum:prune-expired --hours=24',
    fn () => $this->comment('prune-tokens')
)->daily();
