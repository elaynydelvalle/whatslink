<?php
$pdo = new PDO('mysql:host=localhost;dbname=chromo20_chromo_whatslink', 'chromo20_chromo_user_whatslink', 'Whatslink@2026');
$stmt = $pdo->prepare("SELECT id, email, password, active FROM users WHERE email = ?");
$stmt->execute(['elaynydelvalle@gmail.com']);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Usuário não encontrado!";
    exit;
}

echo "ID: " . $user['id'] . "<br>";
echo "Email: " . $user['email'] . "<br>";
echo "Active: " . $user['active'] . "<br>";
echo "Hash guardado: " . substr($user['password'], 0, 30) . "...<br>";
echo "Hash válido: " . (password_verify('Whatslink@2026', $user['password']) ? 'SIM' : 'NÃO') . "<br>";
