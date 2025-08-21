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

// Configurar cabeçalhos anti-spam
$domain = 'iaforte.com.br';
$subject = '[Contato Site] Mensagem de ' . $nome . ' - ' . $empresa;

// Cabeçalhos exatamente como o test-email-antispam.php que funciona
$headers = array();
$headers[] = 'From: IA Forte <' . $smtp_username . '>';
// $headers[] = 'Reply-To: ' . $nome . ' <' . $email . '>'; // Removido - causa FREEMAIL_REPLYTO_NEQ_FROM
$headers[] = 'Return-Path: ' . $smtp_username;
$headers[] = 'Message-ID: <' . time() . '.1b9f07f95d0a2717c1faa75fa7f57afe@' . $domain . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'X-Priority: 3';
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'Content-Transfer-Encoding: 8bit';

// Corpo do email exatamente como o test-email-antispam.php que funciona
$body = "NOVA SOLICITAÇÃO DE CONTATO\n";
$body .= "==============================\n\n";
$body .= "Uma nova mensagem foi enviada através do formulário de contato do site IA Forte.\n\n";
$body .= "DADOS DO CONTATO:\n";
$body .= "-----------------\n";
$body .= "Nome Completo: " . $nome . "\n";
$body .= "Empresa/Organização: " . $empresa . "\n";
$body .= "E-mail de Contato: " . $email . "\n";
$body .= "Telefone: " . $telefone . "\n\n";
$body .= "MENSAGEM:\n";
$body .= "---------\n";
$body .= wordwrap($mensagem, 70, "\n", true) . "\n\n";
$body .= "==============================\n";
$body .= "Data/Hora: " . date('d/m/Y às H:i:s') . "\n";
$body .= "IP do Remetente: " . $_SERVER['REMOTE_ADDR'] . "\n";
$body .= "User Agent: " . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'N/A') . "\n";
$body .= "==============================\n\n";
$body .= "Esta mensagem foi enviada automaticamente pelo sistema de contato do site IA Forte.";

$headers_string = implode("\r\n", $headers);

// Log de debug
error_log("DEBUG - Tentando enviar email para: $to_email");
error_log("DEBUG - Assunto: $subject");
error_log("DEBUG - De: $smtp_username");

$mail_result = mail($to_email, $subject, $body, $headers_string);

// Log do resultado
error_log("DEBUG - Resultado mail(): " . ($mail_result ? 'TRUE' : 'FALSE'));

if ($mail_result) {
    // Registrar tentativa
    $attempts[] = $current_time;
    file_put_contents($rate_limit_file, serialize($attempts));
    
    error_log("SUCCESS - Email enviado com sucesso via formulário");
    echo 'SUCCESS';
} else {
    error_log("ERROR - Falha no envio do email via formulário");
    echo 'MAIL_ERROR';
}
?>