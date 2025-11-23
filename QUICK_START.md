# 🚀 QUICK START GUIDE - Edit Produk Feature

## 5 Menit Setup

### ✅ Step 1: Verify Files (1 menit)
Semua file sudah modified dan siap. Check ini:

```bash
# Di workspace c:\laragon\www\Proyek_UMKM
✓ app/controllers/Kasir.php         (Updated ✓)
✓ app/models/Product_model.php      (Updated ✓)
✓ app/views/kasir/index.php         (Updated ✓)
✓ public/css/style.css              (Updated ✓)
✓ public/js/script.js               (Updated ✓)
```

### ✅ Step 2: Database Check (1 menit)
Pastikan database siap:

```sql
-- Run di MySQL
USE db_umkm_ai;

-- Check tabel products exists
SHOW TABLES LIKE 'products';

-- Verify struktur
DESCRIBE products;
```

Expected output:
```
product_id        INT AUTO_INCREMENT PRIMARY KEY
product_name      VARCHAR(100)
kategori          VARCHAR(50)
price             DECIMAL(10,2)
cost_of_goods     DECIMAL(10,2)
stock_quantity    INT
```

### ✅ Step 3: Start Server (1 menit)
```bash
# Start Laragon atau local PHP server
# Open: http://localhost/Proyek_UMKM/public/index.php/kasir
```

### ✅ Step 4: Test Feature (2 menit)

#### Test 1: Buka Halaman Kasir
- Klik link "Kasir" di navbar
- Harusnya lihat product grid dengan kategori

#### Test 2: Tambah Produk
1. Klik tombol hijau **"Tambah Produk"**
2. Isi form:
   - Nama: `Teh Panas`
   - Kategori: `Minuman`
   - Harga: `12000`
   - Modal: `3000`
   - Stock: `50`
3. Klik **"Simpan"**
4. ✅ Harusnya produk muncul di grid

#### Test 3: Edit Produk
1. Hover di atas salah satu product card
2. Klik ikon **pensil** (edit)
3. Ubah harga jadi `15000`
4. Klik **"Simpan"**
5. ✅ Harusnya harga berubah

#### Test 4: Hapus Produk
1. Hover product card
2. Klik ikon **sampah** (delete)
3. Confirm dialog akan muncul
4. Klik **"OK"**
5. ✅ Harusnya produk hilang

---

## 📍 File Locations Quick Reference

```
Proyek_UMKM/
├── app/
│   ├── controllers/
│   │   └── Kasir.php ..................... API Endpoints di sini
│   ├── models/
│   │   └── Product_model.php ............ Database queries di sini
│   └── views/
│       └── kasir/
│           └── index.php ................ HTML modals di sini
├── public/
│   ├── css/
│   │   └── style.css .................... Styling di sini
│   └── js/
│       └── script.js .................... Event handlers di sini
└── Documentation/
    ├── EDIT_PRODUCTS_GUIDE.md ........... User guide
    ├── TESTING_GUIDE.html .............. Test scenarios
    ├── ARCHITECTURE_DIAGRAM.md ......... Technical diagrams
    ├── IMPLEMENTATION_SUMMARY.md ....... Details
    └── CHANGELOG.md .................... Version history
```

---

## 🔍 Code Snippets Quick Reference

### Tambah Produk (Frontend)
```javascript
// Dari public/js/script.js
btnAddProduct.addEventListener('click', () => {
    document.getElementById('product-modal-title').textContent = 'Tambah Produk Baru';
    productForm.reset();
    productModal.classList.remove('hidden');
});
```

### Tambah Produk (Backend)
```php
// Dari app/controllers/Kasir.php
public function addProduct() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $productModel = $this->model('Product_model');
        $result = $productModel->addProduct($data);
        echo json_encode($result);
    }
}
```

### Database Insert
```php
// Dari app/models/Product_model.php
public function addProduct($data) {
    $this->db->query('INSERT INTO ' . $this->table . 
        ' (product_name, kategori, price, cost_of_goods, stock_quantity) 
          VALUES (:product_name, :kategori, :price, :cost_of_goods, :stock_quantity)');
    
    $this->db->bind(':product_name', $data['product_name']);
    // ... more bindings
    
    if ($this->db->execute()) {
        return ['status' => true, 'message' => 'Produk berhasil ditambahkan'];
    }
}
```

---

## 🐛 Troubleshooting

### ❌ Modal tidak muncul
```javascript
// Check di console (F12)
// Pastikan element ada
console.log(document.getElementById('product-modal'));
// Harusnya return: <div id="product-modal" ...>
```

### ❌ Form tidak bisa submit
```javascript
// Check di console
// Lihat error message
// Pastikan semua field terisi
console.log(productForm);
```

### ❌ Database tidak tersimpan
```sql
-- Check di database
SELECT * FROM products ORDER BY created_at DESC LIMIT 5;
-- Harusnya lihat produk yang ditambahkan
```

### ❌ Halaman error 404
```
Pastikan routing sudah correct:
- /kasir ...................... Kasir page
- /kasir/addProduct ........... Add API
- /kasir/editProduct ......... Edit API
- /kasir/deleteProduct ....... Delete API
```

---

## 📱 Browser DevTools Tips

### Check Network Requests
1. Press F12 (DevTools)
2. Go to "Network" tab
3. Try tambah/edit/delete
4. Check POST requests
5. Look at Response tab

