<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug CSRF Token</h2>";

// 1. Testar sessões PHP
echo "<h3>1. Teste Sessões PHP</h3>";
try {
    session_start();
    echo "✅ session_start() funcionou<br>";
    
    $_SESSION['test'] = 'valor_teste';
    echo "✅ Escrita na sessão funcionou<br>";
    
    echo "Session ID: " . session_id() . "<br>";
    echo "Session status: " . session_status() . "<br>";
    
} catch (Exception $e) {
    echo "❌ ERRO em sessões: " . $e->getMessage() . "<br>";
}

// 2. Testar random_bytes
echo "<h3>2. Teste random_bytes</h3>";
try {
    $token = bin2hex(random_bytes(32));
    echo "✅ random_bytes funcionou<br>";
    echo "Token gerado: " . substr($token, 0, 20) . "...<br>";
} catch (Exception $e) {
    echo "❌ ERRO em random_bytes: " . $e->getMessage() . "<br>";
}

// 3. Teste JSON
echo "<h3>3. Teste JSON</h3>";
try {
    $data = ['test' => 'value'];
    $json = json_encode($data);
    echo "✅ json_encode funcionou<br>";
    echo "JSON: " . $json . "<br>";
} catch (Exception $e) {
    echo "❌ ERRO em JSON: " . $e->getMessage() . "<br>";
}

// 4. Simular get-csrf.php
echo "<h3>4. Simular get-csrf.php</h3>";
try {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    $response = ['csrf_token' => $_SESSION['csrf_token']];
    echo "✅ Lógica CSRF funcionou<br>";
    echo "Response: " . json_encode($response) . "<br>";
    
} catch (Exception $e) {
    echo "❌ ERRO na lógica CSRF: " . $e->getMessage() . "<br>";
}

echo "<h3>5. Headers Test</h3>";
echo "Headers podem ser enviados: " . (headers_sent() ? 'NÃO' : 'SIM') . "<br>";

?>