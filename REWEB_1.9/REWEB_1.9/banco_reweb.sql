-- ============================================================
-- BANCO TEMPORÁRIO DO SITE CORISCO'S PAPER (só para testar)
-- Não é o banco final da Júlia, é só para o site funcionar de verdade.
--
-- Como usar: rodar este arquivo INTEIRO no phpMyAdmin (aba SQL) ou no Workbench.
-- Pode rodar de novo quando quiser: ele apaga tudo e cria do zero
-- (as contas e os dados de teste são apagados junto).
-- ============================================================

CREATE DATABASE IF NOT EXISTS test DEFAULT CHARACTER SET utf8mb4;
USE test;

-- Apaga as tabelas antigas. A ordem importa: primeiro as que dependem das outras
DROP TABLE IF EXISTS historico;
DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS carrinho;
DROP TABLE IF EXISTS favoritos;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS topicos;
DROP TABLE IF EXISTS login;


-- ------------------------------------------------------------
-- 1) LOGIN: as contas dos clientes (a mesma tabela que o João já usa)
-- E-mail e telefone não podem repetir (dá para entrar no site com qualquer um dos dois)
-- As contas de ADM ficam no arquivo Adiministracao/adm.php, não aqui
-- ------------------------------------------------------------
CREATE TABLE login (
  id INT NOT NULL AUTO_INCREMENT,
  telefone VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY email (email),
  UNIQUE KEY telefone (telefone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 2) TOPICOS: as categorias (Brindes, Topper, Papel arroz...)
-- ------------------------------------------------------------
CREATE TABLE topicos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 3) PRODUTOS: o que a Júlia vende (tela "Inserir produto")
-- tipo: 'encomenda' (feito sob pedido) ou 'pronta_entrega' (já está pronto)
-- fotos: guarda só o caminho da imagem, por exemplo imagens/produto-exemplo.jpg
-- ------------------------------------------------------------
CREATE TABLE produtos (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(150) NOT NULL,
  tema VARCHAR(100),
  preco DECIMAL(10,2) NOT NULL,
  especificacoes TEXT,
  id_topico INT,
  tipo VARCHAR(20) NOT NULL DEFAULT 'encomenda',
  cores_disponiveis VARCHAR(255),
  foto1 VARCHAR(255),
  foto2 VARCHAR(255),
  foto3 VARCHAR(255),
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  FOREIGN KEY (id_topico) REFERENCES topicos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 4) FAVORITOS: só quem tem conta favorita (um favorito por produto)
-- O número de corações do produto é a contagem desta tabela
-- ------------------------------------------------------------
CREATE TABLE favoritos (
  id INT NOT NULL AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_produto INT NOT NULL,
  data_favorito DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY um_favorito_por_produto (id_usuario, id_produto),
  FOREIGN KEY (id_usuario) REFERENCES login(id) ON DELETE CASCADE,
  FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 5) CARRINHO: reserva para o futuro (ainda NÃO é usada)
-- Por enquanto o carrinho de todo mundo fica guardado na sessão do navegador.
-- Esta tabela serviria para guardar o carrinho de quem tem conta
-- Os campos do meio são o que a pessoa escreve na tela do produto
-- ------------------------------------------------------------
CREATE TABLE carrinho (
  id INT NOT NULL AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_produto INT NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  tamanho VARCHAR(100),
  idade VARCHAR(20),
  nome_escrito VARCHAR(100),
  tema VARCHAR(100),
  cor VARCHAR(50),
  observacoes VARCHAR(255),
  arte VARCHAR(255),
  PRIMARY KEY (id),
  FOREIGN KEY (id_usuario) REFERENCES login(id) ON DELETE CASCADE,
  FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 6) PEDIDOS: um pedido por compra (a tabela "tipo Excel" do ADM)
