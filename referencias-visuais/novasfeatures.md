# Estatísticas, Planejamento de Campanha & Apresentação ao Cliente

> Documento de referência para evolução da tela de cliente no LeadFlow.
> Foco: dar ao gestor de marketing ferramentas para **planejar campanhas** e **apresentar resultados ao cliente final**.

---

## Estado Atual

| O que existe | Status |
|---|---|
| CRUD de clientes e postagens | Funcional |
| Campanhas com métricas (alcance, CTR, CPC...) | Mock/hardcoded |
| Insights por nicho | Mock/hardcoded |
| Planejamento de campanha real | Não existe |
| Timeline do cliente | Não existe |

---

## 1. Planejamento de Campanha (Nova Tela)

### Problema
Hoje as campanhas são mockadas e o gestor não consegue criar, editar ou acompanhar uma campanha real. Não há ciclo de vida da campanha.

### Proposta: `/clientes/{client}/campanhas/criar`

#### 1.1 Dados do Planejamento

```
campanha
├── nome
├── objetivo (engajamento | conversão | branding | educação | lançamento)
├── período (data_inicio, data_fim)
├── orçamento_previsto (R$)
├── canais_alvo (Instagram, TikTok, etc — subset dos canais do cliente)
├── público_alvo (texto livre ou tags)
├── descrição / briefing
├── status (rascunho | ativo | pausado | finalizado)
└── kpis_meta
    ├── meta_alcance
    ├── meta_impressoes
    ├── meta_cliques
    ├── meta_ctr (%)
    ├── meta_cpc (R$)
    ├── meta_conversoes
    └── meta_engajamento (%)
```

#### 1.2 Cronograma Visual (Timeline da Campanha)

- Uma barra de progresso horizontal mostrando o período da campanha
- Marcadores para cada postagem vinculada (posicionados na data de publicação)
- Indicador visual de "hoje" para campanhas ativas
- Possibilidade de arrastar postagens na timeline para replanejar datas

#### 1.3 Vinculação de Postagens

- Ao criar a campanha, o gestor pode vincular postagens existentes ou criar novas diretamente
- Cada postagem na campanha ganha um campo `scheduled_at` (data de publicação planejada)
- View de checklist: quais posts já foram publicados vs. pendentes

#### 1.4 Migração sugerida: `campaigns`

```php
Schema::create('campaigns', function (Blueprint $table) {
    $table->id();
    $table->foreignId('client_id')->constrained()->cascadeOnDelete();
    $table->string('name', 120);
    $table->string('objective')->nullable();
    $table->date('start_date');
    $table->date('end_date');
    $table->decimal('budget', 10, 2)->nullable();
    $table->json('target_channels')->nullable();
    $table->text('target_audience')->nullable();
    $table->text('description')->nullable();
    $table->string('status')->default('rascunho');
    // Metas
    $table->unsignedInteger('goal_reach')->nullable();
    $table->unsignedInteger('goal_impressions')->nullable();
    $table->unsignedInteger('goal_clicks')->nullable();
    $table->decimal('goal_ctr', 5, 2)->nullable();
    $table->decimal('goal_cpc', 8, 2)->nullable();
    $table->unsignedInteger('goal_conversions')->nullable();
    $table->decimal('goal_engagement', 5, 2)->nullable();
    $table->timestamps();
});
```

E atualizar a tabela `posts`:

```php
Schema::table('posts', function (Blueprint $table) {
    $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
    $table->date('scheduled_at')->nullable();
    $table->string('status')->default('rascunho'); // rascunho | agendado | publicado
});
```

---

## 2. Estatísticas da Campanha (Dashboard de Performance)

### Problema
O gestor não tem visão real do desempenho. Todos os números são mocks.

### Proposta: `/clientes/{client}/campanhas/{campaign}`

#### 2.1 Painel de KPIs com Meta vs. Realizado

Exibir cards comparativos para cada métrica:

```
┌─────────────────────────────┐
│  Alcance                    │
│  12.450 / 15.000  (83%)    │
│  ████████████░░░░           │
│  ▲ 12% vs semana anterior  │
└─────────────────────────────┘
```

Cada card mostra:
- **Valor atual** vs **meta definida no planejamento**
- Barra de progresso visual (% da meta atingida)
- Variação em relação ao período anterior (seta verde/vermelha)
- Cor do card muda conforme performance: verde (≥80%), amarelo (50-79%), vermelho (<50%)

**KPIs sugeridos:**
| Métrica | Descrição |
|---|---|
| Alcance | Pessoas únicas que viram o conteúdo |
| Impressões | Total de vezes que o conteúdo foi exibido |
| Cliques | Interações de clique (link, perfil, etc.) |
| CTR | Cliques / Impressões × 100 |
| CPC | Orçamento gasto / Cliques |
| Conversões | Ações concluídas (cadastro, compra, etc.) |
| Engajamento | (Curtidas + Comentários + Compartilhamentos) / Alcance × 100 |
| ROI | (Receita gerada - Investimento) / Investimento × 100 |

