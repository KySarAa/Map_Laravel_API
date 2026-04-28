<?php
use App\Models\User;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::first();
if ($user) {
    echo $user->createToken('api')->plainTextToken;
} else {
    echo "No user found in database.";
}
