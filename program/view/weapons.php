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
        <th>Action</th>
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
        <td>
            <!-- Edit: membuka form dengan id; Delete: diproses di index.php -->
            <a href="?page=weapon_form&id=<?= $w['id'] ?>">Edit</a>
            <a href="?page=weapons&delete_weapon=<?= $w['id'] ?>" onclick="return confirm('Hapus senjata ini?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<!-- Tombol Add membuka form tanpa id (mode tambah) -->
<button class="add-button" onclick="location.href='?page=weapon_form'">
    + Add Weapon
</button>