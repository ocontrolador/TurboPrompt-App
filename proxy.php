// Módulo principal: Inicialização e eventos
document.addEventListener('DOMContentLoaded', () => {
    const promptInput = document.getElementById('prompt-input');
    const turbinarBtn = document.getElementById('turbinar-btn');
    const limparBtn = document.getElementById('limpar-btn');
    const toggleThemeBtn = document.getElementById('toggle-theme');
    const sugestoesDiv = document.getElementById('sugestoes');
    const historicoList = document.getElementById('historico-list');
    const exportOptions = document.getElementById('export-options');
    const exportFormat = document.getElementById('export-format');
    const exportBtn = document.getElementById('export-btn');

    // Carregar histórico do sessionStorage
    loadHistorico(historicoList, promptInput);

    // Evento pra turbinar
    turbinarBtn.addEventListener('click', async () => {
        const prompt = promptInput.value.trim();
        if (!prompt) return alert('Digite um prompt primeiro!');

        sugestoesDiv.innerHTML = '<p>Processando...</p>'; // Spinner simples

        try {
            const sugestoes = await getSugestoes(prompt);
            displaySugestoes(sugestoes, sugestoesDiv);
            saveToHistorico(prompt, sessionStorage);
            loadHistorico(historicoList, promptInput); // Atualiza histórico
            exportOptions.style.display = 'block'; // Mostra export se há sugestões
        } catch (error) {
            sugestoesDiv.innerHTML = `<p>Erro: ${error.message}</p>`;
        }
    });

    // Limpar
    limparBtn.addEventListener('click', () => {
        promptInput.value = '';
        sugestoesDiv.innerHTML = '';
        exportOptions.style.display = 'none';
    });

    // Toggle tema
    toggleThemeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });

    // Exportar
    exportBtn.addEventListener('click', () => {
        const sugestoes = Array.from(sugestoesDiv.querySelectorAll('div p')).map(p => p.textContent);
        exportSugestoes(sugestoes, exportFormat.value);
    });
});

// Módulo pra fetch sugestões (agora sem apiKey no body, pois vem do .env no PHP)
async function getSugestoes(promptOriginal) {
    const metaPrompt = `Gere três versões melhoradas deste prompt sem executá-lo: ${promptOriginal}. Inclua [Expert Mode] onde fizer sentido, foque em precisão, criatividade e clareza.`;

    // Usa proxy PHP
    const response = await fetch('proxy.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ prompt: metaPrompt })
    });

    if (!response.ok) throw new Error(`Erro no proxy: ${response.statusText}`);

    const data = await response.json();
    if (data.error) throw new Error(data.error);
    return data.sugestoes; // Assume que PHP retorna { sugestoes: [...] }
}

// Módulo pra display (adiciona copy buttons)
function displaySugestoes(sugestoes, div) {
    div.innerHTML = sugestoes.map((sug, i) => `
        <div>
            <strong>Versão ${i+1}:</strong>
            <p>${sug}</p>
            <button class="copy-btn" onclick="copyToClipboard('${sug.replace(/'/g, "\\'")}')">Copiar</button>
        </div>
    `).join('');
}

// Função pra copiar
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copiado!');
    }).catch(err => {
        alert('Erro ao copiar: ' + err);
    });
}

// Módulo de exportação em batch
function exportSugestoes(sugestoes, format) {
    let content = '';
    let mime = 'text/plain';
    let ext = 'txt';

    if (format === 'md') {
        content = sugestoes.map((sug, i) => `### Versão ${i+1}\n${sug}\n`).join('\n');
        mime = 'text/markdown';
        ext = 'md';
    } else if (format === 'json') {
        content = JSON.stringify(sugestoes, null, 2);
        mime = 'application/json';
        ext = 'json';
    } else {
        content = sugestoes.map((sug, i) => `Versão ${i+1}: ${sug}\n\n`).join('');
    }

    const blob = new Blob([content], { type: mime });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `sugestoes.${ext}`;
    a.click();
    URL.revokeObjectURL(url);
}

// Módulo de histórico (sessionStorage) - sem mudanças
function saveToHistorico(prompt, storage) {
    let historico = JSON.parse(storage.getItem('historico') || '[]');
    if (!historico.includes(prompt)) {
        historico.push(prompt);
        storage.setItem('historico', JSON.stringify(historico));
    }
}

function loadHistorico(list, input) {
    const historico = JSON.parse(sessionStorage.getItem('historico') || '[]');
    list.innerHTML = historico.map(prompt => `<li onclick="document.getElementById('prompt-input').value = '${prompt.replace(/'/g, "\\'")}'">${prompt.substring(0, 50)}...</li>`).join('');
}
