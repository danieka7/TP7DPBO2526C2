<?php
// view/transaction_form.php : form Edit transaksi.
// Transaksi baru dibuat lewat form Dispatch di halaman transactions.
// Variabel $transaction, $weapon, $buyer berasal dari index.php.
$id = $_GET['id'] ?? '';
$data = $id ? $transaction->getTransactionById($id) : false;
?>
<h3>Edit Transaction</h3>

<?php if (!$data): ?>
    <p>Transaksi tidak ditemukan.</p>
    <a class="btn" href="?page=transactions">Back</a>
<?php else: ?>
<form method="POST">
    <!-- id tersembunyi: transaksi yang di-UPDATE (dibaca index.php) -->
    <input type="hidden" name="id" value="<?= $data['id'] ?>">

    <label>Weapon:</label>
    <select name="weapon_id">
        <?php foreach ($weapon->getAllWeapons() as $w): ?>
            <option value="<?= $w['id'] ?>" <?= $w['id'] == $data['weapon_id'] ? 'selected' : '' ?>>
                <?= $w['name'] ?> (stock: <?= $w['stock'] ?>)
            </option>
        <?php endforeach; ?>
    </select>

    <label>Buyer:</label>
    <select name="buyer_id">
        <?php foreach ($buyer->getAllBuyers() as $b): ?>
            <option value="<?= $b['id'] ?>" <?= $b['id'] == $data['buyer_id'] ? 'selected' : '' ?>>
                <?= $b['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Dispatch Date:</label>
    <input type="date" name="dispatch_date" required value="<?= $data['dispatch_date'] ?>">

    <!-- Dikosongkan = belum dikembalikan (NULL) -->
    <label>Return Date (kosongkan bila belum kembali):</label>
    <input type="date" name="return_date" value="<?= $data['return_date'] ?? '' ?>">

    <button type="submit" name="save_transaction">Save</button>
    <a class="btn" href="?page=transactions">Cancel</a>
</form>
<?php endif; ?>
