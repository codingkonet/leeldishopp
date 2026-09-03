<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit("CLI only.\n");
require __DIR__ . '/../config/app.php';

$directory = __DIR__ . '/backups';
if (!is_dir($directory)) mkdir($directory, 0750, true);
$filename = $directory . '/lebeldishop-' . date('Y-m-d-His') . '.sql';
$command = sprintf('mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines %s', escapeshellarg(DB_HOST), escapeshellarg(DB_PORT), escapeshellarg(DB_USER), escapeshellarg(DB_PASS), escapeshellarg(DB_NAME));
$output = [];
$returnCode = 0;
exec($command . ' > ' . escapeshellarg($filename) . ' 2>&1', $output, $returnCode);
if ($returnCode !== 0) {
    @unlink($filename);
    fwrite(STDERR, "Backup failed: " . implode(PHP_EOL, $output) . PHP_EOL);
    exit($returnCode);
}
fwrite(STDOUT, "Backup created: {$filename}\n");
