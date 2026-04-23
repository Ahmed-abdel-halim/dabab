<?php
require 'vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$settings = \Illuminate\Support\Facades\DB::table('system_settings')->get();
echo "--- SYSTEM SETTINGS ---\n";
foreach ($settings as $s) {
    echo "Key: {$s->key} | Group: {$s->group}\n";
}
echo "-----------------------\n";
