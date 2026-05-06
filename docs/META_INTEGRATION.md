# Integração Meta (Facebook Ads) — Guia de Configuração

Este guia mostra como conectar a conta Meta de um cliente ao Leadflow para sincronizar campanhas reais e seus insights via **Meta Graph API**.

> A integração **só lê** dados do Meta. Nada é alterado, criado ou pausado lá. "Pausar" um planejamento ou campanha no app é uma sinalização interna nossa.

---

## 1. Visão geral do fluxo

```
[Cliente Meta]                                 [Leadflow]
                                                   │
1. Admin abre /clientes/{id}/configuracoes ────────┘
2. Clica "Conectar conta Meta"
3. Faz login no Facebook e autoriza permissões
4. Volta para o Leadflow → escolhe ad account
5. Em /clientes/{id}/campanhas → "Sincronizar com Meta"
6. Campanhas aparecem no app e podem ser vinculadas
   a postagens e a planejamentos com metas.
```

---

## 2. Pré-requisitos

- Um app no [developers.facebook.com](https://developers.facebook.com) com o produto **Facebook Login** habilitado.
- A conta de anúncios do cliente precisa ter um administrador que possa autorizar o app.
- O usuário que faz a conexão precisa ter permissão de leitura sobre a ad account no Business Manager do cliente.

### 2.1 Criar o app no Meta

1. Acesse [developers.facebook.com/apps](https://developers.facebook.com/apps) → **Criar app**.
2. Tipo: **Empresa**.
3. Após criar, no menu esquerdo:
   - Adicione o produto **Facebook Login para Empresas** (ou "Facebook Login" tradicional).
   - Em **Configurações > Básico**, copie o `App ID` e o `App secret`.

### 2.2 Configurar URI de redirecionamento

Em **Facebook Login → Configurações → URIs de redirecionamento OAuth válidos**, adicione:

```
http://localhost:8000/meta/callback
```

E em produção (substitua pelo seu domínio):

```
https://seudominio.com/meta/callback
```

### 2.3 Permissões necessárias

O Leadflow solicita o seguinte escopo:

```
ads_read, ads_management, pages_show_list, business_management
```

Para enviar o app para análise (App Review) e usar com contas que não sejam dos administradores do app, você precisará passar pelo processo de revisão das permissões `ads_read` e `ads_management`.

> **Em modo de Desenvolvimento**, qualquer admin/dev/tester listado no app já consegue conectar sem App Review.

---

## 3. Configurar o `.env`

Adicione (ou preencha) no arquivo `.env` do Leadflow:

```env
META_APP_ID=123456789012345
META_APP_SECRET=abc123def456...
META_REDIRECT_URI=http://localhost:8000/meta/callback
META_GRAPH_VERSION=v21.0
```

Em seguida, limpe os caches:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 4. Conectar um cliente ao Meta

### 4.1 Iniciar a conexão

1. Faça login no Leadflow.
2. Vá em **Clientes → escolha o cliente → aba Configurações**.
3. Role até o bloco **Conta Meta (Facebook Ads)**.
4. Clique em **Conectar conta Meta**.

Você será redirecionado ao Facebook. Faça login e clique em **Continuar** para autorizar as permissões.

### 4.2 Escolher a conta de anúncios

Ao retornar para o Leadflow, o sistema lista as ad accounts às quais o usuário autenticado tem acesso. Selecione a do cliente e clique em **Salvar**.

> O `meta_ad_account_id` (formato `act_123456789`) fica salvo no cliente. O `access_token` é armazenado **criptografado** no banco (cast `encrypted` no model `Client`).

### 4.3 Verificar conexão

No mesmo bloco da tela de configurações você verá:
- Status: **Conectado**
- Meta User ID
- Conta de anúncios selecionada
- Data de expiração do token (~60 dias com long-lived token)

---

## 5. Sincronizar campanhas

1. Vá em **Cliente → Campanhas**.
2. Clique em **Sincronizar com Meta**.

O sistema chama `GET /{ad_account_id}/campaigns` e, para cada campanha, `GET /{campaign_id}/insights` (lifetime), e faz `upsert` na tabela local `campaigns` por `(client_id, meta_campaign_id)`.

Campos sincronizados:
- `name`, `objective`, `meta_status`, `start_date`, `stop_date`
- `daily_budget`, `lifetime_budget` (convertidos de centavos para reais)
- `insights` JSON: `reach`, `impressions`, `clicks`, `ctr`, `cpc`, `spend`, `frequency`
- `last_synced_at`

> **Idempotente**: rodar de novo apenas atualiza o que mudou. Campanhas removidas no Meta **não** são apagadas localmente automaticamente — assim, vínculos com posts/planejamentos não quebram.

---

## 6. Usar as campanhas no app

Depois de sincronizar:

- **Vincular postagens**: na aba Campanhas, expanda uma campanha e clique em **Vincular postagem**, ou edite uma postagem e escolha a campanha no dropdown.
- **Planejamento**: em **Cliente → Planejamento → Novo planejamento**. Dentro do planejamento, defina metas livres (título, alvo, atual, unidade) e vincule campanhas. O `local_status` por planejamento (`em_execucao`, `pausada`, `concluida`) é independente do status do Meta — útil para registrar pausas internas.

---

## 7. Desconectar

Em **Configurações** → bloco Meta → **Desconectar Meta**.

- Limpa `meta_access_token`, `meta_user_id`, `meta_ad_account_id`, `meta_token_expires_at`.
- **Mantém** as campanhas já sincronizadas (e seus vínculos com posts/planejamentos), apenas para de atualizá-las.

---

## 8. Renovação de token

O token de longa duração dura ~60 dias. Quando expirar:
1. O sync vai falhar com erro 401/190 do Meta.
2. O usuário precisa reconectar a conta seguindo o passo 4 novamente.

> Futuramente: agendar `php artisan schedule:run` com um job que avise por e-mail X dias antes da expiração.

---

## 9. Troubleshooting

| Sintoma | Causa provável | Solução |
|---|---|---|
| `redirect_uri isn't owned by application` | URI de callback não cadastrada no app Meta | Adicione a URI exata em Facebook Login → Configurações |
| `(#10) Application does not have permission` | Permissão `ads_read` não concedida ou app fora do App Review | Em modo Dev, garanta que o usuário é admin/dev/tester do app |
| `Invalid OAuth access token` ao sincronizar | Token expirou | Reconecte (passo 4) |
| Nenhuma ad account aparece | Usuário não tem acesso a ad account no Business Manager | Adicione o usuário como admin/analista da ad account |
| Campanha sumiu do Meta mas continua no app | Sync não deleta — mantém vínculos | Apague manualmente (futuro: botão "limpar campanhas órfãs") |
| Erro `state inválido` no callback | Sessão expirada / cookies bloqueados | Reinicie o fluxo a partir de Configurações |

---

## 10. Referências

- [Graph API — Campaigns](https://developers.facebook.com/docs/marketing-api/reference/ad-campaign-group)
- [Graph API — Insights](https://developers.facebook.com/docs/marketing-api/insights)
- [Facebook Login — Long-Lived Tokens](https://developers.facebook.com/docs/facebook-login/guides/access-tokens/get-long-lived)
- [App Review for ads_read / ads_management](https://developers.facebook.com/docs/app-review)

---

## 11. Arquivos relevantes no código

| Arquivo | Função |
|---|---|
| `config/services.php` (`meta`) | Lê `META_APP_ID`, `META_APP_SECRET`, redirect, versão |
| `app/Services/MetaGraphService.php` | Wrapper HTTP da Graph API (OAuth, campaigns, insights) |
| `app/Services/CampaignSyncService.php` | Upsert de campanhas + insights na tabela local |
| `app/Http/Controllers/MetaAuthController.php` | OAuth: redirect, callback, ad accounts, disconnect |
| `app/Http/Controllers/CampaignController.php` | Index/sync/show de campanhas |
| `app/Models/Client.php` | Campos Meta + cast `encrypted` para o token |
| `app/Models/Campaign.php` | Schema da campanha local |
| `routes/web.php` | Rotas `meta.*` e `campaigns.*` |
| `resources/views/clients/settings.blade.php` | Bloco UI "Conta Meta" |
| `resources/views/clients/campaigns.blade.php` | Listagem + botão de sync |
