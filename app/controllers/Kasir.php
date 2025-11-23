<?php

// File: app/controllers/Kasir.php

class Kasir extends Controller {
    public function index() {
        $data['judul'] = 'Halaman Kasir';

        // 1. Ambil semua produk dari model
        $allProducts = $this->model('Product_model')->getAllProducts();

        // 2. Siapkan array untuk kategori (INI BAGIAN PENTING YANG MEMPERBAIKI ERROR)
        // Kita harus membuat array kosong ini terlebih dahulu.
        $data['makanan'] = [];
        $data['minuman'] = [];
        $data['lainnya'] = [];

        // 3. Logika untuk memisahkan produk berdasarkan kategori
        // Ini adalah cara sederhana tanpa mengubah database
        if (!empty($allProducts)) {
            foreach ($allProducts as $product) {
                $namaProduk = strtolower($product['product_name']);
                
                // Tentukan kategori dan ikon
                if (str_contains($namaProduk, 'teh') || str_contains($namaProduk, 'kopi') || str_contains($namaProduk, 'es jeruk') || str_contains($namaProduk, 'air mineral')) {
                    $product['icon'] = 'coffee'; // Ikon untuk minuman
                    $data['minuman'][] = $product;
                } elseif (str_contains($namaProduk, 'bakwan') || str_contains($namaProduk, 'tahu') || str_contains($namaProduk, 'risol') || str_contains($namaProduk, 'pisang') || str_contains($namaProduk, 'onde') || str_contains($namaProduk, 'kue') || str_contains($namaProduk, 'lemper') || str_contains($namaProduk, 'dadar') || str_contains($namaProduk, 'bika')) {
                    $product['icon'] = 'utensils-crossed'; // Ikon untuk makanan
                    $data['makanan'][] = $product;
                } else {
                    $product['icon'] = 'package'; // Ikon default
                    $data['lainnya'][] = $product;
                }
            }
        }

        // 4. Muat view dan kirimkan data yang sudah dikategorikan
        // Sekarang $data['makanan'] dan $data['minuman'] dijamin ada (meskipun kosong)
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
}