<?php
$pdo = new PDO('mysql:host=localhost;dbname=chromo20_chromo_whatslink', 'chromo20_chromo_user_whatslink', 'Whatslink@2026');
$hash = password_hash('Whatslink@2026', PASSWORD_BCRYPT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$stmt->execute([$hash, 'elaynydelvalle@gmail.com']);
echo 'Atualizado: ' . $stmt->rowCount() . ' registro(s).';
