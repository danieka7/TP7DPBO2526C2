<?php
// =====================================================
// class/Transaction.php
// Model "Transaction" = transaksi keluar-masuk senjata (tabel transactions).
// Menghubungkan Weapon dan Buyer (relasi many-to-one dua arah).
//   dispatchWeapon    -> CREATE : senjata keluar (stok berkurang, transaksi dicatat)
//   getAll/ById       -> READ
//   updateTransaction -> UPDATE : ubah data transaksi (stok disesuaikan)
//   returnWeapon      -> UPDATE : senjata kembali (stok bertambah, tanggal kembali dicatat)
//   deleteTransaction -> DELETE : hapus transaksi (stok dikembalikan bila belum return)
// Aturan stok: transaksi yang BELUM dikembalikan (return_date NULL)
// "memegang" 1 unit stok dari senjata terkait.
// =====================================================
require_once 'config/db.php';
require_once 'class/Weapon.php'; // Transaction bergantung pada Weapon untuk mengubah stok

class Transaction {
    private $db;
    private $weapon;

    public function __construct() {
        $this->db = (new Database())->conn;
        $this->weapon = new Weapon();
    }

    // READ: mengambil semua transaksi; JOIN agar nama senjata dan pembeli ikut tampil.
    // Alias dipakai karena tabel weapons dan buyers sama-sama punya kolom "name".
    // PERBAIKAN: query() diganti prepare() + execute().
    public function getAllTransactions() {
        $stmt = $this->db->prepare("SELECT transactions.*, weapons.name AS weapon_name, buyers.name AS buyer_name
                                    FROM transactions
                                    JOIN weapons ON transactions.weapon_id = weapons.id
                                    JOIN buyers ON transactions.buyer_id = buyers.id
                                    ORDER BY transactions.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ: mengambil satu transaksi berdasarkan id (untuk mengisi form edit)
    public function getTransactionById($id) {
        $stmt = $this->db->prepare("SELECT * FROM transactions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE (Dispatch): hanya berhasil bila stok masih tersedia
    public function dispatchWeapon($weapon_id, $buyer_id) {
        // PERBAIKAN (celah SQL Injection): seluruh nilai dikirim lewat placeholder "?".
        // decreaseStock() hanya mengurangi stok bila stok > 0 (dicek di dalam query).
        if (!$this->weapon->decreaseStock($weapon_id)) {
            return false; // stok habis -> transaksi ditolak
        }
        // Query penyimpanan transaksi (tanggal diisi otomatis oleh MySQL)
        $stmt = $this->db->prepare("INSERT INTO transactions (weapon_id, buyer_id, dispatch_date) VALUES (?, ?, CURDATE())");
        return $stmt->execute([$weapon_id, $buyer_id]);
    }

    // UPDATE: mengubah weapon, buyer, tanggal dispatch, dan tanggal return.
    // Stok disesuaikan: unit yang dipegang transaksi lama dilepas,
    // lalu transaksi baru mengambil unit bila statusnya belum dikembalikan.
    public function updateTransaction($id, $weapon_id, $buyer_id, $dispatch_date, $return_date) {
        $old = $this->getTransactionById($id);
        if (!$old) return false;

        $return_date = ($return_date === '' || $return_date === null) ? null : $return_date;

        // 1. Lepaskan unit stok yang dipegang transaksi lama
        if ($old['return_date'] === null) {
            $this->weapon->increaseStock($old['weapon_id']);
        }

        // 2. Transaksi baru yang belum dikembalikan mengambil 1 unit stok
        if ($return_date === null && !$this->weapon->decreaseStock($weapon_id)) {
            // Stok senjata baru habis -> batalkan dan pulihkan stok lama
            if ($old['return_date'] === null) {
                $this->weapon->decreaseStock($old['weapon_id']);
            }
            return false;
        }

        $stmt = $this->db->prepare("UPDATE transactions SET weapon_id = ?, buyer_id = ?, dispatch_date = ?, return_date = ? WHERE id = ?");
        return $stmt->execute([$weapon_id, $buyer_id, $dispatch_date, $return_date, $id]);
    }

    // UPDATE (Return): mencatat tanggal kembali dan menambah stok
    public function returnWeapon($transaction_id) {
        // PERBAIKAN (celah SQL Injection): sebelumnya "WHERE id = $transaction_id"
        // diambil langsung dari $_GET['return']. Sekarang memakai placeholder.
        $transaction = $this->getTransactionById($transaction_id);

        // PERBAIKAN: transaksi yang tidak ada / sudah dikembalikan tidak diproses lagi,
        // agar stok tidak bertambah berkali-kali.
        if (!$transaction || $transaction['return_date'] !== null) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE transactions SET return_date = CURDATE() WHERE id = ? AND return_date IS NULL");
        $stmt->execute([$transaction_id]);
        if ($stmt->rowCount() > 0) {
            $this->weapon->increaseStock($transaction['weapon_id']); // stok ditambah 1
            return true;
        }
        return false;
    }

    // DELETE: menghapus transaksi; bila senjata belum dikembalikan, stok dikembalikan dulu
    public function deleteTransaction($id) {
        $transaction = $this->getTransactionById($id);
        if (!$transaction) return false;

        if ($transaction['return_date'] === null) {
            $this->weapon->increaseStock($transaction['weapon_id']);
        }
        $stmt = $this->db->prepare("DELETE FROM transactions WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
