# Tugas Praktikum 7 - Desain Pemrograman Berorientasi Objek

## Janji
Saya **Dani Eka Saputra** dengan NIM **2501158** mengerjakan Tugas Praktikum 7 pada Mata Kuliah Desain Pemrograman Berorientasi Objek untuk keberkahan-Nya maka saya tidak melakukan kecurangan seperti yang telah dispesifikasikan. Aamiin


## Penjelasan Tema
Program ini bertema sistem manajemen gudang & distribusi persenjataan administratif. Program ini mencatat stok senjata yang tersedia di gudang, daftar unit/mitra pembeli resmi (seperti garnisun atau divisi), serta log keluar-masuk senjata. Setiap kali senjata dikirim (*dispatch*) ke mitra, stok gudang otomatis berkurang; ketika senjata dikembalikan (*return*) atau transaksi dibatalkan/dihapus, stok otomatis dikembalikan.


## Fitur Utama

1. **Manajemen Inventaris Senjata (Weapon - CRUD)**
   - **Create**: Menambahkan senjata baru beserta nama, pabrikan (*manufacturer*), kode model unik (*model code*), dan jumlah stok awal.
   - **Read**: Menampilkan daftar seluruh senjata beserta indikator peringatan stok menipis (`<= 2` ditandai *low*).
   - **Update**: Mengubah detail senjata dan jumlah stok.
   - **Delete**: Menghapus data senjata (dilengkapi proteksi relasi agar senjata yang tercatat di transaksi tidak menimbulkan *fatal error*).

2. **Manajemen Mitra / Pembeli (Buyer - CRUD)**
   - **Create**: Mendaftarkan pembeli/mitra baru (nama, email unik, nomor telepon).
   - **Read**: Menampilkan daftar pembeli terdaftar.
   - **Update**: Memperbarui informasi kontak pembeli.
   - **Delete**: Menghapus pembeli terdaftar (dilengkapi proteksi *foreign key*).

3. **Manajemen Transaksi Distribusi (Transaction - CRUD Lengkap & Sinkronisasi Stok Otomatis)**
   - **Create (*Dispatch*)**: Mencatat pengiriman senjata ke pembeli dengan tanggal otomatis (CURDATE()) dan mengurangi stok senjata sebanyak 1 unit (ditolak bila stok habis).
   - **Read**: Menampilkan log transaksi lengkap dengan nama senjata dan nama pembeli menggunakan `JOIN`.
   - **Update (*Return* & *Edit*)**:
     - Tombol **Return** untuk mencatat tanggal pengembalian senjata dan menambah kembali stok senjata sebanyak 1 unit.
     - Form **Edit Transaction** untuk mengubah senjata, pembeli, tanggal kirim, maupun tanggal kembali dengan penyesuaian stok otomatis.
   - **Delete**: Menghapus riwayat transaksi dan otomatis mengembalikan stok bila senjata pada transaksi tersebut belum dikembalikan.

4. **Keamanan & Alur OOP**
   - Seluruh interaksi database menggunakan **PDO Prepared Statements** (`prepare()` + `execute()`) untuk mencegah *SQL Injection*.
   - Menggunakan pola *Post/Redirect/Get (PRG)* pada `index.php` agar *refresh* halaman tidak menduplikasi aksi.


## Struktur Projek

```
TP7DPBO2526C2
├── dokumentasi
│   ├── crud.mp4
│   └── diagram.png
├── program
│   ├── class
│   │   ├── Buyer.php
│   │   ├── Transaction.php
│   │   └── Weapon.php
│   ├── config
│   │   └── db.php
│   ├── database
│   │   └── db_armory.sql
│   ├── view
│   │   ├── buyer_form.php
│   │   ├── buyers.php
│   │   ├── footer.php
│   │   ├── header.php
│   │   ├── transaction_form.php
│   │   ├── transactions.php
│   │   ├── weapon_form.php
│   │   └── weapons.php
│   ├── index.php
│   └── style.css
└── README.md
```

## Skema Tabel & Relasi


#### 1. Tabel `weapons`
Menyimpan data inventaris senjata.
| Kolom          | Tipe Data                | Keterangan                          |
| -------------- | ------------------------ | ----------------------------------- |
| `id`           | `INT AUTO_INCREMENT`     | **Primary Key**                     |
| `name`         | `VARCHAR(255) NOT NULL`  | Nama senjata                        |
| `manufacturer` | `VARCHAR(100) NOT NULL`  | Nama pabrikan pembuat               |
| `model_code`   | `VARCHAR(13) UNIQUE`     | Kode model unik (opsional / `NULL`) |
| `stock`        | `INT NOT NULL DEFAULT 0` | Jumlah stok unit tersedia           |

#### 2. Tabel `buyers`
Menyimpan data pembeli/mitra terdaftar.
| Kolom   | Tipe Data               | Keterangan                            |
| ------- | ----------------------- | ------------------------------------- |
| `id`    | `INT AUTO_INCREMENT`    | **Primary Key**                       |
| `name`  | `VARCHAR(100) NOT NULL` | Nama pembeli / divisi                 |
| `email` | `VARCHAR(100) UNIQUE`   | Alamat email unik (opsional / `NULL`) |
| `phone` | `VARCHAR(15)`           | Nomor telepon kontak                  |

#### 3. Tabel `transactions`
Menyimpan data transaksi keluar-masuk senjata dan memiliki relasi *Many-to-One* ke tabel `weapons` dan `buyers`.
| Kolom           | Tipe Data            | Keterangan                                               |
| --------------- | -------------------- | -------------------------------------------------------- |
| `id`            | `INT AUTO_INCREMENT` | **Primary Key**                                          |
| `weapon_id`     | `INT NOT NULL`       | **Foreign Key** mengacu ke `weapons(id)`                 |
| `buyer_id`      | `INT NOT NULL`       | **Foreign Key** mengacu ke `buyers(id)`                  |
| `dispatch_date` | `DATE NOT NULL`      | Tanggal senjata dikirim                                  |
| `return_date`   | `DATE`               | Tanggal senjata dikembalikan (`NULL` jika belum kembali) |

## Diagram 
![diagram](<dokumentasi/diagram.png>)

## Database 

```
CREATE DATABASE db_armory;
USE db_armory;

-- weapons: inventaris senjata
CREATE TABLE weapons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    manufacturer VARCHAR(100) NOT NULL,
    model_code VARCHAR(13) UNIQUE,
    stock INT NOT NULL DEFAULT 0
);

-- buyers: pembeli/mitra terdaftar
CREATE TABLE buyers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15)
);

-- transactions: transaksi keluar-masuk (relasi ke weapons dan buyers lewat foreign key)
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    weapon_id INT NOT NULL,
    buyer_id INT NOT NULL,
    dispatch_date DATE NOT NULL,
    return_date DATE,
    FOREIGN KEY (weapon_id) REFERENCES weapons(id),
    FOREIGN KEY (buyer_id) REFERENCES buyers(id)
);

INSERT INTO weapons (name, manufacturer, model_code, stock) VALUES
('Viper MK-II', 'Ironclad Works', 'W001', 5),
('Falcon S9', 'Northgate Arms', 'W002', 3),
('Bulwark R4', 'Ironclad Works', 'W003', 4),
('Sentinel X1', 'Redfield Armory', 'W004', 2);

INSERT INTO buyers (name, email, phone) VALUES
('Garnisun Alpha', 'alpha@garnisun.test', '08123456789'),
('Divisi Bravo', 'bravo@divisi.test', '08234567890');
```


## Dokumentasi
https://github.com/user-attachments/assets/f0ef8ac1-2e9a-4157-a550-223867c07ddf
