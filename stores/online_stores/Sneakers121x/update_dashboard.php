<?php
require_once '../../config/config.php';

if (!isset($_POST['store_name']) || empty($_POST['store_name'])) {
    die("Numele magazinului lipsește din parametrii.");
}

$storeName = $_POST['store_name'];
$conn = connectDB();

// Verificăm dacă magazinul există în baza de date
$stmt = $conn->prepare("SELECT id FROM stores WHERE store_name = ?");
$stmt->bind_param("s", $storeName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Magazinul specificat nu a fost găsit în baza de date.");
}

$store = $result->fetch_assoc();
$storeId = $store['id'];
$stmt->close();

// === Logica actualizării dashboardului ===
// (Aici poți adăuga orice modificare sau template logic vrei să actualizezi pentru dashboard)

//echo "✅ Dashboard-ul magazinului <strong>$storeName</strong> (ID: $storeId) poate fi actualizat aici.";

// Ex: Poți include un alt fișier cu logica de regenerare:
//include_once 'generate_dashboard.php';
//header("Location: template_admin_dashboard.php?store_name=$storeName");
header("Location: ../online_stores/{$storeName}/{$storeName}_admin_dashboard.php?success=1");
exit();
?>