#### 2.2 Gráficos de Evolução

**Gráfico de linha — Engajamento ao longo do tempo:**
- Eixo X: dias da campanha
- Eixo Y: taxa de engajamento (%)
- Linha pontilhada horizontal na meta
- Área preenchida abaixo da linha

**Gráfico de barras — Performance por postagem:**
- Cada barra = 1 postagem da campanha
- Agrupado por: alcance, engajamento, cliques
- Ordenado por data de publicação
- Hover mostra detalhes da postagem

**Gráfico de pizza/donut — Distribuição de investimento:**
- Quanto do orçamento foi gasto por canal (Instagram, TikTok, etc.)
- Centro do donut mostra total gasto vs. orçamento

#### 2.3 Tabela de Postagens com Métricas

```
| Postagem         | Tipo      | Data       | Alcance | Engajamento | Cliques | Status     |
|------------------|-----------|------------|---------|-------------|---------|------------|
| Post 1 - Título  | Carrossel | 12/04/2026 | 3.200   | 5.8%        | 145     | Publicado  |
| Post 2 - Título  | Reels     | 14/04/2026 | 8.100   | 8.2%        | 312     | Publicado  |
| Post 3 - Título  | Story     | 16/04/2026 | —       | —           | —       | Agendado   |
```

- Ordenável por qualquer coluna
- Filtro por tipo de conteúdo e status
- Link direto para editar a postagem

#### 2.4 Migração sugerida: `campaign_metrics` (registros diários)

```php
Schema::create('campaign_metrics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
    $table->date('date');
    $table->unsignedInteger('reach')->default(0);
    $table->unsignedInteger('impressions')->default(0);
    $table->unsignedInteger('clicks')->default(0);
    $table->unsignedInteger('likes')->default(0);
    $table->unsignedInteger('comments')->default(0);
    $table->unsignedInteger('shares')->default(0);
    $table->unsignedInteger('conversions')->default(0);
    $table->decimal('spend', 10, 2)->default(0);
    $table->timestamps();

    $table->unique(['campaign_id', 'date']);
});
```

E métricas por post:

```php
Schema::create('post_metrics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('post_id')->constrained()->cascadeOnDelete();
    $table->unsignedInteger('reach')->default(0);
    $table->unsignedInteger('impressions')->default(0);
    $table->unsignedInteger('clicks')->default(0);
    $table->unsignedInteger('likes')->default(0);
    $table->unsignedInteger('comments')->default(0);
    $table->unsignedInteger('shares')->default(0);
    $table->unsignedInteger('saves')->default(0);
    $table->timestamps();
});
```

---

## 3. Dashboard do Cliente = Tela de Relatório (`/clientes/{client}`)

### Problema
O gestor não tem como apresentar resultados ao cliente. A ideia é que o próprio dashboard do cliente **já seja** a tela de relatório — sem precisar de uma rota separada.

### Proposta: Transformar `/clientes/{client}` em uma tela apresentável

#### 3.1 Estrutura do Dashboard

```
┌──────────────────────────────────────────────────────────────┐
│  [Avatar] Nome do Cliente  •  Nicho  •  Canais              │
│  Cliente desde: Janeiro/2025                                 │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  KPIs GERAIS (consolidado de todas as campanhas)             │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐             │
│  │Alcanc│ │Engaj.│ │Conver│ │ ROI  │ │Posts │             │
│  │ 125k │ │ 5.4% │ │  312 │ │ 180% │ │  47  │             │
│  └──────┘ └──────┘ └──────┘ └──────┘ └──────┘             │
│                                                              │
│  ════════════════════════════════════════════════════════     │
│  TIMELINE DO CLIENTE (seção 3.2 abaixo)                      │
│  ════════════════════════════════════════════════════════     │
│                                                              │
│  CAMPANHAS ATIVAS — cards resumidos com progresso            │
│  ┌─────────────────┐ ┌─────────────────┐                    │
│  │ Campanha Verão   │ │ Campanha Lanç.  │                    │
│  │ 82% da meta      │ │ 45% da meta     │                    │
│  │ [Ver detalhes]   │ │ [Ver detalhes]  │                    │
│  └─────────────────┘ └─────────────────┘                    │
│                                                              │
│  COMPARATIVO DE CAMPANHAS (tabela lado a lado)               │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

#### 3.2 Timeline do Cliente (Histórico Mensal)

Uma linha do tempo vertical organizada **por mês**, mostrando toda a trajetória do cliente no sistema. É a peça central do dashboard — permite ao gestor contar a história da evolução do cliente.

```
  2026
  ────
  ABR ──●── Campanha "Lançamento Produto Y" iniciada
        │   Orçamento: R$ 8.000 · Meta: 50k alcance
        │   Status: ativo · 3 posts publicados
        │
  MAR ──●── Campanha "Dia da Mulher" finalizada
        │   Resultado: 42k alcance (140% da meta!) · ROI: 220%
        │   ★ Melhor campanha do cliente até agora
        │
        ●── 12 posts publicados no mês
        │   Engajamento médio: 6.1%
        │
  FEV ──●── Campanha "Carnaval Fitness" finalizada
        │   Resultado: 28k alcance (93% da meta) · ROI: 150%
        │
        ●── Novo canal adicionado: TikTok
        │
  JAN ──●── Campanha "Verão 2026" iniciada
        │   Orçamento: R$ 5.000
        │
        ●── Cliente cadastrado no LeadFlow
        │   Nicho: Fitness · Canais: Instagram, Facebook

  2025
  ────
  DEZ ──●── (sem atividade)
        │
  ...
