<?php
// Reset senha
$pdo = new PDO('mysql:host=localhost;dbname=chromo20_chromo_whatslink', 'chromo20_chromo_user_whatslink', 'Whatslink@2026');
$hash = password_hash('Whatslink@2026', PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hash, 'elaynydelvalle@gmail.com']);
echo 'Senha resetada: ' . $stmt->rowCount() . ' registro(s).<br>';

// Limpa caches
putenv('HOME=/home1/chromo20');
putenv('COMPOSER_HOME=/home1/chromo20/.composer');
chdir('/home1/chromo20/whatslink');
foreach (['config:clear','route:clear','view:clear'] as $cmd) {
    $out = [];
    exec("/usr/local/bin/php artisan $cmd 2>&1", $out);
    echo "<b>$cmd:</b> " . implode(' ', $out) . "<br>";
}
echo "Concluído!";
