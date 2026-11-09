<!-- view/weapons.php : tampilan inventaris senjata.
     Variabel $weapon berasal dari index.php (scope include dibagi). -->
<h3>Weapon Inventory</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Manufacturer</th>
        <th>Model Code</th>
        <th>Stock</th>
    </tr>
    <?php foreach ($weapon->getAllWeapons() as $w): ?>
    <tr>
        <td><?= $w['id'] ?></td>
        <td><?= $w['name'] ?></td>
        <td><?= $w['manufacturer'] ?></td>
        <td><?= $w['model_code'] ?></td>
        <!-- Stok <= 2 diberi kelas "stock-low" (merah) dan teks (low),
             agar penanda tidak hanya bergantung pada warna -->
        <td class="<?= $w['stock'] <= 2 ? 'stock-low' : '' ?>"><?= $w['stock'] ?><?= $w['stock'] <= 2 ? ' (low)' : '' ?></td>
    </tr>

    <?php endforeach; ?>
</table>

<button class="add-button">
    + Add Weapon
</button>
