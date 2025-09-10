<?php
function autoloadSpreadsheet($className)
{
    $prefix = 'PhpOffice\\PhpSpreadsheet\\';
    $baseDir = __DIR__ . '/vendor/phpSpreadsheet/src/PhpSpreadsheet/'; // Sesuaikan dengan lokasi folder Anda

    // Pastikan nama kelas sesuai dengan nama file
    $len = strlen($prefix);
    if (strncmp($prefix, $className, $len) !== 0) {
        return;
    }

    // Ambil nama file yang sesuai dengan namespace
    $relativeClass = substr($className, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Jika file ada, include
    if (file_exists($file)) {
        require $file;
    }
}

// Daftarkan autoloader
spl_autoload_register('autoloadSpreadsheet');
