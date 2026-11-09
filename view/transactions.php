<!-- view/transactions.php : log transaksi + form dispatch.
     Variabel $transaction, $weapon, $buyer berasal dari index.php. -->
<h3>Transaction Log</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Weapon</th>
        <th>Buyer</th>
        <th>Dispatch Date</th>
        <th>Return Date</th>
        <th>Action</th>
    </tr>
    <?php foreach ($transaction->getAllTransactions() as $t): ?>
    <tr>
        <td><?= $t['id'] ?></td>
        <td><?= $t['weapon_name'] ?></td>
        <td><?= $t['buyer_name'] ?></td>
        <td><?= $t['dispatch_date'] ?></td>
        <!-- Operator ?? : tampilkan teks bila return_date masih NULL -->
        <td><?= $t['return_date'] ?? 'Not Returned' ?></td>
        <td>
            <?php if (!$t['return_date']): ?>
                <!-- Tautan GET ?return=ID diproses di index.php -->
                <a href="?page=transactions&return=<?= $t['id'] ?>">Return</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Dispatch a Weapon</h3>
<!-- Form POST: tombol bernama "dispatch" dibaca index.php -->
<form method="POST">
    <label>Weapon:</label>
    <select name="weapon_id">
        <?php foreach ($weapon->getAllWeapons() as $w): ?>
            <option value="<?= $w['id'] ?>"><?= $w['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <label>Buyer:</label>
    <select name="buyer_id">
        <?php foreach ($buyer->getAllBuyers() as $b): ?>
            <option value="<?= $b['id'] ?>"><?= $b['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="dispatch">Dispatch</button>
</form>
