<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Política de Privacidade · LeadFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fustat:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-surface text-gray-900">

    {{-- Header --}}
    <header class="border-b border-gray-200 bg-white/80 backdrop-blur-sm sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold text-primary-foreground">Lead<span class="text-primary">Flow</span></span>
            </a>
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-800 transition-colors">
                ← Voltar ao início
            </a>
        </div>
    </header>

    {{-- Content --}}
    <main class="max-w-4xl mx-auto px-6 py-16">

        <div class="mb-12">
            <p class="text-sm font-medium text-primary-foreground/60 uppercase tracking-widest mb-3">Documento legal</p>
            <h1 class="text-4xl font-bold text-primary-foreground mb-4">Política de Privacidade</h1>
            <p class="text-gray-500 text-sm">Última atualização: {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>

        <div class="space-y-10 text-gray-700 leading-relaxed">

            {{-- 1 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">1. Quem somos</h2>
                <p>
                    O <strong>LeadFlow</strong> é uma plataforma de gestão de leads e campanhas desenvolvida para agências de marketing digital e gestores de tráfego. Neste documento, "nós", "nosso" ou "LeadFlow" referem-se à plataforma e seus operadores responsáveis.
                </p>
                <p class="mt-3">
                    Para dúvidas sobre esta política, entre em contato pelo e-mail:
                    <a href="mailto:agenciameoli@gmail.com" class="text-primary-foreground font-medium underline underline-offset-2">agenciameoli@gmail.com</a>
                </p>
            </section>

            {{-- 2 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">2. Quais dados coletamos</h2>
                <p class="mb-4">Coletamos apenas os dados necessários para o funcionamento da plataforma:</p>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span><strong>Dados de cadastro:</strong> nome, e-mail e senha dos usuários da plataforma.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span><strong>Dados de clientes e leads:</strong> informações inseridas pelos gestores sobre seus clientes e contatos (nome, telefone, origem do lead, status).</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span><strong>Dados do Meta Ads:</strong> quando você conecta sua conta de anúncios, coletamos tokens de acesso (armazenados de forma criptografada), identificadores de conta e métricas de campanhas como alcance, impressões, cliques e investimento.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span><strong>Dados de uso:</strong> logs de acesso e histórico de alterações de leads para fins de auditoria interna.</span>
                    </li>
                </ul>
            </section>

            {{-- 3 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">3. Como usamos os dados</h2>
                <p class="mb-4">Os dados coletados são utilizados exclusivamente para:</p>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Autenticar e identificar os usuários da plataforma.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Exibir e organizar as informações de clientes, leads e campanhas cadastradas pelo próprio usuário.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Buscar e sincronizar métricas de campanhas diretamente da API do Meta Ads em nome do usuário conectado.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Gerar relatórios e painéis de desempenho visíveis apenas ao usuário dono dos dados.</span>
                    </li>
                </ul>
                <p class="mt-4 text-sm bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-600">
                    Não vendemos, alugamos ou compartilhamos seus dados com terceiros para fins de publicidade ou qualquer finalidade não descrita nesta política.
                </p>
            </section>

            {{-- 4 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">4. Integração com o Meta (Facebook)</h2>
                <p class="mb-3">
                    O LeadFlow utiliza a <strong>API do Meta for Developers</strong> para permitir que você conecte sua conta de anúncios e visualize métricas dentro da plataforma. Ao autorizar essa conexão:
                </p>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Você autoriza o LeadFlow a ler dados de campanhas e contas de anúncio associadas ao seu perfil no Meta.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>O token de acesso fornecido pelo Meta é armazenado de forma criptografada em nossos servidores.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Você pode desconectar sua conta do Meta a qualquer momento dentro da plataforma, o que remove imediatamente o token armazenado.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Não realizamos nenhuma ação em sua conta de anúncios além da leitura de dados — não criamos, editamos ou excluímos campanhas.</span>
                    </li>
                </ul>
                <p class="mt-4 text-sm text-gray-500">
                    O uso de dados do Meta também está sujeito à
                    <a href="https://www.facebook.com/policy.php" target="_blank" rel="noopener" class="underline underline-offset-2 hover:text-gray-700">Política de Dados do Facebook</a>.
                </p>
            </section>

            {{-- 5 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">5. Armazenamento e segurança</h2>
                <p class="mb-3">Adotamos as seguintes medidas para proteger seus dados:</p>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Tokens de acesso são criptografados em repouso utilizando criptografia AES-256.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Comunicação protegida por HTTPS/TLS em todos os acessos.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Acesso aos dados restrito ao próprio usuário — cada gestor vê apenas seus clientes e campanhas.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                        <span>Senhas armazenadas com hash bcrypt, nunca em texto puro.</span>
                    </li>
                </ul>
            </section>

            {{-- 6 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">6. Seus direitos (LGPD)</h2>
                <p class="mb-4">Em conformidade com a Lei Geral de Proteção de Dados (Lei nº 13.709/2018), você tem direito a:</p>
                <div class="grid sm:grid-cols-2 gap-3">
                    @foreach([
                        ['Confirmar', 'saber se tratamos dados seus'],
                        ['Acessar', 'obter uma cópia dos seus dados'],
                        ['Corrigir', 'atualizar dados incompletos ou desatualizados'],
                        ['Excluir', 'solicitar a remoção dos seus dados'],
                        ['Portabilidade', 'receber seus dados em formato estruturado'],
                        ['Revogar', 'retirar o consentimento a qualquer momento'],
                    ] as [$title, $desc])
                    <div class="bg-white border border-gray-200 rounded-lg px-4 py-3">
                        <span class="font-semibold text-primary-foreground">{{ $title }}:</span>
                        <span class="text-gray-600 text-sm ml-1">{{ $desc }}</span>
                    </div>
                    @endforeach
                </div>
                <p class="mt-4 text-sm text-gray-600">
                    Para exercer qualquer um desses direitos, envie um e-mail para
                    <a href="mailto:agenciameoli@gmail.com" class="text-primary-foreground font-medium underline underline-offset-2">agenciameoli@gmail.com</a>.
                    Responderemos em até 15 dias úteis.
                </p>
            </section>

            {{-- 7 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">7. Cookies e sessão</h2>
                <p>
                    Utilizamos cookies apenas para manter sua sessão autenticada na plataforma. Não utilizamos cookies de rastreamento, publicidade ou análise de comportamento de terceiros.
                </p>
            </section>

            {{-- 8 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">8. Retenção de dados</h2>
                <p>
                    Seus dados são mantidos enquanto sua conta estiver ativa. Ao solicitar a exclusão da conta, todos os dados associados serão removidos em até 30 dias, exceto quando houver obrigação legal de retenção.
                </p>
            </section>

            {{-- 9 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">9. Alterações nesta política</h2>
                <p>
                    Podemos atualizar esta política periodicamente. Quando houver alterações relevantes, notificaremos os usuários por e-mail ou por aviso na plataforma. O uso continuado após a notificação implica na aceitação da nova versão.
                </p>
            </section>

            {{-- 10 --}}
            <section>
                <h2 class="text-xl font-semibold text-primary-foreground mb-3">10. Contato</h2>
                <div class="bg-white border border-gray-200 rounded-xl px-6 py-5">
                    <p class="font-semibold text-primary-foreground mb-1">LeadFlow</p>
                    <p class="text-gray-600 text-sm">E-mail: <a href="mailto:agenciameoli@gmail.com" class="underline underline-offset-2 hover:text-gray-800">agenciameoli@gmail.com</a></p>
                </div>
            </section>

        </div>

        <div class="mt-16 pt-8 border-t border-gray-200 text-center text-sm text-gray-400">
            © {{ date('Y') }} LeadFlow. Todos os direitos reservados.
        </div>

    </main>

</body>
</html>
