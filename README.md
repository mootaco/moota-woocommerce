# Moota WooCommerce
> Cek Mutasi Otomatis Bank Nasional

Plugin ini adalah addon dari [Moota.co](https://moota.co/) sebagai payment 
gateway woocomerce wordpress dan auto konfirmasi.

Contributors: matamerah,  onnayokheng, rezzakurniawan  
Tags: payment gateway, indonesia, woocomerce, bca, mandiri, bni, bri, 
muamalat, otomatis, mutasi, moota, bank

## Requirements

 - PHP version: `5.6.0`
 - Wordpress: `4.8.1`  
 - Tested up to: `4.8.1`  
 - Stable tag: `4.8.1`  

## License

License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

## Deskripsi

Moota.co merupakan aplikasi untuk pengecekkan mutasi dan saldo rekening Anda,  
dimana mutasi rekening Anda, kami dapatkan dari akun iBanking Anda.  
Dan bisa diintegrasikan ke seluruh lini bisnis Anda, misalkan Toko Online.  
Karena kami menyediakan API yang bisa Anda gunakan untuk keperluan bisnis online Anda.

Anda akan mendapatkan notifikasi melalui email, push notif (API), dan SMS 
(summary harian).  
Jadi akan sangat memudahkan Anda dan tim untuk mengelola orderan.

Sudah banyak Bank yang bisa kami support, diantaranya:  

  - BCA,
  - MANDIRI,
  - BNI,
  - BRI,
  - Muamalat
  - dan terus bertambah.

Sistem Moota ini bisa diimplementasikan pada sistem apa saja, karena kami 
sudah menyediakan API-nya juga.

Silahkan daftar terlebih dahulu di [Moota.co](https://moota.co) untuk bisa menggunakan plugin ini.

## Instalasi

### Pasang Plugin
Cara menginstall plugin WooCommerce Moota sangatlah mudah.

1. Unggah plugin ini ke folder `/wp-content/plugins/woomoota`, atau install 
   langsung melalui WordPress plugin secara instan.

2. Aktivkan di menu 'Plugins' WordPress Anda.
3. Masuk ke menu WooCommerce->Settings->WooMoota Tab untuk melakukan setting 
   plugin Moota.

4. Salin "API Endpoint" berupa link contoh: 
   `http://tokoonlineanda.com/?woomoota=push`

5. Lalu lakukan langkah ke-2 di bawah ini.

### Konfigurasi Plugin
Pastikan Anda daftar di web https://moota.co dan mempunyai minimal 1 akun 
rekening yang telah didaftarkan.

1. Kunjungi web https://moota.com lalu login.

2. Edit rekening yang akan digunakan untuk integrasi.

3. Masuk ke tab `notifikasi`.

4. Lalu edit API `Push Notif`, masukkan `API Endpoint` 
   pada langkah pertama tadi.

5. Lalu simpan.


Dan silahkan mulai berjualan.

## Tutorial
Selengkapnya silahkan kunjungi tutorial integrasi Moota dengan WooCommerce di sini: https://moota.co/tutorial/woocommerce/

## FAQ - Frequently Asked Questions

### Bagaimana cara install dan integrasi dengan toko online saya?

  > Selengkapnya silahkan kunjungi tutorial integrasi Moota dengan WooCommerce 
    di sini: https://moota.co/tutorial/woocommerce/

### Apakah ada biaya langganan?

  > Ya, untuk menggunakan layanan Moota.co kami menerapkan sistem deposit.  
    Dan layanan mutasi ini akan dikenakan kredit 1500/hari.

### Apakah data saya aman?

  > Ya, kami menjamin keamanan data Anda. Karena kami akan mengenkripsi data 
    Anda, juga menggunakan protocol khusus dan menggunakan SSL yang akan 
    mengenkripsi aktivitas Anda di browser.
    Sehingga keamanannya akan lebih optimal.

### Berapa kali mutasi akan update?

  > Pengecekkan mutasi dilakukan 15 menit sekali.

### Apakah saya bisa akses Internet Banking saya bila menggunakan layanan ini?

  > Anda bisa buka iBanking Anda kapanpun, tanpa terganggu oleh Moota.

### Melalui apa saja saya akan menerima notifikasi?

  > Sistem akan mengirim notifikasi setiap ada transaksi masuk kepada Anda 
    melalui Email, API dan SMS (ringkasan harian).


## Changelog

### 0.4.5
  - Fix: Hapus die function ketika Plugin WooCommerce tidak terinstall

### 0.4.4
  - Fitur: Pengecekkan bila ada nominal order yang sama
  - Fitur: Penambahan seting limit hari pengecekkan invoice

### 0.4.2
  - Fix: Remote Address Checker
  - Support PHP 5.3

### 0.4.2
  - Fix Remote Address Checker
  - Support PHP 5.3

### 0.4.1
  - Fix Bugs Die WP Admin
  - Show Alert Status

### 0.4.0
  - Fitur: Mode Testing & Production
  - Peningkatan Keamanan

### 0.2.0
  - Fitur: Menambahkan fitur kode unik.

### 0.1.0
  - Inisialisasi di server sendiri

## Upgrade Notice

### 1.0
  - Penambahan fitur kode unik yang nanti akan menjadi pembeda ketika 
    konsumen checkout.
