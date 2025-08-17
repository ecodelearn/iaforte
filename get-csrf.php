<?php
// Versão simplificada para debug
try {
    session_start();
    
    // Gerar token CSRF se não existir
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(16)); // Menor para compatibilidade
    }
    
    // Headers básicos
    header('Content-Type: application/json');
    
    echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
}
?>