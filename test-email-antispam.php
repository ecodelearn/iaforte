<?php
// Teste do novo formato de email anti-spam
require_once 'config.php';

// Dados de teste
$nome = 'Teste Sistema';
$empresa = 'IA Forte Tecnologia';
$email = 'teste@iaforte.com.br';
$telefone = '(11) 99999-9999';
$mensagem = 'Esta é uma mensagem de teste do sistema de contato para verificar se os emails não estão mais sendo classificados como spam. O novo formato inclui cabeçalhos adequados e estrutura profissional.';

$smtp_host = $smtp_config['host'];
$smtp_port = $smtp_config['port'];
$smtp_username = $smtp_config['username'];
$smtp_password = $smtp_config['password'];
$to_email = $smtp_config['to_email'];

// Configurar cabeçalhos anti-spam
$domain = 'iaforte.com.br';
$subject = '[Contato Site] Mensagem de ' . $nome . ' - ' . $empresa;

// Cabeçalhos melhorados para evitar spam
$headers = array();
$headers[] = 'From: IA Forte <' . $smtp_username . '>';
$headers[] = 'Reply-To: ' . $nome . ' <' . $email . '>';
$headers[] = 'Return-Path: ' . $smtp_username;
$headers[] = 'Message-ID: <' . time() . '.' . md5($email . $nome) . '@' . $domain . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'X-Priority: 3';
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'Content-Transfer-Encoding: 8bit';

// Corpo do email mais profissional
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

echo "<h2>Teste de Email Anti-Spam</h2>\n";
echo "<h3>Assunto:</h3>\n";
echo "<p>" . htmlspecialchars($subject) . "</p>\n";
echo "<h3>Cabeçalhos:</h3>\n";
echo "<pre>" . htmlspecialchars($headers_string) . "</pre>\n";
echo "<h3>Corpo do Email:</h3>\n";
echo "<pre>" . htmlspecialchars($body) . "</pre>\n";

echo "<h3>Teste de Envio:</h3>\n";
if (mail($to_email, $subject, $body, $headers_string)) {
    echo "<p style='color: green;'>✅ Email enviado com sucesso!</p>";
} else {
    echo "<p style='color: red;'>❌ Erro no envio do email</p>";
}
?>