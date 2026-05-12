<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
$failed = false;
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $cmd = PHP_BINARY . ' -l ' . escapeshellarg($path) . ' 2>&1';
    exec($cmd, $output, $code);
    if ($code !== 0) {
        echo implode(PHP_EOL, $output) . PHP_EOL;
        $failed = true;
    }
    $output = [];
}
if ($failed) {
    exit(1);
}
echo "All PHP files passed syntax checks.\n";
