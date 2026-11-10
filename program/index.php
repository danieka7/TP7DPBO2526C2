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

// Pesan error yang ditampilkan di halaman bila sebuah aksi gagal
$error = '';

// 3. Memproses aksi SEBELUM HTML dikeluarkan.
// Aksi yang berhasil di-redirect (pola Post/Redirect/Get) agar refresh
// halaman tidak mengulang aksi yang sama.

// --- CRUD Transaction ---
if (isset($_POST['dispatch'])) {
    // CREATE (dispatch)
    if ($transaction->dispatchWeapon($_POST['weapon_id'], $_POST['buyer_id'])) {
        header('Location: index.php?page=transactions');
        exit;
    }
    $error = 'Dispatch gagal: stok senjata habis.';
}
if (isset($_POST['save_transaction'])) {
    // UPDATE
    if ($transaction->updateTransaction($_POST['id'], $_POST['weapon_id'], $_POST['buyer_id'], $_POST['dispatch_date'], $_POST['return_date'])) {
        header('Location: index.php?page=transactions');
        exit;
    }
    $error = 'Update transaksi gagal: stok senjata yang dipilih habis.';
}
if (isset($_GET['return'])) {
    // UPDATE (return)
    $transaction->returnWeapon($_GET['return']);
    header('Location: index.php?page=transactions');
    exit;
}
if (isset($_GET['delete_transaction'])) {
    // DELETE
    $transaction->deleteTransaction($_GET['delete_transaction']);
    header('Location: index.php?page=transactions');
    exit;
}

// --- CRUD Weapon ---
if (isset($_POST['save_weapon'])) {
    if ($_POST['id'] == '') {
        // id kosong -> CREATE
        $ok = $weapon->createWeapon($_POST['name'], $_POST['manufacturer'], $_POST['model_code'], $_POST['stock']);
    } else {
        // id terisi -> UPDATE
        $ok = $weapon->updateWeapon($_POST['id'], $_POST['name'], $_POST['manufacturer'], $_POST['model_code'], $_POST['stock']);
    }
    if ($ok) {
        header('Location: index.php?page=weapons'); // kembali ke daftar setelah simpan
        exit;
    }
    $error = 'Gagal menyimpan senjata: Model Code sudah digunakan.';
}
if (isset($_GET['delete_weapon'])) {
    // DELETE
    if ($weapon->deleteWeapon($_GET['delete_weapon'])) {
        header('Location: index.php?page=weapons');
        exit;
    }
    $error = 'Senjata tidak dapat dihapus karena masih tercatat pada transaksi.';
}

// --- CRUD Buyer ---
if (isset($_POST['save_buyer'])) {
    if ($_POST['id'] == '') {
        $ok = $buyer->createBuyer($_POST['name'], $_POST['email'], $_POST['phone']); // CREATE
    } else {
        $ok = $buyer->updateBuyer($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone']); // UPDATE
    }
    if ($ok) {
        header('Location: index.php?page=buyers');
        exit;
    }
    $error = 'Gagal menyimpan pembeli: Email sudah digunakan.';
}
if (isset($_GET['delete_buyer'])) {
    // DELETE
    if ($buyer->deleteBuyer($_GET['delete_buyer'])) {
        header('Location: index.php?page=buyers');
        exit;
    }
    $error = 'Pembeli tidak dapat dihapus karena masih tercatat pada transaksi.';
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

        <?php if ($error): ?>
            <p class="error"><strong><?= htmlspecialchars($error) ?></strong></p>
        <?php endif; ?>

        <?php
        // 4. Routing sederhana: parameter ?page= menentukan view yang dimuat
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page == 'weapons') include 'view/weapons.php';
            elseif ($page == 'weapon_form') include 'view/weapon_form.php';
            elseif ($page == 'buyers') include 'view/buyers.php';
            elseif ($page == 'buyer_form') include 'view/buyer_form.php';
            elseif ($page == 'transactions') include 'view/transactions.php';
            elseif ($page == 'transaction_form') include 'view/transaction_form.php';
        }
        ?>
    </main>
    <?php include 'view/footer.php'; ?>
</body>
</html>
