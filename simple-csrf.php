<?php
// Versão super simples - apenas gera um token
header('Content-Type: application/json');

try {
    $token = md5(uniqid() . time()); // Simples mas funcional
    echo json_encode(['csrf_token' => $token]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erro ao gerar token']);
}
?>