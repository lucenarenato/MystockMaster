<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Stock Softwares</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-fadeInUp {
            animation: fadeInUp 0.8s ease-out;
        }
        .animate-fadeInDown {
            animation: fadeInDown 0.8s ease-out;
        }
        .animate-slideInLeft {
            animation: slideInLeft 0.8s ease-out;
        }
        .animate-slideInRight {
            animation: slideInRight 0.8s ease-out;
        }
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        .backdrop-blur {
            backdrop-filter: blur(10px);
        }
        .group:hover .group-hover\:opacity-100 {
            opacity: 1;
        }
    </style>
</head>
<body class="antialiased text-white bg-slate-950">
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.16),_transparent_30%),radial-gradient(circle_at_top_right,_rgba(234,179,8,0.16),_transparent_25%),linear-gradient(180deg,rgba(15,23,42,0.95),rgba(15,23,42,0.9))]"></div>

        <header class="relative z-20">
            <div class="flex items-center justify-between px-6 py-6 mx-auto max-w-7xl lg:px-8 animate-fadeInDown">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 text-lg font-semibold rounded-2xl bg-gradient-to-r from-sky-500 to-violet-500">A</span>
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-slate-300">MystockMaster</p>
                        <p class="text-xs text-slate-400">Softwares</p>
                    </div>
                </div>
                <nav class="items-center hidden gap-10 text-sm font-medium text-slate-200 lg:flex">
                    <a href="#origem" class="transition hover:text-white hover:border-b-2 hover:border-sky-500">Nossa Origem</a>
                    <a href="#funcionalidades" class="transition hover:text-white hover:border-b-2 hover:border-sky-500">Funcionalidades</a>
                    <a href="#solucoes" class="transition hover:text-white hover:border-b-2 hover:border-sky-500">Soluções</a>
                    <a href="#contato" class="transition hover:text-white hover:border-b-2 hover:border-sky-500">Contato</a>
                </nav>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-semibold text-white transition border rounded-full border-slate-600 bg-slate-900/70 hover:border-slate-400 hover:bg-slate-800">Entrar</a>
                </div>
            </div>
        </header>

        <main class="relative z-10 flex flex-col gap-16 px-6 py-20 mx-auto max-w-7xl lg:px-8 lg:py-28">
            <section class="grid gap-12 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-center">
                <div class="space-y-6 animate-fadeInUp">
                    <p class="inline-flex rounded-full border border-slate-700 bg-slate-900/60 px-4 py-2 text-xs uppercase tracking-[0.3em] text-sky-300 delay-100 animate-fadeInUp">WMS + Operação Logística</p>
                    <h1 class="text-4xl font-semibold tracking-tight text-white delay-200 sm:text-5xl lg:text-6xl animate-fadeInUp">Transformando processos em resultados na operação de armazém.</h1>
                    <p class="max-w-2xl text-lg leading-8 delay-300 text-slate-300 animate-fadeInUp">Controle de produtividade, rastreabilidade, automação e métricas em tempo real para sua operação logística. Tudo em uma plataforma leve e personalizável.</p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center delay-400 animate-fadeInUp">
                        <a href="#contato" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold transition rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 hover:shadow-lg hover:shadow-sky-500/50">Quero conhecer</a>
                        <a href="#funcionalidades" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold transition border rounded-full border-slate-700 bg-white/5 text-slate-100 hover:border-slate-500 hover:bg-white/10">Ver funcionalidades</a>
                    </div>
                </div>
                <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-white/5 p-6 shadow-2xl shadow-slate-950/50 animate-slideInRight">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(96,165,250,0.15),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(139,92,246,0.18),_transparent_35%)]"></div>
                    <div class="relative flex min-h-[420px] flex-col justify-between rounded-[1.75rem] border border-white/10 bg-slate-950/70 p-8 text-slate-100">
                        <div class="space-y-4">
                            <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Dashboard operacional</p>
                            <h2 class="text-3xl font-semibold">Visão do armazém em tempo real</h2>
                            <p class="text-slate-300">Métricas de produtividade, disponibilidade e pagamentos para tomar decisões rápidas e com precisão.</p>
                        </div>
                        <div class="grid gap-4 p-4 text-sm border rounded-3xl border-white/5 bg-slate-900/70">
                            <div class="flex items-center justify-between px-4 py-4 transition rounded-3xl bg-slate-950/40 hover:bg-slate-900/60">
                                <span class="text-slate-300">Produtividade média por turno</span>
                                <strong class="text-sky-400 animate-pulse">202.17%</strong>
                            </div>
                            <div class="flex items-center justify-between px-4 py-4 transition rounded-3xl bg-slate-950/40 hover:bg-slate-900/60">
                                <span class="text-slate-300">Disponibilidade</span>
                                <strong class="text-emerald-400 animate-pulse">78.5%</strong>
                            </div>
                            <div class="grid gap-3 p-4 rounded-3xl bg-slate-950/40 text-slate-300">
                                <span class="text-sm uppercase tracking-[0.2em] text-slate-400">Visão rápida</span>
                                <div class="grid grid-cols-3 gap-3 text-xs text-center">
                                    <div class="p-3 transition rounded-2xl bg-slate-900/80 hover:bg-sky-900/40">100%</div>
                                    <div class="p-3 transition rounded-2xl bg-slate-900/80 hover:bg-sky-900/40">80%</div>
                                    <div class="p-3 transition rounded-2xl bg-slate-900/80 hover:bg-sky-900/40">120%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="origem" class="pt-16 space-y-6 border-t border-white/10 animate-fadeInUp">
                <div class="max-w-2xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Nossa Origem</p>
                    <h2 class="text-3xl font-semibold text-white">Uma empresa que nasceu com a finalidade de melhorar os resultados da operação.</h2>
                    <p class="mt-4 text-slate-300">A MystockMaster foi criada para dar visibilidade total à operação logística, reduzir desperdícios e transformar dados em ações práticas no chão de fábrica e no armazém.</p>
                </div>
                <div class="grid gap-6 lg:grid-cols-4">
                    <div class="p-6 transition delay-100 border rounded-3xl border-white/10 bg-slate-900/70 text-slate-200 group hover:bg-slate-800/50 hover:border-sky-500/30 animate-fadeInUp">
                        <h3 class="text-xl font-semibold text-white">Plataforma ativa</h3>
                        <p class="mt-3 text-sm text-slate-400">Visão operacional em tempo real para processos logísticos e armazém.</p>
                    </div>
                    <div class="p-6 transition delay-200 border rounded-3xl border-white/10 bg-slate-900/70 text-slate-200 group hover:bg-slate-800/50 hover:border-sky-500/30 animate-fadeInUp">
                        <h3 class="text-xl font-semibold text-white">Eficiência operacional</h3>
                        <p class="mt-3 text-sm text-slate-400">Métricas e gamificação para engajar equipes e reduzir ciclo de execução.</p>
                    </div>
                    <div class="p-6 transition delay-300 border rounded-3xl border-white/10 bg-slate-900/70 text-slate-200 group hover:bg-slate-800/50 hover:border-sky-500/30 animate-fadeInUp">
                        <h3 class="text-xl font-semibold text-white">Alta personalização</h3>
                        <p class="mt-3 text-sm text-slate-400">Configuração adaptada à sua operação, integração e segurança de dados.</p>
                    </div>
                    <div class="p-6 transition border rounded-3xl border-white/10 bg-slate-900/70 text-slate-200 group hover:bg-slate-800/50 hover:border-sky-500/30 delay-400 animate-fadeInUp">
                        <h3 class="text-xl font-semibold text-white">Futuro conectado</h3>
                        <p class="mt-3 text-sm text-slate-400">Arquitetura preparada para acompanhar novos processos e tecnologias logísticas.</p>
                    </div>
                </div>
            </section>

            <section id="funcionalidades" class="grid gap-12 lg:grid-cols-[420px_minmax(0,1fr)] lg:items-center">
                <div class="space-y-6 animate-slideInLeft">
                    <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Funcionalidades</p>
                    <h2 class="text-3xl font-semibold text-white">Tudo o que você precisa para rastrear, planejar e executar com qualidade.</h2>
                    <p class="text-slate-300">Relatórios customizáveis, cadastro ágil, expedição com rastreabilidade e dashboards com indicadores de performance em um único painel.</p>
                    <div class="grid gap-4">
                        <div class="p-5 transition border rounded-3xl border-white/10 bg-slate-900/70 group hover:bg-slate-800/50 hover:border-sky-500/30">
                            <h3 class="font-semibold text-white">Monitoramento de produtividade</h3>
                            <p class="mt-2 text-sm text-slate-400">Métricas em tempo real para acompanhar desempenho de turnos e áreas.</p>
                        </div>
                        <div class="p-5 transition border rounded-3xl border-white/10 bg-slate-900/70 group hover:bg-slate-800/50 hover:border-sky-500/30">
                            <h3 class="font-semibold text-white">Gamificação para equipes</h3>
                            <p class="mt-2 text-sm text-slate-400">Feedback contínuo para melhorar engajamento e resultados.</p>
                        </div>
                        <div class="p-5 transition border rounded-3xl border-white/10 bg-slate-900/70 group hover:bg-slate-800/50 hover:border-sky-500/30">
                            <h3 class="font-semibold text-white">Relatórios e dashboards</h3>
                            <p class="mt-2 text-sm text-slate-400">Visão clara dos indicadores para planejar ações e reduzir custos.</p>
                        </div>
                        <div class="p-5 transition border rounded-3xl border-white/10 bg-slate-900/70 group hover:bg-slate-800/50 hover:border-sky-500/30">
                            <h3 class="font-semibold text-white">Operações de recebimento e expedição</h3>
                            <p class="mt-2 text-sm text-slate-400">Controle de mercadorias com processos simples e rastreáveis.</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-[2rem] bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/60 animate-slideInRight">
                    <img src="{{ asset('images/home/Distribution-center.jpeg') }}" alt="Dashboard" class="object-cover w-full h-auto shadow-lg rounded-2xl">
                </div>
            </section>

            <section class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">
                <div class="p-8 space-y-6 border rounded-3xl border-white/10 bg-slate-900/70 animate-slideInLeft">
                    <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Qual o problema?</p>
                    <h2 class="text-3xl font-semibold text-white">O desafio é ter controle real da produtividade no armazém.</h2>
                    <p class="text-slate-300">A maioria dos WMS tradicionais entrega apenas relatórios genéricos e não integra o fluxo operacional. Isso dificulta a tomada de decisão, desorganiza o processo e compromete a eficiência.</p>
                    <p class="text-slate-300">A MystockMaster resolve esse cenário com um sistema inteligente de convocações ativas, organização em tempo real e uso de colaboradores com mais controle e precisão.</p>
                </div>
                <div class="p-8 space-y-4 border rounded-3xl border-white/10 bg-slate-900/70 animate-slideInRight">
                    <h3 class="text-xl font-semibold text-white">Resultados</h3>
                    <div class="grid gap-4 text-slate-300">
                        <div class="p-5 transition rounded-3xl bg-slate-950/80 hover:bg-sky-900/20">
                            <strong class="text-white">Aumento da produtividade média por turno</strong>
                        </div>
                        <div class="p-5 transition rounded-3xl bg-slate-950/80 hover:bg-sky-900/20">
                            <strong class="text-white">Melhora na ocupação e no planejamento</strong>
                        </div>
                        <div class="p-5 transition rounded-3xl bg-slate-950/80 hover:bg-sky-900/20">
                            <strong class="text-white">Decisões mais rápidas com dados reais</strong>
                        </div>
                    </div>
                </div>
            </section>

            <section id="solucoes" class="pt-16 space-y-8 border-t border-white/10 animate-fadeInUp">
                <div class="max-w-2xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Soluções personalizadas</p>
                    <h2 class="text-3xl font-semibold text-white">Soluções sob medida para o seu armazém e equipe.</h2>
                </div>
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="p-8 space-y-4 transition delay-100 border rounded-3xl border-white/10 bg-slate-900/70 text-slate-200 hover:bg-slate-800/50 animate-fadeInUp">
                        <h3 class="text-xl font-semibold text-white">Painéis visuais no estilo Kanban</h3>
                        <p class="text-slate-400">Acompanhe tarefas, recepção e expedição de forma intuitiva com cards e indicadores visuais.</p>
                        <ul class="space-y-3 text-sm text-slate-300">
                            <li class="flex items-center gap-2"><span class="text-sky-400">✓</span> Monitoramento de produtividade e variáveis</li>
                            <li class="flex items-center gap-2"><span class="text-sky-400">✓</span> Gamificação para engajar equipes</li>
                            <li class="flex items-center gap-2"><span class="text-sky-400">✓</span> Relatórios e dashboards customizáveis</li>
                            <li class="flex items-center gap-2"><span class="text-sky-400">✓</span> Cadastro de produtos, tarefas e usuários</li>
                            <li class="flex items-center gap-2"><span class="text-sky-400">✓</span> Recebimento e movimentação de mercadorias</li>
                            <li class="flex items-center gap-2"><span class="text-sky-400">✓</span> Expedição com rastreabilidade</li>
                        </ul>
                    </div>
                    <div class="p-8 delay-200 border rounded-3xl border-white/10 bg-slate-900/70 animate-fadeInUp">
                        <img src="{{ asset('images/home/diseo-almacenes-distribucin-espacios.1.0.avif') }}" alt="Tecnologia" class="object-cover w-full h-auto shadow-lg rounded-2xl">
                    </div>
                </div>
            </section>

            <section id="contato" class="pt-16 space-y-8 border-t border-white/10 animate-fadeInUp">
                <div class="max-w-2xl">
                    <p class="text-sm uppercase tracking-[0.3em] text-sky-300">Contato</p>
                    <h2 class="text-3xl font-semibold text-white">Quer otimizar sua operação?</h2>
                    <p class="text-slate-300">Fale conosco e descubra como a MystockMaster WMS pode fazer a diferença na sua operação logística.</p>
                </div>
                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="p-8 space-y-4 delay-100 border rounded-3xl border-white/10 bg-slate-900/70 text-slate-200 animate-fadeInUp">
                        <p class="text-lg font-semibold text-white">Entre em contato</p>
                        <div class="space-y-3 text-sm text-slate-300">
                            <p><strong>Telefone:</strong> +55 51 99388-4569</p>
                            <p><strong>Email:</strong> comercial@MystockMastersmart.com.br</p>
                            <p><strong>Curitiba:</strong> Av. Rep. Argentina, 1160 - Sala 907, Água Verde</p>
                            <p><strong>Campo Grande:</strong> R. Alfredo Lisboa, 956, Tijuca</p>
                        </div>
                        <a href="mailto:comercial@MystockMastersmart.com.br" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold transition rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 hover:shadow-lg hover:shadow-sky-500/50">Enviar email</a>
                    </div>
                    <div class="p-8 delay-200 border rounded-3xl border-white/10 bg-slate-900/70 animate-fadeInUp">
                        <div class="grid gap-4">
                            <div class="p-6 transition rounded-3xl bg-slate-950/80 text-slate-200 hover:bg-sky-900/20">
                                <h3 class="text-lg font-semibold text-white">Suporte técnico</h3>
                                <p class="mt-2 text-slate-400">Assistência dedicada para implantação e operação contínua.</p>
                            </div>
                            <div class="p-6 transition rounded-3xl bg-slate-950/80 text-slate-200 hover:bg-sky-900/20">
                                <h3 class="text-lg font-semibold text-white">Segurança</h3>
                                <p class="mt-2 text-slate-400">Segurança de dados e controle de acesso para sua operação.</p>
                            </div>
                            <div class="p-6 transition rounded-3xl bg-slate-950/80 text-slate-200 hover:bg-sky-900/20">
                                <h3 class="text-lg font-semibold text-white">Desempenho de alta</h3>
                                <p class="mt-2 text-slate-400">Plataforma leve e pronta para operações reais de WMS.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="py-8 border-t border-white/10 bg-slate-950/80">
            <div class="flex flex-col gap-6 px-6 mx-auto max-w-7xl text-slate-400 sm:flex-row sm:items-center sm:justify-between lg:px-8">
                <div class="space-y-1 text-sm">
                    <p>MystockMaster Softwares © 2025</p>
                    <p>Desenvolvimento de sistemas logísticos e WMS.</p>
                </div>
                <div class="flex flex-wrap gap-4 text-sm text-slate-300">
                    <a href="https://wa.me/+5551993884569" target="_blank" class="transition hover:text-sky-400">WhatsApp</a>
                    <a href="https://instagram.com" target="_blank" class="transition hover:text-sky-400">Instagram</a>
                    <a href="https://linkedin.com" target="_blank" class="transition hover:text-sky-400">LinkedIn</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