```

**O que aparece na timeline:**
- **Início/fim de campanhas** — com nome, orçamento, resultado final (se finalizada), badges de status
- **Marcos mensais** — total de posts publicados, engajamento médio do mês
- **Destaques automáticos** — campanha que bateu meta ganha estrela, pior mês ganha alerta

**Dados necessários:**
- `campaigns.start_date`, `campaigns.end_date`, `campaigns.status` — posicionar na timeline
- `campaign_metrics` agregado — resultados finais
- `posts.created_at` agrupado por mês — contagem mensal
- `clients.created_at` — marco inicial

**Interatividade:**
- Clicar em uma campanha na timeline expande um card inline com KPIs detalhados
- Filtro por ano (dropdown no topo da timeline)
- Scroll suave entre meses, com o mês atual destacado
- Em modo apresentação, a timeline fica mais compacta (só campanhas, sem atividade vazia)

---

## 4. Outras Melhorias na Tela de Cliente

### 4.1 KPIs Reais (substituir mocks)

Os cards atuais devem puxar dados reais agregando `campaign_metrics` e `post_metrics`:

- **Alcance total:** `SUM(campaign_metrics.reach)` de todas as campanhas do cliente
- **Postagens:** contagem real (já funciona via DB)
- **Campanhas ativas:** `COUNT(campaigns) WHERE status = 'ativo'`
- **Engajamento médio:** média ponderada calculada a partir de `post_metrics`
- **ROI geral:** agregado de todas as campanhas finalizadas

### 4.2 Comparativo entre Campanhas

Tabela que o gestor usa para mostrar evolução ao cliente:

| Métrica | Campanha A | Campanha B | Variação |
|---|---|---|---|
| Alcance | 45.000 | 32.000 | +40.6% |
| Engajamento | 5.2% | 3.8% | +36.8% |
| CPC | R$ 0,45 | R$ 0,72 | -37.5% |
| Conversões | 89 | 52 | +71.2% |

---

## 5. Resumo de Rotas Novas

```
# Campanhas (CRUD real)
GET    /clientes/{client}/campanhas                    → Lista (já existe, adaptar)
GET    /clientes/{client}/campanhas/criar               → Form de criação
POST   /clientes/{client}/campanhas                    → Salvar campanha
GET    /clientes/{client}/campanhas/{campaign}          → Dashboard da campanha
GET    /clientes/{client}/campanhas/{campaign}/editar   → Editar campanha
PUT    /clientes/{client}/campanhas/{campaign}          → Atualizar
DELETE /clientes/{client}/campanhas/{campaign}          → Excluir

# Dashboard do cliente (já existe, evoluir)
GET    /clientes/{client}                               → Dashboard + Relatório + Timeline

# Métricas (entrada manual ou via Meta API — ver README-META-API.md)
POST   /clientes/{client}/campanhas/{campaign}/metricas → Registrar métricas do dia
POST   /clientes/{client}/postagens/{post}/metricas     → Registrar métricas do post
GET    /clientes/{client}/meta/sync                     → Sincronizar dados da Meta API
```

---

## 6. Prioridade de Implementação

| Fase | O que fazer | Impacto |
|---|---|---|
| **1** | CRUD real de campanhas (migration + model + controller + views) | Alto — base para tudo |
| **2** | Métricas de campanha e postagem (migrations + entrada manual) | Alto — dados reais |
| **3** | Dashboard do cliente com Timeline mensal + KPIs reais + cards de campanha | Alto — tela central |
| **4** | Modo apresentação + considerações/próximos passos no dashboard | Alto — diferencial |
| **5** | Integração Meta API para alimentar métricas automaticamente | Alto — ver README-META-API.md |
| **6** | Dashboard de performance individual da campanha (gráficos) | Alto — valor pro gestor |
| **7** | Comparativo entre campanhas | Médio — análise avançada |
| **8** | Exportar PDF + link compartilhável | Médio — conveniência |

---

## 7. Bibliotecas Sugeridas

| Lib | Para quê |
|---|---|
| **Chart.js** ou **ApexCharts** | Gráficos de linha, barra, donut |
| **dompdf** (Laravel) | Exportar relatório em PDF |
| **Alpine.js** (já no projeto) | Interatividade dos dashboards |
