<?php
// Script de debug para formulário
session_start();

echo "<h2>Debug do Formulário de Contato</h2>\n";
echo "<h3>Método HTTP:</h3>\n";
echo "<p>" . $_SERVER['REQUEST_METHOD'] . "</p>\n";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Dados POST recebidos:</h3>\n";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>\n";
    
    echo "<h3>Headers HTTP:</h3>\n";
    echo "<pre>";
    foreach (getallheaders() as $name => $value) {
        echo "$name: $value\n";
    }
    echo "</pre>\n";
    
    echo "<h3>Variáveis do servidor:</h3>\n";
    echo "<pre>";
    echo "REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR'] . "\n";
    echo "HTTP_USER_AGENT: " . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'N/A') . "\n";
    echo "</pre>\n";
    
    // Testar se consegue enviar email com os dados do formulário
    if (isset($_POST['nome']) && isset($_POST['email'])) {
        echo "<h3>Teste de envio com dados do formulário:</h3>\n";
        
        require_once 'config.php';
        
        $nome = $_POST['nome'] ?? 'N/A';
        $empresa = $_POST['empresa'] ?? 'N/A';
        $email = $_POST['email'] ?? 'teste@iaforte.com.br';
        $telefone = $_POST['telefone'] ?? 'N/A';
        $mensagem = $_POST['mensagem'] ?? 'N/A';
        
        $smtp_username = $smtp_config['username'];
        $to_email = $smtp_config['to_email'];
        $domain = 'iaforte.com.br';
        $subject = '[DEBUG FORM] Mensagem de ' . $nome . ' - ' . $empresa;
        
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
        
        $body = "DEBUG - FORMULÁRIO DE CONTATO\n";
        $body .= "==============================\n\n";
        $body .= "Nome: " . $nome . "\n";
        $body .= "Empresa: " . $empresa . "\n";
        $body .= "Email: " . $email . "\n";
        $body .= "Telefone: " . $telefone . "\n";
        $body .= "Mensagem: " . $mensagem . "\n\n";
        $body .= "Data: " . date('d/m/Y às H:i:s') . "\n";
        $body .= "IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
        
        $headers_string = implode("\r\n", $headers);
        
        if (mail($to_email, $subject, $body, $headers_string)) {
            echo "<p style='color: green;'>✅ Email DEBUG enviado com sucesso!</p>";
        } else {
            echo "<p style='color: red;'>❌ Erro no envio do email DEBUG</p>";
        }
    }
} else {
    echo "<p>Envie dados via POST para este script para fazer o debug.</p>";
}
?>