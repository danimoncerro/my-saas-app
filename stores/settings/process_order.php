<?php
session_start();
require_once "../../config/config.php";

// Verifică dacă există date din formular
if (
    !isset($_POST['nume'], $_POST['telefon'], $_POST['adresa'], $_POST['delivery_method']) ||
    empty($_SESSION['cart']) || !isset($_SESSION['customer']['id']) || !isset($_SESSION['store_id'])
) {
    die("Date lipsă pentru finalizarea comenzii.");
}

$customer_id = $_SESSION['customer']['id'];
$store_id = $_SESSION['store_id'];
$nume = $_POST['nume'];
$telefon = $_POST['telefon'];
$adresa = $_POST['adresa'];
$delivery_method = $_POST['delivery_method'];
$observatii = $_POST['observatii'] ?? '';
$total = $_POST['total'] ?? 0.00;
$order_code = 'ORD' . time(); // cod unic de comandă
$is_finalized = 1;
$discount = 0;

$conn = connectDB();

// Inserare comandă
$stmt = $conn->prepare("
    INSERT INTO orders 
    (customer_id, store_id, nume, telefon, adresa, delivery_method, observatii, total, discount, order_code, is_finalized)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    die("Eroare pregătire query: " . $conn->error);
}

$stmt->bind_param(
    "iisssssddsi",
    $customer_id,
    $store_id,
    $nume,
    $telefon,
    $adresa,
    $delivery_method,
    $observatii,
    $total,
    $discount,
    $order_code,
    $is_finalized
);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Inserare produse din coș în `order_items`
    $cart = $_SESSION['cart'];
    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price_unit, total_price)
        VALUES (?, ?, ?, ?, ?)
    ");

    if (!$stmt_item) {
        die("Eroare la pregătirea inserării produselor: " . $conn->error);
    }

    foreach ($cart as $productId => $item) {
        $cantitate = $item['cantitate'];
        $pret = $item['pret'];
        $subtotal = $cantitate * $pret;

        $stmt_item->bind_param("iiidd", $order_id, $productId, $cantitate, $pret, $subtotal);
        $stmt_item->execute();
    }

    $stmt_item->close();
    $conn->close();

    // Golește coșul
    unset($_SESSION['cart']);

    // Afișează mesaj de confirmare
    echo "<h2>Comanda a fost trimisă cu succes!</h2>";
    echo "<p>Cod comandă: <strong>$order_code</strong></p>";
    echo "<a href='../../index.php' class='btn btn-primary'>Înapoi la magazin</a>";
} else {
    echo "Eroare la salvarea comenzii: " . $stmt->error;
}
