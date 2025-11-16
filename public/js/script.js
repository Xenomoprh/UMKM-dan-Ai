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
        // (Kode kasir Anda yang sudah berfungsi tetap di sini, tidak ada perubahan)
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        const cartItemsContainer = document.getElementById('cart-items');
        const cartTotalElement = document.getElementById('cart-total');

        if (!cartItemsContainer || !cartTotalElement) {
            console.error('Elemen keranjang kasir tidak ditemukan!');
        } else {
            let cart = {}; 

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

            function updateCartView() {
                cartItemsContainer.innerHTML = '';
                let total = 0;
                let hasItems = false;

                for (const productId in cart) {
                    hasItems = true;
                    const item = cart[productId];
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;

                    const cartItemElement = document.createElement('div');
                    cartItemElement.className = 'cart-item';
                    cartItemElement.innerHTML = `
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-controls">
                            <button class="quantity-btn minus-btn" data-id="${productId}">-</button>
                            <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-id="${productId}">
                            <button class="quantity-btn plus-btn" data-id="${productId}">+</button>
                        </div>
                        <div class="cart-item-price">Rp ${number_format(itemTotal)}</div>
                    `;
                    cartItemsContainer.appendChild(cartItemElement);
                }

                if (!hasItems) {
                    cartItemsContainer.innerHTML = '<p>Keranjang masih kosong.</p>';
                }

                cartTotalElement.innerText = `Total: Rp ${number_format(total)}`;
                addCartEventListeners();
            }

            function addCartEventListeners() {
                document.querySelectorAll('.minus-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.id;
                        if (cart[productId] && cart[productId].quantity > 1) {
                            cart[productId].quantity--;
                        } else {
                            delete cart[productId];
                        }
                        updateCartView();
                    });
                });

                document.querySelectorAll('.plus-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const productId = this.dataset.id;
                        cart[productId].quantity++;
                        updateCartView();
                    });
                });

                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('change', function() {
                        const productId = this.dataset.id;
                        const newQuantity = parseInt(this.value);
                        if (newQuantity > 0) {
                            cart[productId].quantity = newQuantity;
                        } else {
                            delete cart[productId];
                        }
                        updateCartView();
                    });
                });
            }

            const bayarButton = document.querySelector('.btn-bayar');
            bayarButton.addEventListener('click', function() {
                if (Object.keys(cart).length === 0) {
                    alert('Keranjang masih kosong!');
                    return;
                }
                fetch(BASEURL + '/kasir/prosesTransaksi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'cart=' + JSON.stringify(cart)
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        cart = {};
                        updateCartView();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghubungi server.');
                });
            });
        }
    }


    // ====================================================================
    // LOGIKA UNTUK HALAMAN ANALISIS AI (DENGAN MEMORI)
    // ====================================================================
    const chatForm = document.getElementById('chat-input-form');
    if (chatForm) {
        const chatInput = document.getElementById('chat-input');
        const chatBox = document.getElementById('chat-box');
        
        // Kunci untuk menyimpan data di browser
        const CHAT_HISTORY_KEY = 'ai_chat_history';
        let chatHistory = []; // Array untuk menyimpan semua percakapan

        // Fungsi untuk menambahkan pesan ke tampilan DOM
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

        // Fungsi untuk menghapus indikator "sedang mengetik"
        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) typingIndicator.remove();
        }

        // Fungsi untuk menyimpan seluruh riwayat chat ke sessionStorage
        function saveHistory() {
            sessionStorage.setItem(CHAT_HISTORY_KEY, JSON.stringify(chatHistory));
        }

        // Fungsi untuk memuat riwayat chat saat halaman dibuka
        function loadHistory() {
            const savedHistory = sessionStorage.getItem(CHAT_HISTORY_KEY);
            
            chatBox.innerHTML = ''; // Kosongkan chat box dulu

            if (savedHistory) {
                chatHistory = JSON.parse(savedHistory);
                // Gambar ulang semua chat dari riwayat
                chatHistory.forEach(item => {
                    appendMessageToDOM(item.message, item.type);
                });
            } else {
                // Jika tidak ada riwayat, buat pesan selamat datang
                const welcomeMessage = 'Halo! Saya adalah asisten AI Anda. Silakan ajukan pertanyaan, contohnya: "Produk apa yang paling laku hari ini?"';
                chatHistory = [{ type: 'ai-message', message: welcomeMessage }];
                appendMessageToDOM(welcomeMessage, 'ai-message');
                saveHistory(); // Simpan pesan selamat datang sebagai awal riwayat
            }
        }

        // Fungsi utama saat mengirim pesan
        const sendMessage = (event) => {
            event.preventDefault();
            const userMessage = chatInput.value.trim();
            if (userMessage === '') return;

            // 1. Tampilkan & simpan pesan pengguna
            appendMessageToDOM(userMessage, 'user-message');
            chatHistory.push({ type: 'user-message', message: userMessage });
            saveHistory();
            
            chatInput.value = ''; // Kosongkan input
            
            // 2. Tampilkan indikator "sedang mengetik"
            appendMessageToDOM('AI sedang berpikir...', 'ai-message', true);

            // 3. Kirim ke server
            fetch(BASEURL + '/analisis/tanyaAi', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'question=' + encodeURIComponent(userMessage)
            })
            .then(response => response.json())
            .then(data => {
                removeTypingIndicator();
                
                // 4. Tampilkan & simpan jawaban AI
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

        // Event listener untuk form submit
        chatForm.addEventListener('submit', sendMessage);

        // --- INI LANGKAH KUNCINYA ---
        // Muat riwayat chat saat halaman pertama kali dibuka
        loadHistory();
    }
});