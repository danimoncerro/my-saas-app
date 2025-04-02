<?php
session_start();

require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        die("Eroare: utilizatorul nu este autentificat.");
    }

    $storeName = trim($_POST['store_name']);
    if (empty($storeName)) {
        die("Numele magazinului este obligatoriu.");
    }

    $userId = $_SESSION['user_id'];
    $conn = connectDB();

    // Verifică dacă magazinul există deja
    $checkStmt = $conn->prepare("SELECT id FROM stores WHERE store_name = ?");
    $checkStmt->bind_param("s", $storeName);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        die("Magazinul există deja.");
    }

    $checkStmt->close();

    // Inserare magazin cu user_id
    $stmt = $conn->prepare("INSERT INTO stores (store_name, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $storeName, $userId);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Creează folderul magazinului
    $storeFolder = "../stores/online_stores/$storeName";
    if (!is_dir($storeFolder)) {
        mkdir($storeFolder, 0755, true);
    }

    // Căi către template-uri
    $templateAdmin = '../stores/settings/template_admin_dashboard.php';
    $templateDashboard = '../stores/settings/template_dashboard.php';
    $templatePublic = '../stores/settings/template_public.php';

    if (!file_exists($templateAdmin) || !file_exists($templateDashboard) || !file_exists($templatePublic)) {
        die("Fișierele template necesare nu au fost găsite.");
    }

    // Destinațiile fișierelor copiate
    $adminTarget = "$storeFolder/{$storeName}_admin_dashboard.php";
    $dashboardTarget = "$storeFolder/{$storeName}_dashboard.php";
    $publicTarget = "$storeFolder/{$storeName}.php";

    copy($templateAdmin, $adminTarget);
    copy($templateDashboard, $dashboardTarget);
    copy($templatePublic, $publicTarget);

    // Redirecționare la dashboard
    header("Location: ../stores/online_stores/$storeName/{$storeName}_admin_dashboard.php?success=1");
    exit;
} else {
    echo "Acces nepermis.";
}
