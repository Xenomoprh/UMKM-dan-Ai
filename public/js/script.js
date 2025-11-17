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

                    fetch(BASEURL + '/kasir/prosesTransaksi', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: postData.toString()
                    })
                    .then(response => response.json())
                    .then(data => {
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
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menghubungi server.');
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
            const formattedMessage = message.replace(/\n/g, '<br>');
            messageBubble.innerHTML = `<p>${formattedMessage}</p>`;
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
});