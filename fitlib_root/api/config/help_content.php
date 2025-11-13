    <?php
/**
 * Retorna o conteúdo de ajuda (título e descrição) para uma página específica.
 */
function getHelpContent($pageKey) {
    $helpData = [
        // --- PÁGINAS PRINCIPAIS ---
        'dashboard' => [
            'title' => 'Ajuda: Dashboard',
            'description' => "Esta é a tela principal do sistema.
            - Ela exibe um resumo rápido das principais métricas (que você pode adicionar no futuro).
            - Use o menu lateral para navegar entre as seções."
        ],
        'exercicios' => [
            'title' => 'Ajuda: Gerenciar Exercícios',
            'description' => "Esta tela lista todos os exercícios cadastrados.
            - **Adicionar Novo:** Abre o formulário para criar um novo exercício.
            - **Filtrar:** Permite buscar exercícios por nome ou ordenar a lista.
            - **Editar:** Altera os dados de um exercício existente.
            - **Excluir:** Remove um exercício permanentemente do banco de dados."
        ],
        'equipamentos' => [
            'title' => 'Ajuda: Gerenciar Equipamentos',
            'description' => "Esta tela lista todos os equipamentos da academia.
            - **Adicionar Novo:** Abre o formulário para criar um novo equipamento.
            - **Editar:** Altera o nome ou o QR Code de um equipamento.
            - **Excluir:** Remove um equipamento."
        ],
        'grupos_musculares' => [
            'title' => 'Ajuda: Grupos Musculares',
            'description' => "Esta tela lista e permite adicionar/editar os grupos musculares.
            - **Adicionar Novo:** Use o formulário no topo da página para criar um novo grupo.
            - **Editar:** Altera o nome de um grupo existente.
            - **Excluir:** Remove um grupo muscular."
        ],
        'usuarios' => [
            'title' => 'Ajuda: Gerenciar Usuários',
            'description' => "Esta tela lista todos os administradores e professores cadastrados no painel.
            - **Adicionar Novo:** Abre o formulário para criar um novo usuário (adm ou professor).
            - **Editar:** Permite alterar nome, e-mail, tipo e senha de um usuário.
            - **Excluir:** Remove um usuário do sistema."
        ],

        // --- PÁGINAS DE FORMULÁRIO ---
        'exercicios/form' => [
            'title' => 'Ajuda: Formulário de Exercício',
            'description' => "Use esta tela para criar ou editar um exercício.
            - **Nome:** O nome oficial do exercício (deve ser único).
            - **Descrição:** O passo a passo de como executar o exercício.
            - **Avisos:** Cuidados especiais ou dicas de postura.
            - **Grupo/Equipamento:** Associe o exercício às listas cadastradas.
            - **GIF do Exercício:** O campo de texto para o caminho do GIF (ex: /images/uploads/gifs/nome.gif)."
        ],
        'equipamentos/form' => [
            'title' => 'Ajuda: Formulário de Equipamento',
            'description' => "Use esta tela para criar ou editar um equipamento.
            - **Nome:** O nome oficial do equipamento (deve ser único).
            - **QRcode:** O QRcode deve conter 3 caracteres alfabéticos."
        ],
        // Adicione 'equipamentos/form' e 'usuarios/form'
        'default' => [
            'title' => 'Ajuda não encontrada',
            'description' => 'Ainda não há uma descrição de ajuda disponível para esta página.'
        ]
    ];

    // Se a chave da página existir, retorne-a. Senão, retorne o padrão.
    if (array_key_exists($pageKey, $helpData)) {
        return $helpData[$pageKey];
    } else {
        // Tenta encontrar uma chave base (ex: 'exercicios' se a URL for 'exercicios/form')
        $parts = explode('/', $pageKey);
        $baseKey = $parts[0];
        if (array_key_exists($baseKey, $helpData)) {
            return $helpData[$baseKey];
        }
    }
    return $helpData['default'];
}
?>