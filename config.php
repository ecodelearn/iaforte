<?php
// Função para carregar variáveis do arquivo .env
function loadEnv($filepath) {
    if (!file_exists($filepath)) {
        return false;
    }
    
    $lines = file($filepath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Ignora comentários
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    
    return true;
}

// Carregar variáveis de ambiente
$envLoaded = loadEnv(__DIR__ . '/.env');

// Função para obter variável de ambiente com fallback
function getEnvVar($key, $default = null) {
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
    return $value !== false ? $value : $default;
}

// Configurações SMTP
$smtp_config = [
    'host' => getEnvVar('SMTP_HOST', 'mail.iaforte.com.br'),
    'port' => (int)getEnvVar('SMTP_PORT', 465),
    'username' => getEnvVar('SMTP_USERNAME', 'contato@iaforte.com.br'),
    'password' => getEnvVar('SMTP_PASSWORD', ''),
    'to_email' => getEnvVar('TO_EMAIL', 'contato@iaforte.com.br')
];

// Verificar se as configurações estão completas
if (empty($smtp_config['password'])) {
    error_log('AVISO: Senha SMTP não configurada no arquivo .env');
}
?>