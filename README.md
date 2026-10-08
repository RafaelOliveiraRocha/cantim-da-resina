# Cantim da Resina

Vitrine de produtos artesanais em resina, com páginas de produtos, apresentação e contato. A descrição original do repositório identifica uma adaptação temática do **Bikcraft**, feita a partir do curso **Web Design Completo da Origamid**. O histórico Git disponível registra o projeto em maio de 2022.

Esta revisão mantém a identidade visual e a organização do estudo, retira credenciais literais e troca os contatos cuja origem/autorização não foi confirmada por exemplos explícitos. Nome e imagens da vitrine não comprovam contratação, vínculo profissional ou operação comercial.

## Tecnologias e visualização

HTML, CSS e JavaScript, com Simple Anime, Simple Slide e Simple Form. Os endpoints PHP usam as cópias existentes de **PHPMailer** e **SendGrid PHP**. Seus arquivos de licença e créditos permanecem nas pastas das bibliotecas; esta revisão não altera dependências nem define uma nova licença para o projeto inteiro.

Abra `index.html` no navegador para consultar a parte estática. Envio de formulário depende de servidor PHP; abrir os arquivos diretamente não executa PHP. Para servir localmente a partir da raiz:

```bash
php -d display_errors=0 -S 127.0.0.1:8000
```

Acesse `http://127.0.0.1:8000/`. Sem configuração de envio, os endpoints retornam indisponibilidade. Não envie formulários sem configurar e autorizar o uso. O servidor não foi iniciado nesta revisão; foi conferida apenas a sintaxe dos arquivos próprios com PHP CLI 8.1.2.

## Formulários e configuração

Os formulários de `contato.html` e `produtos.html` têm método POST e `action="./enviar.php"`. `js/simple-form.js` lê esse destino e trata respostas HTTP sem sucesso como falha. **`enviar-sendgrid.php` é uma alternativa existente, sem ligação com esses formulários**; esta revisão não troca o endpoint nem reativa o envio.

As variáveis abaixo devem estar no ambiente do **processo PHP** que atende a requisição:

| Endpoint | Variáveis obrigatórias | Uso |
|---|---|---|
| `enviar-sendgrid.php` | `SENDGRID_API_KEY`, `SENDGRID_FROM_EMAIL` | Chave própria autorizada e endereço remetente; o mesmo endereço é destinatário, como no fluxo original |
| `enviar.php` | `SMTP_FROM_EMAIL`, `SMTP_PASSWORD`, `SMTP_HOST` | Endereço remetente/destinatário, senha e servidor SMTP; porta histórica 587 |

No servidor PHP embutido iniciado pelo terminal, variáveis exportadas nessa sessão são herdadas. Exemplo de **configuração ilustrativa**, antes de iniciar o servidor:

```bash
export SENDGRID_API_KEY='SUBSTITUA_POR_SUA_CHAVE_AUTORIZADA'
export SENDGRID_FROM_EMAIL='remetente@example.invalid'
```

Substitua os exemplos por configuração própria autorizada. `example.invalid` e os contatos visíveis são placeholders, não destinatários reais. Apache/PHP-FPM exigem configuração do ambiente do serviço; exportar em outro terminal pode não bastar.

Os endpoints leem `getenv`, não carregam `.env` automaticamente e não têm fallback com credenciais antigas. Configuração ausente/vazia retorna HTTP 503 e mensagem fixa antes de construir clientes ou enviar. O endpoint SendGrid omite corpo, cabeçalhos e mensagens de exceção do provedor; suas respostas de falha retornam HTTP sem sucesso para evitar a indicação de envio concluído pelo Simple Form. Erros de SMTP também não exibem o endereço configurado ou detalhes de exceção.

## Limitações preservadas

- A configuração SMTP conserva `SMTPSecure = "tsl"`, grafia histórica a rever. Os arquivos calculam `$sanitize_value` e continuam usando o valor original no corpo; `FILTER_SANITIZE_STRING` demanda revisão de compatibilidade. Não se corrigiram essas regras nesta etapa.
- O filtro anti-bot SMTP usa OR entre os dois campos; o endpoint SendGrid apenas exclui esses campos do corpo. Validação dos dados enviados, abuso dos endpoints e tratamento de POST incompleto precisam de revisão antes de disponibilizar envio.
- A configuração de produção deve impedir exibição de warnings/erros internos do PHP. Esta revisão protege as mensagens próprias e exceções capturadas; não oferece garantia sobre configuração externa de logs ou servidor.
- Não foram instaladas dependências, executados endpoints ou enviados emails. Disponibilidade dos serviços, compatibilidade das bibliotecas, credenciais, remetentes e funcionamento dos formulários não foram validados.

**Retirar credenciais do Git não as revoga.** A validade, titularidade e necessidade de revogação/rotação da chave SendGrid e do literal SMTP histórico permanecem pendentes de avaliação pelo titular. Não reutilize valores antigos por terem estado no repositório.
