<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $verification_code = $_POST['verification_code'];

    if ($verification_code == $_SESSION['verification_code']) {
        // Codul de verificare este corect
        unset($_SESSION['verification_code']);
        header("Location: ../secure/customer_dashboard.php");
        exit();
    } else {
        // Codul de verificare este greșit
        header("Location: ../public/customer_verify.php?error=invalid_code");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Verify</h2>
        <form action="../secure/customer_verify.php" method="post">
            <div class="form-group">
                <label for="verification_code">Verification Code:</label>
                <input type="text" class="form-control" id="verification_code" name="verification_code" required>
            </div>
            <button type="submit" class="btn btn-primary">Verify</button>
        </form>
    </div>
</body>
</html>