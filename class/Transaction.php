<?php
// =====================================================
// class/Transaction.php
// Model "Transaction" = transaksi keluar-masuk senjata (tabel transactions).
// Menghubungkan Weapon dan Buyer (relasi many-to-one dua arah).
//   dispatchWeapon -> senjata keluar (stok berkurang, transaksi dicatat)
//   returnWeapon   -> senjata kembali (stok bertambah, tanggal kembali dicatat)
// =====================================================
require_once 'config/db.php';

class Transaction {
    private $db;

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // Mengambil semua transaksi; JOIN agar nama senjata dan pembeli ikut tampil.
    // Alias dipakai karena tabel weapons dan buyers sama-sama punya kolom "name".
    public function getAllTransactions() {
        $stmt = $this->db->query("SELECT transactions.*, weapons.name AS weapon_name, buyers.name AS buyer_name 
                                  FROM transactions 
                                  JOIN weapons ON transactions.weapon_id = weapons.id 
                                  JOIN buyers ON transactions.buyer_id = buyers.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dispatch: hanya berhasil bila stok masih tersedia
    public function dispatchWeapon($weapon_id, $buyer_id) {
        // Query penyimpanan transaksi (tanggal diisi otomatis oleh MySQL)
        $stmt = $this->db->prepare("INSERT INTO transactions (weapon_id, buyer_id, dispatch_date) VALUES (?, ?, CURDATE())");
        $weapon = new Weapon(); // Transaction bergantung pada Weapon untuk mengubah stok
        $weaponData = $this->db->query("SELECT stock FROM weapons WHERE id = $weapon_id")->fetch();
        if ($weaponData['stock'] > 0) {
            $weapon->updateStock($weapon_id, $weaponData['stock'] - 1); // stok dikurangi 1
            return $stmt->execute([$weapon_id, $buyer_id]);
        }
        return false; // stok habis -> transaksi ditolak
    }

    // Return: mencatat tanggal kembali dan menambah stok
    public function returnWeapon($transaction_id) {
        $stmt = $this->db->prepare("UPDATE transactions SET return_date = CURDATE() WHERE id = ?");
        $transaction = $this->db->query("SELECT weapon_id FROM transactions WHERE id = $transaction_id")->fetch();
        $weapon = new Weapon();
        $weaponData = $this->db->query("SELECT stock FROM weapons WHERE id = {$transaction['weapon_id']}")->fetch();
        $weapon->updateStock($transaction['weapon_id'], $weaponData['stock'] + 1); // stok ditambah 1
        return $stmt->execute([$transaction_id]);
    }
}
?>
