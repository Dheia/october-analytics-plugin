<?php

/*
@todo
use System\Classes\PluginManager;
use Synder\Analytics\Models\Settings;

$plugins = PluginManager::instance();
$settings = Settings::intance();

if ($settings->get('bot_robots') === '1' && !$plugins->exists('zen.robots')) {
    Route::get('robots.txt', function () use ($settings) {
        return $settings->generateRobotsTxt();
    });
}
*/
