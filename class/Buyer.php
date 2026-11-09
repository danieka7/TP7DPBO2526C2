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

    // READ: mengambil seluruh pembeli terdaftar
    public function getAllBuyers() {
        $stmt = $this->db->prepare("SELECT * FROM buyers");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ: mengambil satu pembeli berdasarkan id (untuk mengisi form edit)
    public function getBuyerById($id) {
        $stmt = $this->db->prepare("SELECT * FROM buyers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE: menambah pembeli baru
    // email kosong disimpan sebagai NULL agar tidak bentrok dengan constraint UNIQUE.
    // Mengembalikan false bila gagal (mis. email duplikat).
    public function createBuyer($name, $email, $phone) {
        try {
            $stmt = $this->db->prepare("INSERT INTO buyers (name, email, phone) VALUES (?, ?, ?)");
            return $stmt->execute([$name, $email !== '' ? $email : null, $phone]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE: mengubah data pembeli berdasarkan id
    public function updateBuyer($id, $name, $email, $phone) {
        try {
            $stmt = $this->db->prepare("UPDATE buyers SET name = ?, email = ?, phone = ? WHERE id = ?");
            return $stmt->execute([$name, $email !== '' ? $email : null, $phone, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // DELETE: menghapus pembeli berdasarkan id
    // Gagal (false) bila pembeli masih dipakai di tabel transactions (foreign key).
    public function deleteBuyer($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM buyers WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>