### Check Console Errors
1. Press F12
2. Go to "Console" tab
3. Try any action
4. Look for red error messages
5. Click untuk lihat detail

### Check Elements
1. Press F12
2. Go to "Elements" tab
3. Find `.product-modal` element
4. Check if hidden class present
5. Look at computed styles

---

## 🎓 Learning Path

### If you want to UNDERSTAND the code:

#### Day 1: Frontend
- Read `app/views/kasir/index.php`
- Understand modal HTML structure
- Learn form field naming

#### Day 2: Styling
- Read `public/css/style.css`
- Understand `.modal-form` classes
- Learn `.product-card` structure

#### Day 3: JavaScript
- Read `public/js/script.js`
- Understand fetch API usage
- Learn event listener patterns

#### Day 4: Backend
- Read `app/controllers/Kasir.php`
- Understand `addProduct()` method
- Learn JSON response handling

#### Day 5: Database
- Read `app/models/Product_model.php`
- Understand prepared statements
- Learn PDO binding

### If you want to MODIFY the code:

#### Change Button Color
```css
/* public/css/style.css */
.btn-add-product {
    background-color: #27ae60;  /* Change this */
}
```

#### Change Modal Title
```php
<!-- app/views/kasir/index.php -->
<h2 id="product-modal-title">Tambah Produk Baru</h2>
<!-- Change text here -->
```

#### Add New Field
1. Add to HTML form (kasir/index.php)
2. Add to JavaScript (script.js)
3. Add to formData
4. Add to PHP controller
5. Add to Model query

---

## 🔧 Common Customizations

### Change Kategori List
```javascript
// public/js/script.js
const defaultCategories = [
    'Jajanan & Makanan',
    'Minuman',
    'Lainnya'
    // Add more here
];
```

### Change Validation Rules
```javascript
// public/js/script.js
if (!formData.product_name || !formData.kategori || !formData.price) {
    alert('Harap isi semua field yang diperlukan (*)');
    // Modify validation here
}
```

### Change Alert Messages
```javascript
// public/js/script.js
alert('Produk berhasil disimpan');  // Change message here
```

### Change API Response Handling
```javascript
// public/js/script.js
if (result.status) {
    // Change what happens on success
}
```

---

## ✨ Pro Tips

### Tip 1: Use Console for Testing
```javascript
// Test API manually
fetch('/kasir/addProduct', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({product_name: 'Test'})
}).then(r => r.json()).then(d => console.log(d))
```

### Tip 2: Monitor Database in Real-time
```sql
-- Terminal 1: Watch database changes
WATCH "SELECT COUNT(*) as total FROM products"

-- Terminal 2: Run operations
-- New product should show COUNT increase
```

### Tip 3: Disable Auto-Reload for Testing
```javascript
// Comment out di public/js/script.js
// location.reload();
// Then manually refresh saat dibutuhkan
```

### Tip 4: Add Debug Logs
```javascript
// public/js/script.js
console.log('productId:', productId);
console.log('formData:', formData);
console.log('response:', result);
```

---

## 📊 API Reference Quick

### Endpoint: `/kasir/addProduct`
```bash
curl -X POST http://localhost/Proyek_UMKM/public/index.php/kasir/addProduct \
  -H "Content-Type: application/json" \
  -d '{
    "product_name":"Kopi",
    "kategori":"Minuman",
    "price":15000,
    "cost_of_goods":5000,
    "stock_quantity":50
  }'
```

### Endpoint: `/kasir/editProduct`
```bash
curl -X POST http://localhost/Proyek_UMKM/public/index.php/kasir/editProduct \
  -H "Content-Type: application/json" \
  -d '{
    "product_id":5,
    "product_name":"Kopi Premium",
    "kategori":"Minuman",
    "price":18000,
    "cost_of_goods":6000,
    "stock_quantity":45
  }'
```

### Endpoint: `/kasir/deleteProduct`
```bash
curl -X POST http://localhost/Proyek_UMKM/public/index.php/kasir/deleteProduct \
  -H "Content-Type: application/json" \
  -d '{"product_id":5}'
```

---

## ✅ Pre-Launch Checklist

- [ ] All files updated
- [ ] Database verified
- [ ] Server running
- [ ] Test add product ✓
- [ ] Test edit product ✓
- [ ] Test delete product ✓
- [ ] Test manage categories ✓
- [ ] Check database persistence ✓
- [ ] No console errors ✓
- [ ] Documentation read ✓

---

## 🎉 You're Ready!

**Status:** Ready to Use ✅

Sekarang Anda bisa:
1. ✅ Tambah produk baru
2. ✅ Edit produk existing
3. ✅ Hapus produk
4. ✅ Kelola kategori

**Next Steps:**
1. Lakukan testing sesuai TESTING_GUIDE.html
2. Gather user feedback
3. Plan untuk fitur tambahan
4. Monitor performance
5. Plan maintenance schedule

---

## 📚 More Resources

- 📖 [Full User Guide](./EDIT_PRODUCTS_GUIDE.md)
- 🧪 [Testing Scenarios](./TESTING_GUIDE.html)
- 🏗️ [Architecture Details](./ARCHITECTURE_DIAGRAM.md)
- 📊 [Implementation Details](./IMPLEMENTATION_SUMMARY.md)
- 📝 [Changelog](./CHANGELOG.md)

---

**Happy Coding! 🚀**

*Last Updated: November 23, 2024*
*Quick Start v1.0*
