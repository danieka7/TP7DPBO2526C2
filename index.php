<?php
// =====================================================
// index.php : entry point (front controller sederhana)
// Alur: muat kelas -> buat objek -> proses aksi -> render view
// =====================================================

// 1. Memuat kelas model (satu file per kelas)
require_once 'class/Weapon.php';
require_once 'class/Buyer.php';
require_once 'class/Transaction.php';

// 2. Membuat objek model; dipakai bersama oleh seluruh view
$weapon = new Weapon();
$buyer = new Buyer();
$transaction = new Transaction();

// 3. Memproses aksi SEBELUM HTML dikeluarkan
if (isset($_POST['dispatch'])) {
    $transaction->dispatchWeapon($_POST['weapon_id'], $_POST['buyer_id']); // dispatch
}
if (isset($_GET['return'])) {
    $transaction->returnWeapon($_GET['return']); // return
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- viewport: agar media query responsif pada CSS bekerja di layar kecil -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Armory Trade System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'view/header.php'; ?>
    <main>
        <h2>Welcome to Armory Trade System</h2>
        <nav>
            <a href="?page=weapons">Weapons</a> |
            <a href="?page=buyers">Buyers</a> |
            <a href="?page=transactions">Transactions</a>
        </nav>

        <?php
        // 4. Routing sederhana: parameter ?page= menentukan view yang dimuat
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page == 'weapons') include 'view/weapons.php';
            elseif ($page == 'buyers') include 'view/buyers.php';
            elseif ($page == 'transactions') include 'view/transactions.php';
        }
        ?>
    </main>
    <?php include 'view/footer.php'; ?>
</body>
</html>
