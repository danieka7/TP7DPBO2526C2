<?php
// =====================================================
// class/Weapon.php
// Model "Weapon" = inventaris senjata (tabel weapons).
// Atribut: name, manufacturer, model_code (unik), stock.
// =====================================================
require_once 'config/db.php'; // memuat kelas Database

abstract class Weapon {
    private $db; // menyimpan objek koneksi PDO (private = enkapsulasi)

    // Constructor: membuat objek Database lalu mengambil koneksinya
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh data inventaris sebagai array asosiatif
    public function getAllWeapons() {
        $stmt = $this->db->query("SELECT * FROM weapons");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Mengubah stok berdasarkan id memakai prepared statement
    public function updateStock($id, $stock) {
        $stmt = $this->db->prepare("UPDATE weapons SET stock = ? WHERE id = ?");
        return $stmt->execute([$stock, $id]);
    }
}
?>
