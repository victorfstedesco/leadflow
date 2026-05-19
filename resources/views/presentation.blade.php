<!DOCTYPE html>
<html lang="pt-BR" style="color-scheme:dark;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $presentation->title ?? 'Resultados' }} · {{ $presentation->client->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { background: #080f1e !important; color: #fff; font-family: 'Fustat', sans-serif; min-height: 100vh; }

        .hero-number {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, #fff 0%, #9EEA6C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255,255,255,.25);
        }
        .card-dark {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
        }
        .card-dark:hover {
            background: rgba(255,255,255,.06);
            border-color: rgba(158,234,108,.2);
        }
        .metric-pill {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .metric-pill .value {
            font-size: 1.9rem;
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
        }
        .metric-pill .label {
            font-size: 11px;
            color: rgba(255,255,255,.4);
            margin-top: 6px;
            font-weight: 500;
            line-height: 1.3;
        }
        .progress-bar-bg {
            background: rgba(255,255,255,.07);
            border-radius: 99px;
            height: 6px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            border-radius: 99px;
            background: linear-gradient(90deg, #9EEA6C, #6ee7b7);
            transition: width 1.5s cubic-bezier(.22,1,.36,1);
        }
        .pulse {
            display: inline-block;
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #4ade80;
            position: relative;
        }
        .pulse::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 50%;
            background: rgba(74,222,128,.3);
            animation: pulse 2s ease-out infinite;
        }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0;transform:scale(2)} }
        .divider { border: none; border-top: 1px solid rgba(255,255,255,.06); }
        .tag { display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11px;font-weight:600; }
        .tag-green { background:rgba(74,222,128,.12);color:#4ade80; }
        .tag-amber { background:rgba(251,191,36,.12);color:#fbbf24; }
        .tag-gray  { background:rgba(255,255,255,.08);color:#9ca3af; }

        @media (max-width: 640px) {
            .grid-4 { grid-template-columns: repeat(2, 1fr) !important; }
            .grid-6 { grid-template-columns: repeat(2, 1fr) !important; }
        }
    </style>
</head>
<body>

@php
    $storedInsights = $presentation->insights ?? [];

    // Totais
    $totals = ['reach' => 0, 'impressions' => 0, 'clicks' => 0, 'spend' => 0];
    $ctrNum = 0; $ctrDen = 0;
    $cpcNum = 0; $cpcDen = 0;
    $maxReach = 0;

    foreach ($campaigns as $c) {
        $ins = $storedInsights[$c->id] ?? $storedInsights[(string)$c->id] ?? null;
        if (!$ins) continue;
        $totals['reach']       += $ins['reach'] ?? 0;
        $totals['impressions'] += $ins['impressions'] ?? 0;
        $totals['clicks']      += $ins['clicks'] ?? 0;
        $totals['spend']       += $ins['spend'] ?? 0;
        if (!empty($ins['impressions'])) { $ctrNum += ($ins['ctr'] ?? 0) * $ins['impressions']; $ctrDen += $ins['impressions']; }
        if (!empty($ins['clicks']))      { $cpcNum += ($ins['cpc'] ?? 0) * $ins['clicks'];       $cpcDen += $ins['clicks']; }
        $maxReach = max($maxReach, $ins['reach'] ?? 0);
    }

    $avgCtr = $ctrDen > 0 ? $ctrNum / $ctrDen : null;
    $avgCpc = $cpcDen > 0 ? $cpcNum / $cpcDen : null;
    $hasData = $totals['reach'] > 0 || $totals['clicks'] > 0 || $totals['spend'] > 0;

    // Frase do hero
    if ($totals['reach'] > 0) {
        $heroNumber = number_format($totals['reach'], 0, ',', '.');
        $heroLabel  = 'pessoas alcançadas';
    } elseif ($totals['clicks'] > 0) {
        $heroNumber = number_format($totals['clicks'], 0, ',', '.');
        $heroLabel  = 'pessoas visitaram';
    } else {
        $heroNumber = number_format($totals['spend'], 2, ',', '.');
        $heroLabel  = 'investidos';
    }

    // Ctr em linguagem humana: "de cada 100 pessoas, X clicaram"
    $ctrHuman = $avgCtr !== null ? round($avgCtr, 1) : null;
@endphp

<div style="max-width:860px;margin:0 auto;padding:48px 24px 80px;">

    {{-- ── TOPBAR ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:56px;flex-wrap:wrap;gap:16px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <span style="width:32px;height:32px;border-radius:8px;background:rgba(158,234,108,.15);border:1px solid rgba(158,234,108,.25);display:inline-flex;align-items:center;justify-content:center;font-weight:900;font-size:16px;color:#9EEA6C;">L</span>
            <span style="color:rgba(255,255,255,.3);font-size:13px;font-weight:600;">LeadFlow</span>
        </div>
        <div style="text-align:right;">
            <div style="color:rgba(255,255,255,.55);font-size:14px;font-weight:600;">{{ $presentation->client->name }}</div>
            @if ($presentation->since && $presentation->until)
                <div style="color:rgba(255,255,255,.25);font-size:12px;margin-top:2px;">
                    {{ \Carbon\Carbon::parse($presentation->since)->format('d/m/Y') }} → {{ \Carbon\Carbon::parse($presentation->until)->format('d/m/Y') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ── HERO ── --}}
    <div style="margin-bottom:64px;">
        @if ($presentation->title)
            <div class="section-label" style="margin-bottom:12px;">{{ $presentation->title }}</div>
        @endif

        @if ($hasData)
            <div class="hero-number">{{ $heroNumber }}</div>
            <div style="font-size:1.3rem;color:rgba(255,255,255,.5);margin-top:10px;font-weight:500;">
                {{ $heroLabel }}
                @if ($presentation->since && $presentation->until)
                    <span style="color:rgba(255,255,255,.25);">
                        · {{ \Carbon\Carbon::parse($presentation->since)->format('d/m') }} a {{ \Carbon\Carbon::parse($presentation->until)->format('d/m/Y') }}
                    </span>
                @endif
            </div>

            {{-- Frase de impacto --}}
            <div style="margin-top:28px;padding:20px 24px;background:rgba(158,234,108,.07);border:1px solid rgba(158,234,108,.15);border-radius:12px;max-width:580px;">
                <p style="font-size:15px;color:rgba(255,255,255,.7);line-height:1.6;">
                    @if ($totals['clicks'] > 0 && $totals['reach'] > 0)
                        Dos <strong style="color:#fff;">{{ number_format($totals['reach'], 0, ',', '.') }}</strong> alcançados,
                        <strong style="color:#9EEA6C;">{{ number_format($totals['clicks'], 0, ',', '.') }} foram até o site</strong> ou entraram em contato.
                        @if ($totals['spend'] > 0)
                            Tudo isso com um investimento de <strong style="color:#fff;">R$ {{ number_format($totals['spend'], 2, ',', '.') }}</strong>.
                        @endif
                    @elseif ($totals['reach'] > 0)
                        Seus anúncios foram exibidos para
                        <strong style="color:#9EEA6C;">{{ number_format($totals['reach'], 0, ',', '.') }} pessoas</strong>
                        @if ($totals['spend'] > 0)
                            com investimento de <strong style="color:#fff;">R$ {{ number_format($totals['spend'], 2, ',', '.') }}</strong>.
                        @endif
                    @else
                        Acompanhe abaixo os resultados das suas campanhas no período.
                    @endif
                </p>
            </div>
        @else
            <div class="hero-number" style="font-size:clamp(2rem,5vw,3.5rem);opacity:.3;">Sem dados</div>
            <div style="color:rgba(255,255,255,.3);margin-top:10px;">Nenhum insight disponível para este período.</div>
        @endif
    </div>

    {{-- ── NÚMEROS GERAIS ── --}}
    @if ($hasData && $campaigns->count() > 0)
        <div style="margin-bottom:64px;">
            <div class="section-label" style="margin-bottom:16px;">Números do período</div>
            <div class="grid-4" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">

                <div class="metric-pill">
                    <span class="material-symbols-outlined" style="font-size:28px;color:rgba(158,234,108,.6);display:block;margin-bottom:10px;">visibility</span>
                    <div class="value">{{ number_format($totals['reach'], 0, ',', '.') }}</div>
                    <div class="label">pessoas viram seus anúncios</div>
                </div>

                <div class="metric-pill">
                    <span class="material-symbols-outlined" style="font-size:28px;color:rgba(158,234,108,.6);display:block;margin-bottom:10px;">ads_click</span>
                    <div class="value">{{ number_format($totals['clicks'], 0, ',', '.') }}</div>
                    <div class="label">clicaram para saber mais</div>
                </div>

                @if ($avgCtr !== null)
                    <div class="metric-pill">
                        <span class="material-symbols-outlined" style="font-size:28px;color:rgba(158,234,108,.6);display:block;margin-bottom:10px;">hub</span>
                        <div class="value">{{ number_format($ctrHuman, 1, ',', '.') }}%</div>
                        <div class="label">de engajamento — de 100 pessoas, {{ round($ctrHuman) }} clicaram</div>
                    </div>
                @endif

                @if ($totals['spend'] > 0)
                    <div class="metric-pill">
                        <span class="material-symbols-outlined" style="font-size:28px;color:rgba(158,234,108,.6);display:block;margin-bottom:10px;">payments</span>
                        <div class="value" style="font-size:1.5rem;">R$&nbsp;{{ number_format($totals['spend'], 2, ',', '.') }}</div>
                        <div class="label">investimento total no período</div>
                    </div>
                @endif

            </div>

            @if ($avgCpc !== null && $totals['clicks'] > 0)
                <div style="margin-top:12px;padding:14px 20px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:12px;display:flex;align-items:center;gap:12px;">
                    <span class="material-symbols-outlined" style="color:rgba(158,234,108,.5);font-size:20px;">info</span>
                    <p style="font-size:13px;color:rgba(255,255,255,.45);line-height:1.5;">
                        Cada pessoa que clicou custou em média <strong style="color:rgba(255,255,255,.7);">R$ {{ number_format($avgCpc, 2, ',', '.') }}</strong>.
                        @if ($avgCpc < 2)
                            Esse é um custo excelente para o seu segmento.
                        @elseif ($avgCpc < 5)
                            Resultado dentro do esperado para o mercado.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    @endif

    {{-- ── CAMPANHAS ── --}}
    @if ($campaigns->count() > 0)
        <div>
            <div class="section-label" style="margin-bottom:16px;">Campanhas ({{ $campaigns->count() }})</div>
            <div style="display:flex;flex-direction:column;gap:12px;">

                @foreach ($campaigns as $campaign)
                    @php
                        $ins = $storedInsights[$campaign->id] ?? $storedInsights[(string)$campaign->id] ?? null;
                        $cReach  = $ins['reach']  ?? 0;
                        $cClicks = $ins['clicks'] ?? 0;
                        $cSpend  = $ins['spend']  ?? 0;
                        $cCtr    = $ins['ctr']    ?? null;

                        [$tagClass, $statusLabel, $isRunning] = match($campaign->meta_status) {
                            'ACTIVE'   => ['tag-green', 'Rodando', true],
                            'PAUSED'   => ['tag-amber', 'Pausada', false],
                            'ARCHIVED' => ['tag-gray',  'Encerrada', false],
                            default    => ['tag-gray',  'Inativa', false],
                        };

                        $reachPct = $maxReach > 0 ? ($cReach / $maxReach) * 100 : 0;
                    @endphp

                    <div class="card-dark" style="padding:24px;transition:all .2s;">
                        {{-- Header da campanha --}}
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:{{ $ins ? '20px' : '0' }};">
                            <div style="flex:1;min-width:0;">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                                    @if ($isRunning)
                                        <span class="pulse"></span>
                                    @endif
                                    <span class="tag {{ $tagClass }}">{{ $statusLabel }}</span>
                                </div>
                                <h3 style="font-size:15px;font-weight:700;color:#fff;margin-top:6px;line-height:1.3;">{{ $campaign->name }}</h3>
                                @if ($campaign->start_date)
                                    <div style="font-size:12px;color:rgba(255,255,255,.25);margin-top:4px;">
                                        {{ $campaign->start_date->format('d/m/Y') }} → {{ $campaign->stop_date?->format('d/m/Y') ?? 'em andamento' }}
                                    </div>
                                @endif
                            </div>
                            @if ($cSpend > 0)
                                <div style="text-align:right;flex-shrink:0;">
                                    <div style="font-size:11px;color:rgba(255,255,255,.25);margin-bottom:2px;">investido</div>
                                    <div style="font-size:18px;font-weight:800;color:#fff;">R$ {{ number_format($cSpend, 2, ',', '.') }}</div>
                                </div>
                            @endif
                        </div>

                        @if ($ins)
                            {{-- Barra de alcance relativo (só se tiver mais de 1 campanha) --}}
                            @if ($campaigns->count() > 1 && $maxReach > 0)
                                <div style="margin-bottom:16px;">
                                    <div style="display:flex;justify-content:space-between;font-size:11px;color:rgba(255,255,255,.3);margin-bottom:6px;">
                                        <span>Alcance relativo</span>
                                        <span>{{ number_format($cReach, 0, ',', '.') }} pessoas</span>
                                    </div>
                                    <div class="progress-bar-bg">
                                        <div class="progress-bar-fill" style="width:{{ $reachPct }}%;"></div>
                                    </div>
                                </div>
                            @endif

                            {{-- Métricas em linguagem humana --}}
                            <div class="grid-6" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                                @if ($cReach > 0)
                                    <div style="padding:14px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid rgba(255,255,255,.06);">
                                        <div style="font-size:10px;color:rgba(255,255,255,.3);font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px;">Alcançou</div>
                                        <div style="font-size:1.4rem;font-weight:800;color:#fff;">{{ number_format($cReach, 0, ',', '.') }}</div>
                                        <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:2px;">pessoas únicas</div>
                                    </div>
                                @endif

                                @if ($cClicks > 0)
                                    <div style="padding:14px;background:rgba(158,234,108,.05);border-radius:10px;border:1px solid rgba(158,234,108,.12);">
                                        <div style="font-size:10px;color:rgba(158,234,108,.5);font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px;">Clicaram</div>
                                        <div style="font-size:1.4rem;font-weight:800;color:#9EEA6C;">{{ number_format($cClicks, 0, ',', '.') }}</div>
                                        <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:2px;">foram ao site/contato</div>
                                    </div>
                                @endif

                                @if ($cCtr !== null)
                                    <div style="padding:14px;background:rgba(255,255,255,.03);border-radius:10px;border:1px solid rgba(255,255,255,.06);">
                                        <div style="font-size:10px;color:rgba(255,255,255,.3);font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px;">Engajamento</div>
                                        <div style="font-size:1.4rem;font-weight:800;color:#fff;">{{ number_format((float)$cCtr, 1, ',', '.') }}%</div>
                                        <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:2px;">de quem viu, clicou</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div style="padding:20px;border:1px dashed rgba(255,255,255,.08);border-radius:10px;text-align:center;color:rgba(255,255,255,.2);font-size:13px;">
                                Sem dados para o período selecionado
                            </div>
                        @endif
                    </div>
                @endforeach

            </div>
        </div>
    @endif

    {{-- ── FOOTER ── --}}
    <div style="margin-top:64px;padding-top:24px;border-top:1px solid rgba(255,255,255,.06);display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;color:rgba(255,255,255,.15);font-size:12px;">
        <span>Relatório gerado pelo <strong style="color:rgba(255,255,255,.25);">LeadFlow</strong></span>
        <span>{{ $presentation->created_at->format('d/m/Y \à\s H:i') }}</span>
    </div>

</div>

</body>
</html>
