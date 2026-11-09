<?php
// =====================================================
// config/db.php
// Kelas Database: satu-satunya tempat konfigurasi dan
// pembuatan koneksi ke MySQL (konsep: single responsibility).
// =====================================================
class Database {
    // Properti private: detail koneksi disembunyikan dari luar (enkapsulasi)
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "db_armory";

    // Properti public agar kelas model dapat memakai koneksi ini
    public $conn;

    // Constructor langsung membuka koneksi saat objek dibuat
    public function __construct() {
        try {
            // PDO = lapisan abstraksi database (DSN berisi driver, host, nama db)
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            // Mode exception: error SQL dilempar sebagai PDOException
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
?>