-- id_usuario fica vazio (NULL) quando a pessoa comprou sem conta
-- criado_por: 'site' (o cliente fez) ou 'adm' (a Júlia anotou na mão)
-- forma_entrega: 'retirada' (pede só o telefone) ou 'entrega' (pede o endereço)
-- pago / pronto / buscou: 0 = não, 1 = sim
-- ------------------------------------------------------------
CREATE TABLE pedidos (
  id INT NOT NULL AUTO_INCREMENT,
  id_usuario INT,
  criado_por VARCHAR(10) NOT NULL DEFAULT 'site',
  cliente VARCHAR(150) NOT NULL,
  telefone VARCHAR(100),
  email VARCHAR(100),
  forma_entrega VARCHAR(20) NOT NULL DEFAULT 'retirada',
  endereco VARCHAR(255),
  data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
  prazo DATE,
  pago TINYINT(1) NOT NULL DEFAULT 0,
  pronto TINYINT(1) NOT NULL DEFAULT 0,
  buscou TINYINT(1) NOT NULL DEFAULT 0,
  observacoes VARCHAR(255),
  PRIMARY KEY (id),
  FOREIGN KEY (id_usuario) REFERENCES login(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 7) ITENS_PEDIDO: cada produto dentro de um pedido
-- O nome e o valor são copiados para cá, assim o pedido não muda se o produto mudar
-- precisa_arte: 1 quando o cliente ainda precisa enviar a arte (filtro "Enviar arte")
-- O total da linha é valor * quantidade (calculado na hora, não fica guardado)
-- ------------------------------------------------------------
CREATE TABLE itens_pedido (
  id INT NOT NULL AUTO_INCREMENT,
  id_pedido INT NOT NULL,
  id_produto INT,
  produto VARCHAR(150) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  tema VARCHAR(100),
  tamanho VARCHAR(100),
  idade VARCHAR(20),
  nome_escrito VARCHAR(100),
  cor VARCHAR(50),
  observacoes VARCHAR(255),
  arte VARCHAR(255),
  precisa_arte TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE CASCADE,
  FOREIGN KEY (id_produto) REFERENCES produtos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
-- 8) HISTORICO: a caixinha amarela do painel ("Juliana adicionou encomenda...")
-- ------------------------------------------------------------
CREATE TABLE historico (
  id INT NOT NULL AUTO_INCREMENT,
  descricao VARCHAR(255) NOT NULL,
  data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
  id_pedido INT,
  PRIMARY KEY (id),
  FOREIGN KEY (id_pedido) REFERENCES pedidos(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
-- DADOS DE EXEMPLO (para testar). Pode apagar tudo daqui para baixo
-- ============================================================

INSERT INTO topicos (nome) VALUES
('Sublimação'), ('Brindes'), ('Papel arroz'), ('Encadernação'), ('Topper'), ('Louças'), ('Têxtil');

-- As fotos de exemplo são só duas, por enquanto todos usam elas
INSERT INTO produtos (nome, tema, preco, especificacoes, id_topico, tipo, cores_disponiveis, foto1, foto2, foto3) VALUES
('Topper Batman', 'Batman', 22.00, 'Topper em papel especial, acompanha o nome e a idade escolhidos.', 5, 'encomenda', 'Amarelo, Preto', 'imagens/produto-exemplo.jpg', 'imagens/produto-exemplo.jpg', 'imagens/produto-exemplo.jpg'),
('Topper cenário Harry Potter luxo', 'Harry Potter', 55.00, '3 torres/ 15cm/ gaiola cintilante/ papel premium/ modelagem', 5, 'encomenda', NULL, 'imagens/carrinho-exemplo.jpg', 'imagens/carrinho-exemplo.jpg', 'imagens/carrinho-exemplo.jpg'),
('Copo térmico personalizado', NULL, 65.00, 'Copo térmico com a arte que você escolher.', 6, 'pronta_entrega', 'Branco, Preto', 'imagens/produto-exemplo.jpg', NULL, NULL),
('Porta copos marcas de cerveja 4und', NULL, 20.00, 'Kit com 4 porta copos.', 2, 'pronta_entrega', NULL, 'imagens/produto-exemplo.jpg', NULL, NULL),
('Chaveiros mdf formatos', NULL, 8.00, 'Chaveiro em MDF no formato que você escolher.', 2, 'pronta_entrega', NULL, 'imagens/produto-exemplo.jpg', NULL, NULL),
('Papel de arroz', NULL, 17.00, 'Papel de arroz com a estampa do tema escolhido.', 3, 'encomenda', NULL, 'imagens/produto-exemplo.jpg', NULL, NULL),
('Bottom', NULL, 8.00, 'Bottom personalizado.', 2, 'encomenda', NULL, 'imagens/produto-exemplo.jpg', NULL, NULL);

-- Os 3 pedidos que aparecem na tela do administrador
INSERT INTO pedidos (criado_por, cliente, forma_entrega, data_pedido, prazo, pago, pronto, buscou, observacoes) VALUES
('adm',  'Jak Motta', 'retirada', '2026-10-22 13:00:00', '2026-12-10', 0, 0, 0, 'produzir'),
('site', 'Debby',     'retirada', '2026-10-22 10:00:00', '2026-12-10', 1, 0, 0, 'enviar arte'),
('adm',  'Juny',      'retirada', '2026-10-22 09:00:00', '2026-12-01', 0, 0, 1, 'pagamento pendente');

INSERT INTO itens_pedido (id_pedido, id_produto, produto, valor, quantidade, tema, precisa_arte) VALUES
(1, 1, 'Topper', 22.00, 1, 'Bluey', 0),
(2, 6, 'Papel arroz', 17.00, 2, 'Os Rosa', 1),
(3, 7, 'Bottom', 8.00, 2, 'Luna', 0);

INSERT INTO historico (descricao, data_hora, id_pedido) VALUES
('Juliana adicionou encomenda', '2026-10-22 08:00:00', 1),
('Jak Motta solicitou encomenda', '2026-10-22 13:00:00', 1);
