-- Cria o banco de dados se ele não existir, utilizando um conjunto de caracteres
-- que suporta emojis e caracteres especiais (utf8mb4).
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados recém-criado para executar os comandos seguintes.

-- -----------------------------------------------------
-- Exclusão de Tabelas (em ordem reversa de dependência)
-- -----------------------------------------------------
DROP TABLE IF EXISTS Log;
DROP TABLE IF EXISTS Exercicio;
DROP TABLE IF EXISTS Equipamento;
DROP TABLE IF EXISTS Grupo_muscular;
DROP TABLE IF EXISTS Adm;
-- -----------------------------------------------------
-- Tabela: Adm
-- Armazena os dados dos administradores que gerenciam o conteúdo.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Adm (
  id_adm INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(30) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL, -- Armazenará a senha com hash (ex: bcrypt)
  tipo ENUM('adm', 'professor') NOT NULL DEFAULT 'professor' COMMENT 'Define o nível de permissão do usuário',
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  data_modificacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nome_adm (nome) -- Índice para buscas por nome
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela: Grupo_muscular
-- Tabela de domínio para os grupos musculares (Ex: Peitoral, Costas, Perna).
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Grupo_muscular (
  id_grupo_muscular INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(50) NOT NULL UNIQUE,
  regiao ENUM('superior', 'central', 'inferior') NOT NULL COMMENT 'Região corporal principal do grupo muscular',
  icone VARCHAR(255) NULL COMMENT 'Caminho ou identificador para o ícone do grupo',
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  data_modificacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela: Equipamento
-- Armazena os equipamentos da academia e itens gerais.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Equipamento (
  id_equipamento INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  qrcode_equipamento VARCHAR(3) UNIQUE NULL,
  id_adm_cadastro INT NOT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  data_modificacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nome_equipamento (nome), -- Índice para buscas por nome
  FOREIGN KEY (id_adm_cadastro) REFERENCES Adm(id_adm) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela: Exercicio
-- Tabela central que armazena todos os detalhes dos exercícios.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Exercicio (
  id_exercicio INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  descricao TEXT NOT NULL,
  avisos TEXT NULL, -- Pode ser nulo se não houver avisos.
  gif_path VARCHAR(255), -- Caminho para o arquivo GIF no servidor.
  id_grupo_muscular INT NOT NULL,
  id_equipamento INT NOT NULL,
  id_adm_cadastro INT NOT NULL,
  data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  data_modificacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_nome_exercicio (nome), -- Índice para buscas por nome
  FOREIGN KEY (id_grupo_muscular) REFERENCES Grupo_muscular(id_grupo_muscular) ON DELETE RESTRICT,
  FOREIGN KEY (id_equipamento) REFERENCES Equipamento(id_equipamento) ON DELETE RESTRICT,
  FOREIGN KEY (id_adm_cadastro) REFERENCES Adm(id_adm) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- Tabela: Log
-- Registra todas as ações de CRUD realizadas pelos administradores.
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Log (
  id_log INT PRIMARY KEY AUTO_INCREMENT,
  id_adm INT NOT NULL,
  acao VARCHAR(50) NOT NULL COMMENT 'Ex: INSERCAO, ATUALIZACAO, REMOCAO',
  tabela_afetada VARCHAR(50) NOT NULL COMMENT 'Ex: Exercicio, Equipamento',
  id_registro_afetado INT NULL COMMENT 'ID do registro na tabela_afetada',
  detalhes TEXT NULL COMMENT 'Descrição da alteração, ex: "Nome alterado de X para Y"',
  data_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_acao_log (acao), -- Índice para filtrar por tipo de ação
  FOREIGN KEY (id_adm) REFERENCES Adm(id_adm) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- -----------------------------------------------------
-- DADOS INICIAIS (SUGESTÃO)
-- -----------------------------------------------------

-- 1. Criar um administrador padrão
INSERT INTO Adm (nome, email, senha, tipo) VALUES ('Admin Padrão', 'admin@fitlib.com', '$2y$10$2Boe9V6v3L4tZlXbGrqO5eS4O/aX1YDEYVKy61K44y1PJ4sD5hOUK', 'adm');

-- 2. Criar o "equipamento" para exercícios livres
INSERT INTO Equipamento (nome, qrcode_equipamento, id_adm_cadastro) VALUES ('Peso Corporal / Livre', 'LIV', 1);

-- Inserir equipamentos adicionais
INSERT INTO Equipamento (nome, qrcode_equipamento, id_adm_cadastro) VALUES
('Halteres', 'HAL', 1),
('Barra com Anilhas', 'BAR', 1),
('Banco Regulável', 'BAN', 1),
('Máquina Smith', 'SMI', 1),
('Puxador Alto', 'PUX', 1),
('Remada Baixa', 'REM', 1),
('Leg Press 45°', 'LEG', 1),
('Cadeira Extensora', 'EXT', 1),
('Mesa Flexora', 'FLE', 1),
('Crossover', 'CRO', 1),
('Cadeira Abdutora', 'CAB', 1),
('Cadeira Adutora', 'CAD', 1),
('Máquina de Panturrilha', 'PAN', 1);

-- 3. Inserir uma lista inicial de grupos musculares
INSERT INTO Grupo_muscular (nome, regiao, icone) VALUES
('Peitoral', 'superior', 'public/uploads/icons/peitoral.png'),
('Costas', 'superior', 'public/uploads/icons/costas.png'),
('Ombros', 'superior', 'public/uploads/icons/ombros.png'),
('Bíceps', 'superior', 'public/uploads/icons/biceps.png'),
('Tríceps', 'superior', 'public/uploads/icons/triceps.png'),
('Abdômen', 'central', 'public/uploads/icons/abdomen.png'),
('Adutores', 'inferior', 'public/uploads/icons/adutores.png'),
('Abdutores', 'inferior', 'public/uploads/icons/abdutores.png'),
('Antebraço', 'superior', 'public/uploads/icons/antebraco.png'),
('Quadríceps', 'inferior', 'public/uploads/icons/quadriceps.png'),
('Posteriores', 'inferior', 'public/uploads/icons/posteriores.png'),
('Glúteo', 'inferior', 'public/uploads/icons/gluteo.png'),
('Lombar', 'central', 'public/uploads/icons/lombar.png'),
('Panturrilha', 'inferior', 'public/uploads/icons/panturrilha.png');

-- 4. Popular a tabela de exercícios com uma variedade de exemplos
-- Peitoral (ID: 1)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Supino Reto com Barra', 'Deitado no banco, desça a barra até o peito e empurre para cima.', 'Mantenha os cotovelos em um ângulo de 90 graus.', '/gifs/supino_reto_barra.gif', 1, 3, 1),
('Supino Inclinado com Halteres', 'Deitado no banco inclinado, levante os halteres acima do peito.', 'Não deixe os halteres se tocarem no topo.', '/gifs/supino_inclinado_halteres.gif', 1, 2, 1),
('Crucifixo com Halteres', 'Deitado no banco, abra e feche os braços com os halteres.', 'Mantenha uma leve flexão nos cotovelos.', '/gifs/crucifixo_halteres.gif', 1, 2, 1),
('Flexão de Braço', 'Com as mãos no chão, desça o corpo e empurre para cima.', 'Mantenha o corpo reto como uma prancha.', '/gifs/flexao_braco.gif', 1, 1, 1),
('Crucifixo no Crossover', 'Em pé, puxe as polias do crossover até que se cruzem na frente do corpo.', 'Concentre a força no peitoral.', '/gifs/crucifixo_crossover.gif', 1, 11, 1);

-- Costas (ID: 2)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Puxada Frontal no Puxador', 'Sentado, puxe a barra do puxador alto até a parte superior do peito.', 'Mantenha as costas retas e não use o impulso do corpo.', '/gifs/puxada_frontal.gif', 2, 6, 1),
('Remada Curvada com Barra', 'Incline o tronco para frente e puxe a barra em direção ao abdômen.', 'Mantenha a coluna neutra para evitar lesões.', '/gifs/remada_curvada.gif', 2, 3, 1),
('Remada Baixa (Triângulo)', 'Sentado na máquina, puxe a manopla em direção ao abdômen.', 'Estique completamente os braços na volta.', '/gifs/remada_baixa.gif', 2, 7, 1),
('Barra Fixa', 'Pendurado na barra, puxe o corpo para cima até o queixo passar da barra.', 'Pode ser feito com pegada pronada ou supinada.', '/gifs/barra_fixa.gif', 2, 1, 1),
('Remada Serrote com Halter', 'Apoiado no banco, puxe o halter para cima ao lado do tronco.', 'Mantenha o cotovelo próximo ao corpo.', '/gifs/remada_serrote.gif', 2, 2, 1);

-- Quadríceps (ID: 10)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Agachamento Livre com Barra', 'Com a barra nas costas, agache como se fosse sentar em uma cadeira.', 'Mantenha os joelhos alinhados com os pés. Foco principal em quadríceps e glúteos.', '/gifs/agachamento_livre.gif', 10, 3, 1),
('Leg Press 45°', 'Sentado na máquina, empurre a plataforma com os pés.', 'Não trave os joelhos no final do movimento.', '/gifs/leg_press.gif', 10, 8, 1),
('Cadeira Extensora', 'Sentado na máquina, estenda as pernas contra a resistência.', 'Exercício isolador para o quadríceps. Controle o movimento na subida e na descida.', '/gifs/cadeira_extensora.gif', 10, 9, 1),
('Afundo com Halteres', 'Dê um passo à frente e agache, alternando as pernas.', 'O joelho de trás deve quase tocar o chão.', '/gifs/afundo_halteres.gif', 10, 2, 1),
('Agachamento Búlgaro', 'Com um pé apoiado no banco, agache com a perna da frente.', 'Excelente para estabilidade e força unilateral.', '/gifs/agachamento_bulgaro.gif', 10, 4, 1);

-- Ombros (ID: 3)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Desenvolvimento com Halteres', 'Sentado, levante os halteres acima da cabeça.', 'Não deixe os halteres se tocarem.', '/gifs/desenvolvimento_halteres.gif', 3, 2, 1),
('Elevação Lateral com Halteres', 'Em pé, eleve os halteres lateralmente até a altura dos ombros.', 'Mantenha os cotovelos levemente flexionados.', '/gifs/elevacao_lateral.gif', 3, 2, 1),
('Elevação Frontal com Barra', 'Em pé, eleve a barra à frente do corpo até a altura dos ombros.', 'Evite balançar o corpo.', '/gifs/elevacao_frontal.gif', 3, 3, 1),
('Remada Alta', 'Puxe a barra ou halteres para cima, em direção ao queixo.', 'Mantenha os cotovelos mais altos que as mãos.', '/gifs/remada_alta.gif', 3, 3, 1),
('Crucifixo Invertido na Máquina', 'Sentado na máquina, abra os braços para trás contra a resistência.', 'Foque na contração dos músculos posteriores do ombro.', '/gifs/crucifixo_invertido_maquina.gif', 3, 11, 1);

-- Bíceps (ID: 4)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Rosca Direta com Barra', 'Em pé, flexione os cotovelos trazendo a barra para cima.', 'Não use o impulso das costas.', '/gifs/rosca_direta_barra.gif', 4, 3, 1),
('Rosca Alternada com Halteres', 'Sentado ou em pé, flexione um cotovelo de cada vez.', 'Gire o punho durante o movimento (supinação).', '/gifs/rosca_alternada.gif', 4, 2, 1),
('Rosca Martelo', 'Em pé, levante os halteres com uma pegada neutra (palmas para dentro).', 'Trabalha também o músculo braquial.', '/gifs/rosca_martelo.gif', 4, 2, 1),
('Rosca Scott com Barra W', 'Apoiado no banco Scott, flexione os cotovelos.', 'Isola o bíceps, evitando que outros músculos ajudem.', '/gifs/rosca_scott.gif', 4, 3, 1),
('Rosca Concentrada', 'Sentado, com o cotovelo apoiado na parte interna da coxa, flexione o braço.', 'Excelente para pico de bíceps.', '/gifs/rosca_concentrada.gif', 4, 2, 1);

-- Tríceps (ID: 5)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Tríceps Pulley com Barra Reta', 'Em pé, empurre a barra para baixo até estender completamente os cotovelos.', 'Mantenha os cotovelos fixos ao lado do corpo.', '/gifs/triceps_pulley.gif', 5, 6, 1),
('Tríceps Testa com Barra', 'Deitado, desça a barra em direção à testa e estenda os braços.', 'Use uma pegada fechada para segurança.', '/gifs/triceps_testa.gif', 5, 3, 1),
('Mergulho no Banco', 'Com as mãos apoiadas no banco, desça e suba o corpo.', 'Quanto mais esticadas as pernas, mais difícil.', '/gifs/mergulho_banco.gif', 5, 4, 1),
('Tríceps Francês com Halter', 'Sentado ou em pé, segure um halter com as duas mãos e estenda-o acima da cabeça.', 'Mantenha os cotovelos apontados para cima.', '/gifs/triceps_frances.gif', 5, 2, 1),
('Tríceps Coice com Halter', 'Inclinado, estenda o cotovelo para trás contra a gravidade.', 'Mantenha o braço paralelo ao chão.', '/gifs/triceps_coice.gif', 5, 2, 1);

-- Abdômen (ID: 6)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Abdominal Supra', 'Deitado, eleve o tronco em direção aos joelhos.', 'Não puxe o pescoço com as mãos.', '/gifs/abdominal_supra.gif', 6, 1, 1),
('Prancha Abdominal', 'Apoie-se nos antebraços e pontas dos pés, mantendo o corpo reto.', 'Contraia o abdômen e os glúteos.', '/gifs/prancha.gif', 6, 1, 1),
('Elevação de Pernas', 'Deitado, eleve as pernas esticadas até formarem 90 graus com o quadril.', 'Mantenha a lombar apoiada no chão.', '/gifs/elevacao_pernas.gif', 6, 1, 1),
('Abdominal Oblíquo (Ciclista)', 'Deitado, leve o cotovelo direito ao joelho esquerdo e vice-versa, alternadamente.', 'Simula o movimento de pedalar.', '/gifs/abdominal_ciclista.gif', 6, 1, 1),
('Abdominal na Polia Alta', 'Ajoelhado, puxe a corda da polia para baixo, flexionando o tronco.', 'Sinta a contração do abdômen ao descer.', '/gifs/abdominal_polia.gif', 6, 6, 1);

-- Adutores (ID: 7)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Cadeira Adutora', 'Sentado na máquina, junte as pernas contra a resistência.', 'Mantenha a coluna reta e controle o movimento.', '/gifs/cadeira_adutora.gif', 7, 13, 1),
('Agachamento Sumô com Halter', 'Com as pernas bem afastadas, agache segurando um halter no meio.', 'Mantenha os pés apontados para fora.', '/gifs/agachamento_sumo.gif', 7, 2, 1),
('Adução de Quadril na Polia Baixa', 'Em pé, puxe a perna para dentro, cruzando na frente da outra.', 'Use um apoio para se equilibrar.', '/gifs/aducao_polia.gif', 7, 11, 1),
('Adução de Quadril Deitado', 'Deitado de lado, levante a perna de baixo.', 'Exercício de solo que não requer equipamento.', '/gifs/aducao_deitado.gif', 7, 1, 1);

-- Abdutores (ID: 8)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Cadeira Abdutora', 'Sentado na máquina, afaste as pernas contra a resistência.', 'Evite usar impulso. Concentre a força na parte externa das coxas.', '/gifs/cadeira_abdutora.gif', 8, 12, 1),
('Abdução de Quadril na Polia Baixa', 'Em pé, puxe a perna para fora, afastando-a do corpo.', 'Mantenha o tronco estável.', '/gifs/abducao_polia.gif', 8, 11, 1),
('Elevação Lateral de Perna Deitado', 'Deitado de lado, eleve a perna de cima.', 'Concentre a força no glúteo médio.', '/gifs/elevacao_lateral_perna.gif', 8, 1, 1),
('Passada Lateral com Elástico', 'Com um elástico nos tornozelos ou joelhos, dê passos para o lado.', 'Mantenha a tensão no elástico.', '/gifs/passada_lateral_elastico.gif', 8, 1, 1),
('Hidrante', 'Em quatro apoios, eleve o joelho lateralmente, como um cachorro fazendo xixi.', 'Mantenha o abdômen contraído.', '/gifs/hidrante.gif', 8, 1, 1);

-- Antebraço (ID: 9)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Rosca Punho', 'Sentado com os antebraços apoiados nas coxas, flexione os punhos para cima e para baixo.', 'Use uma barra ou halteres.', '/gifs/rosca_punho.gif', 9, 3, 1),
('Rosca Punho Inversa', 'Similar à rosca punho, mas com a palma das mãos virada para baixo.', 'Foco na parte extensora do antebraço.', '/gifs/rosca_punho_inversa.gif', 9, 3, 1);

-- Posteriores (ID: 11)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Mesa Flexora', 'Deitado na máquina, flexione os joelhos trazendo o apoio para perto dos glúteos.', 'Concentre a força nos músculos posteriores da coxa.', '/gifs/mesa_flexora.gif', 11, 10, 1),
('Stiff com Barra', 'Em pé, incline o tronco para frente mantendo as pernas quase retas, descendo a barra.', 'Mantenha a coluna reta para evitar lesões na lombar.', '/gifs/stiff_barra.gif', 11, 3, 1),
('Levantamento Terra Romeno', 'Similar ao Stiff, mas com uma leve flexão dos joelhos.', 'Excelente para posteriores e glúteos.', '/gifs/terra_romeno.gif', 11, 3, 1);

-- Glúteo (ID: 12)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Elevação Pélvica com Barra', 'Deitado com as costas apoiadas em um banco, eleve o quadril com a barra sobre ele.', 'Contraia bem os glúteos no topo do movimento.', '/gifs/elevacao_pelvica.gif', 12, 3, 1),
('Glúteo na Polia Baixa (Coice)', 'Em quatro apoios ou em pé, puxe o cabo da polia para trás com o pé.', 'Mantenha o abdômen contraído para estabilizar.', '/gifs/gluteo_polia.gif', 12, 11, 1),
('Máquina de Glúteo (Coice)', 'Ajoelhado na máquina, empurre a plataforma para trás com um dos pés.', 'Controle o movimento de volta.', '/gifs/maquina_gluteo.gif', 12, 1, 1);

-- Lombar (ID: 13)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Hiperextensão Lombar', 'No banco específico, flexione o tronco para baixo e suba até ficar alinhado.', 'Não suba além da linha do corpo para evitar hiperextensão.', '/gifs/hiperextensao_lombar.gif', 13, 4, 1),
('Bom Dia com Barra', 'Com a barra nas costas, incline o tronco para frente com as pernas semi-flexionadas.', 'Use pouca carga e foque na postura.', '/gifs/bom_dia_barra.gif', 13, 3, 1);

-- Panturrilha (ID: 14)
INSERT INTO Exercicio (nome, descricao, avisos, gif_path, id_grupo_muscular, id_equipamento, id_adm_cadastro) VALUES
('Panturrilha em Pé na Máquina', 'Na máquina específica (ou Smith), apoie os ombros sob as almofadas e eleve os calcanhares o máximo que puder.', 'Alongue bem na descida e segure a contração no topo.', '/gifs/panturrilha_pe.gif', 14, 14, 1),
('Panturrilha Sentado na Máquina', 'Sentado na máquina, com o apoio sobre os joelhos, eleve os calcanhares.', 'Este exercício foca mais no músculo sóleo.', '/gifs/panturrilha_sentado.gif', 14, 14, 1),
('Panturrilha no Leg Press', 'Apoie a ponta dos pés na parte inferior da plataforma e empurre, estendendo os tornozelos.', 'Mantenha os joelhos levemente flexionados e não os trave.', '/gifs/panturrilha_legpress.gif', 14, 8, 1);