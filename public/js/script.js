// Fungsi helper untuk format angka
function number_format(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Jalankan kode hanya setelah seluruh halaman HTML dimuat
document.addEventListener('DOMContentLoaded', function () {

    // ====================================================================
    // LOGIKA UNTUK HALAMAN KASIR
    // ====================================================================
    const kasirContainer = document.querySelector('.kasir-container');
    if (kasirContainer) {
        // --- Ambil Elemen ---
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');
        const paymentAmountInput = document.getElementById('payment-amount'); 
        const changeDisplayElement = document.getElementById('change-display');
        const totalItemsElement = document.getElementById('total-items');
        const clearCartButton = document.getElementById('btn-clear-cart');

        if (!cartItemsContainer || !cartTotalElement || !paymentAmountInput || !changeDisplayElement) {
            console.error('Elemen kasir (keranjang/pembayaran) tidak ditemukan!');
        } else {
            let cart = {}; 
            let currentTotal = 0; 

            // --- Fungsi Kalkulator ---
            function calculateChange() {
                const payment = parseFloat(paymentAmountInput.value) || 0;
                
                if (payment === 0) {
                    changeDisplayElement.innerText = 'Rp 0';
                    return;
                }

                const change = payment - currentTotal;

                if (change < 0) {
                    changeDisplayElement.innerText = `Rp ${number_format(Math.abs(change))} (KURANG)`;
                    changeDisplayElement.style.color = '#e74c3c';
                } else {
                    changeDisplayElement.innerText = `Rp ${number_format(change)}`;
                    changeDisplayElement.style.color = '#27ae60';
                }
            }

            // --- Event Listener untuk Input Pembayaran ---
            paymentAmountInput.addEventListener('input', calculateChange); 

            // --- Tambah ke Keranjang ---
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.dataset.id;
                    const productName = this.dataset.name;
                    const productPrice = parseFloat(this.dataset.price);

                    if (cart[productId]) {
                        cart[productId].quantity++;
                    } else {
                        cart[productId] = { name: productName, price: productPrice, quantity: 1 };
                    }
                    updateCartView();
                });
            });

            // --- Perbarui Tampilan Keranjang ---
            function updateCartView() {
                cartItemsContainer.innerHTML = '';
                let total = 0; 
                let totalItems = 0;
                let hasItems = false;

                for (const productId in cart) {
                    hasItems = true;
                    const item = cart[productId];
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;
                    totalItems += item.quantity;

                    const cartItemElement = document.createElement('div');
                    cartItemElement.className = 'cart-item';
                    cartItemElement.innerHTML = `
                        <div class="cart-item-details">
                            <div class="cart-item-name">${item.name}</div>
                            <div class="cart-item-qty">@ Rp ${number_format(item.price)}</div>
                            <div class="cart-item-controls">
                                <button class="qty-btn qty-minus" data-id="${productId}" title="Kurangi jumlah">−</button>
                                <input type="number" class="qty-input" value="${item.quantity}" min="1" data-id="${productId}" title="Masukkan jumlah">
                                <button class="qty-btn qty-plus" data-id="${productId}" title="Tambah jumlah">+</button>
                            </div>
                        </div>
                        <div class="cart-item-price">Rp ${number_format(itemTotal)}</div>
                        <button class="cart-item-remove" data-id="${productId}" title="Hapus dari keranjang">
                            ✕
                        </button>
                    `;
                    cartItemsContainer.appendChild(cartItemElement);

                    // Event listener untuk tombol minus
                    cartItemElement.querySelector('.qty-minus').addEventListener('click', function() {
                        if (cart[productId].quantity > 1) {
                            cart[productId].quantity--;
                        } else {
                            delete cart[productId];
                        }
                        updateCartView();
                    });

                    // Event listener untuk tombol plus
                    cartItemElement.querySelector('.qty-plus').addEventListener('click', function() {
                        cart[productId].quantity++;
                        updateCartView();
                    });

                    // Event listener untuk input quantity
                    cartItemElement.querySelector('.qty-input').addEventListener('change', function() {
                        const newQty = parseInt(this.value);
                        if (newQty > 0) {
                            cart[productId].quantity = newQty;
                        } else {
                            this.value = cart[productId].quantity;
                        }
                        updateCartView();
                    });

                    // Event listener untuk tombol hapus
                    cartItemElement.querySelector('.cart-item-remove').addEventListener('click', function() {
                        delete cart[productId];
                        updateCartView();
                    });
                }

                if (!hasItems) {
                    cartItemsContainer.innerHTML = '<p class="empty-cart-message"><i data-lucide="inbox" style="width: 32px; height: 32px; display: block; margin: 0 auto 10px; opacity: 0.5;"></i>Keranjang masih kosong</p>';
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }

                // Update total items
                if (totalItemsElement) {
                    totalItemsElement.innerText = totalItems;
                }

                cartTotalElement.innerText = `Rp ${number_format(total)}`;
                currentTotal = total; 
                calculateChange();

                // Recreate lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            // --- Clear Cart Button ---
            if (clearCartButton) {
                clearCartButton.addEventListener('click', function() {
                    if (Object.keys(cart).length === 0) {
                        alert('Keranjang sudah kosong!');
                        return;
                    }
                    if (confirm('Yakin ingin menghapus semua item dari keranjang?')) {
                        cart = {};
                        paymentAmountInput.value = '';
                        updateCartView();
                    }
                });
            }

            // --- PRESET AMOUNT BUTTONS ---
            const presetButtons = document.querySelectorAll('.preset-btn');
            presetButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const amount = parseInt(this.dataset.amount);
                    paymentAmountInput.value = amount;
                    paymentAmountInput.dispatchEvent(new Event('input'));
                });
            });

            // --- FUNGSI TOMBOL "BAYAR" ---
            const bayarButton = document.querySelector('.btn-bayar');
            if (bayarButton) {
                bayarButton.addEventListener('click', function() {
                    if (Object.keys(cart).length === 0) {
                        alert('Keranjang masih kosong!');
                        return;
                    }
                    
                    const payment = parseFloat(paymentAmountInput.value) || 0;
                    if (payment < currentTotal) {
                        alert('Uang pelanggan kurang!');
                        return;
                    }

                    const changeAmount = payment - currentTotal;

                    // Siapkan data untuk dikirim ke server
                    const postData = new URLSearchParams();
                    postData.append('cart', JSON.stringify(cart));
                    postData.append('payment_received', payment);
                    postData.append('payment_change', changeAmount);

                    console.log('Cart data:', cart);
                    console.log('Cart JSON:', JSON.stringify(cart));
                    console.log('POST data:', postData.toString());
                    console.log('URL:', BASEURL + '/index.php/kasir/prosesTransaksi');
                    
                    fetch(BASEURL + '/index.php/kasir/prosesTransaksi', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: postData.toString()
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        if (!response.ok) {
                            console.error('HTTP Error:', response.statusText);
                            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                        }
                        return response.text();
                    })
                    .then(responseText => {
                        console.log('Response text:', responseText);
                        try {
                            const data = JSON.parse(responseText);
                            console.log('Parsed data:', data);
                            
                            if (data.status === 'success') {
                                // Tampilkan modal dengan detail pembayaran
                                showPaymentModal(currentTotal, payment, changeAmount);
                                
                                // Reset cart setelah pembayaran
                                cart = {};
                                paymentAmountInput.value = ''; 
                                updateCartView();
                            } else {
                                alert(data.message || 'Pembayaran gagal!');
                            }
                        } catch (parseError) {
                            console.error('JSON Parse error:', parseError);
                            console.error('Response was:', responseText.substring(0, 500));
                            alert('Server Error: Response tidak valid JSON\n\n' + responseText.substring(0, 200));
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        console.error('Error message:', error.message);
                        console.error('Error stack:', error.stack);
                        alert('Terjadi kesalahan saat menghubungi server:\n' + error.message);
                    });
                });
            }

            // --- FUNGSI MODAL PEMBAYARAN ---
            function showPaymentModal(total, payment, change) {
                const modal = document.getElementById('payment-modal');
                const modalTotal = document.getElementById('modal-total');
                const modalPayment = document.getElementById('modal-payment');
                const modalChange = document.getElementById('modal-change');

                if (modal && modalTotal && modalPayment && modalChange) {
                    modalTotal.innerText = `Rp ${number_format(total)}`;
                    modalPayment.innerText = `Rp ${number_format(payment)}`;
                    modalChange.innerText = `Rp ${number_format(change)}`;
                    
                    modal.classList.remove('hidden');
                }
            }

            // --- MODAL CLOSE EVENT ---
            const modalCloseBtn = document.getElementById('modal-close');
            const modalOkBtn = document.getElementById('btn-modal-ok');
            const paymentModal = document.getElementById('payment-modal');

            if (modalCloseBtn && paymentModal) {
                modalCloseBtn.addEventListener('click', function() {
                    paymentModal.classList.add('hidden');
                });
            }

            if (modalOkBtn && paymentModal) {
                modalOkBtn.addEventListener('click', function() {
                    paymentModal.classList.add('hidden');
                });
            }

            // Close modal saat klik di luar modal
            if (paymentModal) {
                paymentModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
                });
            }
        }
    }

    // ====================================================================
    // LOGIKA UNTUK HALAMAN ANALISIS AI (Tetap sama)
    // ====================================================================
    const chatForm = document.getElementById('chat-input-form');
    if (chatForm) {
        // (Semua kode chat AI Anda yang sudah ada tetap di sini)
        const chatInput = document.getElementById('chat-input');
        const chatBox = document.getElementById('chat-box');
        const CHAT_HISTORY_KEY = 'ai_chat_history';
        let chatHistory = []; 

        function appendMessageToDOM(message, type, isTyping = false) {
            const messageWrapper = document.createElement('div');
            messageWrapper.className = `chat-message ${type}`;
            if (isTyping) messageWrapper.id = 'typing-indicator';
            
            const messageBubble = document.createElement('div');
            messageBubble.className = 'message-bubble';
            
            // LOGIKA BARU: Menggunakan Marked.js untuk pesan AI
            // Cek apakah pesan ini dari AI ('ai-message') DAN library 'marked' sudah termuat
            if (type === 'ai-message' && typeof marked !== 'undefined' && !isTyping) {
                // Gunakan marked.parse() untuk mengubah Markdown (tabel/bold) menjadi HTML
                messageBubble.innerHTML = marked.parse(message);
            } else {
                // Untuk pesan pengguna atau loading, gunakan teks biasa
                const formattedMessage = message.replace(/\n/g, '<br>');
                messageBubble.innerHTML = `<p>${formattedMessage}</p>`;
            }

            messageWrapper.appendChild(messageBubble);
            chatBox.appendChild(messageWrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) typingIndicator.remove();
        }

        function saveHistory() {
            sessionStorage.setItem(CHAT_HISTORY_KEY, JSON.stringify(chatHistory));
        }

        function loadHistory() {
            const savedHistory = sessionStorage.getItem(CHAT_HISTORY_KEY);
            chatBox.innerHTML = ''; 
            if (savedHistory) {
                chatHistory = JSON.parse(savedHistory);
                chatHistory.forEach(item => {
                    appendMessageToDOM(item.message, item.type);
                });
            } else {
                const welcomeMessage = 'Halo! Saya adalah asisten AI Anda. Silakan ajukan pertanyaan, contohnya: "Produk apa yang paling laku hari ini?"';
                chatHistory = [{ type: 'ai-message', message: welcomeMessage }];
                appendMessageToDOM(welcomeMessage, 'ai-message');
                saveHistory(); 
            }
        }

        const sendMessage = (event) => {
            event.preventDefault();
            const userMessage = chatInput.value.trim();
            if (userMessage === '') return;
            appendMessageToDOM(userMessage, 'user-message');
            chatHistory.push({ type: 'user-message', message: userMessage });
            saveHistory();
            chatInput.value = ''; 
            appendMessageToDOM('AI sedang berpikir...', 'ai-message', true);
            fetch(BASEURL + '/analisis/tanyaAi', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'question=' + encodeURIComponent(userMessage)
            })
            .then(response => response.json())
            .then(data => {
                removeTypingIndicator();
                appendMessageToDOM(data.answer, 'ai-message');
                chatHistory.push({ type: 'ai-message', message: data.answer });
                saveHistory();
            })
            .catch(error => {
                console.error('Error:', error);
                removeTypingIndicator();
                const errorMessage = 'Maaf, terjadi kesalahan saat menghubungi server.';
                appendMessageToDOM(errorMessage, 'ai-message');
                chatHistory.push({ type: 'ai-message', message: errorMessage });
                saveHistory();
            });
        };
        chatForm.addEventListener('submit', sendMessage);
        loadHistory();
    }

    // ====================================================================
    // LOGIKA UNTUK HALAMAN HISTORY
    // ====================================================================
    const toggleHistoryBtn = document.getElementById('btn-toggle-history');
    const historyContent = document.getElementById('history-content');

    if (toggleHistoryBtn && historyContent) {
        toggleHistoryBtn.addEventListener('click', function() {
            historyContent.classList.toggle('hidden');
            toggleHistoryBtn.classList.toggle('open');
        });
    }

    // Toggle transaction details
    function toggleTransactionDetails(element) {
        const card = element.closest('.trx-card');
        const details = card.querySelector('.trx-details');
        const icon = card.querySelector('.expand-icon');

        if (details) {
            details.classList.toggle('hidden');
            icon.style.transform = details.classList.contains('hidden') 
                ? 'rotate(0deg)' 
                : 'rotate(180deg)';
        }
    }


    
    // Modal Elements
    const productModal = document.getElementById('product-modal');
    const productForm = document.getElementById('product-form');
    const btnAddProduct = document.getElementById('btn-add-product');
    const btnEditProduct = document.querySelectorAll('.btn-edit-product');
    const btnDeleteProduct = document.querySelectorAll('.btn-delete-product');
    const productModalClose = document.getElementById('product-modal-close');
    const productModalCancel = document.getElementById('product-modal-cancel');
    const productModalSave = document.getElementById('product-modal-save');
    const categoryModal = document.getElementById('category-modal');
    const btnManageCategories = document.getElementById('btn-manage-categories');
    const categoryModalClose = document.getElementById('category-modal-close');
    const categoryModalDone = document.getElementById('category-modal-done');

    // ====================================================================
    // Toggle untuk Show/Hide Edit & Delete Buttons
    // ====================================================================
    const toggleEditDelete = document.getElementById('toggle-edit-delete');
    
    if (toggleEditDelete) {
        // Load state dari localStorage
        const isVisible = localStorage.getItem('showEditDeleteButtons') === 'true';
        toggleEditDelete.checked = isVisible;
        updateProductActionsVisibility(isVisible);
        
        // Event listener untuk toggle
        toggleEditDelete.addEventListener('change', (e) => {
            const isChecked = e.target.checked;
            localStorage.setItem('showEditDeleteButtons', isChecked);
            updateProductActionsVisibility(isChecked);
        });
    }
    
    // Function untuk update visibility product-actions
    function updateProductActionsVisibility(isVisible) {
        const productActions = document.querySelectorAll('.product-actions');
        productActions.forEach(actions => {
            if (isVisible) {
                actions.classList.add('visible');
            } else {
                actions.classList.remove('visible');
            }
        });
        
        // Re-render lucide icons setelah visibility berubah
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    if (btnAddProduct) {
        // Event untuk membuka modal tambah produk
        btnAddProduct.addEventListener('click', () => {
            document.getElementById('product-modal-title').textContent = 'Tambah Produk Baru';
            productForm.reset();
            document.getElementById('product-id').value = '';
            productModal.classList.remove('hidden');
        });

        // Event untuk menutup modal produk
        const closeProductModal = () => {
            productModal.classList.add('hidden');
            productForm.reset();
        };

        if (productModalClose) productModalClose.addEventListener('click', closeProductModal);
        if (productModalCancel) productModalCancel.addEventListener('click', closeProductModal);

        // Event untuk menutup modal saat klik di luar
        productModal.addEventListener('click', (e) => {
            if (e.target === productModal) {
                closeProductModal();
            }
        });

        // Event untuk menyimpan produk
        if (productModalSave) {
            productModalSave.addEventListener('click', async () => {
                const productId = document.getElementById('product-id').value;
                const isEditing = productId !== '';

                const formData = {
                    product_id: productId,
                    product_name: document.getElementById('product-name').value,
                    kategori: document.getElementById('product-category').value,
                    price: parseFloat(document.getElementById('product-price').value),
                    cost_of_goods: parseFloat(document.getElementById('product-cost').value) || 0,
                    stock_quantity: parseInt(document.getElementById('product-stock').value) || 0
                };

                if (!formData.product_name || !formData.kategori || !formData.price) {
                    alert('Harap isi semua field yang diperlukan (*)');
                    return;
                }

                const endpoint = isEditing ? 'editProduct' : 'addProduct';
                const url = `${BASEURL}/index.php/kasir/${endpoint}`;
                
                console.log('Sending to:', url);
                console.log('FormData:', formData);
                
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    console.log('Response status:', response.status);
                    const responseText = await response.text();
                    console.log('Response text:', responseText);
                    
                    let result;
                    try {
                        result = JSON.parse(responseText);
                    } catch (parseError) {
                        console.error('JSON Parse Error:', parseError);
                        console.error('Response was:', responseText);
                        alert('Server Error: Response bukan JSON.\n\nResponse: ' + responseText.substring(0, 200));
                        return;
                    }
                    
                    console.log('API Response:', result);

                    if (result.status) {
                        alert(result.message || 'Produk berhasil disimpan');
                        closeProductModal();
                        // Reload halaman untuk menampilkan perubahan
                        location.reload();
                    } else {
                        alert('Error: ' + (result.message || 'Gagal menyimpan produk'));
                    }
                } catch (error) {
                    console.error('Fetch Error:', error);
                    alert('Terjadi kesalahan saat menyimpan produk: ' + error.message);
                }
            });
        }

        // Event untuk tombol edit produk
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-edit-product')) {
                const btn = e.target.closest('.btn-edit-product');
                const productId = btn.getAttribute('data-id');
                
                // Ambil data produk dari product-card-main
                const productCard = btn.closest('.product-card');
                const productCardMain = productCard.querySelector('.product-card-main');
                const productName = productCardMain.getAttribute('data-name');
                const productPrice = productCardMain.getAttribute('data-price');

                // Isi form dengan data produk
                document.getElementById('product-id').value = productId;
                document.getElementById('product-name').value = productName;
                document.getElementById('product-price').value = productPrice;

                // Tentukan kategori berdasarkan data-category di parent
                const category = productCard.getAttribute('data-category');
                let categoryValue = '';
                if (category === 'makanan') categoryValue = 'Jajanan & Makanan';
                else if (category === 'minuman') categoryValue = 'Minuman';
                else categoryValue = 'Lainnya';

                document.getElementById('product-category').value = categoryValue;
                document.getElementById('product-cost').value = '0';
                document.getElementById('product-stock').value = '0';

                document.getElementById('product-modal-title').textContent = 'Edit Produk';
                productModal.classList.remove('hidden');
            }
        });

        // Event untuk tombol delete produk
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-delete-product')) {
                const btn = e.target.closest('.btn-delete-product');
                const productId = btn.getAttribute('data-id');
                const productCard = btn.closest('.product-card');
                const productName = productCard.querySelector('.product-card-name').textContent;

                if (confirm(`Apakah Anda yakin ingin menghapus produk "${productName}"?`)) {
                    deleteProduct(productId);
                }
            }
        });
    }

    // Fungsi untuk menghapus produk
    async function deleteProduct(productId) {
        try {
            const response = await fetch(`${BASEURL}/index.php/kasir/deleteProduct`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            });

            const result = await response.json();

            if (result.status) {
                alert(result.message || 'Produk berhasil dihapus');
                location.reload();
            } else {
                alert('Error: ' + (result.message || 'Gagal menghapus produk'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus produk');
        }
    }

    // Event untuk manage categories
    if (btnManageCategories) {
        btnManageCategories.addEventListener('click', () => {
            loadCategories();
            categoryModal.classList.remove('hidden');
        });

        const closeCategoryModal = () => {
            categoryModal.classList.add('hidden');
        };

        if (categoryModalClose) categoryModalClose.addEventListener('click', closeCategoryModal);
        if (categoryModalDone) categoryModalDone.addEventListener('click', closeCategoryModal);

        categoryModal.addEventListener('click', (e) => {
            if (e.target === categoryModal) {
                closeCategoryModal();
            }
        });

        // Event untuk tombol add category
        const btnAddCategory = document.getElementById('btn-add-category');
        if (btnAddCategory) {
            btnAddCategory.addEventListener('click', async () => {
                const newCategoryInput = document.getElementById('new-category');
                const newCategory = newCategoryInput.value.trim();

                if (!newCategory) {
                    alert('Nama kategori tidak boleh kosong');
                    return;
                }

                // Validasi: kategori sudah ada atau tidak
                const categoryList = document.getElementById('category-list');
                const existingCategories = Array.from(categoryList.querySelectorAll('.category-item span'))
                    .map(el => el.textContent);

                if (existingCategories.includes(newCategory)) {
                    alert('Kategori ini sudah ada');
                    return;
                }

                // Tambahkan kategori ke UI
                const categoryItem = document.createElement('div');
                categoryItem.className = 'category-item';
                categoryItem.innerHTML = `
                    <span>${newCategory}</span>
                    <button class="btn-delete-category" type="button">Hapus</button>
                `;

                categoryList.appendChild(categoryItem);
                newCategoryInput.value = '';

                // Update select options di form produk
                const categorySelect = document.getElementById('product-category');
                const option = document.createElement('option');
                option.value = newCategory;
                option.textContent = newCategory;
                categorySelect.appendChild(option);

                // Event delete untuk kategori baru
                categoryItem.querySelector('.btn-delete-category').addEventListener('click', (e) => {
                    categoryItem.remove();
                    // Remove dari select juga
                    const opts = categorySelect.querySelectorAll('option');
                    opts.forEach(opt => {
                        if (opt.value === newCategory) opt.remove();
                    });
                });
            });
        }
    }

    // Fungsi untuk load categories
    function loadCategories() {
        const categoryList = document.getElementById('category-list');
        categoryList.innerHTML = '';

        // Default categories
        const defaultCategories = ['Jajanan & Makanan', 'Minuman', 'Lainnya'];
        
        defaultCategories.forEach(category => {
            const categoryItem = document.createElement('div');
            categoryItem.className = 'category-item';
            categoryItem.innerHTML = `
                <span>${category}</span>
                <button class="btn-delete-category" type="button" disabled title="Kategori default tidak bisa dihapus">Hapus</button>
            `;
            
            const deleteBtn = categoryItem.querySelector('.btn-delete-category');
            deleteBtn.style.opacity = '0.5';
            deleteBtn.style.cursor = 'not-allowed';

            categoryList.appendChild(categoryItem);
        });
    }

});
