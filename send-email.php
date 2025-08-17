<?php
session_start();

// Configurações de segurança
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Rate limiting simples (5 tentativas por hora por IP)
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = sys_get_temp_dir() . '/contact_rate_' . md5($ip);
$current_time = time();
$rate_limit_window = 3600; // 1 hora
$max_attempts = 5;

if (file_exists($rate_limit_file)) {
    $attempts = json_decode(file_get_contents($rate_limit_file), true);
    $attempts = array_filter($attempts, function($timestamp) use ($current_time, $rate_limit_window) {
        return ($current_time - $timestamp) < $rate_limit_window;
    });
    
    if (count($attempts) >= $max_attempts) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Muitas tentativas. Tente novamente em 1 hora.']);
        exit;
    }
} else {
    $attempts = [];
}

// Verificar se é POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
    exit;
}

// Verificar token CSRF
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Token de segurança inválido.']);
    exit;
}

// Validar e sanitizar dados
$nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
$empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$telefone = filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING);
$mensagem = filter_input(INPUT_POST, 'mensagem', FILTER_SANITIZE_STRING);

// Validações
$errors = [];

if (empty($nome) || strlen($nome) < 2) {
    $errors[] = 'Nome deve ter pelo menos 2 caracteres.';
}

if (empty($empresa) || strlen($empresa) < 2) {
    $errors[] = 'Empresa deve ter pelo menos 2 caracteres.';
}

if (!$email) {
    $errors[] = 'Email inválido.';
}

if (empty($telefone)) {
    $errors[] = 'Telefone é obrigatório.';
}

if (empty($mensagem) || strlen($mensagem) < 10) {
    $errors[] = 'Mensagem deve ter pelo menos 10 caracteres.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Carregar configurações seguras
require_once 'config.php';

// Configurações SMTP do arquivo .env
$smtp_host = $smtp_config['host'];
$smtp_port = $smtp_config['port'];
$smtp_username = $smtp_config['username'];
$smtp_password = $smtp_config['password'];
$to_email = $smtp_config['to_email'];

// Usar PHPMailer se disponível, senão usar mail() nativo
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    require_once 'vendor/autoload.php';
    
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = $smtp_port;
        $mail->CharSet = 'UTF-8';
        
        // Destinatários
        $mail->setFrom($smtp_username, 'Site IA Forte');
        $mail->addAddress($to_email);
        $mail->addReplyTo($email, $nome);
        
        // Conteúdo
        $mail->isHTML(true);
        $mail->Subject = 'Novo contato do site - ' . $nome;
        
        $html_body = "
        <h2>Novo contato recebido do site</h2>
        <p><strong>Nome:</strong> " . htmlspecialchars($nome) . "</p>
        <p><strong>Empresa:</strong> " . htmlspecialchars($empresa) . "</p>
        <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
        <p><strong>Telefone:</strong> " . htmlspecialchars($telefone) . "</p>
        <p><strong>Mensagem:</strong></p>
        <div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>
            " . nl2br(htmlspecialchars($mensagem)) . "
        </div>
        <hr>
        <p><small>Enviado em: " . date('d/m/Y H:i:s') . "</small></p>
        ";
        
        $mail->Body = $html_body;
        $mail->AltBody = "Nome: $nome\nEmpresa: $empresa\nEmail: $email\nTelefone: $telefone\nMensagem: $mensagem";
        
        $mail->send();
        
        // Registrar tentativa bem-sucedida
        $attempts[] = $current_time;
        file_put_contents($rate_limit_file, json_encode($attempts));
        
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
        
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        error_log("Erro no envio de email: " . $mail->ErrorInfo);
        echo json_encode(['success' => false, 'message' => 'Erro interno. Tente novamente mais tarde.']);
    }
    
} else {
    // Fallback para mail() nativo (menos seguro)
    $subject = 'Novo contato do site - ' . $nome;
    $headers = [
        'From: ' . $smtp_username,
        'Reply-To: ' . $email,
        'Content-Type: text/html; charset=UTF-8',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $body = "
    <h2>Novo contato recebido do site</h2>
    <p><strong>Nome:</strong> " . htmlspecialchars($nome) . "</p>
    <p><strong>Empresa:</strong> " . htmlspecialchars($empresa) . "</p>
    <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
    <p><strong>Telefone:</strong> " . htmlspecialchars($telefone) . "</p>
    <p><strong>Mensagem:</strong></p>
    <div style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>
        " . nl2br(htmlspecialchars($mensagem)) . "
    </div>
    <hr>
    <p><small>Enviado em: " . date('d/m/Y H:i:s') . "</small></p>
    ";
    
    if (mail($to_email, $subject, $body, implode("\r\n", $headers))) {
        // Registrar tentativa bem-sucedida
        $attempts[] = $current_time;
        file_put_contents($rate_limit_file, json_encode($attempts));
        
        echo json_encode(['success' => true, 'message' => 'Mensagem enviada com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro interno. Tente novamente mais tarde.']);
    }
}
?>