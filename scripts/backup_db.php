<?php
@include_once(__DIR__ . '/../config.php');

$host = defined('DB_HOST') ? DB_HOST : 'localhost';
$user = defined('DB_USER') ? DB_USER : 'root';
$pass = defined('DB_PASS') ? DB_PASS : '';
$name = defined('DB_NAME') ? DB_NAME : 'almamater';
$mysqldump = defined('BACKUP_MYSQLDUMP_PATH') ? BACKUP_MYSQLDUMP_PATH : 'mysqldump';

$backupDir = __DIR__ . '/../backups';
if (!is_dir($backupDir)) { @mkdir($backupDir, 0775, true); }

$file = $backupDir . '/backup_' . $name . '_' . date('Ymd_His') . '.sql';

// Construir comando seguro
$cmd = sprintf('"%s" -h%s -u%s %s %s > "%s"',
    $mysqldump,
    escapeshellarg($host),
    escapeshellarg($user),
    ($pass !== '' ? ('-p' . escapeshellarg($pass)) : ''),
    escapeshellarg($name),
    $file
);

// Ejecutar
$exitCode = 0;
if (stripos(PHP_OS, 'WIN') === 0) {
    $cmd = 'cmd /C ' . $cmd;
}
system($cmd, $exitCode);

if ($exitCode !== 0) {
    echo "Error realizando backup (codigo $exitCode)" . PHP_EOL;
    exit(1);
}

// Retención
$retentionDays = defined('BACKUP_RETENTION_DAYS') ? (int)BACKUP_RETENTION_DAYS : 30;
$files = glob($backupDir . '/*.sql');
$now = time();
foreach ($files as $f) {
    if (is_file($f) && ($now - filemtime($f)) > ($retentionDays * 86400)) {
        @unlink($f);
    }
}

echo "Backup generado: $file" . PHP_EOL;
?>


