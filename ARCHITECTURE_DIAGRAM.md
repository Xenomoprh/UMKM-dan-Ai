# 🔄 Architecture & Flow Diagram

## System Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                          USER INTERFACE                             │
│                        (kasir/index.php)                            │
└──────────────────┬──────────────────────────────────────┬───────────┘
                   │                                      │
         ┌─────────▼──────────┐              ┌────────────▼────────┐
         │  Manage Products   │              │   Action Buttons    │
         │  Bar (Top Section) │              │  (Hover on Cards)   │
         └──────────┬─────────┘              └────────────┬────────┘
                    │                                     │
        ┌───────────┴───────────┐            ┌────────────┴─────────┐
        │                       │            │                      │
   "Tambah      "Kelola      "Edit"      "Delete"
   Produk"      Kategori"      Button      Button
        │                       │            │                      │
        └────┬──────────────────┴────────────┴──────┬───────────────┘
             │                                      │
             ▼                                      ▼
        ┌──────────────────────────┐     ┌──────────────────────────┐
        │    MODAL DIALOGS         │     │   EVENT HANDLERS (JS)    │
        │                          │     │  (public/js/script.js)   │
        ├──────────────────────────┤     └──────────────────────────┘
        │ 1. Product Modal         │            │
        │ 2. Category Modal        │            │ Fetch API
        │ 3. Payment Modal         │            │
        └────────┬─────────────────┘            │
                 │                             │
                 │ Form Submission             │
                 │ + Validation                │
                 │                             ▼
                 │                    ┌─────────────────────────┐
                 └───────────────────►│  BACKEND CONTROLLER     │
                                      │  (app/controllers/...)  │
                                      └────────────┬────────────┘
                                                   │
                                       ┌───────────┼───────────┐
                                       │           │           │
                                    ┌──▼──┐    ┌──▼──┐    ┌───▼──┐
                                    │Add  │    │Edit │    │Delete│
                                    │     │    │     │    │      │
                                    └──┬──┘    └──┬──┘    └───┬──┘
                                       │          │           │
                                       └──────────┼───────────┘
                                                  │
                                      ┌───────────▼────────────┐
                                      │  MODEL LAYER           │
                                      │(Product_model.php)    │
                                      └───────────┬────────────┘
                                                  │
                                       ┌──────────┼──────────┐
                                       │          │          │
                                    ┌──▼──┐  ┌───▼────┐ ┌───▼───┐
                                    │DB   │  │Prepared│ │Result │
                                    │Query│  │Stmt    │ │JSON   │
                                    └─────┘  └────────┘ └───────┘
                                            │
                                            ▼
                                      ┌────────────────┐
                                      │  MySQL DB      │
                                      │  (products)    │
                                      └────────────────┘
```

## User Flow: Tambah Produk

```
┌─────────────────┐
│  Halaman Kasir  │
│  (Loaded)       │
└────────┬────────┘
         │
         ▼
    ┌─────────────────────────────────┐
    │ User Clicks "Tambah Produk"     │
    │         Button                  │
    └────────┬────────────────────────┘
             │
             ▼
    ┌─────────────────────────────────┐
    │ Modal Form Opens                │
    │ (product-modal)                 │
    │                                 │
    │ - Nama Produk [  ]              │
    │ - Kategori    [ ▼ ]             │
    │ - Harga Jual  [    ]            │
    │ - Harga Modal [    ]            │
    │ - Stock       [    ]            │
    │                                 │
    │ [Cancel]  [Simpan]              │
    └────────┬────────────────────────┘
             │
             ├─ Jika Cancel
             │  └─► Close Modal ─────────────────► Kembali ke Grid
             │
             └─ Jika Simpan
                  │
                  ▼
                ┌──────────────────────┐
                │ Client Validation    │
                │ (JavaScript)         │
                │                      │
                │ ✓ Nama tidak kosong  │
                │ ✓ Kategori dipilih   │
                │ ✓ Harga > 0          │
                └────────┬─────────────┘
                         │
                    ┌────┴────┐
                    │ Valid?  │
                    └────┬────┘
                    ┌────┴────┐
                    │          │
                   NO         YES
                    │          │
                    ▼          ▼
              ┌──────────┐  ┌──────────────────┐
              │Alert:   │  │Fetch POST Request│
              │Error    │  │/kasir/addProduct │
              │Message  │  │+ JSON Body       │
              └──────────┘  └────────┬─────────┘
                                    │
                                    ▼
                            ┌───────────────────┐
                            │ Server Processing │
                            │  (PHP Controller) │
                            │                   │
                            │ - Validasi Input  │
                            │ - Call Model      │
                            │ - Insert to DB    │
                            └───────┬───────────┘
                                    │
                            ┌───────┴────────┐
                            │                │
                          SUCCESS           ERROR
                            │                │
                            ▼                ▼
                    ┌───────────────┐   ┌──────────────┐
                    │JSON Response: │   │JSON Response:│
                    │{              │   │{             │
                    │ status: true, │   │ status: false│
                    │ message: "..." │   │ message:"..." │
                    │}              │   │}             │
                    └───────┬───────┘   └──────┬───────┘
                            │                 │
                            ▼                 ▼
                    ┌───────────────┐   ┌──────────────┐
                    │Show Alert:   │   │Show Alert:   │
                    │"Berhasil!"   │   │"Error: ..."  │
                    │              │   │              │
                    │location.     │   │Modal tetap   │
                    │reload()      │   │terbuka       │
                    └───────────────┘   └──────────────┘
                            │
                            ▼
                    ┌───────────────────┐
                    │ Halaman Reload    │
                    │ Query DB Updated  │
                    │ Grid Shows New    │
                    │ Product           │
                    └───────────────────┘
