# File: ai_server/ai_server.py
# Ini adalah versi yang benar untuk library google-generativeai versi baru

import os
import google.generativeai as genai
import mysql.connector
from flask import Flask, request, jsonify
from dotenv import load_dotenv
import datetime

# Muat variabel lingkungan (API Key) dari file .env
load_dotenv()
print("--- 1. File .env dimuat ---")

# Konfigurasi Database
DB_CONFIG = {
    'user': 'root',
    'password': '',
    'host': '127.0.0.1',
    'database': 'db_umkm_ai'
}
print("--- 2. Konfigurasi DB Siap ---")

# Skema Database (untuk diajarkan ke AI)
DATABASE_SCHEMA = """
Tabel `products`:
- `product_id` (INT, Primary Key)
- `product_name` (VARCHAR)
- `price` (DECIMAL) - Harga Jual
- `cost_of_goods` (DECIMAL) - Harga Modal
- `stock_quantity` (INT)
Tabel `transactions`:
- `transaction_id` (INT, Primary Key)
- `transaction_time` (TIMESTAMP)
- `total_amount` (DECIMAL)
Tabel `transaction_details`:
- `detail_id` (INT, Primary Key)
- `transaction_id` (INT, Foreign Key ke `transactions`)
- `product_id` (INT, Foreign Key ke `products`)
- `quantity` (INT)
- `subtotal` (DECIMAL)
"""

try:
    # Konfigurasi API Key (Cara yang benar untuk library baru)
    genai.configure(api_key=os.environ["GOOGLE_API_KEY"])
    
    # Membuat model (Cara yang benar untuk library baru)
    model = genai.GenerativeModel(model_name='gemini-2.0-flash')
    
    print("--- 3. Konfigurasi Gemini (GenerativeModel) Berhasil ---")
except Exception as e:
    print(f"!!! GAGAL KONFIGURASI GEMINI: {e}")
    exit()

def run_sql_query(sql_query):
    """Menjalankan kueri SQL dan mengembalikan hasilnya."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)
        cursor.execute(sql_query)
        result = cursor.fetchall()
        conn.close()
        return result
    except Exception as e:
        return f"Error saat menjalankan SQL: {e}"

def process_question(user_question):
    """Memproses pertanyaan pengguna, mengubahnya jadi SQL, dan jadi jawaban."""
    try:
        # === TAHAP 1: Ubah Pertanyaan menjadi Kueri SQL ===
        prompt_tahap_1 = f"""
        Anda adalah analis data AI ahli untuk database MySQL.
        Tugas Anda adalah mengubah pertanyaan pengguna menjadi satu kueri SQL yang valid.
        Gunakan skema database ini: {DATABASE_SCHEMA}
        Aturan:
        1. Jawab HANYA dengan kueri SQL yang bisa dieksekusi.
        2. Jangan tambahkan "```sql" atau penjelasan apa pun.
        3. Jika pertanyaan tentang "keuntungan", hitung dari (price - cost_of_goods).
        4. Waktu saat ini adalah {datetime.datetime.now()} untuk referensi.
        Pertanyaan Pengguna: "{user_question}"
        Kueri SQL:
        """
        
        response_tahap_1 = model.generate_content(prompt_tahap_1)
        sql_query = response_tahap_1.text.strip().replace('```sql', '').replace('```', '')

        # === TAHAP 2: Jalankan Kueri SQL ===
        sql_result = run_sql_query(sql_query)

        # === TAHAP 3: Ubah Hasil SQL menjadi Bahasa Manusia ===
        prompt_tahap_2 = f"""
        Anda adalah asisten AI yang ramah.
        Pertanyaan awal pengguna adalah: "{user_question}"
        Kueri SQL yang dijalankan: "{sql_query}"
        Hasil dari database (dalam format JSON): "{sql_result}"
        Tugas Anda:
        Jawab pertanyaan awal pengguna dalam bahasa Indonesia yang ramah dan mudah dimengerti berdasarkan hasil data tersebut.
        Jangan sebutkan kueri SQL-nya.
        Jika datanya kosong atau [] (list kosong), katakan bahwa tidak ada data yang ditemukan.
        Jawaban Anda:
        """
        
        response_tahap_2 = model.generate_content(prompt_tahap_2)
        final_answer = response_tahap_2.text
        
        return final_answer
    except Exception as e:
        return f"Maaf, terjadi kesalahan internal pada AI: {e}"

# ===============================================
# BAGIAN SERVER FLASK
# ===============================================
app = Flask(__name__)
print("--- 4. Aplikasi Flask Dibuat ---")

@app.route('/')
def home():
    return "Server AI Python (versi GenerativeModel) aktif dan berjalan."

@app.route('/tanya_ai', methods=['POST'])
def handle_question():
    data = request.json
    if not data or 'question' not in data:
        return jsonify({'error': 'Pertanyaan tidak ditemukan'}), 400
    
    question = data['question']
    answer = process_question(question)
    
    return jsonify({'answer': answer})

if __name__ == '__main__':
    print("--- 5. Menjalankan Server Flask... ---")
    app.run(debug=True, port=5000)