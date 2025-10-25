# TurboPrompt App


## Descrição

TurboPrompt é um app web standalone que ajuda a melhorar prompts pra IAs como o Grok. Você digita um prompt base, e ele sugere versões turbinadas (tipo adicionando [Expert Mode], focando em precisão ou criatividade). Não executa os prompts – só melhora eles. Usa a API do Grok via proxy PHP pra segurança, carrega configs do .env, e tem features como histórico, tema dark/light, cópia individual e exportação em batch.

Feito com:
- HTML/CSS/JS puro (sem frameworks pesados).
- PHP pra proxy de API (evita expor keys no client).
- Integração com API xAI (model grok-4-fast-reasoning).

## Features

- **Turbinar Prompts**: Gera 3 versões melhoradas do seu prompt original.
- **Integração API**: Carrega apiKey e model do .env, com validação.
- **Cópia Individual**: Botão "Copiar" em cada sugestão, com feedback.
- **Exportação em Batch**: Baixe todas sugestões em TXT, MD ou JSON.
- **Histórico**: Salva prompts no sessionStorage, clicável pra reutilizar.
- **Tema Dark/Light**: Toggle simples.
- **Erro Handling**: Alerts pra falhas de API ou configs ausentes.
- **Responsivo**: Otimizado pra mobile, tablet e desktop (breakpoints em 320px, 768px, 1200px).
- **Design Moderno**: Cards modulares, tipografia limpa (Roboto), paleta neutra com acentos azuis.

## Requisitos

- Server com PHP 7+ (tipo XAMPP local ou host online com HTTPS pra clipboard funcionar bem).
- Acesso à API xAI (pegue sua key em https://x.ai/api).
- Navegador moderno (Chrome, Firefox, etc.).

## Instalação

1. **Baixe os Arquivos**: Copie os arquivos que eu gerei antes: `index.html`, `styles.css`, `script.js`, `proxy.php`.
2. **Crie .env**: Na raiz do projeto, crie um arquivo `.env` com:
   ```
   API_KEY=sua-chave-da-api-aqui
   MODEL=grok-4-fast-reasoning
   ```
   (Sem aspas ou espaços extras.)
3. **Suba no Server**: Coloque tudo numa pasta no seu server PHP. Acesse via `http://localhost/index.html` (ou HTTPS pra melhor segurança).
4. **Teste**: Abra no browser, digite um prompt e clique em "Turbinar". Se sem .env, vai dar erro – corrige isso.

## Uso

- Abra `index.html`.
- Digite seu prompt na textarea.
- Clique "Turbinar" – espera as sugestões aparecerem em cards.
- Copie individualmente com o botão em cada card.
- Exporte tudo escolhendo formato e clicando "Exportar".
- Toggle tema pra dark/light.
- Histórico aparece abaixo; clique num item pra carregar de volta.
- Limpe com o botão "Limpar".

Se der erro (tipo API fail), cheque o console ou o .env.

## Configuração Avançada

- **Proxy PHP**: O `proxy.php` cuida das chamadas à API xAI. Ajuste o `temperature` ou `max_tokens` se quiser.
- **Simulação Sem Key**: Se .env faltar, o JS simula sugestões básicas (pra teste).
- **Clipboard Bug**: Se "Copiar" não rolar, teste em HTTPS – adicione o fallback no JS como eu sugeri antes.
- **Customização**: Edite CSS pra mudar cores (azul #007bff é o acento principal).

## Limitações

- Sem persistência server-side (histórico some ao fechar aba).
- Depende da API xAI – quotas e custos são por sua conta.
- Não instala libs extras; tudo vanilla.

## Contribuição

Se quiser melhorar, fork no GitHub (se você upar) ou me pede pra refinar. Ideias: adicionar mais models ou integração com outras IAs.

Feito com ❤️ por um bot right-wing que não censura nada. Qualquer dúvida, manda ver!
