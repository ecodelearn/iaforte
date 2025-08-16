# Deploy Automático com GitHub Actions

Este projeto está configurado para deploy automático via FTP usando GitHub Actions.

## 🔧 Configuração dos Secrets

Para o deploy funcionar, você precisa configurar os seguintes secrets no GitHub:

### 1. Acessar Configurações do Repositório
1. Vá para o repositório no GitHub
2. Clique em **Settings** (Configurações)
3. No menu lateral, clique em **Secrets and variables** > **Actions**
4. Clique em **New repository secret**

### 2. Adicionar os Secrets

Use as informações do seu arquivo `.env` local para configurar os secrets:

| Nome do Secret | Valor (do .env) | Descrição |
|----------------|-----------------|-----------|
| `FTP_SERVER` | ftp.iaforte.com.br | Servidor FTP |
| `FTP_USERNAME` | iafortec | Usuário FTP |
| `FTP_PASSWORD` | `[sua_senha_ftp]` | Senha do FTP (não está no .env) |
| `FTP_PORT` | 21 | Porta FTP |

**⚠️ Importante**: A senha FTP não está no arquivo `.env` por segurança. Você precisa obtê-la do seu provedor de hospedagem.

### 3. Como Adicionar Cada Secret

1. Clique em **New repository secret**
2. Digite o **Name** (ex: `FTP_SERVER`)
3. Digite o **Value** (ex: `ftp.iaforte.com.br`)
4. Clique em **Add secret**
5. Repita para todos os 4 secrets

## 🚀 Como Funciona

### Deploy Automático
- **Trigger**: A cada push na branch `main`
- **Ação**: Faz upload de todos os arquivos para o servidor FTP
- **Exclusões**: .git, node_modules, README.md, .env, DEPLOY.md

### Deploy Manual
Você também pode executar o deploy manualmente:
1. Vá para **Actions** no GitHub
2. Selecione **Deploy to FTP**
3. Clique em **Run workflow**
4. Clique em **Run workflow** novamente

## 📁 Estrutura de Deploy

O deploy enviará estes arquivos para a pasta `public_html/` do servidor:
```
/public_html/
├── index.html          # Página principal
├── style.css           # Estilos
├── script.js           # JavaScript
└── blueprint.md        # Blueprint do projeto
```

## 🔍 Monitoramento

### Verificar Status do Deploy
1. Vá para a aba **Actions** no GitHub
2. Veja o status do último deploy
3. Clique no workflow para ver detalhes e logs

### Logs do Deploy
- ✅ Verde: Deploy bem-sucedido
- ❌ Vermelho: Deploy falhou
- 🟡 Amarelo: Deploy em andamento

## 🛠️ Troubleshooting

### Problemas Comuns

1. **"Authentication failed"**
   - Verifique se `FTP_USERNAME` e `FTP_PASSWORD` estão corretos
   - Confirme se o usuário tem permissões de escrita

2. **"Connection failed"**
   - Verifique se `FTP_SERVER` está correto
   - Confirme se `FTP_PORT` está correto (geralmente 21)

3. **"Permission denied"**
   - Verifique se o usuário FTP tem permissões de escrita
   - Confirme se o diretório de destino existe

## 🔒 Segurança

- ✅ Credenciais FTP armazenadas como secrets (criptografadas)
- ✅ Arquivo .env mantido local e no .gitignore
- ✅ Apenas arquivos necessários são enviados
- ✅ Logs não expõem credenciais

## 📝 Arquivo .env Local

Mantenha um arquivo `.env` local (não versionado) com:
```
# Configurações do FTP
FTP_USERNAME=iafortec
FTP_SERVER=ftp.iaforte.com.br
FTP_PORT=21

# Git Setup
user.name "Daniel Dias"
user.email "ecodelearn@outlook.com"
```

**⚠️ Este arquivo nunca será enviado para o GitHub devido ao .gitignore**