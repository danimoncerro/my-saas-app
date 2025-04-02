<?php
session_start();
$storeName = $_SESSION['store_name']; // Asigură-te că setezi această variabilă la autentificare
session_unset();
session_destroy();
header("Location: ../stores/{$storeName}.php");
exit();
?>