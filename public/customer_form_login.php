<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php
    session_start();

    // Generează un token CSRF dacă nu există deja
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    $store_name = isset($_GET['store_name']) ? $_GET['store_name'] : (isset($_SESSION['store_name']) ? $_SESSION['store_name'] : '');

    if (!$store_name) {
        die("Store name is missing! Please check if the URL contains store_name or if the session is set.");
    }
    ?>
    <div class="container">
        <h2>Login</h2>
        <form action="../secure/customer_login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="store_name" value="<?php echo htmlspecialchars($store_name, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>