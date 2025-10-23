# Display Antrian SAFFMedic

## Deskripsi  
Aplikasi tampilan antrian real-time untuk layanan kesehatan SAFFMedic.  
Dibangun dengan Laravel 6 dan PHP 7.4.33.  
Menampilkan nomor urut pasien dan status layanan secara langsung pada monitor/TV ruang tunggu.

## Fitur Utama  
- Nomor antrian otomatis ter-update dari sistem pendaftaran.  
- Tampilan publik (layar monitor) untuk menampilkan antrian saat ini.  
- Panel admin untuk memanggil nomor antrian dan mengubah status layanan.  
- Mendukung banyak pintu layanan atau poli jika diperlukan.  
- Responsif untuk berbagai ukuran layar (tablet, monitor, TV).

## Teknologi  
- Backend: Laravel 6.x (minimal PHP 7.2.5) :contentReference[oaicite:2]{index=2}  
- PHP versi 7.4.33.  
- Database: MySQL (atau MariaDB) sesuai konfigurasi Anda.  
- Frontend: Blade templates &/atau JavaScript (sesuaikan dengan implementasi).  
- Web Socket atau polling untuk pembaruan real-time (jika diimplementasikan).

## Persyaratan Sistem  
- PHP >= 7.2.5 untuk Laravel 6.x :contentReference[oaicite:3]{index=3}  
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML. :contentReference[oaicite:4]{index=4}  
- Composer ter-instal.  
- MySQL atau MariaDB sebagai database.

## Instalasi  
1. Clone repository:  
   ```bash
   git clone https://github.com/revanzaRaihan/display-antrian-saffmedic.git
   cd display-antrian-saffmedic
Install dependency backend:

bash
Salin kode
composer install
Copy file environment:

bash
Salin kode
cp .env.example .env
Sesuaikan konfigurasi di file .env (database, host, port, layanan antrian, dsb).

Generate key aplikasi:

bash
Salin kode
php artisan key:generate
Jalankan migrasi dan seeding jika ada:

bash
Salin kode
php artisan migrate --seed
Jika menggunakan asset build frontend (npm/yarn):

bash
Salin kode
npm install
npm run dev
Jalankan aplikasi:

bash
Salin kode
php artisan serve
Akses di http://localhost:8000 atau sesuai konfigurasi server Anda.

Penggunaan
Akses panel admin untuk memanggil nomor antrian.

Arahkan tampilan monitor ke URL tampilan publik agar pasien dapat melihat nomor dan status.

Pastikan koneksi internet/lokal aktif dan sistem refresh/update secara real-time jika fitur tersebut diaktifkan.

Cocok digunakan di klinik, rumah sakit, apotek, atau layanan publik lainnya yang membutuhkan sistem antrian.

Kontribusi
Kontribusi diterima dengan senang hati.
Silakan fork repository ini, buat branch baru (feature/nama-fitur), dan lakukan pull request.
Pastikan Anda mengikuti standar pengkodean, menambahkan dokumentasi dan mengetes fitur baru sebelum mengirim.

Lisensi
Proyek ini dilisensikan di bawah lisensi MIT (sesuaikan jika Anda memilih lisensi lain).
Lihat file LICENSE untuk rincian.

csharp
Salin kode

Jika Anda memiliki bagian fitur khusus (misalnya API endpoint, integrasi WebSocket, Docker setup) saya dapat membantu memperluas README dengan bagian tersebut.
::contentReference[oaicite:5]{index=5}
