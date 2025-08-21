# Resolução de Problemas de Email - IA FORTE

## 📧 Arquitetura do Sistema de Email

### Fluxo de Envio
1. **Frontend** → `script.js` captura dados do formulário
2. **CSRF Token** → `no-json-csrf.php` gera token de segurança
3. **Processamento** → `no-json-email.php` valida e envia email
4. **SMTP** → Servidor de email processa e encaminha
5. **Forward** → Email chega ao destino final via forward

### Arquivos Envolvidos
- `index.html` - Formulário de contato
- `script.js` - Validação frontend e envio AJAX
- `no-json-csrf.php` - Geração de token CSRF
- `no-json-email.php` - Processamento e envio principal
- `config.php` - Configurações SMTP e variáveis de ambiente
- `.env` - Credenciais sensíveis (não versionado)

## 🚨 Problema: Classificação como rSPAM

### Sintomas Identificados
```
550 This message was classified as rSPAM and may not be delivered
Event: failure error
Score: 27.37 (muito alto)
```

### Análise de Triggers de Spam
Principais triggers identificados na análise detalhada:

#### **Críticos (Score Alto)**
- `SRS_POSS_SPAM_RUN_3_STRICT=10` 
- `FLAGGING_SUSPICIOUS_480=5`
- `FLAGGING_SUSPICIOUS_321=3` 
- `FROM_NEQ_ENVFROM=2`
- `FREEMAIL_REPLYTO_NEQ_FROM=2` ⚠️ **RESOLVIDO**
- `NEURAL_SPAM=1` (IA classificou como spam)

#### **Menores**
- `INSECURE_PHP=0.5`
- `ISSRS=0.5` 
- `RCVD_NO_TLS_LAST=0.1`

## ✅ Soluções Implementadas

### 1. **Headers Otimizados**
```php
// Headers finais que funcionam
$headers = array();
$headers[] = 'From: IA Forte <' . $smtp_username . '>';
// Reply-To REMOVIDO - causava FREEMAIL_REPLYTO_NEQ_FROM=2
$headers[] = 'Return-Path: ' . $smtp_username;
$headers[] = 'Message-ID: <' . time() . '.1b9f07f95d0a2717c1faa75fa7f57afe@' . $domain . '>';
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'X-Priority: 3';
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'Content-Transfer-Encoding: 8bit';
```

### 2. **Message-ID Fixo**
- **Problema**: IDs únicos eram suspeitos
- **Solução**: Usar hash fixo `1b9f07f95d0a2717c1faa75fa7f57afe` como no teste que funcionou

### 3. **Remoção de Headers Problemáticos**
- **Reply-To**: Removido completamente (trigger crítico)
- **Sender**: Removido (não estava no teste que funcionou)
- **Headers extras**: Eliminados todos os headers "anti-spam" que na verdade causavam suspeita

### 4. **Corpo do Email Simplificado**
```php
// Formato que funcionou
$body = "NOVA SOLICITAÇÃO DE CONTATO\n";
$body .= "==============================\n\n";
// ... resto igual ao test-email-antispam.php que passou
```

## 📊 Comparação: Antes vs Depois

### **ANTES (Score: 27.37)**
```
Reply-To: Nome Cliente <email@cliente.com>  ❌ +2 pontos
Sender: <servidor@host.com>                 ❌ Suspeito  
Message-ID: <hash-único-sempre-diferente>   ❌ Padrão suspeito
Headers complexos com 15+ campos            ❌ Over-engineering
```

### **DEPOIS (Score: Aprovado)**  
```
From: IA Forte <contato@iaforte.com.br>     ✅ Consistente
Return-Path: contato@iaforte.com.br         ✅ Mesmo domínio
Message-ID: <time.hash-fixo@iaforte.com.br> ✅ Padrão reconhecido
Headers mínimos (8 campos)                  ✅ Simplicidade
```

## 🔍 Metodologia de Debug

### 1. **Teste de Controle**
- Arquivo `test-email-antispam.php` funcionando ✅
- Usado como referência para comparação

### 2. **Análise de Logs**
- URL de análise: `https://mail.outboundspamprotection.com/mailinfo?id=...`
- Scores detalhados de cada trigger
- Identificação de padrões problemáticos

### 3. **Iteração Baseada em Dados**
- Cada commit baseado em trigger específico identificado
- Testes incrementais para validar melhorias
- Comparação direta com formato que funcionava

## 🛠️ Scripts de Teste

### Debug Form
```php
// debug-form.php - Para testar dados do formulário
// Mostra exatamente que dados estão sendo recebidos
```

### Email Antispam Test  
```php
// test-email-antispam.php - Controle que funciona
// Usado como padrão de referência
```

## 📋 Checklist de Validação

Antes de modificar o sistema de email, verificar:

- [ ] Headers mínimos (máximo 8-10 campos)
- [ ] Sem Reply-To com domínio diferente do From
- [ ] Message-ID consistente (não completamente aleatório)
- [ ] From e Return-Path do mesmo domínio
- [ ] Corpo em formato simples, não HTML
- [ ] Teste com `test-email-antispam.php` primeiro
- [ ] Análise de spam score se disponível

## 🎯 Resultado Final

**Status**: ✅ **RESOLVIDO**
- Emails passam pelo filtro anti-spam
- Forward funcionando corretamente  
- Score reduzido de 27.37 para aprovado
- Sistema estável e confiável

## 📝 Lições Aprendidas

1. **Simplicidade vence complexidade** - Headers mínimos são melhores
2. **Reply-To com domínio diferente** é trigger crítico de spam
3. **Padrões muito únicos** (like Message-IDs sempre diferentes) são suspeitos  
4. **Análise baseada em dados** é essencial vs tentativa e erro
5. **Ter um controle funcionando** acelera muito o debug

## 🔄 Manutenção Futura

- Monitorar logs de entrega periodicamente
- Se spam score aumentar, revisar este documento
- Manter `test-email-antispam.php` como referência
- Não adicionar headers "anti-spam" sem testar primeiro