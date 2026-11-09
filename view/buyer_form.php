<?php
// view/buyer_form.php : satu form untuk Add dan Edit pembeli.
$id = $_GET['id'] ?? '';
$data = $id ? $buyer->getBuyerById($id) : [];
?>
<h3><?= $id ? 'Edit Buyer' : 'Add Buyer' ?></h3>

<form method="POST">
    <input type="hidden" name="id" value="<?= $id ?>">

    <label>Name:</label>
    <input type="text" name="name" required value="<?= $data['name'] ?? '' ?>">

    <label>Email:</label>
    <input type="email" name="email" value="<?= $data['email'] ?? '' ?>">

    <label>Phone:</label>
    <input type="text" name="phone" value="<?= $data['phone'] ?? '' ?>">

    <button type="submit" name="save_buyer">Save</button>
    <a class="btn" href="?page=buyers">Cancel</a>
</form>