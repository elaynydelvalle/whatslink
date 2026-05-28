<?php
putenv('HOME=/home1/chromo20');
putenv('COMPOSER_HOME=/home1/chromo20/.composer');
$cmds = [
    'config:clear',
    'route:clear',
    'view:clear',
    'cache:clear',
];
chdir('/home1/chromo20/whatslink');
foreach ($cmds as $cmd) {
    $out = [];
    exec("/usr/local/bin/php artisan $cmd 2>&1", $out);
    echo "<b>$cmd:</b> " . implode(' ', $out) . "<br>";
}
echo "Concluído!";
