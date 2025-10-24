# 🚀 Guia de Instalação - Sistema de Reserva de Ocupação

## 📋 Pré-requisitos

### Software Necessário

- **PHP 7.4+** (recomendado PHP 8.0+)
- **MySQL 5.7+** ou **MariaDB 10.3+**
- **Apache** ou **Nginx**
- **Composer** (opcional, para dependências futuras)

### Extensões PHP Necessárias

- `pdo`
- `pdo_mysql`
- `session`
- `json`
- `mbstring` (recomendado)

## 🔧 Instalação Passo a Passo

### 1. Preparar o Ambiente

#### Windows (XAMPP/WAMP)

1. Baixe e instale o XAMPP
2. Inicie o Apache e MySQL
3. Acesse `http://localhost/phpmyadmin`

#### Linux (Ubuntu/Debian)

```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-json php-mbstring
sudo systemctl start apache2 mysql
sudo systemctl enable apache2 mysql
```

#### macOS (MAMP/Homebrew)

```bash
brew install php mysql apache
# ou use MAMP
```

### 2. Configurar o Banco de Dados

#### Criar o Banco

1. Acesse o phpMyAdmin ou MySQL CLI
2. Execute o script SQL:

```sql
-- Via phpMyAdmin: Importar -> Escolher arquivo -> schema.sql
-- Via CLI:
mysql -u root -p < schema.sql
```

#### Verificar a Criação

```sql
USE reservas_ocupacao;
SHOW TABLES;
-- Deve mostrar: usuarios, reservas
```

### 3. Configurar o Projeto

#### Estrutura de Pastas

```
/var/www/html/reservas_ocupacao/  (Linux)
C:\xampp\htdocs\reservas_ocupacao\ (Windows)
```

#### Permissões (Linux)

```bash
sudo chown -R www-data:www-data /var/www/html/reservas_ocupacao/
sudo chmod -R 755 /var/www/html/reservas_ocupacao/
```

### 4. Configurar Conexão com Banco

Edite o arquivo `config/database.php`:

```php
<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost');        // Seu host
define('DB_NAME', 'reservas_ocupacao'); // Nome do banco
define('DB_USER', 'root');              // Seu usuário
define('DB_PASS', 'sua_senha');        // Sua senha
```

### 5. Testar a Instalação

1. Acesse: `http://localhost/reservas_ocupacao/`
2. Verifique se a página carrega sem erros
3. Teste o login com as credenciais padrão

## 🔐 Contas Padrão

### Administrador

- **Email**: admin@senac.com
- **Senha**: admin123

### Funcionário de Teste

- **Email**: joao.silva@senac.com
- **Senha**: funcionario123

## 🛠️ Configurações Avançadas

### Configuração de Email (Futuro)

Para notificações por email, configure em `config/email.php`:

```php
<?php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'seu-email@gmail.com');
define('SMTP_PASS', 'sua-senha-app');
```

### Configuração de Logs

Crie a pasta `logs/` e configure em `config/logging.php`:

```php
<?php
define('LOG_LEVEL', 'INFO');
define('LOG_FILE', 'logs/sistema.log');
```

### Configuração de Backup

Configure backup automático do banco:

```bash
# Script de backup (backup.sh)
#!/bin/bash
mysqldump -u root -p reservas_ocupacao > backup_$(date +%Y%m%d_%H%M%S).sql
```

## 🚨 Solução de Problemas

### Erro de Conexão com Banco

```
Erro: SQLSTATE[HY000] [2002] No such file or directory
```

**Solução**: Verifique se o MySQL está rodando e as credenciais estão corretas.

### Erro 500 - Internal Server Error

```
Fatal error: Uncaught PDOException
```

**Solução**:

1. Verifique as permissões dos arquivos
2. Confirme se as extensões PHP estão instaladas
3. Verifique os logs de erro do Apache

### Página em Branco

**Solução**:

1. Ative a exibição de erros no PHP
2. Verifique se todos os arquivos foram copiados
3. Confirme se o Apache está rodando

### Problemas de Sessão

```
Warning: session_start(): Cannot send session cookie
```

**Solução**:

1. Verifique se as sessões estão habilitadas no PHP
2. Confirme as permissões da pasta de sessões
3. Limpe o cache do navegador

## 🔒 Segurança

### Configurações Recomendadas

#### PHP (php.ini)

```ini
display_errors = Off
log_errors = On
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

#### Apache (.htaccess)

```apache
# Já incluído no arquivo .htaccess do projeto
Header always set X-Frame-Options SAMEORIGIN
Header always set X-Content-Type-Options nosniff
```

#### MySQL

```sql
-- Criar usuário específico para a aplicação
CREATE USER 'reservas_user'@'localhost' IDENTIFIED BY 'senha_forte';
GRANT SELECT, INSERT, UPDATE, DELETE ON reservas_ocupacao.* TO 'reservas_user'@'localhost';
FLUSH PRIVILEGES;
```

## 📊 Monitoramento

### Logs do Sistema

- **Apache**: `/var/log/apache2/error.log`
- **PHP**: Configurado em `php.ini`
- **MySQL**: `/var/log/mysql/error.log`

### Métricas Importantes

- Número de usuários ativos
- Reservas por dia/semana
- Tempo de resposta das páginas
- Uso de memória do PHP

## 🔄 Atualizações

### Backup Antes de Atualizar

```bash
# Backup do banco
mysqldump -u root -p reservas_ocupacao > backup_antes_atualizacao.sql

# Backup dos arquivos
tar -czf backup_arquivos_$(date +%Y%m%d).tar.gz /var/www/html/reservas_ocupacao/
```

### Processo de Atualização

1. Fazer backup completo
2. Baixar nova versão
3. Substituir arquivos (exceto config/)
4. Executar scripts de migração (se houver)
5. Testar funcionalidades
6. Restaurar backup se necessário

## 📞 Suporte

### Logs de Erro Comuns

- **Erro 404**: Verificar rotas e arquivos
- **Erro 500**: Verificar logs do servidor
- **Erro de banco**: Verificar conexão e credenciais
- **Erro de sessão**: Verificar configurações PHP

### Contatos

- **Desenvolvedor**: [seu-email@senac.com]
- **Suporte Técnico**: [suporte@senac.com]
- **Documentação**: [link-para-docs]

---

**Sistema desenvolvido para o Senac** 🎓
