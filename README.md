# MasISMA (Manajemen Siswa Magang)

**MasISMA** adalah aplikasi berbasis Laravel 7 yang dirancang untuk memudahkan pengelolaan mahasiswa magang di lingkungan Rumah Sakit SLG Kediri.  
Dengan MasISMA, admin dapat melakukan: laporan periode dan cetak PDF, serta manajemen ruangan/layanan.

---

## 📌 Fitur Utama

- Login/Logout untuk admin  
- Halaman dashboard admin: ringkasan magang, statistik, grafis  
- CRUD untuk **Mahasiswa** (nama, jurusan, kelas, status magang)  
- CRUD untuk **Ruangan/Layanan** tempat magang   
- Import data mahasiswa via Excel  
- Cetak laporan 30 hari terakhir berdasarkan jurusan & universitas (format .xlsx)   

---

## 📂 Instalasi

1. Clone repository  
   ```bash
   git clone https://github.com/AkyasMahira/masisma.git
   cd masisma
    ````

2. Install dependencies

   ```bash
   composer install
   npm install && npm run dev
   ```

3. Copy file lingkungan konfigurasi, atur database

   ```bash
   cp .env.example .env
   # lalu buka .env dan sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD
   php artisan key:generate
   ```

4. Jalankan migrasi & seeder 

   ```bash
   php artisan migrate --seed
   ```

5. Jalankan aplikasi lokal

   ```bash
   php artisan serve
   ```

6. Akses aplikasi di browser: `http://localhost:8000`

---

## 🔧 Kontribusi

Semua kontribusi sangat diterima! Jika kamu menemukan bug atau punya ide fitur baru, silakan buka *issue* atau lakukan *pull request*.
Mohon sertakan deskripsi singkat dan screenshot (jika perlu) agar review lebih mudah.

---

## 📝 Lisensi

Aplikasi ini dilisensikan di bawah lisensi [MIT](LICENSE) — silakan digunakan & dikembangkan sesuai kebutuhan.

---

## 🎨 Icon Aplikasi

Berikut icon aplikasi MasISMA:
![MasISMA Icon](path/to/icon.png)

---

## 🤝 Terima kasih

Terima kasih sudah menggunakan & mengembangkan MasISMA — semoga bisa membantu meningkatkan pengalaman magang mahasiswa! 🚀
