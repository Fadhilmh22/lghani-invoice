<?php
// Ini adalah skrip diagnostik untuk menemukan file konfigurasi PHP (php.ini)
// yang sedang digunakan oleh web server Anda.

echo "<h1>Status Konfigurasi PHP Anda</h1>";

// Tampilkan semua konfigurasi PHP
phpinfo();

// Harap perhatikan dua bagian penting:
// 1. Loaded Configuration File (Lokasi file php.ini yang aktif)
// 2. max_execution_time (Pastikan Local Value menunjukkan 300 atau lebih)
?>