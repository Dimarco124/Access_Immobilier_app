<?php

use App\Helpers\ModuleHelper;

if (!function_exists('hasModuleAccess')) {
    /**
     * Vérifie si l'utilisateur a accès à un module
     */
    function hasModuleAccess(string $moduleCode, string $minimumLevel = 'viewer'): bool
    {
        return ModuleHelper::hasAccess($moduleCode, $minimumLevel);
    }
}

if (!function_exists('getUserModules')) {
    /**
     * Récupère tous les modules de l'utilisateur
     */
    function getUserModules(): array
    {
        return ModuleHelper::getUserModules();
    }
}

if (!function_exists('canViewKPI')) {
    /**
     * Vérifie si l'utilisateur peut voir un KPI
     */
    function canViewKPI(string $kpiModule): bool
    {
        return ModuleHelper::canViewKPI($kpiModule);
    }
}
