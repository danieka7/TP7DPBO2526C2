<!-- view/buyers.php : tampilan pembeli/mitra terdaftar.
     Variabel $buyer berasal dari index.php. -->
<h3>Registered Buyers</h3>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
    </tr>
    <?php foreach ($buyer->getAllBuyers() as $b): ?>
    <tr>
        <td><?= $b['id'] ?></td>
        <td><?= $b['name'] ?></td>
        <td><?= $b['email'] ?></td>
        <td><?= $b['phone'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<button class="add-button">
    + Add Buyer
</button>
