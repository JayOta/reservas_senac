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

# Sistema de Reserva de Ocupação - Senac

Um sistema web completo para gerenciamento de reservas de espaços, desenvolvido para o Senac com três níveis de acesso distintos.

## 🎯 Funcionalidades Principais

### 👨‍💼 Administrador

- Dashboard completo com estatísticas
- Aprovação/recusa de reservas
- Visualização de todas as solicitações
- Gerenciamento de usuários

### 👨‍💻 Funcionário

- Solicitação de reservas
- Acompanhamento do status
- Visualização do calendário
- Histórico pessoal

### 👤 Usuário Público

- Visualização da disponibilidade
- Calendário público
- Sem necessidade de login

## 🛠️ Tecnologias Utilizadas

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Servidor**: Apache/Nginx

## 📋 Pré-requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🚀 Instalação

### 1. Clone o Repositório

```bash
git clone [url-do-repositorio]
cd reservas_ocupacao
```

### 2. Configure o Banco de Dados

1. Crie um banco de dados MySQL
2. Execute o script `schema.sql` para criar as tabelas:

```sql
mysql -u root -p < schema.sql
```

### 3. Configure a Conexão

Edite o arquivo `config/database.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'reservas_ocupacao');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 4. Configure o Servidor Web

- Coloque os arquivos na pasta do servidor web
- Certifique-se de que o PHP está configurado corretamente
- Acesse via navegador: `http://localhost/reservas_ocupacao`

## 👥 Contas de Teste

### Administrador

- **Email**: admin@senac.com
- **Senha**: admin123

### Funcionário

- **Email**: joao.silva@senac.com
- **Senha**: funcionario123

## 📁 Estrutura do Projeto

```
reservas_ocupacao/
├── admin/                  # Área administrativa
│   └── dashboard.php
├── ajax/                   # Endpoints AJAX
│   ├── approve_reservation.php
│   ├── reject_reservation.php
│   └── calendar.php
├── config/                 # Configurações
│   └── database.php
├── css/                    # Estilos
│   └── style.css
├── funcionario/            # Área do funcionário
│   └── dashboard.php
├── includes/              # Classes e funções
│   ├── auth.php
│   ├── reserva.php
│   └── logout.php
├── js/                     # JavaScript
│   └── main.js
├── public/                 # Área pública
│   └── calendario.php
├── index.php              # Página inicial
├── login.php              # Login
├── register.php            # Cadastro
├── schema.sql             # Script do banco
└── README.md              # Este arquivo
```

## 🎨 Design e Interface

### Características Visuais

- Design moderno e profissional
- Paleta de cores neutra (facilmente customizável)
- Totalmente responsivo
- Animações suaves e transições
- Interface intuitiva

### Cores Principais

- **Primária**: #2c3e50 (Azul escuro)
- **Secundária**: #3498db (Azul)
- **Sucesso**: #27ae60 (Verde)
- **Aviso**: #f39c12 (Laranja)
- **Perigo**: #e74c3c (Vermelho)

## 🔧 Funcionalidades Técnicas

### Sistema de Autenticação

- Login seguro com hash de senhas
- Controle de sessões
- Diferentes níveis de acesso
- Proteção contra acesso não autorizado

### Gerenciamento de Reservas

- Verificação de conflitos
- Status: Pendente, Aprovada, Recusada
- Histórico completo
- Observações do administrador

### Calendário Interativo

- Visualização mensal
- Navegação entre meses
- Cores diferenciadas por status
- Responsivo para mobile

## 📱 Responsividade

O sistema é totalmente responsivo e funciona perfeitamente em:

- **Desktop**: Interface completa
- **Tablet**: Layout adaptado
- **Mobile**: Interface otimizada

## 🔒 Segurança

- Senhas criptografadas com `password_hash()`
- Validação de dados de entrada
- Proteção contra SQL Injection (PDO)
- Controle de sessões
- Sanitização de outputs

## 🚀 Como Usar

### Para Administradores

1. Faça login com as credenciais de admin
2. Acesse o dashboard administrativo
3. Visualize e gerencie todas as reservas
4. Aprove ou recuse solicitações pendentes

### Para Funcionários

1. Cadastre-se ou faça login
2. Acesse seu dashboard pessoal
3. Solicite novas reservas
4. Acompanhe o status das suas solicitações

### Para Usuários Públicos

1. Acesse a página pública
2. Visualize o calendário de disponibilidade
3. Veja quais horários estão ocupados

## 🛠️ Personalização

### Cores e Logo

Para personalizar as cores e adicionar o logo do Senac:

1. **Cores**: Edite as variáveis CSS em `css/style.css`:

```css
:root {
  --primary-color: #sua-cor-primaria;
  --secondary-color: #sua-cor-secundaria;
  /* ... */
}
```

2. **Logo**: Substitua o texto do logo em:
   - `index.php` (linha do logo)
   - `login.php`
   - `register.php`
   - Outras páginas conforme necessário

### Configurações do Banco

Edite `config/database.php` para suas configurações específicas.

## 🐛 Solução de Problemas

### Erro de Conexão com Banco

- Verifique as credenciais em `config/database.php`
- Certifique-se de que o MySQL está rodando
- Verifique se o banco foi criado corretamente

### Problemas de Sessão

- Verifique se as sessões estão habilitadas no PHP
- Limpe o cache do navegador
- Verifique as permissões dos arquivos

### Erro 500

- Verifique os logs de erro do servidor
- Certifique-se de que todas as extensões PHP estão instaladas
- Verifique as permissões dos arquivos

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema:

- Verifique a documentação
- Consulte os logs de erro
- Entre em contato com a equipe de desenvolvimento

## 📄 Licença

Este projeto foi desenvolvido especificamente para o Senac. Todos os direitos reservados.

---

**Desenvolvido com ❤️ para o Senac**
