<?php
require 'vendor/autoload.php';

use App\Models\User;

echo "Testing Balance Data:\n";
echo "=====================\n";

$users = User::take(3)->get();
foreach ($users as $u) {
    $saldo_awal = $u->saldo_awal ?? 'null';
    $saldo = $u->saldo ?? 'null';
    echo "User ID: " . $u->id . "\n";
    echo "  saldo_awal: " . $saldo_awal . "\n";
    echo "  saldo: " . $saldo . "\n";
    echo "  name: " . $u->name . "\n";
    echo "---\n";
}