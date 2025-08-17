<?php
session_start();

// Gerar token CSRF se não existir
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
?>