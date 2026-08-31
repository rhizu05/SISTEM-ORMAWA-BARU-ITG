<?php

// Bootstrap Laravel
bootstrap_app();

use App\Models\User;

echo "Testing Balance Data:\n";
echo "=====================\n";

$users = User::whereNotNull('saldo_awal')->get();
foreach ($users as $u) {
    $saldo_awal = $u->saldo_awal ?? 'null';
    $saldo = $u->saldo ?? 'null';
    $role = $u->roles->first()?->name ?? 'none';
    echo "User ID: " . $u->id . "\n";
    echo "  Name: " . $u->name . "\n";
    echo "  Role: " . $role . "\n";
    echo "  saldo_awal: " . $saldo_awal . "\n";
    echo "  saldo: " . $saldo . "\n";
    echo "---\n";
}
EOF