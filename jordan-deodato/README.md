# Sistema de Recrutamento

Documentação para instalação e execução do sistema de gerenciamento de vagas e candidaturas.

## 🚀 Instalação

1. Clone o repositório:
```bash
git clone [url-do-repositorio]
cd [nome-da-pasta]
```

2. Instale as dependências:
```bash
composer install
```

3. Configure o ambiente:
```bash
cp .env.example .env (Se já possuir o arquivo .env é só alterar para inserir suas informações)
```

4. Configure as variáveis de banco de dados no .env:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=root
DB_PASSWORD=
```

## 🛠️ Configuração do Banco de Dados
4. Execute o comando personalizado para configurar o banco:
```bash
php artisan setup:database
```
Este comando irá:
Criar o banco de dados principal
Criar o banco de dados de teste
Executar todas as migrations
Rodar os seeders

## 📊 Importação de Dados
5. Para importar dados de um arquivo CSV:
```bash
php artisan import:csv caminho/para/arquivo.csv
```

## 🔐 Autenticação
6. O sistema usa Sanctum para autenticação. Rotas protegidas requerem token.
Utilize o Postman, Insomnia ou outra ferramenta de acessos de API.
Login:
```text
POST /api/login
```
Login para recrutador
```bash
{
    "email": "recrutador@teste.com",
    "password": "Recrutador12345@"
}
```
Login para candidato
```bash
{
    "email": "candidato@teste.com",
    "password": "Candidato12345@"
}
```
Logout (autenticado):
```bash
POST /api/logout
```
Header:
```bash
Authorization: Bearer [token]
```

## 📚 Rotas da API
## 👤 Usuários
7. Rotas para acessar as funcionalidades da aplicação (protegidas por autenticação)

```text
GET /api/user - Listar usuários
```
```text
GET /api/user/{uuid} - Mostrar usuário
```
```text
POST /api/user - Criar usuário
```
```text
PUT /api/user/{uuid} - Atualizar usuário
```
```text
DELETE /api/user/{uuid} - Deletar usuário
```
```text
DELETE /api/user/delete-all - Deletar todos usuários
```
```text
DELETE /api/user/delete-by-uuid - Deletar por lista de UUIDs
```

## 🏷️ Tipos de Usuário
```text
GET /api/user-type - Listar tipos
```
```text
GET /api/user-type/{uuid} - Mostrar tipo
```

## 💼 Vagas
```text
GET /api/vacancy - Listar vagas
```
```text
GET /api/vacancy/{uuid} - Mostrar vaga
```
```text
POST /api/vacancy - Criar vaga
```
```text
PUT /api/vacancy/{uuid} - Atualizar vaga
```
```text
PUT /api/vacancy/close/{uuid} - Fechar vaga
```
```text
DELETE /api/vacancy/{uuid} - Deletar vaga
```
```text
DELETE /api/vacancy/delete-all - Deletar todas vagas
```
```text
DELETE /api/vacancy/delete-by-uuid - Deletar por lista de UUIDs
```

## 🏷️ Tipos de Vaga
```text
GET /api/vacancy-type - Listar tipos
```
```text
GET /api/vacancy-type/{uuid} - Mostrar tipo
```

## 📝 Candidaturas
```text
GET /api/application - Listar candidaturas
```
```text
GET /api/application/{uuid} - Mostrar candidatura
```
```text
POST /api/application - Criar candidatura
```
```text
PUT /api/application/{uuid} - Atualizar candidatura
```
```text
DELETE /api/application/{uuid} - Deletar candidatura
```
```text
DELETE /api/application/delete-all - Deletar todas candidaturas
```
```text
DELETE /api/application/delete-by-uuid - Deletar por lista de UUIDs
```

## 👨‍💼 Candidatos
```text
GET /api/candidate - Listar candidatos
```
```text
GET /api/candidate/{uuid} - Mostrar candidato
```
```text
POST /api/candidate - Criar candidato
```
```text
PUT /api/candidate/{uuid} - Atualizar candidato
```
```text
DELETE /api/candidate/{uuid} - Deletar candidato
```
```text
DELETE /api/candidate/delete-all - Deletar todos candidatos
```
```text
DELETE /api/candidate/delete-by-uuid - Deletar por lista de UUIDs
```

## 📊 CSV
```text
GET /api/csv/analyze - Analisar dados CSV
```

🧪 Testes
8. Para executar os testes:
```text
# Rodar todos os testes
php artisan test

# Rodar testes específicos
php artisan test --filter NomeDoTeste

# Gerar relatório de cobertura
php artisan test --coverage-html coverage-report
```

## ⚙️ Variáveis de Ambiente Importantes
```bash
QUEUE_CONNECTION=database # Para processamento de CSV em background
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1 # Domínios para autenticação
```
