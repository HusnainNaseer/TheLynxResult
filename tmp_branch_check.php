<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$rows = DB::select('SHOW COLUMNS FROM classes');
echo "Classes columns:\n";
foreach ($rows as $row) {
    echo "{$row->Field} {$row->Type}\n";
}

echo "\nSample classes rows:\n";
$classes = DB::table('classes')->limit(20)->get();
foreach ($classes as $c) {
    echo json_encode($c) . "\n";
}

echo "\nUsers with branch_name and branch_id columns:\n";
$rows = DB::select('SHOW COLUMNS FROM users LIKE "branch%"');
foreach ($rows as $row) {
    echo "{$row->Field} {$row->Type}\n";
}
$users = DB::table('users')->select('id','name','branch_id','branch_name')->whereNotNull('branch_name')->limit(20)->get();
foreach ($users as $u) {
    echo json_encode($u) . "\n";
}
