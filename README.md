# Display Antrian SAFF Medic

Aplikasi tampilan antrian real-time untuk layanan kesehatan SAFF Medic. Dibangun dengan Laravel 6 dan PHP 7.4.33 untuk menampilkan nomor urut pasien dan status layanan secara langsung pada monitor atau TV di ruang tunggu.

## Fitur Utama

- **Nomor antrian otomatis**: Ter-update dari sistem pendaftaran
- **Tampilan publik**: Layar monitor untuk menampilkan antrian saat ini
- **Panel admin**: Memanggil nomor antrian dan mengubah status layanan
- **Multi-layanan**: Mendukung banyak pintu layanan atau poli
- **Responsif**: Kompatibel dengan tablet, monitor, dan TV
- **Real-time update**: WebSocket atau polling untuk pembaruan langsung

## Tech Stack

- **Backend**: Laravel 6.x
- **PHP**: 7.4.33 (minimal 7.2.5)
- **Database**: MySQL atau MariaDB
- **Frontend**: Blade Templates + JavaScript

## Requirements

### Server
- PHP >= 7.2.5
- Composer
- MySQL atau MariaDB

### Ekstensi PHP Wajib
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML

## Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/revanzaRaihan/display-antrian-saffmedic.git
cd display-antrian-saffmedic
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` sesuai konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=display_antrian
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Database
```bash
php artisan migrate --seed
```

### 5. Jalankan Aplikasi
```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Struktur Direktori

```
display-antrian-saffmedic/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   └── Models/
├── config/
├── database/
│   ├── migrations/
│   └── seeds/
├── public/
├── resources/
│   └── views/
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
└── .env
```

## Konfigurasi

### Permission Folder
Pastikan folder berikut memiliki write permission:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Clear Cache
Jika terjadi error, jalankan perintah berikut:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

## Deployment

### Optimasi Production
```bash
php artisan config:cache
php artisan route:cache
php artisan optimize
```

### Checklist Deployment
- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Pastikan `.env` tidak publik
- [ ] Jalankan `php artisan optimize`
- [ ] Cek permission `storage/` dan `bootstrap/cache/`
- [ ] Setup queue worker jika menggunakan jobs
- [ ] Setup supervisor untuk queue (opsional)

## Troubleshooting

### Database Error
```bash
php artisan migrate:fresh --seed
php artisan tinker
# Cek koneksi: DB::connection()->getPdo();
```

### Autoload Error
```bash
composer dump-autoload
```

### Permission Denied
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

## Testing

Jalankan unit test dengan:
```bash
php artisan test
# atau
./vendor/bin/phpunit
```

## Lisensi

Silakan cek file LICENSE di repository.

## Kontribusi

Pull request dan issue report sangat diterima di [GitHub Repository](https://github.com/revanzaRaihan/display-antrian-saffmedic).

## Support

Untuk pertanyaan atau masalah teknis, buat issue di repository GitHub.
