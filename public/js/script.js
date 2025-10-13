// Fungsi helper untuk format angka
function number_format(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

document.addEventListener('DOMContentLoaded', function () {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');

    if (!cartItemsContainer || !cartTotalElement) {
        console.error('Elemen keranjang tidak ditemukan!');
        return;
    }

    let cart = {}; 

    // Event listener untuk tombol "Tambah" di daftar produk
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

    // Fungsi utama untuk menggambar ulang seluruh keranjang
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

    // Fungsi untuk menambahkan event listener ke tombol-tombol di dalam keranjang
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

    // Bagian tombol Bayar
    const bayarButton = document.querySelector('.btn-bayar');
    bayarButton.addEventListener('click', function() {
        if (Object.keys(cart).length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }
        // Gunakan variabel BASEURL dari JavaScript, bukan PHP
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

    // Cek apakah kita berada di halaman analisis
const chatForm = document.getElementById('chat-input-form');
if (chatForm) {
    const chatInput = document.getElementById('chat-input');
    const chatBox = document.getElementById('chat-box');
    const sendBtn = document.getElementById('send-btn');

    // Fungsi untuk mengirim pesan
    const sendMessage = (event) => {
        event.preventDefault(); // Mencegah form refresh halaman
        const userMessage = chatInput.value.trim();

        if (userMessage === '') {
            return; // Jangan kirim pesan kosong
        }

        // 1. Tampilkan pesan pengguna di layar
        appendMessage(userMessage, 'user-message');
        chatInput.value = ''; // Kosongkan input

        // 2. (Untuk sekarang) Tampilkan pesan "sedang mengetik" dari AI
        appendMessage('AI sedang berpikir...', 'ai-message', true);

        // 3. (Simulasi) AI menjawab setelah beberapa saat
        setTimeout(() => {
            // Hapus pesan "sedang mengetik"
            removeTypingIndicator();
            const aiResponse = "Ini adalah jawaban simulasi. Backend AI belum terhubung.";
            appendMessage(aiResponse, 'ai-message');
        }, 2000); // Jeda 2 detik
    };

        chatForm.addEventListener('submit', sendMessage);

        // Fungsi untuk menambahkan pesan ke chat box
        function appendMessage(message, type, isTyping = false) {
            const messageWrapper = document.createElement('div');
            messageWrapper.className = `chat-message ${type}`;
            
            if (isTyping) {
                messageWrapper.id = 'typing-indicator';
            }

            const messageBubble = document.createElement('div');
            messageBubble.className = 'message-bubble';
            messageBubble.innerHTML = `<p>${message}</p>`;

            messageWrapper.appendChild(messageBubble);
            chatBox.appendChild(messageWrapper);

            // Scroll otomatis ke pesan terbaru
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function removeTypingIndicator() {
            const typingIndicator = document.getElementById('typing-indicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }
    }
});