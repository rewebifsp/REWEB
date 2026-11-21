# REWEB — Corisco's Paper

Projeto acadêmico de loja virtual para a papelaria criativa Corisco's Paper.

## Funcionalidades

- Catálogo e pesquisa de produtos
- Carrinho e lista de favoritos
- Cadastro e login de clientes
- Área administrativa para acompanhar encomendas e produtos
- Recursos de acessibilidade com AccessibleWeb e VLibras nas páginas públicas

## Tecnologias

- PHP
- MySQL
- HTML, CSS e JavaScript
- XAMPP para execução local

## Como executar com XAMPP

1. Instale e abra o XAMPP.
2. Inicie **Apache** e **MySQL** no painel do XAMPP.
3. Baixe `REWEB_ULTIMA_1.10.zip` e extraia seu conteúdo para `C:\xampp\htdocs`.
4. O ZIP contém duas pastas chamadas `REWEB_1.10`. Abra a pasta interna e importe o arquivo `banco_reweb.sql` pelo phpMyAdmin (`http://localhost/phpmyadmin`).
5. Confira as credenciais locais do MySQL em `Adiministracao/conexao.php`.
6. Abra `http://localhost/REWEB_1.10/REWEB_1.10/` no navegador.

### Atenção ao banco de dados de teste

O arquivo `banco_reweb.sql` usa o banco `test` e apaga/recria as tabelas desse banco ao ser executado. Ele é destinado ao ambiente de teste; os dados existentes nessas tabelas serão substituídos.

## Estrutura da versão 1.10

- `Adiministracao/` — páginas e arquivos da área administrativa
- `Cadastro/` — cadastro, login e recuperação de senha
- `Carrinho/`, `Cobranca/` e `Favoritos/` — recursos de compra
- `Includes/` — componentes compartilhados
- `Pesquisa/`, `Produto/`, `ProntaEntrega/` e `Topicos/` — páginas de catálogo
- `imagens/` — imagens usadas pelo site
- `banco_reweb.sql` — estrutura e dados de demonstração do banco

Os widgets de acessibilidade carregam serviços externos e precisam de conexão com a internet.
