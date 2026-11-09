<?php
// view/weapon_form.php : satu form untuk Add dan Edit.
// Ada id di URL -> mode edit (data dimuat dari DB); tanpa id -> mode tambah.
$id = $_GET['id'] ?? '';
$data = $id ? $weapon->getWeaponById($id) : [];
?>
<h3><?= $id ? 'Edit Weapon' : 'Add Weapon' ?></h3>

<form method="POST">
    <!-- id tersembunyi: kosong = INSERT, terisi = UPDATE (dibaca index.php) -->
    <input type="hidden" name="id" value="<?= $id ?>">

    <label>Name:</label>
    <input type="text" name="name" required value="<?= $data['name'] ?? '' ?>">

    <label>Manufacturer:</label>
    <input type="text" name="manufacturer" required value="<?= $data['manufacturer'] ?? '' ?>">

    <label>Model Code:</label>
    <input type="text" name="model_code" value="<?= $data['model_code'] ?? '' ?>">

    <label>Stock:</label>
    <input type="number" name="stock" required value="<?= $data['stock'] ?? 0 ?>">

    <button type="submit" name="save_weapon">Save</button>
    <a class="btn" href="?page=weapons">Cancel</a>
</form>