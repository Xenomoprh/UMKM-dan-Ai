<?php require_once '../app/views/templates/header.php'; ?>

<div class="container content">
    <h2>Asisten Analis AI Anda</h2>
    <p>Ajukan pertanyaan tentang data penjualan Anda dalam bahasa biasa.</p>

    <div class="chat-container">
        <div id="chat-box" class="chat-box">
            <div class="chat-message ai-message">
                <div class="message-bubble">
                    <p>Halo! Saya adalah asisten AI Anda. Silakan ajukan pertanyaan, contohnya: "Produk apa yang paling laku hari ini?"</p>
                </div>
            </div>
        </div>
        <div class="chat-input-container">
            <form id="chat-input-form">
                <input type="text" id="chat-input" placeholder="Ketik pertanyaan Anda di sini..." autocomplete="off">
                <button id="send-btn" type="submit">Kirim</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/templates/footer.php'; ?>