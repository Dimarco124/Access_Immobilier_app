<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\FinanceService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Exécution automatique des tâches financières chaque jour à minuit
Schedule::call(function () {
    $financeService = app(FinanceService::class);
    $financeService->generateMonthlyInvoices();
    $financeService->applyLateFees();
    $financeService->sendPaymentReminders();
})->daily();
