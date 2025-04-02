<?php
session_start();
require_once "../../config/config.php";

// Simulează sesiunea clientului (doar pentru testare)
if (!isset($_SESSION['customer'])) {
    $_SESSION['customer'] = [
        'id' => 1,
        'nume' => 'Test User',
        'telefon' => '0740123456',
        'adresa' => 'Strada Exemplu 123'
    ];
}

// Simulează store_id (dacă nu e deja setat)
if (!isset($_SESSION['store_id'])) {
    $_SESSION['store_id'] = 1;
}

// Verifică dacă există produse în coș
$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "Coșul este gol.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Finalizare comandă</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Finalizează comanda</h2>

    <form action="process_order.php" method="POST">
        <div class="form-group">
            <label>Nume</label>
            <input type="text" name="nume" class="form-control" value="<?= htmlspecialchars($_SESSION['customer']['nume']) ?>" required>
        </div>
        <div class="form-group">
            <label>Telefon</label>
            <input type="text" name="telefon" class="form-control" value="<?= htmlspecialchars($_SESSION['customer']['telefon']) ?>" required>
        </div>
        <div class="form-group">
            <label>Adresă livrare</label>
            <input type="text" name="adresa" class="form-control" value="<?= htmlspecialchars($_SESSION['customer']['adresa']) ?>" required>
        </div>
        <div class="form-group">
            <label>Metodă de livrare</label>
            <select name="delivery_method" class="form-control" required>
                <option value="Curier">Curier</option>
                <option value="Ridicare personală">Ridicare personală</option>
            </select>
        </div>
        <div class="form-group">
            <label>Observații</label>
            <textarea name="observatii" class="form-control" rows="3"></textarea>
        </div>

        <h4 class="mt-4">Produse în coș</h4>
        <ul class="list-group mb-4">
            <?php
            $total = 0;
            foreach ($cart as $productId => $item):
                $subtotal = $item['pret'] * $item['cantitate'];
                $total += $subtotal;
            ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($item['nume']) ?> × <?= $item['cantitate'] ?>
                    <span><?= number_format($subtotal, 2) ?> lei</span>
                </li>
            <?php endforeach; ?>
            <li class="list-group-item d-flex justify-content-between font-weight-bold">
                Total: <span><?= number_format($total, 2) ?> lei</span>
            </li>
        </ul>

        <input type="hidden" name="total" value="<?= $total ?>">
        <button type="submit" class="btn btn-success btn-block">Trimite comanda</button>
    </form>
</div>
</body>
</html>
