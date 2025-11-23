# 🐛 Troubleshooting Guide - Error "Terjadi kesalahan saat menyimpan produk"

## 🔍 Debug Steps

Jika Anda mengalami error "Terjadi kesalahan saat menyimpan produk", ikuti langkah-langkah ini:

### Step 1: Buka Browser Developer Console (F12)

1. **Press F12** untuk buka DevTools
2. Go to **"Console"** tab
3. Coba tambah produk lagi
4. Lihat error messages yang muncul

#### Apa yang harus dilihat:

```
BASEURL: http://localhost/Proyek_UMKM/public/index.php
API Endpoint akan ke: http://localhost/Proyek_UMKM/public/index.php/kasir/addProduct
Sending to: http://localhost/Proyek_UMKM/public/index.php/kasir/addProduct
FormData: { product_name: "...", kategori: "...", price: ..., ... }
Response status: 200
API Response: { status: true/false, message: "..." }
```

---

## 🎯 Common Issues & Solutions

### Issue 1: BASEURL Tidak Benar

**Error Messages:**
- Response status: 404
- API tidak ditemukan

**Solution:**
Pastikan URL di browser adalah salah satu dari:
```
http://localhost/Proyek_UMKM/public/index.php/kasir
atau
http://localhost/Proyek_UMKM/public/index.php?url=kasir
```

Jangan:
```
http://localhost/kasir
http://localhost:8080/Proyek_UMKM/kasir
```

---

### Issue 2: Database Connection Error

**Error Messages di Console:**
```
API Response: { status: false, message: "Error: SQLSTATE[HY000]..." }
```

**Checklist:**
- [ ] MySQL Server running (check Laragon taskbar)
- [ ] Database `db_umkm_ai` exists
- [ ] Table `products` exists

**How to Check:**

Buka terminal dan run:
```bash
# Check MySQL service
mysql -u root -p

# Once logged in
USE db_umkm_ai;
SHOW TABLES;
DESCRIBE products;
```

Expected output:
```
+------------------+
| Tables_in_db_umkm_ai |
+------------------+
| products         |
| transactions     |
| transaction_details |
+------------------+
```

---

### Issue 3: Form Field Tidak Terisi

**Error Messages:**
```
Alert: "Harap isi semua field yang diperlukan (*)"
```

**Solution:**
- [ ] Nama Produk: Jangan kosong
- [ ] Kategori: Pilih dari dropdown
- [ ] Harga Jual: Masukkan angka > 0
- [ ] Harga Modal: Bisa kosong (default 0)
- [ ] Stock: Bisa kosong (default 0)

---

### Issue 4: Kategori Dropdown Kosong

**Problem:** Dropdown kategori tidak ada pilihan

**Solution:**
Check HTML element ada atau tidak:

Di Console (F12), run:
```javascript
console.log(document.getElementById('product-category'));
// Harusnya return: <select id="product-category" ...>
```

Jika return `null`, berarti element belum di-load atau ID salah.

---

### Issue 5: Error: "Network Error"

**Symptoms:**
```
Terjadi kesalahan saat menyimpan produk: Failed to fetch
```

**Causes:**
1. Server Apache/PHP tidak running
2. Laragon tidak active
3. Network/CORS issue

**Solution:**

**Option A: Check if Server is Running**
```bash
# Di PowerShell
Test-NetConnection localhost -Port 80

# Output harusnya:
# TcpTestSucceeded : True
```

**Option B: Restart Laragon**
1. Click Laragon icon di taskbar
2. Click "Stop All"
3. Wait 2 seconds
4. Click "Start All"
5. Refresh browser

---

## 🔧 Manual Testing API

Jika ingin test API directly, gunakan curl atau Postman:

### Using cURL (Windows PowerShell):

```powershell
$body = @{
    product_name = "Kopi Arabika"
    kategori = "Minuman"
    price = 15000
    cost_of_goods = 5000
    stock_quantity = 50
} | ConvertTo-Json

Invoke-WebRequest `
  -Uri "http://localhost/Proyek_UMKM/public/index.php/kasir/addProduct" `
  -Method POST `
  -Headers @{"Content-Type"="application/json"} `
  -Body $body
```

### Expected Response:
```json
{
  "status": true,
  "message": "Produk berhasil ditambahkan"
}
```

---

## 📋 Advanced Debugging

### Check Server Logs

**Windows dengan Laragon:**
1. Laragon → Tools → Log → Apache Error Log
2. Laragon → Tools → Log → PHP Error Log

**Lihat untuk error seperti:**
```
[error] ... Call to undefined method Product_model::addProduct()
[error] ... SQLSTATE[42S22]: Column not found: 1054
```

---

### Enable PHP Error Reporting

Edit `app/config.php`:

```php
// Tambahkan di top file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Untuk debugging request
$_DEBUG = true;
```

Kemudian lihat response di browser.

---

### Check Database Structure

Pastikan tabel `products` punya column:

```sql
DESCRIBE products;
```

Expected output:
```
+------------------+--------------+------+-----+---------+----------------+
| Field            | Type         | Null | Key | Default | Extra          |
+------------------+--------------+------+-----+---------+----------------+
| product_id       | int(11)      | NO   | PRI | NULL    | auto_increment |
| product_name     | varchar(100) | NO   |     | NULL    |                |
| kategori         | varchar(50)  | YES  |     | NULL    |                |
| price            | decimal(10,2)| NO   |     | NULL    |                |
| cost_of_goods    | decimal(10,2)| YES  |     | NULL    |                |
| stock_quantity   | int(11)      | YES  |     | 0       |                |
| created_at       | timestamp    | YES  |     | CURRENT | on update      |
+------------------+--------------+------+-----+---------+----------------+
```

Jika ada column yang kurang, tambahkan:
```sql
ALTER TABLE products ADD COLUMN cost_of_goods DECIMAL(10,2);
ALTER TABLE products ADD COLUMN stock_quantity INT DEFAULT 0;
```

---

## ✅ Verification Checklist

Sebelum troubleshoot lebih lanjut, pastikan:

- [ ] Laragon berjalan (icon di taskbar ada "L")
- [ ] MySQL aktif (Apache + MySQL di Laragon)
- [ ] Database `db_umkm_ai` ada
- [ ] Table `products` ada dengan struktur lengkap
- [ ] File PHP tidak ada syntax error
- [ ] Browser console tidak ada error
- [ ] URL benar (check BASEURL di console)

---

## 🆘 Masih Error?

Jika masih error, collect info ini:

1. **Console screenshot** (F12 → Console tab)
2. **Network tab screenshot** (F12 → Network tab → try add product)
3. **Error message detail**
4. **Database structure** (run: `DESCRIBE products;`)
5. **PHP error log** (Laragon → Tools → Log)

---

## 📞 Quick Contact Info

- **Ask in Console:** `console.log('BASEURL: ' + BASEURL)`
- **Check Status:** Open developer tools and look for "Response status"
- **Database Check:** Use MySQL client to verify structure

---

**Last Updated:** November 23, 2025
**Version:** 1.0
