<?php
session_start();

// Rate limiting simples
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = sys_get_temp_dir() . '/contact_rate_' . md5($ip);
$current_time = time();
$max_attempts = 5;
$rate_limit_window = 3600;

if (file_exists($rate_limit_file)) {
    $attempts = unserialize(file_get_contents($rate_limit_file));
    $attempts = array_filter($attempts, function($timestamp) use ($current_time, $rate_limit_window) {
        return ($current_time - $timestamp) < $rate_limit_window;
    });
    
    if (count($attempts) >= $max_attempts) {
        http_response_code(429);
        echo 'RATE_LIMIT';
        exit;
    }
} else {
    $attempts = [];
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'METHOD_NOT_ALLOWED';
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || empty($_POST['csrf_token'])) {
    http_response_code(403);
    echo 'INVALID_TOKEN';
    exit;
}

// Validar dados
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

if (empty($nome) || strlen($nome) < 2 ||
    empty($empresa) || strlen($empresa) < 2 ||
    !$email ||
    empty($telefone) ||
    empty($mensagem) || strlen($mensagem) < 10) {
    echo 'VALIDATION_ERROR';
    exit;
}

// Carregar configurações
require_once 'config.php';

$smtp_host = $smtp_config['host'];
$smtp_port = $smtp_config['port'];
$smtp_username = $smtp_config['username'];
$smtp_password = $smtp_config['password'];
$to_email = $smtp_config['to_email'];

// Tentar envio por mail() simples
$subject = 'Novo contato do site - ' . $nome;
$headers = 'From: ' . $smtp_username . "\r\n";
$headers .= 'Reply-To: ' . $email . "\r\n";
$headers .= 'Content-Type: text/plain; charset=UTF-8' . "\r\n";

$body = "Novo contato recebido do site\n\n";
$body .= "Nome: " . $nome . "\n";
$body .= "Empresa: " . $empresa . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Telefone: " . $telefone . "\n";
$body .= "Mensagem: " . $mensagem . "\n";
$body .= "\nEnviado em: " . date('d/m/Y H:i:s');

if (mail($to_email, $subject, $body, $headers)) {
    // Registrar tentativa
    $attempts[] = $current_time;
    file_put_contents($rate_limit_file, serialize($attempts));
    
    echo 'SUCCESS';
} else {
    echo 'MAIL_ERROR';
}
?>