<?php
putenv('HOME=/home1/chromo20');
chdir('/home1/chromo20/whatslink');

// Fix permissions
$dirs = ['storage', 'storage/framework', 'storage/framework/sessions', 'storage/framework/views', 'storage/framework/cache', 'storage/logs', 'bootstrap/cache'];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    chmod($dir, 0775);
    echo "chmod 775 $dir<br>";
}

// Clear caches
foreach (['config:clear','route:clear','view:clear'] as $cmd) {
    $out = [];
    exec("/usr/local/bin/php artisan $cmd 2>&1", $out);
    echo "<b>$cmd:</b> " . implode(' ', $out) . "<br>";
}
echo "Concluído!";
