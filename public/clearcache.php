<?php
putenv('HOME=/home1/chromo20');
require '/home1/chromo20/whatslink/vendor/autoload.php';
$app = require '/home1/chromo20/whatslink/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'elaynydelvalle@gmail.com')->first();
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('Whatslink@2026');
    $user->save();
    echo 'Senha resetada! Email: ' . $user->email;
} else {
    echo 'Usuário não encontrado.';
}
