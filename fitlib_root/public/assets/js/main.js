// Espera o HTML da página carregar completamente
document.addEventListener('DOMContentLoaded', function() {
    
    // Pega os elementos que acabamos de criar
    const helpButton = document.getElementById('help-button');
    const helpModal = document.getElementById('help-modal');
    const closeButton = document.getElementById('help-modal-close');

    // Se os elementos existirem na página...
    if (helpButton && helpModal && closeButton) {
        
        // 1. Ao clicar no botão (?), mostra o modal
        helpButton.onclick = function() {
            helpModal.style.display = 'block';
        }

        // 2. Ao clicar no (X) dentro do modal, esconde o modal
        closeButton.onclick = function() {
            helpModal.style.display = 'none';
        }

        // 3. Ao clicar fora da caixa (no fundo escuro), esconde o modal
        window.onclick = function(event) {
            if (event.target == helpModal) {
                helpModal.style.display = 'none';
            }
        }
    }
});