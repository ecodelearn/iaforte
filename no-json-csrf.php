<?php
// Versão sem json_encode para compatibilidade
header('Content-Type: text/plain');

try {
    $token = md5(uniqid() . time());
    echo $token; // Retorna só o token
} catch (Exception $e) {
    echo 'ERROR';
}
?>