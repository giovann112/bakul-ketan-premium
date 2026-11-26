# Bakul Ketan Premium - Website Promosi dan Pemesanan (Laravel)

Lapisan aplikasi (model, controller, view, migrasi, tes) untuk website promosi dan pemesanan
digital Bakul Ketan Premium. Tampilan mengikuti desain ungu dan emas (Playfair Display + DM Sans),
tanpa proses build (tanpa npm / Vite): cukup satu berkas CSS di `public/css/bkp.css`.

Berkas di zip ini **ditempelkan ke instalasi Laravel baru**, bukan proyek utuh dengan folder `vendor`.

## Prasyarat
- PHP 8.2 atau lebih baru
- Composer
- SQLite (bawaan) atau MySQL/MariaDB

## Pemasangan

```bash
# 1. Buat proyek Laravel baru
composer create-project laravel/laravel bakul-ketan
cd bakul-ketan

# 2. Salin isi zip ini ke dalam proyek (menimpa 3 berkas bawaan, lihat catatan di bawah)
#    Linux/macOS:  cp -R /lokasi/bakul-ketan-laravel/. .
#    Windows:      salin isi folder, pilih "Replace the files"

# 3. Atur .env (lihat .env.example.append), lalu:
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Buka http://localhost:8000. Panel admin ada di `/admin/login`
(email dan kata sandi dari `ADMIN_EMAIL` / `ADMIN_PASSWORD`; bawaan `admin@bakulketan.test` / `password123`,
**wajib diganti sebelum online**).

### Berkas bawaan Laravel yang ditimpa
| Berkas | Alasan |
|---|---|
| `bootstrap/app.php` | mendaftarkan middleware `admin` |
| `routes/web.php` | rute website |
| `app/Models/User.php` | menambah kolom `is_admin` |

Bila versi Laravel Anda berbeda dari 12 dan `bootstrap/app.php` bawaannya berbeda, jangan timpa:
cukup tambahkan alias `'admin' => \App\Http\Middleware\IsAdmin::class` pada `withMiddleware`.

## Fitur (mengikuti SKPL)
| Kode | Fitur | Lokasi |
|---|---|---|
| BKP-01 | Landing page dan profil UMKM | `home.blade.php`, `about.blade.php` |
| BKP-02 | Katalog dan detail produk | `ProductController`, `products/*` |
| BKP-03 | Keranjang belanja (sesi) | `CartController`, `app/Support/Cart.php` |
| BKP-04 | Checkout dan pencatatan pesanan | `CheckoutController@store` |
| BKP-05 | Konfirmasi via WhatsApp | `CheckoutController@waLink` |
| BKP-06 | Pelacakan pesanan | `TrackingController` |
| BKP-07 | Login admin | `Admin\AuthController`, middleware `IsAdmin` |
| BKP-08 | Produk dan kategori | `Admin\ProductController`, `Admin\CategoryController` |
| BKP-09 | Stok | otomatis saat checkout dan pembatalan; ubah di form produk |
| BKP-10 | Pesanan dan status | `Admin\OrderController` |
| BKP-11 | Dashboard analitik | `Admin\DashboardController` |

## Aturan penting yang sudah dikodekan
- Harga dan total selalu dibaca dari basis data, bukan dari data peramban.
- Checkout berjalan dalam satu transaksi dengan penguncian baris: stok tidak bisa minus saat ada pesanan bersamaan.
- Item pesanan menyimpan salinan nama dan harga (riwayat aman bila harga berubah).
- Pesanan dibatalkan mengembalikan stok satu kali, dan status batal bersifat final.
- Halaman sukses hanya bisa dibuka oleh pembuat pesanan (lewat sesi). Pelacakan butuh kode dan nomor HP yang cocok.
- Login admin dan pelacakan dibatasi (throttle) terhadap tebakan berulang.
- Tidak ada pendaftaran akun publik.

## Tes
```bash
php artisan test
```

## Menyesuaikan
- Warna dan huruf: variabel di awal `public/css/bkp.css`.
- Teks profil usaha: `resources/views/about.blade.php` (cari tanda `[...]`).
- Logo: ganti `public/images/logo.png`.
- Foto produk: unggah dari panel admin (Produk, Ubah). Tanpa foto, kartu memakai panel warna.

## Sebelum online
1. `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` diisi domain asli, HTTPS aktif.
2. Ganti kata sandi admin dan isi `WA_NUMBER`, `SHOP_ADDRESS`, `SHOP_HOURS`.
3. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
4. Arahkan document root ke folder `public/`. Pastikan `storage/` dan `bootstrap/cache/` dapat ditulis.
5. Jadwalkan pencadangan basis data.
