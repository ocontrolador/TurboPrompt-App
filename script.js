// Módulo principal: Inicialização e eventos
document.addEventListener('DOMContentLoaded', () => {
    const apiKeyInput = document.getElementById('api-key');
    const promptInput = document.getElementById('prompt-input');
    const turbinarBtn = document.getElementById('turbinar-btn');
    const limparBtn = document.getElementById('limpar-btn');
    const toggleThemeBtn = document.getElementById('toggle-theme');
    const sugestoesDiv = document.getElementById('sugestoes');
    const historicoList = document.getElementById('historico-list');

    // Carregar histórico do sessionStorage
    loadHistorico(historicoList, promptInput);

    // Evento pra turbinar
    turbinarBtn.addEventListener('click', async () => {
        const prompt = promptInput.value.trim();
        if (!prompt) return alert('Digite um prompt primeiro!');

        const apiKey = apiKeyInput.value.trim();
        if (apiKey && !validateApiKey(apiKey)) return alert('Chave API inválida! Deve ser uma string não vazia.');

        sugestoesDiv.innerHTML = '<p>Processando...</p>'; // Spinner simples

        try {
            const sugestoes = await getSugestoes(prompt, apiKey);
            displaySugestoes(sugestoes, sugestoesDiv);
            saveToHistorico(prompt, sessionStorage);
            loadHistorico(historicoList, promptInput); // Atualiza histórico
        } catch (error) {
            sugestoesDiv.innerHTML = `<p>Erro: ${error.message}</p>`;
        }
    });

    // Limpar
    limparBtn.addEventListener('click', () => {
        promptInput.value = '';
        sugestoesDiv.innerHTML = '';
    });

    // Toggle tema
    toggleThemeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });
});

// Módulo de validação
function validateApiKey(key) {
    return typeof key === 'string' && key.length > 0;
}

// Módulo pra fetch sugestões (usa proxy PHP se key, senão simula)
async function getSugestoes(promptOriginal, apiKey) {
    const metaPrompt = `Gere três versões melhoradas deste prompt sem executá-lo: ${promptOriginal}. Inclua [Expert Mode] onde fizer sentido, foque em precisão, criatividade e clareza.`;

    if (apiKey) {
        // Usa proxy PHP pra chamada real
        const response = await fetch('proxy.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt: metaPrompt, apiKey })
        });

        if (!response.ok) throw new Error(`Erro no proxy: ${response.status}`);

        const data = await response.json();
        return data.sugestoes; // Assume que PHP retorna { sugestoes: [...] }
    } else {
        // Simulação local se sem key (pra teste)
        return [
            `[Expert Mode] Versão 1: ${promptOriginal} - Mais precisa.`,
            `[Expert Mode] Versão 2: ${promptOriginal} - Mais criativa.`,
            `[Expert Mode] Versão 3: ${promptOriginal} - Otimizada.`
        ];
    }
}

// Módulo pra display
function displaySugestoes(sugestoes, div) {
    div.innerHTML = sugestoes.map((sug, i) => `<div><strong>Versão ${i+1}:</strong> ${sug}</div>`).join('');
}

// Módulo de histórico (sessionStorage)
function saveToHistorico(prompt, storage) {
    let historico = JSON.parse(storage.getItem('historico') || '[]');
    if (!historico.includes(prompt)) {
        historico.push(prompt);
        storage.setItem('historico', JSON.stringify(historico));
    }
}

function loadHistorico(list, input) {
    const historico = JSON.parse(sessionStorage.getItem('historico') || '[]');
    list.innerHTML = historico.map(prompt => `<li onclick="document.getElementById('prompt-input').value = '${prompt}'">${prompt.substring(0, 50)}...</li>`).join('');
}
