
<?php
session_start();
require_once __DIR__ . '/../../../config/config.php';

// Identifică magazinul
$currentFile = basename(__FILE__);
preg_match('/^(.*?)_admin_dashboard\.php$/', $currentFile, $matches);
$storeName = $matches[1] ?? '';
$storePublicUrl = "/my-saas-app/stores/online_stores/$storeName/$storeName.php";

if (!$storeName) {
    die("<p style='color: red;'>Eroare: Nu a fost identificat magazinul curent.</p>");
}

$conn = connectDB();
$stmt = $conn->prepare("SELECT id FROM stores WHERE store_name = ?");
$stmt->bind_param("s", $storeName);
$stmt->execute();
$result = $stmt->get_result();
$store = $result->fetch_assoc();
$storeId = $store['id'] ?? 0;
$stmt->close();

// Obține setările de personalizare
$settingsStmt = $conn->prepare("SELECT * FROM store_settings WHERE store_id = ?");
$settingsStmt->bind_param("i", $storeId);
$settingsStmt->execute();
$settingsResult = $settingsStmt->get_result();
$settings = $settingsResult->fetch_assoc();
$settingsStmt->close();

// Ștergere produs dacă e cazul
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = intval($_POST['delete_id']);
    $deleteStmt = $conn->prepare("DELETE FROM produse WHERE id_produs = ? AND store_id = ?");
    $deleteStmt->bind_param("ii", $deleteId, $storeId);
    $deleteStmt->execute();
    $deleteStmt->close();
}

// Produse
$produse = [];
if ($storeId) {
    $result = $conn->query("SELECT * FROM produse WHERE store_id = $storeId ORDER BY id_produs DESC");
    while ($row = $result->fetch_assoc()) {
        $produse[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Magazin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial; margin: 0; padding: 0; background: #f4f4f4; }
        .sidebar {
            width: 220px;
            background: #2c3e50;
            min-height: 100vh;
            float: left;
            color: white;
        }
        .sidebar ul { list-style-type: none; padding: 0; }
        .sidebar li { padding: 15px; }
        .sidebar li a { color: white; text-decoration: none; display: block; }
        .sidebar li:hover { background: #34495e; }
        .content { margin-left: 220px; padding: 20px; }
        .sidebar li i { margin-right: 10px; }
        #addProductForm { display: none; }
    </style>
    <script>
        function showSection(id) {
            document.querySelectorAll('.section').forEach(s => s.style.display = 'none');
            document.getElementById(id).style.display = 'block';
        }
        function toggleForm() {
            const form = document.getElementById('addProductForm');
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
        }
        window.onload = function () {
            const url = new URL(window.location.href);
            const section = url.searchParams.get("section") || "dashboard";
            showSection(section);
        }
    </script>
</head>
<body>
<div class="sidebar">
    <ul>
        <li><a href="#" onclick="showSection('dashboard')"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="#" onclick="showSection('produse')"><i class="fas fa-box"></i> Produse</a></li>
        <li><a href="#" onclick="showSection('personalizare')"><i class="fas fa-paint-brush"></i> Personalizare</a></li>
        <li><a href="<?= $storePublicUrl ?>" target="_blank"><i class="fas fa-store"></i> Vezi magazinul</a></li>
        <li><a href="/my-saas-app/public/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="content">
    <div id="dashboard" class="section">
        <h1>Bine ai venit în dashboard-ul magazinului!</h1>
        <p>Aici poți gestiona produsele, personalizarea și alte funcționalități.</p>
    </div>

    <div id="produse" class="section" style="display:none">
        <h2>Produse</h2>
        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">✅ Produsul a fost adăugat cu succes.</div>
        <?php endif; ?>
        <button class="btn btn-success mb-3" onclick="toggleForm()">Adaugă produs nou</button>

        <form id="addProductForm" action="../../settings/process_add_product.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <input type="hidden" name="store_id" value="<?= $storeId ?>">
            <div class="form-row">
                <div class="col"><input type="text" name="denumire" class="form-control" placeholder="Nume produs"></div>
                <div class="col"><input type="text" name="pret" class="form-control" placeholder="Preț"></div>
                <div class="col"><input type="text" name="UM" class="form-control" placeholder="Unitate"></div>
                <div class="col"><input type="number" name="stoc" class="form-control" placeholder="Stoc"></div>
                <div class="col"><input type="number" name="stoc_limitat" class="form-control" placeholder="Limită stoc"></div>
            </div>
            <div class="form-group mt-2">
                <textarea name="descriere" class="form-control" rows="3" placeholder="Descriere"></textarea>
            </div>
            <div class="form-group">
                <label>Imagine produs</label>
                <input type="file" name="image" class="form-control-file">
            </div>
            <button type="submit" class="btn btn-success">Adaugă produs</button>
        </form>

        <h4>Lista produselor</h4>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Imagine</th>
                    <th>Nume</th>
                    <th>Preț</th>
                    <th>Unitate</th>
                    <th>Stoc</th>
                    <th>Limită</th>
                    <th>Descriere</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produse as $produs): ?>
                    <tr>
                        <td><?php if (!empty($produs['pic'])): ?><img src="/my-saas-app/public/images/<?= $produs['pic'] ?>" width="50"><?php endif; ?></td>
                        <td><?= htmlspecialchars($produs['denumire']) ?></td>
                        <td><?= htmlspecialchars($produs['pret']) ?> lei</td>
                        <td><?= htmlspecialchars($produs['UM']) ?></td>
                        <td><?= htmlspecialchars($produs['stoc']) ?></td>
                        <td><?= htmlspecialchars($produs['stoc_limitat']) ?></td>
                        <td><?= htmlspecialchars($produs['descriere']) ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="../../settings/edit_product.php?id=<?= $produs['id_produs'] ?>&store=<?= $storeName ?>" class="btn btn-warning btn-sm mr-1">Editează</a>
                                <form method="POST" onsubmit="return confirm('Sigur dorești să ștergi acest produs?');">
                                    <input type="hidden" name="delete_id" value="<?= $produs['id_produs'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Șterge</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div id="personalizare" class="section" style="display:none">
        <h2>🎨 Personalizare Magazin</h2>
        <form method="POST" action="../../settings/update_store_style.php">
            <input type="hidden" name="store_id" value="<?= $storeId ?>">
            <input type="hidden" name="store_name" value="<?= $storeName ?>">

            <div class="form-group">
                <label for="title">Titlu magazin</label>
                <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($settings['title'] ?? $storeName) ?>">
            </div>
            <div class="form-group">
                <label for="slogan">Slogan</label>
                <input type="text" class="form-control" name="slogan" value="<?= htmlspecialchars($settings['slogan'] ?? 'Fructe, legume și sucuri naturale - direct de la fermieri') ?>">
            </div>
            <div class="form-group">
                <label for="header_color">Culoare Header</label>
                <input type="color" class="form-control" name="header_color" value="<?= htmlspecialchars($settings['header_color'] ?? '#28a745') ?>">
            </div>
            <div class="form-group">
                <label for="background_color">Culoare Fundal</label>
                <input type="color" class="form-control" name="background_color" value="<?= htmlspecialchars($settings['background_color'] ?? '#f0fdfb') ?>">
            </div>
            <button type="submit" class="btn btn-success">💾 Salvează personalizarea</button>
        </form>
    </div>
</div>
</body>
</html>
