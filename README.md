# Display Antrian SAFFMedic

## Deskripsi  
Aplikasi tampilan antrian real-time untuk layanan kesehatan SAFFMedic.  
Dibangun dengan Laravel 6 dan PHP 7.4.33.  
Menampilkan nomor urut pasien dan status layanan secara langsung pada monitor atau TV di ruang tunggu.

## Fitur Utama  
- Nomor antrian otomatis ter-update dari sistem pendaftaran.  
- Tampilan publik (layar monitor) untuk menampilkan antrian saat ini.  
- Panel admin untuk memanggil nomor antrian dan mengubah status layanan.  
- Mendukung banyak pintu layanan atau poli jika diperlukan.  
- Responsif untuk berbagai ukuran layar (tablet, monitor, TV).

## Teknologi  
- Backend: Laravel 6.x (minimal PHP 7.2.5)  
- PHP versi 7.4.33  
- Database: MySQL atau MariaDB  
- Frontend: Blade templates dan JavaScript  
- WebSocket atau polling untuk pembaruan real-time (opsional)

## Persyaratan Sistem  
- PHP >= 7.2.5 untuk Laravel 6.x  
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML  
- Composer terinstal  
- MySQL atau MariaDB sebagai database

## Instalasi dan Menjalankan Aplikasi  
```bash
Jalankan perintah berikut secara berurutan di terminal:  
- git clone https://github.com/revanzaRaihan/display-antrian-saffmedic.git
- cd display-antrian-saffmedic
- composer install
- cp .env.example .env
- php artisan key:generate
- php artisan migrate --seed
- php artisan serve
