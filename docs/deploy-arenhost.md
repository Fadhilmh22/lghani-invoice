# Panduan deploy ke ArenHost

Panduan ini untuk cPanel/shared hosting ArenHost. Laravel 13 memerlukan PHP 8.3 atau lebih baru; gunakan PHP 8.4 untuk aplikasi ini hanya jika versi tersebut tersedia pada paket hosting. Informasi publik ArenHost menyebut dukungan beberapa versi PHP 8.x, tetapi tidak memastikan PHP 8.4 pada setiap paket. Konfirmasikan versi PHP web dan CLI serta ekstensi yang tersedia ke support ArenHost sebelum migrasi.

## 1. Siapkan hosting dan database

1. Pastikan paket menyediakan PHP 8.4 (untuk website dan SSH/Terminal), Composer 2, ekstensi PHP `Ctype`, `cURL`, `DOM`, `Fileinfo`, `Filter`, `Hash`, `Mbstring`, `OpenSSL`, `PCRE`, `PDO`, `pdo_mysql`, `Session`, `Tokenizer`, dan `XML`. Aktifkan ekstensi yang tidak tersedia melalui MultiPHP INI Editor atau minta bantuan support.
2. Jika tersedia, buka **MultiPHP Manager** atau **Select PHP Version** di cPanel, pilih PHP 8.4 untuk domain, lalu simpan. Cek versi PHP website dan CLI secara terpisah; jika PHP 8.4 tidak tersedia di pilihan, minta konfirmasi/support ArenHost sebelum melanjutkan. Jangan menambahkan handler PHP versi tertentu secara manual ke `.htaccess`.
3. Buat database MySQL dan user melalui cPanel **MySQL Databases**. Catat nama database, user, password, dan host; pada banyak cPanel nama database/user mendapat prefiks akun.
4. Aktifkan SSH/Terminal jika tersedia. Pastikan `php -v` dan `composer --version` menunjukkan versi yang sesuai. Jika Composer atau PHP CLI tidak tersedia, tanyakan metode deploy yang didukung oleh ArenHost.
5. Untuk mengurangi risiko, unggah dan uji dahulu pada subdomain/staging jika paket menyediakan fitur tersebut.

## 2. Unggah aplikasi dengan aman

**Jangan letakkan seluruh project Laravel di `public_html`.** File `.env`, source code, dan `vendor` harus berada di luar document root.

1. Di direktori home akun, buat folder aplikasi, misalnya `~/lghani-app`, lalu unggah source code ke sana. Jangan unggah `.env`, `vendor`, atau folder `node_modules` dari komputer lokal.
2. Atur document root domain di cPanel agar menunjuk ke `~/lghani-app/public`, jika pengaturan domain mengizinkannya. Ini pilihan yang disarankan.
3. Jika cPanel mengharuskan document root tetap `public_html`, letakkan **isi** folder project (termasuk `.env` dan `vendor`) di `~/lghani-app`, lalu salin hanya isi folder `public` ke `~/public_html`. Edit `public_html/index.php`: ubah kedua path bootstrap Composer dan aplikasi agar menunjuk ke folder privat, misalnya:

   ```php
   require __DIR__.'/../lghani-app/vendor/autoload.php';
   $app = require_once __DIR__.'/../lghani-app/bootstrap/app.php';
   ```

   Sesuaikan `../lghani-app` dengan lokasi sebenarnya. Pastikan tidak ada salinan `.env`, `vendor`, atau source code di `public_html`.
4. Pertahankan `.htaccess` Laravel dari folder `public` agar URL selain file statis diteruskan ke `index.php`. Jangan gunakan handler PHP versi lama yang mungkin tersalin dari hosting lain.

## 3. Pasang dependensi dan konfigurasi

Di Terminal, pindah ke folder aplikasi privat:

```sh
cd ~/lghani-app
php -v
composer install --no-dev --prefer-dist --optimize-autoloader
```

Buat `.env` di folder aplikasi privat berdasarkan `.env.example` (atau unggah `.env` yang sudah disiapkan secara aman), kemudian atur sekurangnya:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database
```

Gunakan nilai database persis seperti yang diberikan cPanel/ArenHost. Jangan pernah membagikan atau memasukkan `.env` ke Git. Buat `APP_KEY` satu kali untuk instalasi baru dengan `php artisan key:generate`; untuk migrasi aplikasi yang sudah berjalan, pertahankan `APP_KEY` lama agar data terenkripsi dan sesi yang masih kompatibel tidak rusak.

Pastikan folder `storage` dan `bootstrap/cache` dapat ditulis oleh PHP. Jalankan:

```sh
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

Jika symlink tidak diizinkan, minta bantuan support ArenHost untuk mengatur akses file upload dengan aman; jangan mengekspos folder `storage` atau seluruh source project.

## 4. Pindahkan data dan verifikasi

1. Ekspor database lama sebelum perubahan apa pun. Impor dump tersebut ke database ArenHost lewat phpMyAdmin atau Terminal.
2. Jangan jalankan `migrate:fresh` pada database berisi data. Periksa `php artisan migrate:status`; jalankan `php artisan migrate --force` hanya jika ada migration aplikasi yang memang belum diterapkan dan setelah database dicadangkan.
3. Buka website melalui HTTPS. Uji login, pembuatan tiket/invoice, unggah logo/file, unduh/cetak PDF, dan fungsi database utama.
4. Jika muncul HTTP 500, periksa `storage/logs/laravel.log`, versi/ekstensi PHP yang dipakai domain, kredensial database, hak tulis direktori, dan path di `index.php`. Jangan mengaktifkan `APP_DEBUG=true` pada website publik.

## 5. Rilis perubahan berikutnya

Cadangkan database dan file `.env`, unggah perubahan, lalu jalankan dari folder aplikasi:

```sh
composer install --no-dev --prefer-dist --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan view:cache
```

Lewati perintah migrasi bila rilis tidak menambahkan migration. Uji website setelah deploy dan siapkan salinan file/database sebelumnya untuk pemulihan jika terjadi masalah.

### Sumber

- [Persyaratan deployment Laravel 13](https://laravel.com/docs/13.x/deployment)
- [Panduan upgrade Laravel 13](https://laravel.com/docs/13.x/upgrade)
- [Informasi paket Cheap Hosting ArenHost](https://arenhost.id/cheap-hosting)
