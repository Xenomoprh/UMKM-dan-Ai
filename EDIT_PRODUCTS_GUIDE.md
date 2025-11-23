# Panduan Fitur Edit Produk & Kategori di Kasir

## Daftar Fitur

Fitur edit kasir memungkinkan Anda untuk:

### 1. **Menambah Produk Baru**
   - Klik tombol "Tambah Produk" di bagian atas produk grid
   - Isi form dengan data produk (Nama, Kategori, Harga Jual, Harga Modal, Stock)
   - Klik "Simpan" untuk menyimpan

### 2. **Edit Produk Existing**
   - Hover di atas product card
   - Klik ikon edit (pensil) di sudut kanan bawah
   - Ubah data yang diperlukan
   - Klik "Simpan" untuk menyimpan perubahan

### 3. **Menghapus Produk**
   - Hover di atas product card
   - Klik ikon delete (sampah) di sudut kanan bawah
   - Konfirmasi penghapusan
   - Produk akan terhapus dari sistem

### 4. **Kelola Kategori**
   - Klik tombol "Kelola Kategori" di bagian atas
   - Lihat semua kategori (Jajanan & Makanan, Minuman, Lainnya)
   - Tambahkan kategori baru di bagian bawah form
   - Kategori default tidak bisa dihapus

---

## Struktur Teknis

### Backend (PHP)

**Controller: `app/controllers/Kasir.php`**
- `addProduct()` - API endpoint untuk menambah produk
- `editProduct()` - API endpoint untuk edit produk
- `deleteProduct()` - API endpoint untuk menghapus produk
- `updateStock()` - API endpoint untuk update stock
- `getCategories()` - API endpoint untuk ambil daftar kategori

**Model: `app/models/Product_model.php`**
- `addProduct()` - Menyimpan produk baru ke database
- `updateProduct()` - Update data produk
- `deleteProduct()` - Hapus produk dari database
- `updateStock()` - Update stock produk
- `getCategories()` - Ambil daftar kategori unik

### Frontend (HTML/CSS/JS)

**View: `app/views/kasir/index.php`**
- Tombol "Tambah Produk" dan "Kelola Kategori"
- Product cards dengan action buttons (edit/delete)
- Modal untuk form tambah/edit produk
- Modal untuk kelola kategori

**CSS: `public/css/style.css`**
- `.manage-products-bar` - Bar dengan tombol manage
- `.product-actions` - Action buttons di product card
- `.modal-form` - Styling untuk form modals
- `.category-list` - Styling untuk daftar kategori

**JavaScript: `public/js/script.js`**
- Event handlers untuk buttons
- Form submission & API calls
- Modal management
- Reload page setelah operasi berhasil

---

## API Endpoints

### 1. Tambah Produk
```
POST /kasir/addProduct
Content-Type: application/json

{
    "product_name": "Kopi Hitam",
    "kategori": "Minuman",
    "price": 15000,
    "cost_of_goods": 5000,
    "stock_quantity": 50
}
```

### 2. Edit Produk
```
POST /kasir/editProduct
Content-Type: application/json

{
    "product_id": 5,
    "product_name": "Kopi Hitam Premium",
    "kategori": "Minuman",
    "price": 18000,
    "cost_of_goods": 6000,
    "stock_quantity": 45
}
```

### 3. Hapus Produk
```
POST /kasir/deleteProduct
Content-Type: application/json

{
    "product_id": 5
}
```

### 4. Update Stock
```
POST /kasir/updateStock
Content-Type: application/json

{
    "product_id": 5,
    "quantity": 10  // Bisa positif (tambah) atau negatif (kurangi)
}
```

### 5. Ambil Kategori
```
GET /kasir/getCategories
```

---

## Database Schema

Pastikan tabel `products` memiliki struktur:

```sql
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    product_name VARCHAR(100) NOT NULL,
    kategori VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    cost_of_goods DECIMAL(10,2),
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

---

## Fitur Keamanan

1. **Prepared Statements** - Semua query menggunakan prepared statements untuk mencegah SQL Injection
2. **Input Validation** - Server side validation untuk semua input
3. **Error Handling** - Response JSON yang jelas untuk setiap operasi
4. **Confirmation Dialog** - Konfirmasi dari user sebelum delete

---

## Testing

### Manual Testing
1. Buka halaman kasir
2. Test tambah produk baru
3. Test edit produk existing
4. Test delete produk
5. Test kelola kategori
6. Refresh halaman untuk verify perubahan persisten

### Database Verification
```sql
-- Check semua produk
SELECT * FROM products;

-- Check kategori
SELECT DISTINCT kategori FROM products;

-- Check produk per kategori
SELECT * FROM products WHERE kategori = 'Minuman';
```

---

## Troubleshooting

### Form tidak bisa submit
- Check browser console untuk error messages
- Pastikan semua field required terisi
- Refresh halaman dan coba lagi

### Perubahan tidak tersimpan
- Check database connection
- Lihat error message di modal
- Check server logs

### API tidak responding
- Pastikan controller methods sudah ditambahkan
- Check routing di config
- Verify BASEURL di JavaScript

---

## Catatan Penting

- Kategori default (Jajanan & Makanan, Minuman, Lainnya) tidak bisa dihapus
- Delete produk akan menghapus data selamanya dari database
- Stock bisa negatif jika menggunakan update stock dengan nilai negatif
- Halaman akan refresh otomatis setelah operasi berhasil

---

## Update Future

Fitur yang bisa ditambahkan di masa depan:
- [ ] Bulk edit produk
- [ ] Import/Export produk dari Excel
- [ ] Product images
- [ ] Product descriptions
- [ ] SKU management
- [ ] Barcode scanning
- [ ] Price history
- [ ] Stock alerts
