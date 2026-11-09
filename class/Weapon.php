<?php
// =====================================================
// class/Weapon.php
// Model "Weapon" = inventaris senjata (tabel weapons).
// Atribut: name, manufacturer, model_code (unik), stock.
// =====================================================
require_once 'config/db.php'; // memuat kelas Database

class Weapon {
    private $db; // menyimpan objek koneksi PDO (private = enkapsulasi)

    // Constructor: membuat objek Database lalu mengambil koneksinya
    public function __construct() {
        $this->db = (new Database())->conn;
    }

    // READ: mengambil seluruh data inventaris sebagai array asosiatif
    public function getAllWeapons() {
        $stmt = $this->db->prepare("SELECT * FROM weapons");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ: mengambil satu senjata berdasarkan id (untuk mengisi form edit)
    public function getWeaponById($id) {
        $stmt = $this->db->prepare("SELECT * FROM weapons WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CREATE: menambah senjata baru
    // model_code kosong disimpan sebagai NULL agar tidak bentrok dengan constraint UNIQUE.
    // Mengembalikan false bila gagal (mis. model_code duplikat).
    public function createWeapon($name, $manufacturer, $model_code, $stock) {
        try {
            $stmt = $this->db->prepare("INSERT INTO weapons (name, manufacturer, model_code, stock) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$name, $manufacturer, $model_code !== '' ? $model_code : null, $stock]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // UPDATE: mengubah seluruh data senjata berdasarkan id
    public function updateWeapon($id, $name, $manufacturer, $model_code, $stock) {
        try {
            $stmt = $this->db->prepare("UPDATE weapons SET name = ?, manufacturer = ?, model_code = ?, stock = ? WHERE id = ?");
            return $stmt->execute([$name, $manufacturer, $model_code !== '' ? $model_code : null, $stock, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Mengubah stok saja
    public function updateStock($id, $stock) {
        $stmt = $this->db->prepare("UPDATE weapons SET stock = ? WHERE id = ?");
        return $stmt->execute([$stock, $id]);
    }

    // Menambah stok sebanyak 1 (dipakai kelas Transaction saat senjata kembali)
    public function increaseStock($id) {
        $stmt = $this->db->prepare("UPDATE weapons SET stock = stock + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Mengurangi stok sebanyak 1 hanya bila stok masih > 0 (dipakai kelas Transaction).
    // Mengembalikan true bila stok berhasil dikurangi, false bila stok habis.
    public function decreaseStock($id) {
        $stmt = $this->db->prepare("UPDATE weapons SET stock = stock - 1 WHERE id = ? AND stock > 0");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    // DELETE: menghapus senjata berdasarkan id
    // Gagal (false) bila senjata masih dipakai di tabel transactions (foreign key).
    public function deleteWeapon($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM weapons WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
}
?>