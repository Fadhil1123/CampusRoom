#CampusRoom

CampusRoom adalah website yang digunakan untuk melihat ketersediaan ruangan dan melakukan peminjaman ruangan kampus secara online.  
Sistem ini mendukung peminjaman ruangan untuk kegiatan perkuliahan maupun kegiatan non-akademik secara lebih mudah, cepat, dan efisien.

Project ini dikembangkan sebagai tugas **Ujian Akhir Semester (UAS)** mata kuliah **Pemrograman Web II**  
Program Studi Teknologi Informasi — Universitas Lambung Mangkurat.

---

#Fitur Utama

- Authentication (Login & Logout)
- Dashboard User dan Admin
- Melihat daftar serta detail ruangan
- Melihat status dan ketersediaan ruangan
- Booking ruangan perkuliahan
- Booking ruangan kegiatan
- Upload & download surat peminjaman
- Approval booking oleh admin
- Riwayat peminjaman ruangan
- Validasi bentrok jadwal
- Multi-room booking

---

# Tim Pengembang

| Nama | NIM | Role | GitHub |
|---|---|---|---|
| Fadhil Syahdama Mahatma Putra | 2410817210026 | Backend Developer & Database Engineer | @Fadhil1123 |
| Nabilla Putri Nugraha | 2410817220009 | Frontend Developer & UI/UX Designer | @NabillaPutriNugraha |

---

# Tech Stack

| Komponen | Teknologi |
|---|---|
| Frontend | HTML, CSS, Bootstrap |
| Backend | PHP, Laravel |
| Database | MySQL |
| Database Management | phpMyAdmin |
| Code Editor | Visual Studio Code |
| Version Control | Git & GitHub |
| Local Server | XAMPP |

---

# Alur Status Peminjaman

## Booking Perkuliahan

```text
Diproses
   ↓
Cek Ketersediaan Ruangan
   ├── Tersedia
   │      ↓
   │   Dipinjam
   │
   └── Tidak Tersedia
          ↓
        Ditolak
```

---

## Booking Kegiatan

```text
Diproses
   ↓
Mengirim Surat Peminjaman
   ↓
Verifikasi Admin
   ├── Disetujui
   │      ↓
   │   Dipinjam
   │
   └── Ditolak
```

---

# Role Pengguna

## User / Mahasiswa

User dapat:

- Login dan logout sistem
- Melihat daftar dan detail ruangan
- Melihat status serta ketersediaan ruangan
- Melakukan booking ruangan untuk perkuliahan
- Melakukan booking ruangan untuk kegiatan
- Download template surat peminjaman
- Upload surat peminjaman kegiatan
- Melihat status booking
- Melihat riwayat booking pribadi

---

## Admin

Admin dapat:

- Mengakses dashboard admin
- Approval atau penolakan booking kegiatan
- Mengelola data ruangan
- Mengelola jadwal perkuliahan
- Mengelola data kegiatan
- Melihat seluruh data booking
- Melihat detail booking
- Mengubah status booking
- Menghapus booking
- Monitoring seluruh aktivitas peminjaman ruangan dalam sistem

---

# Jenis Peminjaman

## Perkuliahan

- Booking otomatis disetujui apabila ruangan tersedia
- Tidak memerlukan surat peminjaman
- Sistem otomatis memvalidasi bentrok jadwal

## Kegiatan Non-Akademik

- Wajib upload surat peminjaman
- Pengajuan minimal H-2
- Membutuhkan approval admin
- Mendukung peminjaman lebih dari satu ruangan

---

# Business Rules

- Tidak boleh terjadi bentrok jadwal ruangan
- Satu booking kegiatan dapat menggunakan beberapa ruangan
- Ruangan yang sedang dipakai akan otomatis tidak tersedia
- Booking kegiatan wajib melalui proses approval admin
- Booking kegiatan wajib mengunggah surat peminjaman
- Sistem akan menolak booking apabila jadwal bertabrakan

---

# Tujuan Sistem

CampusRoom dibuat untuk membantu proses peminjaman ruangan kampus agar lebih:

- Efisien
- Terorganisir
- Transparan
- Mudah diakses
- Mengurangi bentrok penggunaan ruangan

---

# Status Project

Project sedang dalam tahap pengembangan.