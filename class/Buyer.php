<?php
// =====================================================
// class/Buyer.php
// Model "Buyer" = pembeli/mitra terdaftar (tabel buyers).
// Atribut: name, email (unik), phone.
// =====================================================
require_once 'config/db.php'; // memuat kelas Database

class Buyer {
    private $db; // koneksi PDO

    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // Mengambil seluruh pembeli terdaftar
    public function getAllBuyers() {
        $stmt = $this->db->query("SELECT * FROM buyers");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