```

## User Flow: Edit Produk

```
┌─────────────────┐
│ Lihat Product   │
│ di Grid         │
└────────┬────────┘
         │
         ▼
    ┌──────────────────────┐
    │ Hover Product Card   │
    │ Show Action Buttons  │
    │ (Edit & Delete Icons)│
    └────────┬─────────────┘
             │
             ▼
    ┌──────────────────────┐
    │ Click Edit Icon      │
    │ (Pencil Button)      │
    └────────┬─────────────┘
             │
             ▼
    ┌────────────────────────────────┐
    │ Get Product Data from Card     │
    │ - ID, Name, Price              │
    │ - Category from parent div     │
    │                                │
    │ Populate Form with Data        │
    │ - product-id: <hidden>         │
    │ - product-name: Pre-filled     │
    │ - product-category: Pre-filled │
    │ - product-price: Pre-filled    │
    │ - product-cost: Set to 0       │
    │ - product-stock: Set to 0      │
    │                                │
    │ Change Modal Title to "Edit"   │
    └────────┬────────────────────────┘
             │
             ▼
    ┌────────────────────────────────┐
    │ Modal Opens                    │
    │ (User dapat edit semua field)  │
    │                                │
    │ Misal: Ubah harga 15000->18000 │
    └────────┬────────────────────────┘
             │
             ▼
    ┌────────────────────────────────┐
    │ Click "Simpan" Button          │
    └────────┬────────────────────────┘
             │
             ├─ Jika Cancel
             │  └─► Close Modal ─────► Kembali ke Grid
             │
             └─ Jika Simpan
                  │
                  ▼
                ┌──────────────────────────┐
                │ Validation (JS)          │
                │ ✓ product_id not empty   │
                │ ✓ All fields filled      │
                └────────┬─────────────────┘
                         │
                         ▼
                ┌──────────────────────────┐
                │ Fetch POST Request       │
                │ /kasir/editProduct       │
                │ + JSON dengan product_id │
                │   dan semua field        │
                └────────┬─────────────────┘
                         │
                         ▼
                ┌──────────────────────────┐
                │ Server Update DB         │
                │ UPDATE products          │
                │ WHERE product_id = ?     │
                │ SET all columns          │
                └────────┬─────────────────┘
                         │
                         ▼
                ┌──────────────────────────┐
                │ Return Success Response  │
                │ {status: true}           │
                └────────┬─────────────────┘
                         │
                         ▼
                ┌──────────────────────────┐
                │ location.reload()        │
                │ Halaman refresh          │
                │ Product terbaru ditampil │
                └──────────────────────────┘
```

## Database Schema & Relationships

```
┌──────────────────────────────────┐
│         PRODUCTS TABLE           │
├──────────────────────────────────┤
│ PK  product_id        INT        │
│     product_name      VARCHAR    │
│     kategori          VARCHAR    │
│     price             DECIMAL    │◄─────┐
│     cost_of_goods     DECIMAL    │      │
│     stock_quantity    INT        │      │
│     created_at        TIMESTAMP  │      │
│     updated_at        TIMESTAMP  │      │
└──────────────────────────────────┘      │
                                          │
                                     Used By
                                          │
