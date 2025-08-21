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

// Cabeçalhos otimizados para evitar classificação como spam
$headers = array();
$headers[] = 'From: "IA Forte - Contato Site" <' . $smtp_username . '>';
$headers[] = 'Reply-To: "' . $nome . ' (' . $empresa . ')" <' . $email . '>';
$headers[] = 'Return-Path: <' . $smtp_username . '>';
$headers[] = 'Sender: <' . $smtp_username . '>';
$headers[] = 'Message-ID: <' . date('YmdHis') . '.' . uniqid() . '@' . $domain . '>';
$headers[] = 'Date: ' . date('r');
$headers[] = 'X-Mailer: IA Forte Contact Form v1.0';
$headers[] = 'X-Priority: 3 (Normal)';
$headers[] = 'X-MSMail-Priority: Normal';
$headers[] = 'Importance: Normal';
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8; format=flowed';
$headers[] = 'Content-Transfer-Encoding: 8bit';
$headers[] = 'X-Auto-Response-Suppress: All';
$headers[] = 'X-Entity-ID: iaforte-contact-form';
$headers[] = 'List-Unsubscribe: <mailto:' . $smtp_username . '?subject=unsubscribe>';
$headers[] = 'X-Originating-IP: [' . $_SERVER['REMOTE_ADDR'] . ']';

// Corpo do email otimizado para evitar filtros de spam
$body = "Olá,\n\n";
$body .= "Você recebeu uma nova solicitação de contato através do site IA Forte.\n\n";
$body .= "Informações do interessado:\n\n";
$body .= "Nome: " . $nome . "\n";
$body .= "Empresa: " . $empresa . "\n";
$body .= "Email: " . $email . "\n";
$body .= "Telefone: " . $telefone . "\n\n";
$body .= "Mensagem:\n";
$body .= wordwrap($mensagem, 72, "\n", true) . "\n\n";
$body .= "---\n";
$body .= "Detalhes técnicos:\n";
$body .= "Enviado em: " . date('d/m/Y às H:i:s') . "\n";
$body .= "Origem: " . $_SERVER['REMOTE_ADDR'] . "\n\n";
$body .= "Atenciosamente,\n";
$body .= "Sistema de Contato IA Forte\n";
$body .= "https://iaforte.com.br";

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