<?php
$host   = 'mysql-produits-srv.mysql.database.azure.com';
$dbname = 'catalogueproduits';
$user   = 'azureuser';
$pass   = 'Issamasm@123456';

try {
    $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8",
    $user, $pass,
    [
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ]
);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erreur connexion : ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'])) {
    $stmt = $pdo->prepare(
        'INSERT INTO Produits (nom, description, prix, stock, categorie)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $_POST['nom'], $_POST['description'],
        $_POST['prix'], $_POST['stock'], $_POST['categorie']
    ]);
    header('Location: index.php?success=1');
    exit;
}

$produits = $pdo->query('SELECT * FROM Produits ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Catalogue Produits — Azure</title>
  <style>
    body  { font-family: Arial; max-width: 1000px; margin: 30px auto; padding: 20px; }
    h1    { color: #1F4E79; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th    { background: #1F4E79; color: white; padding: 10px; }
    td    { border: 1px solid #ddd; padding: 8px; }
    tr:nth-child(even) { background: #f2f2f2; }
    .form-box { background: #eef5fb; padding: 20px; border-radius: 8px; margin: 20px 0; }
    input, select, textarea { width: 100%; padding: 8px; margin: 5px 0 12px; box-sizing: border-box; }
    button  { background: #2E75B6; color: white; padding: 10px 20px; border: none; cursor: pointer; }
    .success { background: #d5f5e3; padding: 10px; border-radius: 5px; color: #1e8449; }
  </style>
</head>
<body>
  <h1>🛒 Catalogue Produits — Azure App Service + MySQL</h1>

  <?php if (isset($_GET['success'])): ?>
    <p class="success">✅ Produit ajouté avec succès !</p>
  <?php endif; ?>

  <div class="form-box">
    <h2>Ajouter un produit</h2>
    <form method="POST">
      <label>Nom : <input type="text" name="nom" required></label>
      <label>Description : <textarea name="description"></textarea></label>
      <label>Prix (€) : <input type="number" step="0.01" name="prix" required></label>
      <label>Stock : <input type="number" name="stock" value="0"></label>
      <label>Catégorie : <input type="text" name="categorie"></label>
      <button type="submit">➕ Ajouter le produit</button>
    </form>
  </div>

  <h2>Liste des produits (<?= count($produits) ?> articles)</h2>
  <table>
    <tr>
      <th>ID</th><th>Nom</th><th>Description</th>
      <th>Prix</th><th>Stock</th><th>Catégorie</th>
    </tr>
    <?php foreach ($produits as $p): ?>
    <tr>
      <td><?= $p['id'] ?></td>
      <td><?= htmlspecialchars($p['nom']) ?></td>
      <td><?= htmlspecialchars($p['description']) ?></td>
      <td><?= $p['prix'] ?> €</td>
      <td><?= $p['stock'] ?></td>
      <td><?= htmlspecialchars($p['categorie']) ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
