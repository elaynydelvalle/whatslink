<?php
putenv('HOME=/home1/chromo20');
chdir('/home1/chromo20/whatslink');
foreach (['config:clear','route:clear','view:clear'] as $cmd) {
    $out = [];
    exec("/usr/local/bin/php artisan $cmd 2>&1", $out);
    echo "<b>$cmd:</b> " . implode(' ', $out) . "<br>";
}
echo "Concluído!";
