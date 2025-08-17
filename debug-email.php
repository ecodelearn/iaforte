<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug do Sistema de Email</h2>";

// 1. Verificar arquivo .env
echo "<h3>1. Arquivo .env</h3>";
if (file_exists('.env')) {
    echo "✅ Arquivo .env existe<br>";
    $envContent = file_get_contents('.env');
    echo "Tamanho: " . strlen($envContent) . " bytes<br>";
    echo "Conteúdo (primeiros 100 chars): " . htmlspecialchars(substr($envContent, 0, 100)) . "<br>";
} else {
    echo "❌ Arquivo .env NÃO encontrado<br>";
}

// 2. Testar config.php
echo "<h3>2. Carregar config.php</h3>";
try {
    require_once 'config.php';
    echo "✅ config.php carregado com sucesso<br>";
    
    echo "Configurações SMTP:<br>";
    echo "Host: " . htmlspecialchars($smtp_config['host']) . "<br>";
    echo "Port: " . $smtp_config['port'] . "<br>";
    echo "Username: " . htmlspecialchars($smtp_config['username']) . "<br>";
    echo "Password: " . (empty($smtp_config['password']) ? '❌ VAZIA' : '✅ Definida (' . strlen($smtp_config['password']) . ' chars)') . "<br>";
    echo "To Email: " . htmlspecialchars($smtp_config['to_email']) . "<br>";
    
} catch (Exception $e) {
    echo "❌ ERRO ao carregar config.php: " . $e->getMessage() . "<br>";
    exit;
}

// 3. Verificar extensões PHP
echo "<h3>3. Extensões PHP</h3>";
$required_extensions = ['openssl', 'mbstring', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ $ext<br>";
    } else {
        echo "❌ $ext (FALTANDO)<br>";
    }
}

// 4. Testar função mail básica
echo "<h3>4. Teste função mail()</h3>";
$test_to = $smtp_config['to_email'];
$test_subject = 'Teste básico - ' . date('Y-m-d H:i:s');
$test_message = 'Teste de envio básico do PHP mail()';
$test_headers = 'From: ' . $smtp_config['username'];

if (mail($test_to, $test_subject, $test_message, $test_headers)) {
    echo "✅ Função mail() funcionou<br>";
} else {
    echo "❌ Função mail() falhou<br>";
}

// 5. Verificar se há erro específico no send-email.php
echo "<h3>5. Testar send-email.php (GET)</h3>";
try {
    // Simular um GET request para ver se há erro de sintaxe
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    include 'send-email.php';
    $output = ob_get_clean();
    echo "✅ send-email.php não tem erro de sintaxe<br>";
    echo "Resposta: " . htmlspecialchars($output) . "<br>";
} catch (Exception $e) {
    echo "❌ ERRO em send-email.php: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Informações do Servidor</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

?>