# LogiFlow — Gerenciador Logístico

Projeto acadêmico da disciplina Fundamentos de Programação para Internet (UNIPAR EAD).

## Tecnologias
- PHP 8+
- MySQL/MariaDB
- HTML, CSS, JavaScript
- Bootstrap 5 (CDN)
- PDO para acesso ao banco

## Instalação no XAMPP
1. Copie a pasta `LogiFlow` para `C:\xampp\htdocs\`.
2. Inicie Apache e MySQL no painel do XAMPP.
3. Abra `http://localhost/phpmyadmin`.
4. Importe o arquivo `database/logiflow.sql`.
5. Confira usuário e senha em `config/database.php` (padrão XAMPP: usuário `root`, senha vazia).
6. Acesse `http://localhost/LogiFlow/`.

## Funcionalidades desta primeira versão
- Dashboard com indicadores.
- Cadastro, listagem, edição e exclusão de produtos, endereços e transportadores.
- Geração de frete com um transportador, origem, destino e vários produtos.
- Relatório diário de fretes com exportação CSV.

## Estrutura
- `config/`: conexão com o banco.
- `includes/`: cabeçalho, menu e rodapé.
- `database/`: script SQL.
- `assets/`: CSS e JavaScript.
- `products.php`, `addresses.php`, `carriers.php`: cadastros.
- `freights.php`: criação e listagem de fretes.
- `reports.php`: consulta e exportação por data.

## Observações
Os dados de demonstração são fictícios. O projeto é uma base acadêmica e deve ser testado localmente antes da apresentação.