┌──────────────────────────────────┐     │
│   TRANSACTIONS TABLE             │     │
├──────────────────────────────────┤     │
│ PK  transaction_id    INT        │     │
│     transaction_time  TIMESTAMP  │     │
│     total_amount      DECIMAL    │     │
└──────────────────────────────────┘     │
                  ▲                       │
                  │ Has Many              │
                  │                       │
┌──────────────────────────────────┐     │
│ TRANSACTION_DETAILS TABLE        │     │
├──────────────────────────────────┤     │
│ PK  detail_id         INT        │     │
│ FK  transaction_id    INT ────┐  │     │
│ FK  product_id        INT ────┼──┼─────┘
│     quantity          INT      │  │
│     subtotal          DECIMAL  │  │
└──────────────────────────────────┘  │
                                    One-to-Many
```

## API Call Flow

```
╔════════════════════════════════════════════════════════════════╗
║                    FRONTEND (JavaScript)                       ║
║                                                                ║
║  const response = await fetch('/kasir/addProduct', {           ║
║    method: 'POST',                                             ║
║    headers: {'Content-Type': 'application/json'},              ║
║    body: JSON.stringify({                                      ║
║      product_name: 'Kopi',                                     ║
║      kategori: 'Minuman',                                      ║
║      price: 15000,                                             ║
║      ...                                                        ║
║    })                                                          ║
║  })                                                            ║
║                                                                ║
║  const result = await response.json()                          ║
║  if(result.status) { location.reload() }                       ║
╚════════════════════════════════════════════════════════════════╝
                            │
                            │ HTTP POST
                            │ JSON Payload
                            ▼
╔════════════════════════════════════════════════════════════════╗
║                    ROUTER (Framework)                          ║
║                                                                ║
║  Match Route:                                                  ║
║  POST /kasir/addProduct                                        ║
║                                                                ║
║  Call Controller Method:                                       ║
║  Kasir::addProduct()                                           ║
╚════════════════════════════════════════════════════════════════╝
                            │
                            ▼
╔════════════════════════════════════════════════════════════════╗
║              CONTROLLER (app/controllers/Kasir.php)            ║
║                                                                ║
║  public function addProduct() {                                ║
║    $data = json_decode(file_get_contents(...), true)          ║
║    // Validate input                                           ║
║    // Call Model                                               ║
║    $result = $this->model('Product_model')                     ║
║      ->addProduct($data)                                       ║
║    // Return JSON                                              ║
║    echo json_encode($result)                                   ║
║  }                                                             ║
╚════════════════════════════════════════════════════════════════╝
                            │
                            ▼
╔════════════════════════════════════════════════════════════════╗
║                 MODEL (app/models/Product_model.php)           ║
║                                                                ║
║  public function addProduct($data) {                           ║
║    $this->db->query('INSERT INTO products ...')                ║
║    $this->db->bind(':product_name', $data['product_name'])    ║
║    $this->db->bind(':kategori', $data['kategori'])            ║
║    $this->db->bind(':price', $data['price'])                  ║
║    ...                                                         ║
║    if($this->db->execute()) {                                  ║
║      return ['status'=>true,'message'=>'Berhasil']             ║
║    }                                                           ║
║  }                                                             ║
╚════════════════════════════════════════════════════════════════╝
                            │
                            ▼
╔════════════════════════════════════════════════════════════════╗
║                   DATABASE (MySQL)                             ║
║                                                                ║
║  INSERT INTO products                                          ║
║  (product_name, kategori, price, ...)                          ║
║  VALUES (?, ?, ?, ...)                                         ║
║                                                                ║
║  [SUCCESS] ✓ Row inserted                                      ║
╚════════════════════════════════════════════════════════════════╝
                            │
                            │ Response
                            ▼
╔════════════════════════════════════════════════════════════════╗
║              JSON Response to Frontend                          ║
║                                                                ║
║  {                                                             ║
║    "status": true,                                             ║
║    "message": "Produk berhasil ditambahkan"                    ║
║  }                                                             ║
╚════════════════════════════════════════════════════════════════╝
                            │
                            │
                            ▼
╔════════════════════════════════════════════════════════════════╗
║             FRONTEND (JavaScript Continue)                     ║
║                                                                ║
║  if(result.status) {                                           ║
║    alert(result.message)                                       ║
║    closeProductModal()                                         ║
║    location.reload()  ◄─── Reload page                         ║
║  }                                                             ║
╚════════════════════════════════════════════════════════════════╝
```

---

**Generated:** November 2024
**Version:** 1.0
**Status:** Architecture Documented ✓
