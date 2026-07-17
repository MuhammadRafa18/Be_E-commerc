# 📋 Dokumentasi Sistem Backend
**Arliva Skincare — API, Queue, Payment & Inventaris**

---

## 1. Daftar Endpoint API (Lengkap)

Berikut adalah daftar seluruh endpoint API yang tersedia pada sistem admin Arliva Skincare.

| Kategori | Method | Endpoint | Fungsi |
|----------|--------|----------|--------|
| Dashboard | `GET` | `/api/admin/dashboard/visit` | Data statistik kunjungan |
| Dashboard | `GET` | `/api/admin/dashboard/low-stock` | Cek produk stok tipis |
| Dashboard | `GET` | `/api/admin/dashboard/categories` | Top kategori transaksi |
| Dashboard | `GET` | `/api/admin/dashboard/stats` | Count Order, User, & Payment |
| Produk | `GET` `POST` | `/api/admin/product` | List & Tambah Produk |
| Produk | `GET` `PUT` `DEL` | `/api/admin/product/{slug/id}` | Detail, Update, Hapus Produk |
| Transaksi | `GET` `POST` | `/api/admin/order` | Kelola data pesanan |
| Payment | `POST` | `/api/admin/payment/webhook` | Midtrans Callback (Queue handled) |
| Master | `CRUD` | `/api/admin/faq-category` | Kategori FAQ |
| Master | `CRUD` | `/api/admin/skin-type` | Tipe kulit |
| Master | `CRUD` | `/api/admin/result` | Data hasil / media |
| Logistik | `CRUD` | `/api/admin/shipping-zone` | Zona pengiriman |
| Logistik | `CRUD` | `/api/admin/zone-region` | Estimasi & wilayah |
| User | `GET` `POST` | `/api/admin/user` | List & Tambah User (Admin) |
| User | `GET` `PUT` `DEL` | `/api/admin/user/{id}` | Detail, Update, Hapus User |

---

## 2. Integrasi Payment — Midtrans Webhook

Sistem menggunakan Webhook untuk menerima notifikasi pembayaran dari Midtrans. Karena proses ini krusial, setiap callback masuk ke dalam Queue agar tidak membebani server saat traffic tinggi.

### Alur Kerja

1. Midtrans mengirim data ke endpoint `/api/admin/payment/webhook`
2. Sistem memvalidasi **signature** dari Midtrans
3. Job dipicu untuk memperbarui status pesanan menjadi `paid` atau `failed`
4. Proses update database berjalan di background melalui **Queue Worker**, tidak di main thread HTTP

### Implementasi Queue

Gunakan `dispatch` berikut agar proses update tidak memblokir response HTTP:

```php
dispatch(new ProcessPaymentWebhook($request->all()));
```

---

## 3. Otomatisasi — Console Commands

Sistem menjalankan scheduled tasks untuk menjaga efisiensi operasional. Daftarkan kedua command di bawah dalam `app/Console/Kernel.php`.

| Command | Fungsi | Detail |
|---------|--------|--------|
| `products:check-low-stock` | Low Stock Alert | Periksa SKU stok `<= 5` (namun `> 0`); kirim email notifikasi ke arlivacosmetics@gmail.com |
| `orders:auto-complete` | Auto Complete Order | Ubah status pesanan dari **Dikirim** ke **Selesai** jika melewati `estimated_delivery_max` |

### Konfigurasi Scheduler (`Kernel.php`)

```php
$schedule->command('products:check-low-stock')->daily();
$schedule->command('orders:auto-complete')->daily();
```

### Detail: `products:check-low-stock`

- **Fungsi:** Memeriksa stok SKU yang `<= 5` (namun `> 0`)
- **Tindakan 1:** Mencatat peringatan ke `Log::warning`
- **Tindakan 2:** Mengirim email notifikasi ke `arlivacosmetics@gmail.com`
- **Rekomendasi:** Gunakan `Mail::queue()` agar pengiriman email tidak membuat command hang

### Detail: `orders:auto-complete`

- **Fungsi:** Mengubah status pesanan dari **Dikirim** menjadi **Selesai** jika tanggal saat ini melewati `estimated_delivery_max`
- **Service:** Menggunakan `OrderCompletionService` untuk memproses logika bisnis terkait perubahan status dan finalisasi transaksi
- **Error Handling:** Sudah menggunakan `try-catch` di `CompleteExpiredDeliveryOrders`; pastikan logika di `OrderCompletionService` aman terhadap *race condition* dengan **Database Transaction**

---

## 4. Arsitektur Komponen

Gambaran alur data antar komponen utama sistem:

```
User/Customer
      │
      ▼
API Controller
      │
      ▼
Logic Processor ──────────────────────────────────────────────────────────┐
      │                                                                    │
      ▼                                                                    ▼
Midtrans Webhook                                                 Console Commands
      │                                                                    │
      ▼                                                                    ▼
Queue Worker                                                         Email Queue
      │                                                                    │
      ▼                                                                    ▼
  Database                                                           Mail Server
```

---

## 5. Tips Implementasi Frontend & Backend

| Aspek | Detail |
|-------|--------|
| **Base URL** | Semua endpoint diawali dengan `base_url` (contoh: `http://localhost:8000`) |
| **Auth Header** | Sertakan `Authorization: Bearer {token}` untuk setiap request (Laravel Sanctum/Passport) |
| **File Upload** | Untuk endpoint Product, SkinType, dan Result gunakan `Content-Type: multipart/form-data` |
| **Queue Config** | Atur `QUEUE_CONNECTION=redis` atau `database` di file `.env` |
| **Error Handling** | Gunakan Database Transaction di `OrderCompletionService` untuk menghindari *race condition* |

---

> **Catatan:** Dokumen ini merupakan gabungan dari dua referensi teknis sistem Arliva Skincare. Selalu pastikan konfigurasi `.env` dan `Kernel.php` diperbarui sebelum deployment ke production.