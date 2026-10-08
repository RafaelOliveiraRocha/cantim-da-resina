# Cantim da Resina

Vitrine de produtos artesanais em resina, com páginas de produtos, apresentação e contato. É uma adaptação temática do Bikcraft, baseada no curso Web Design Completo da Origamid, com registro histórico de maio de 2022.

## Tecnologias e visualização

O site usa HTML, CSS e JavaScript, com Simple Anime, Simple Slide e Simple Form. Os endpoints de envio usam PHP, PHPMailer e SendGrid PHP. Créditos e licenças das bibliotecas estão preservados em suas respectivas pastas.

Abra `index.html` no navegador para consultar as páginas estáticas. Os formulários dependem de um servidor PHP. Para servir o projeto localmente, na raiz:

```bash
php -d display_errors=0 -S 127.0.0.1:8000
```

Acesse `http://127.0.0.1:8000/` e encerre com `Ctrl+C`. Sem configuração de envio, os endpoints retornam indisponibilidade. Os contatos com `example.invalid` e os telefones indicados como exemplo são placeholders.

## Formulários e configuração

Os formulários de `contato.html` e `produtos.html` enviam POST para `./enviar.php`. O Simple Form usa esse destino e apresenta erro quando a resposta HTTP não é bem-sucedida. `enviar-sendgrid.php` é uma alternativa de envio existente, sem ligação com esses formulários.

Configure as variáveis no ambiente do processo PHP que atende às requisições:

| Endpoint | Variáveis obrigatórias | Uso |
|---|---|---|
| `enviar.php` | `SMTP_FROM_EMAIL`, `SMTP_PASSWORD`, `SMTP_HOST` | Remetente/destinatário, senha e servidor SMTP; porta configurada: 587 |
| `enviar-sendgrid.php` | `SENDGRID_API_KEY`, `SENDGRID_FROM_EMAIL` | Chave de acesso e endereço remetente/destinatário |

Cada endpoint usa o mesmo endereço para remetente e destinatário. No servidor PHP embutido, as variáveis exportadas na sessão são herdadas. Exemplo ilustrativo para a alternativa SendGrid, antes de iniciar o servidor:

```bash
export SENDGRID_API_KEY='SUBSTITUA_POR_SUA_CHAVE'
export SENDGRID_FROM_EMAIL='remetente@example.invalid'
```

Substitua os placeholders por sua configuração. Apache/PHP-FPM precisam receber as variáveis no ambiente do serviço; exportá-las em outro terminal pode não bastar.

Os endpoints usam `getenv` e **não carregam `.env` automaticamente**. Configuração ausente ou vazia retorna HTTP 503 antes da criação do cliente. Exceções SMTP retornam HTTP 500; falhas SendGrid retornam HTTP não bem-sucedido. As mensagens não exibem detalhes das exceções ou respostas do provedor.

## Limitações técnicas

- O transporte SMTP contém `SMTPSecure = "tsl"`. As variáveis de ambiente não corrigem essa grafia nem redefinem a porta ou o transporte do script.
- O resultado de `$sanitize_value` é descartado e o corpo usa os valores originais. O código também usa `FILTER_SANITIZE_STRING`, cuja compatibilidade depende da versão do PHP.
- O filtro anti-bot SMTP usa OR entre dois campos. A alternativa SendGrid exclui esses campos do corpo, sem usá-los como bloqueio de envio.
- A validação de POST é limitada. Configurar o ambiente não garante o funcionamento dos formulários ou sua adequação para disponibilização pública.
- A exibição de warnings, erros internos e logs depende da configuração do servidor PHP. O exemplo de servidor local desativa a exibição de erros.
