<?php

// File: app/controllers/Kasir.php

class Kasir extends Controller {
    public function index() {
        $data['judul'] = 'Halaman Kasir';

        // 1. Ambil semua produk dari model
        $productModel = $this->model('Product_model');
        $allProducts = $productModel->getAllProducts();

        // 2. Siapkan array untuk kategori DINAMIS (menggunakan kategori yang ada)
        $data['categories'] = [];

        // 3. Logika untuk memisahkan produk berdasarkan kategori DINAMIS
        if (!empty($allProducts)) {
            foreach ($allProducts as $product) {
                $kategori = trim($product['kategori'] ?? 'Lainnya');
                
                // Inisialisasi kategori jika belum ada
                if (!isset($data['categories'][$kategori])) {
                    $data['categories'][$kategori] = [
                        'name' => $kategori,
                        'icon' => $this->getIconForCategory($kategori),
                        'products' => []
                    ];
                }
                
                // Tambahkan icon ke produk
                $product['icon'] = $this->getIconForProduct($kategori);
                $data['categories'][$kategori]['products'][] = $product;
            }
        }

        // 4. Urutkan kategori: prioritas Jajanan & Makanan, Minuman, lalu kategori lain, Lainnya di bawah
        $sortedCategories = [];
        $lainnyaCategory = null;
        $priorityOrder = ['Jajanan & Makanan', 'Minuman'];
        
        // Tambahkan kategori prioritas dulu
        foreach ($priorityOrder as $priority) {
            if (isset($data['categories'][$priority])) {
                $sortedCategories[$priority] = $data['categories'][$priority];
            }
        }
        
        // Tambahkan kategori lainnya (yang bukan prioritas dan bukan Lainnya)
        foreach ($data['categories'] as $key => $cat) {
            if (!in_array($key, $priorityOrder) && $key !== 'Lainnya') {
                $sortedCategories[$key] = $cat;
            }
        }
        
        // Tambahkan Lainnya di paling bawah
        if (isset($data['categories']['Lainnya'])) {
            $sortedCategories['Lainnya'] = $data['categories']['Lainnya'];
        }
        
        $data['categories'] = $sortedCategories;

        // 5. Muat view dan kirimkan data yang sudah dikategorikan
        $this->view('kasir/index', $data);
    }

    public function prosesTransaksi() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $transactionModel = $this->model('Transaction_model');
                
                if ($transactionModel->simpanTransaksi($_POST)) {
                    echo json_encode(['status' => 'success', 'message' => 'Transaksi berhasil disimpan!']);
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.']);
                    exit;
                }
            } catch (Exception $e) {
                echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            header('Location: ' . BASEURL . '/kasir');
            exit;
        }
    }

    /**
     * API untuk menambah produk baru
     */
    public function addProduct() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (empty($data['product_name']) || empty($data['kategori']) || empty($data['price'])) {
                    echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
                    exit;
                }
                
                $productModel = $this->model('Product_model');
                $result = $productModel->addProduct([
                    'product_name' => $data['product_name'],
                    'kategori' => $data['kategori'],
                    'price' => floatval($data['price']),
                    'cost_of_goods' => floatval($data['cost_of_goods'] ?? 0),
                    'stock_quantity' => intval($data['stock_quantity'] ?? 0)
                ]);
                
                echo json_encode($result);
                exit;
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['status' => false, 'message' => 'Method tidak diizinkan']);
        exit;
    }

    /**
     * API untuk mengubah produk
     */
    public function editProduct() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (empty($data['product_id']) || empty($data['product_name']) || empty($data['kategori']) || empty($data['price'])) {
                    echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
                    exit;
                }
                
                $productModel = $this->model('Product_model');
                $result = $productModel->updateProduct($data['product_id'], [
                    'product_name' => $data['product_name'],
                    'kategori' => $data['kategori'],
                    'price' => floatval($data['price']),
                    'cost_of_goods' => floatval($data['cost_of_goods'] ?? 0),
                    'stock_quantity' => intval($data['stock_quantity'] ?? 0)
                ]);
                
                echo json_encode($result);
                exit;
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['status' => false, 'message' => 'Method tidak diizinkan']);
        exit;
    }

    /**
     * API untuk menghapus produk
     */
    public function deleteProduct() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (empty($data['product_id'])) {
                    echo json_encode(['status' => false, 'message' => 'ID produk tidak ditemukan']);
                    exit;
                }
                
                $productModel = $this->model('Product_model');
                $result = $productModel->deleteProduct($data['product_id']);
                
                echo json_encode($result);
                exit;
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['status' => false, 'message' => 'Method tidak diizinkan']);
        exit;
    }

    /**
     * API untuk mengupdate stock produk
     */
    public function updateStock() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (empty($data['product_id']) || !isset($data['quantity'])) {
                    echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
                    exit;
                }
                
                $productModel = $this->model('Product_model');
                $result = $productModel->updateStock($data['product_id'], $data['quantity']);
                
                echo json_encode($result);
                exit;
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['status' => false, 'message' => 'Method tidak diizinkan']);
        exit;
    }

    /**
     * API untuk mendapatkan semua kategori
     */
    public function getCategories() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                $productModel = $this->model('Product_model');
                $categories = $productModel->getCategories();
                echo json_encode(['status' => true, 'data' => $categories]);
                exit;
            } catch (Exception $e) {
                echo json_encode(['status' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        }
        echo json_encode(['status' => false, 'message' => 'Method tidak diizinkan']);
        exit;
    }

    /**
     * Helper method untuk mendapatkan icon kategori
     */
    private function getIconForCategory($kategori) {
        $icons = [
            'Jajanan & Makanan' => 'utensils',
            'Minuman' => 'coffee',
            'Makanan' => 'utensils',
            'Makan' => 'utensils',
            'Snack' => 'cookie',
            'Minuman Panas' => 'flame',
            'Minuman Dingin' => 'ice-cream',
            'Dessert' => 'cake',
            'Kue' => 'cake'
        ];
        
        return $icons[$kategori] ?? 'package';
    }

    /**
     * Helper method untuk mendapatkan icon produk berdasarkan kategori
     */
    private function getIconForProduct($kategori) {
        $icons = [
            'Jajanan & Makanan' => 'utensils-crossed',
            'Minuman' => 'coffee',
            'Makanan' => 'utensils-crossed',
            'Makan' => 'utensils-crossed',
            'Snack' => 'cookie',
            'Minuman Panas' => 'flame',
            'Minuman Dingin' => 'ice-cream',
            'Dessert' => 'cake',
            'Kue' => 'cake'
        ];
        
        return $icons[$kategori] ?? 'package';
    }
}