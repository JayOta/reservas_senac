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
