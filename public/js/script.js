// Fungsi helper untuk format angka, letakkan di luar agar bisa diakses dari mana saja
function number_format(number) {
    return new Intl.NumberFormat('id-ID').format(number);
}

// Jalankan kode hanya setelah seluruh halaman HTML dimuat
document.addEventListener('DOMContentLoaded', function () {

    // Ambil elemen-elemen penting dari halaman
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const cartItemsContainer = document.getElementById('cart-items');
    const cartTotalElement = document.getElementById('cart-total');

    // Jika elemen penting tidak ditemukan, hentikan eksekusi untuk mencegah error
    if (!cartItemsContainer || !cartTotalElement) {
        console.error('Elemen keranjang tidak ditemukan!');
        return;
    }

    let cart = {}; // Objek untuk menyimpan item di keranjang

    // Tambahkan event listener ke setiap tombol 'Tambah'
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.dataset.id;
            const productName = this.dataset.name;
            const productPrice = parseFloat(this.dataset.price);

            if (cart[productId]) {
                cart[productId].quantity++;
            } else {
                cart[productId] = {
                    name: productName,
                    price: productPrice,
                    quantity: 1
                };
            }
            
            updateCartView();
        });
    });

    function updateCartView() {
        cartItemsContainer.innerHTML = '';
        let total = 0;
        let hasItems = false;

        for (const productId in cart) {
            hasItems = true; // Tandai bahwa keranjang sudah ada isinya
            const item = cart[productId];
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const cartItemElement = document.createElement('div');
            cartItemElement.className = 'cart-item';
            cartItemElement.innerHTML = `
                <span>${item.name} (x${item.quantity})</span>
                <span>Rp ${number_format(itemTotal)}</span>
            `;
            cartItemsContainer.appendChild(cartItemElement);
        }

        if (!hasItems) {
            cartItemsContainer.innerHTML = '<p>Keranjang masih kosong.</p>';
        }

        cartTotalElement.innerText = `Total: Rp ${number_format(total)}`;
    }
});