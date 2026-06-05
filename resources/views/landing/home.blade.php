<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>MystockMaster — WMS & Operação Logística</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        @keyframes float { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-12px); } }
        .animate-fade-up { animation: fadeInUp 0.9s ease-out forwards; }
        .animate-float { animation: float 5s ease-in-out infinite; }
        .reveal { opacity:0; transform:translateY(40px); transition:opacity 0.7s ease, transform 0.7s ease; }
        .reveal-left { opacity:0; transform:translateX(-40px); transition:opacity 0.7s ease, transform 0.7s ease; }
        .reveal-right { opacity:0; transform:translateX(40px); transition:opacity 0.7s ease, transform 0.7s ease; }
        .reveal.visible, .reveal-left.visible, .reveal-right.visible { opacity:1; transform:translate(0,0); }
        .delay-1 { transition-delay:0.1s; } .delay-2 { transition-delay:0.2s; }
        .delay-3 { transition-delay:0.3s; } .delay-4 { transition-delay:0.4s; }
        .header-scrolled { backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); background:rgba(15,23,42,0.92)!important; border-bottom:1px solid rgba(255,255,255,0.08); }
        .text-gradient { background:linear-gradient(135deg,#38bdf8,#818cf8); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    </style>
</head>
<body class="antialiased text-white bg-slate-950 overflow-x-hidden">

    <!-- STICKY HEADER -->
    <header id="site-header" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-gradient-to-b from-slate-950/90 to-transparent">
        <div class="flex items-center justify-between px-6 py-4 mx-auto max-w-7xl lg:px-8">
            <a href="/" class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-500 to-violet-600 shadow-lg shadow-sky-500/30 flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26.55 5.15H26.45C26.1 5.15 25.75 5.15 25.4 5.15V4C25.4 2.3 24 0.85 22.25 0.85H7.15C5.4 0.9 4 2.3 4 4V21.1C4 21.75 4.25 22.35 4.7 22.85L12 30.35C12.45 30.85 13.1 31.1 13.8 31.1H22.95C24.3 31.1 25.45 30 25.45 28.6V28.15L27.85 23.3C27.95 23.15 27.95 23 27.95 22.8L28 6.6C28 5.85 27.35 5.2 26.55 5.15ZM22.95 28.9H14.15V21.95C14.15 21.15 13.5 20.55 12.75 20.55H6.25V4C6.25 3.5 6.65 3.1 7.15 3.1H22.35C22.85 3.1 23.25 3.5 23.25 4V5.1C22.9 5.1 22.6 5.1 22.25 5.1C21.4 5.1 20.75 5.75 20.75 6.55L20.7 22.8C20.7 23 20.75 23.15 20.8 23.3L23.25 28.15V28.6C23.2 28.75 23.1 28.9 22.95 28.9Z" fill="white"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-widest text-white leading-none">MystockMaster</p>
                    <p class="text-xs text-slate-400">WMS & Logística</p>
                </div>
            </a>

            <nav class="items-center hidden gap-8 text-sm font-medium text-slate-300 lg:flex">
                <a href="#origem" class="transition-colors hover:text-sky-400 duration-200">Nossa Origem</a>
                <a href="#funcionalidades" class="transition-colors hover:text-sky-400 duration-200">Funcionalidades</a>
                <a href="#solucoes" class="transition-colors hover:text-sky-400 duration-200">Soluções</a>
                <a href="#contato" class="transition-colors hover:text-sky-400 duration-200">Contato</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="hidden sm:inline-flex items-center px-5 py-2 text-sm font-medium rounded-full border border-slate-600 bg-white/5 text-slate-200 hover:border-sky-500/40 hover:bg-sky-500/10 hover:text-sky-300 transition-all duration-200">Entrar</a>
                <a href="#contato" class="hidden sm:inline-flex items-center px-5 py-2 text-sm font-semibold rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 shadow-lg shadow-sky-500/25 transition-all duration-200">Solicitar demo</a>
                <button id="menu-btn" class="lg:hidden flex flex-col gap-1.5 p-2" aria-label="Menu">
                    <span id="l1" class="block w-6 h-0.5 bg-white transition-all duration-300 origin-center"></span>
                    <span id="l2" class="block w-6 h-0.5 bg-white transition-all duration-300"></span>
                    <span id="l3" class="block w-4 h-0.5 bg-white transition-all duration-300 origin-center"></span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-slate-950 border-t border-white/10 px-6 pb-6">
            <nav class="flex flex-col gap-1 pt-4 text-sm font-medium text-slate-300">
                <a href="#origem" class="py-3 border-b border-white/5 hover:text-sky-400 transition-colors">Nossa Origem</a>
                <a href="#funcionalidades" class="py-3 border-b border-white/5 hover:text-sky-400 transition-colors">Funcionalidades</a>
                <a href="#solucoes" class="py-3 border-b border-white/5 hover:text-sky-400 transition-colors">Soluções</a>
                <a href="#contato" class="py-3 hover:text-sky-400 transition-colors">Contato</a>
                <div class="flex gap-3 pt-4">
                    <a href="{{ route('login') }}" class="flex-1 text-center py-2.5 text-sm border border-slate-600 rounded-full hover:border-sky-500/40 transition-colors">Entrar</a>
                    <a href="#contato" class="flex-1 text-center py-2.5 text-sm font-semibold rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 transition-colors">Demo</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- HERO -->
    <section class="relative min-h-screen flex items-center overflow-hidden" style="background-color:#020617;">
        <!-- Fundo com imagem escurecida via filter -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0" style="
                background-image: url('{{ asset('images/home/logistica_intelligenza_artificiale.jpg') }}');
                background-size: cover;
                background-position: center;
                filter: brightness(0.25) saturate(0.6);
            "></div>
            <!-- overlay sólido escuro -->
            <div class="absolute inset-0" style="background: linear-gradient(180deg, rgba(2,6,23,0.55) 0%, rgba(2,6,23,0.25) 50%, rgba(2,6,23,0.95) 100%);"></div>
            <div class="absolute inset-0" style="background: linear-gradient(90deg, rgba(2,6,23,0.7) 0%, rgba(2,6,23,0.1) 60%, rgba(2,6,23,0.5) 100%);"></div>
        </div>
        <!-- Brilhos coloridos sutis -->
        <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(ellipse 60% 50% at 20% 60%, rgba(56,189,248,0.07) 0%, transparent 100%), radial-gradient(ellipse 50% 40% at 75% 25%, rgba(139,92,246,0.07) 0%, transparent 100%);"></div>

        <div class="relative z-10 px-6 pt-40 pb-28 mx-auto max-w-7xl lg:px-8 w-full">
            <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
                <!-- Left -->
                <div class="space-y-8 animate-fade-up">
                    <div class="inline-flex items-center gap-2 rounded-full border border-sky-500/40 bg-sky-500/10 px-4 py-2 text-xs uppercase tracking-widest text-sky-300">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                        WMS + Operação Logística
                    </div>
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl lg:text-6xl xl:text-7xl leading-[1.08]">
                        Transformando<br>processos em<br>
                        <span class="text-gradient">resultados.</span>
                    </h1>
                    <p class="max-w-xl text-lg leading-relaxed text-slate-200">
                        Controle de produtividade, rastreabilidade, automação e métricas em tempo real para sua operação logística. Tudo em uma plataforma leve e personalizável.
                    </p>
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <a href="#contato" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-sm font-semibold rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 shadow-xl shadow-sky-500/30 transition-all duration-300 hover:-translate-y-0.5">
                            Solicitar demonstração
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </a>
                        <a href="#funcionalidades" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 text-sm font-medium rounded-full border border-white/30 bg-white/8 text-white hover:border-white/50 hover:bg-white/12 transition-all duration-300">
                            Ver funcionalidades
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-5 pt-2">
                        <span class="flex items-center gap-2 text-sm text-slate-300"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7l3 3 7-7" stroke="#38bdf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Sem instalação complexa</span>
                        <span class="flex items-center gap-2 text-sm text-slate-300"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7l3 3 7-7" stroke="#38bdf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Suporte dedicado</span>
                        <span class="flex items-center gap-2 text-sm text-slate-300"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M2 7l3 3 7-7" stroke="#38bdf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>Alta disponibilidade</span>
                    </div>
                </div>

                <!-- Right: Dashboard -->
                <div class="relative animate-float">
                    <div class="absolute -inset-6 rounded-[2.5rem] bg-gradient-to-r from-sky-500/10 to-violet-500/10 blur-3xl"></div>
                    <div class="relative overflow-hidden rounded-[2rem] border border-slate-700/60 bg-slate-900 p-6 shadow-2xl">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-sky-300">Dashboard Operacional</p>
                                <h2 class="text-lg font-semibold text-white mt-0.5">Visão do armazém</h2>
                            </div>
                            <span class="flex items-center gap-1.5 text-xs text-emerald-400 bg-emerald-400/10 px-3 py-1 rounded-full border border-emerald-400/20">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>Ao vivo
                            </span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1">Produtividade/turno</p>
                                <p class="text-2xl font-bold text-sky-400">202%</p>
                                <p class="text-xs text-emerald-400 mt-0.5">↑ +14% esta semana</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700/50">
                                <p class="text-xs text-slate-400 mb-1">Disponibilidade</p>
                                <p class="text-2xl font-bold text-emerald-400">99.7%</p>
                                <p class="text-xs text-slate-400 mt-0.5">Uptime garantido</p>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-800 border border-slate-700/50 mb-3">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-xs text-slate-400">Performance por área</p>
                                <p class="text-xs text-sky-300">Últimos 7 dias</p>
                            </div>
                            <div class="flex items-end gap-1.5 h-10">
                                <div class="flex-1 rounded-sm bg-sky-500/50" style="height:45%"></div>
                                <div class="flex-1 rounded-sm bg-sky-500/60" style="height:65%"></div>
                                <div class="flex-1 rounded-sm bg-sky-500/70" style="height:80%"></div>
                                <div class="flex-1 rounded-sm bg-sky-400" style="height:100%"></div>
                                <div class="flex-1 rounded-sm bg-sky-500/80" style="height:90%"></div>
                                <div class="flex-1 rounded-sm bg-sky-500/70" style="height:75%"></div>
                                <div class="flex-1 rounded-sm bg-sky-500/60" style="height:85%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-800 border border-slate-700/50">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    <div class="w-6 h-6 rounded-full bg-sky-500 flex items-center justify-center text-xs font-bold border-2 border-slate-900">A</div>
                                    <div class="w-6 h-6 rounded-full bg-violet-500 flex items-center justify-center text-xs font-bold border-2 border-slate-900">B</div>
                                    <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-xs font-bold border-2 border-slate-900">C</div>
                                </div>
                                <span class="text-xs text-slate-300">12 operadores ativos</span>
                            </div>
                            <span class="text-xs text-emerald-400">● Online</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1 text-slate-500 animate-bounce">
            <span class="text-xs tracking-widest uppercase">Scroll</span>
            <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M4 7l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
    </section>

    <!-- STATS -->
    <section class="py-16 border-y border-slate-800 bg-slate-900">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">
            <div class="grid grid-cols-2 gap-8 lg:grid-cols-4 text-center">
                <div class="reveal">
                    <p class="text-3xl lg:text-4xl font-bold text-sky-400">200%+</p>
                    <p class="mt-2 text-sm text-slate-400">Produtividade por turno</p>
                </div>
                <div class="reveal delay-1">
                    <p class="text-3xl lg:text-4xl font-bold text-violet-400">99.7%</p>
                    <p class="mt-2 text-sm text-slate-400">Disponibilidade</p>
                </div>
                <div class="reveal delay-2">
                    <p class="text-3xl lg:text-4xl font-bold text-emerald-400">-35%</p>
                    <p class="mt-2 text-sm text-slate-400">Tempo de ciclo</p>
                </div>
                <div class="reveal delay-3">
                    <p class="text-3xl lg:text-4xl font-bold text-amber-400">24/7</p>
                    <p class="mt-2 text-sm text-slate-400">Monitoramento</p>
                </div>
            </div>
        </div>
    </section>

    <main class="px-6 mx-auto max-w-7xl lg:px-8 bg-slate-950">

        <!-- NOSSA ORIGEM -->
        <section id="origem" class="py-24 space-y-14">
            <div class="max-w-3xl reveal">
                <p class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-sky-300 mb-4"><span class="w-8 h-px bg-sky-500"></span>Nossa Origem</p>
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">Uma empresa que nasceu para transformar a operação logística.</h2>
                <p class="mt-6 text-lg text-slate-300 leading-relaxed">A MystockMaster foi criada para dar visibilidade total à operação logística, reduzir desperdícios e transformar dados em ações práticas no chão de fábrica e no armazém.</p>
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/30 transition-all duration-300 group reveal">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('images/icons/icon-04.svg') }}" alt="" class="w-6 h-6 opacity-80">
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Plataforma ativa</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Visão operacional em tempo real para processos logísticos e armazém.</p>
                </div>
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/30 transition-all duration-300 group reveal delay-1">
                    <div class="w-12 h-12 rounded-2xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('images/icons/icon-02.svg') }}" alt="" class="w-6 h-6 opacity-80">
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Eficiência operacional</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Métricas e gamificação para engajar equipes e reduzir o ciclo de execução.</p>
                </div>
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/30 transition-all duration-300 group reveal delay-2">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('images/icons/icon-03.svg') }}" alt="" class="w-6 h-6 opacity-80">
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Alta personalização</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Configuração adaptada à sua operação, integração e segurança de dados.</p>
                </div>
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/30 transition-all duration-300 group reveal delay-3">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ asset('images/icons/icon-05.svg') }}" alt="" class="w-6 h-6 opacity-80">
                    </div>
                    <h3 class="text-lg font-semibold text-white mb-2">Futuro conectado</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Arquitetura preparada para acompanhar novos processos e tecnologias.</p>
                </div>
            </div>
        </section>

        <!-- FUNCIONALIDADES -->
        <section id="funcionalidades" class="py-24 border-t border-white/5">
            <div class="grid gap-16 lg:grid-cols-2 lg:items-center">
                <div class="relative order-2 lg:order-1 reveal-left">
                    <div class="absolute -inset-4 rounded-[2.5rem] bg-gradient-to-br from-sky-500/10 to-violet-500/10 blur-2xl"></div>
                    <div class="relative overflow-hidden rounded-[2rem] border border-white/10 shadow-2xl">
                        <img src="{{ asset('images/home/Distribution-center.jpeg') }}" alt="Centro de distribuição" class="w-full h-auto object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                        <div class="absolute bottom-5 left-5 right-5">
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-900 border border-white/10">
                                <div class="w-8 h-8 rounded-xl bg-sky-500/20 flex items-center justify-center flex-shrink-0">
                                    <img src="{{ asset('images/icons/icon-04.svg') }}" alt="" class="w-4 h-4 opacity-80">
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Produtividade hoje</p>
                                    <p class="text-sm font-semibold text-sky-400">+202% acima da meta</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8 order-1 lg:order-2 reveal-right">
                    <div>
                        <p class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-sky-300 mb-4"><span class="w-8 h-px bg-sky-500"></span>Funcionalidades</p>
                        <h2 class="text-3xl sm:text-4xl font-bold text-white leading-tight">Tudo que você precisa para rastrear, planejar e executar.</h2>
                        <p class="mt-4 text-slate-300 leading-relaxed">Relatórios customizáveis, cadastro ágil, expedição com rastreabilidade e dashboards com indicadores de performance em um único painel.</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-white/5 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/20 transition-all duration-300 group">
                            <div class="w-9 h-9 rounded-xl bg-sky-500/15 flex items-center justify-center flex-shrink-0 group-hover:bg-sky-500/25 transition"><img src="{{ asset('images/icons/icon-04.svg') }}" alt="" class="w-4 h-4 opacity-80"></div>
                            <div><h3 class="font-semibold text-white mb-1">Monitoramento de produtividade</h3><p class="text-sm text-slate-400">Métricas em tempo real para acompanhar desempenho de turnos e áreas.</p></div>
                        </div>
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-white/5 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/20 transition-all duration-300 group">
                            <div class="w-9 h-9 rounded-xl bg-violet-500/15 flex items-center justify-center flex-shrink-0 group-hover:bg-violet-500/25 transition"><img src="{{ asset('images/icons/icon-05.svg') }}" alt="" class="w-4 h-4 opacity-80"></div>
                            <div><h3 class="font-semibold text-white mb-1">Gamificação para equipes</h3><p class="text-sm text-slate-400">Feedback contínuo para melhorar engajamento e resultados das equipes.</p></div>
                        </div>
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-white/5 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/20 transition-all duration-300 group">
                            <div class="w-9 h-9 rounded-xl bg-emerald-500/15 flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-500/25 transition"><img src="{{ asset('images/icons/icon-01.svg') }}" alt="" class="w-4 h-4 opacity-80"></div>
                            <div><h3 class="font-semibold text-white mb-1">Relatórios e dashboards</h3><p class="text-sm text-slate-400">Visão clara dos indicadores para planejar ações e reduzir custos operacionais.</p></div>
                        </div>
                        <div class="flex items-start gap-4 p-4 rounded-2xl border border-white/5 bg-slate-900 hover:bg-slate-800 hover:border-sky-500/20 transition-all duration-300 group">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/15 flex items-center justify-center flex-shrink-0 group-hover:bg-amber-500/25 transition"><img src="{{ asset('images/icons/icon-02.svg') }}" alt="" class="w-4 h-4 opacity-80"></div>
                            <div><h3 class="font-semibold text-white mb-1">Recebimento e expedição</h3><p class="text-sm text-slate-400">Controle de mercadorias com processos simples, ágeis e rastreáveis.</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PROBLEMA / SOLUÇÃO -->
        <section class="py-24 border-t border-white/5">
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="relative overflow-hidden p-8 rounded-3xl border border-white/10 bg-slate-900 reveal-left">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-red-500/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
                    <p class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-red-300 mb-4"><span class="w-8 h-px bg-red-500/60"></span>O desafio</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-6 leading-tight">O controle real da produtividade no armazém é o maior desafio logístico.</h2>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center flex-shrink-0 mt-0.5"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1.5 1.5l5 5M6.5 1.5l-5 5" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                            <p class="text-sm text-slate-300 leading-relaxed">WMS tradicionais entregam apenas relatórios genéricos sem integração ao fluxo operacional</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center flex-shrink-0 mt-0.5"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1.5 1.5l5 5M6.5 1.5l-5 5" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                            <p class="text-sm text-slate-300 leading-relaxed">Dificuldade na tomada de decisão e desorganização do processo logístico</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center flex-shrink-0 mt-0.5"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1.5 1.5l5 5M6.5 1.5l-5 5" stroke="#f87171" stroke-width="1.5" stroke-linecap="round"/></svg></div>
                            <p class="text-sm text-slate-300 leading-relaxed">Baixo engajamento das equipes sem visibilidade de metas e performance individual</p>
                        </div>
                    </div>
                </div>

                <div class="relative overflow-hidden p-8 rounded-3xl border border-sky-500/20 bg-slate-900 border-sky-500/20 reveal-right">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-sky-500/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl"></div>
                    <p class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-sky-300 mb-4"><span class="w-8 h-px bg-sky-500"></span>Nossa solução</p>
                    <h2 class="text-2xl sm:text-3xl font-bold text-white mb-6 leading-tight">MystockMaster resolve isso com inteligência e precisão.</h2>
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-sky-500/20 border border-sky-500/30 flex items-center justify-center flex-shrink-0 mt-0.5"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1 4l2.5 2.5 3.5-3.5" stroke="#38bdf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p class="text-sm text-slate-300 leading-relaxed">Sistema inteligente de convocações ativas e organização em tempo real</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-sky-500/20 border border-sky-500/30 flex items-center justify-center flex-shrink-0 mt-0.5"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1 4l2.5 2.5 3.5-3.5" stroke="#38bdf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p class="text-sm text-slate-300 leading-relaxed">Métricas de produtividade e gamificação para engajar colaboradores</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-5 h-5 rounded-full bg-sky-500/20 border border-sky-500/30 flex items-center justify-center flex-shrink-0 mt-0.5"><svg width="8" height="8" viewBox="0 0 8 8" fill="none"><path d="M1 4l2.5 2.5 3.5-3.5" stroke="#38bdf8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                            <p class="text-sm text-slate-300 leading-relaxed">Dados em tempo real para decisões rápidas com mais precisão e controle</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="text-center p-3 rounded-2xl bg-slate-900 border border-white/5">
                            <p class="text-xl font-bold text-sky-400">+200%</p>
                            <p class="text-xs text-slate-400 mt-1">Produtividade</p>
                        </div>
                        <div class="text-center p-3 rounded-2xl bg-slate-900 border border-white/5">
                            <p class="text-xl font-bold text-emerald-400">-35%</p>
                            <p class="text-xs text-slate-400 mt-1">Tempo de ciclo</p>
                        </div>
                        <div class="text-center p-3 rounded-2xl bg-slate-900 border border-white/5">
                            <p class="text-xl font-bold text-violet-400">3x</p>
                            <p class="text-xs text-slate-400 mt-1">Mais controle</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SOLUÇÕES -->
        <section id="solucoes" class="py-24 border-t border-white/5">
            <div class="max-w-3xl mb-14 reveal">
                <p class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-sky-300 mb-4"><span class="w-8 h-px bg-sky-500"></span>Soluções personalizadas</p>
                <h2 class="text-3xl sm:text-4xl font-bold text-white leading-tight">Soluções sob medida para o seu armazém e equipe.</h2>
            </div>
            <div class="grid gap-8 lg:grid-cols-2">
                <div class="p-8 rounded-3xl border border-white/10 bg-slate-900 hover:border-sky-500/20 hover:bg-slate-800 transition-all duration-300 reveal-left">
                    <div class="w-12 h-12 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center mb-6">
                        <img src="{{ asset('images/icons/icon-06.svg') }}" alt="" class="w-6 h-6 opacity-80">
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-3">Painéis visuais estilo Kanban</h3>
                    <p class="text-slate-400 text-sm mb-6 leading-relaxed">Acompanhe tarefas, recepção e expedição de forma intuitiva com cards e indicadores visuais em tempo real.</p>
                    <ul class="space-y-2.5 text-sm text-slate-300">
                        <li class="flex items-center gap-2.5"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#0ea5e9" stroke-width="1.2"/><path d="M4 7l2 2 4-4" stroke="#0ea5e9" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Monitoramento de produtividade e variáveis</li>
                        <li class="flex items-center gap-2.5"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#0ea5e9" stroke-width="1.2"/><path d="M4 7l2 2 4-4" stroke="#0ea5e9" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Gamificação para engajar equipes</li>
                        <li class="flex items-center gap-2.5"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#0ea5e9" stroke-width="1.2"/><path d="M4 7l2 2 4-4" stroke="#0ea5e9" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Relatórios e dashboards customizáveis</li>
                        <li class="flex items-center gap-2.5"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#0ea5e9" stroke-width="1.2"/><path d="M4 7l2 2 4-4" stroke="#0ea5e9" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Cadastro de produtos, tarefas e usuários</li>
                        <li class="flex items-center gap-2.5"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#0ea5e9" stroke-width="1.2"/><path d="M4 7l2 2 4-4" stroke="#0ea5e9" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Recebimento e movimentação de mercadorias</li>
                        <li class="flex items-center gap-2.5"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="6" stroke="#0ea5e9" stroke-width="1.2"/><path d="M4 7l2 2 4-4" stroke="#0ea5e9" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>Expedição com rastreabilidade completa</li>
                    </ul>
                </div>
                <div class="relative overflow-hidden rounded-3xl min-h-[400px] reveal-right">
                    <img src="{{ asset('images/home/vista-de-perto-do-armazem_23-2148923142.avif') }}" alt="Armazém tecnológico" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-950/20 to-slate-950/60"></div>
                    <div class="absolute inset-0 flex items-end p-8">
                        <div class="bg-slate-900 rounded-2xl p-5 border border-white/10">
                            <p class="text-xs text-sky-300 uppercase tracking-widest mb-1">Operação em tempo real</p>
                            <p class="text-white font-semibold text-sm">Armazém 100% monitorado e controlado pela MystockMaster</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- CTA FULL WIDTH -->
    <section class="relative overflow-hidden py-28 bg-slate-950">
        <div class="absolute inset-0">
            <img src="{{ asset('images/home/images (1).jpeg') }}" alt="Armazém" class="w-full h-full object-cover" style="opacity:0.12; filter:brightness(0.4);">
            <div class="absolute inset-0" style="background:rgba(2,6,23,0.75);"></div>
        </div>
        <div class="relative z-10 px-6 mx-auto max-w-4xl lg:px-8 text-center reveal">
            <p class="inline-flex items-center gap-3 text-xs uppercase tracking-widest text-sky-300 mb-6"><span class="w-8 h-px bg-sky-500"></span>Pronto para começar?<span class="w-8 h-px bg-sky-500"></span></p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-6 leading-tight">Transforme sua operação logística hoje mesmo.</h2>
            <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto">Agende uma demonstração gratuita e veja como a MystockMaster pode elevar a produtividade do seu armazém.</p>
            <div class="flex flex-col gap-4 sm:flex-row justify-center">
                <a href="#contato" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-sm font-semibold rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 shadow-xl shadow-sky-500/30 transition-all duration-300 hover:-translate-y-0.5">
                    Solicitar demonstração gratuita
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a href="https://wa.me/+5562984306136" target="_blank" class="inline-flex items-center justify-center gap-2 px-8 py-4 text-sm font-medium rounded-full border border-white/20 bg-white/5 text-white hover:bg-white/10 transition-all duration-300">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M13.5 2.5a6.5 6.5 0 01-9.19 9.19L1.5 13.5l1.81-2.81A6.5 6.5 0 0113.5 2.5z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg>
                    Falar pelo WhatsApp
                </a>
            </div>
        </div>
    </section>

    <!-- CONTATO -->
    <section id="contato" class="py-24 px-6 mx-auto max-w-7xl lg:px-8 bg-slate-950">
        <div class="max-w-3xl mb-14 reveal">
            <p class="inline-flex items-center gap-2 text-xs uppercase tracking-widest text-sky-300 mb-4"><span class="w-8 h-px bg-sky-500"></span>Contato</p>
            <h2 class="text-3xl sm:text-4xl font-bold text-white leading-tight">Quer otimizar sua operação?</h2>
            <p class="mt-4 text-lg text-slate-300">Fale conosco e descubra como a MystockMaster WMS pode fazer a diferença.</p>
        </div>
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="p-8 rounded-3xl border border-white/10 bg-slate-900 space-y-6 reveal">
                <h3 class="text-lg font-semibold text-white">Informações de contato</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sky-500/10 flex items-center justify-center flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.5 2a5.5 5.5 0 01-7.85 7.85L1 12l2.15-2.65A5.5 5.5 0 0111.5 2z" stroke="#38bdf8" stroke-width="1.2" stroke-linejoin="round"/></svg>
                        </div>
                        <div><p class="text-xs text-slate-400">WhatsApp</p><a href="https://wa.me/+5562984306136" class="text-sm text-white hover:text-sky-400 transition-colors">+55 62 98430-6136</a></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sky-500/10 flex items-center justify-center flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1.5 3.5l5.5 4.5 5.5-4.5M1.5 3.5h11v8h-11V3.5z" stroke="#38bdf8" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div><p class="text-xs text-slate-400">Email</p><a href="mailto:comercial@MystockMastersmart.com.br" class="text-sm text-white hover:text-sky-400 transition-colors break-all">comercial@mystockmastersmart.com.br</a></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-sky-500/10 flex items-center justify-center flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M7 1a4.5 4.5 0 00-4.5 4.5C2.5 9 7 13 7 13s4.5-4 4.5-7.5A4.5 4.5 0 007 1zm0 6a1.5 1.5 0 110-3 1.5 1.5 0 010 3z" stroke="#38bdf8" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </div>
                        <div><p class="text-xs text-slate-400">Localização</p><p class="text-sm text-white">Av. 20, 0 - C1, Aparecida de Goiânia - GO</p></div>
                    </div>
                </div>
                <div class="pt-4 border-t border-white/10">
                    <p class="text-xs text-slate-400 mb-3">Redes sociais</p>
                    <div class="flex gap-2">
                        <a href="https://wa.me/+5562984306136" target="_blank" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-sky-500/10 hover:text-sky-400 hover:border-sky-500/30 transition-all"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M11.5 2a5.5 5.5 0 01-7.85 7.85L1 12l2.15-2.65A5.5 5.5 0 0111.5 2z" stroke="currentColor" stroke-width="1.2" stroke-linejoin="round"/></svg></a>
                        <a href="https://instagram.com" target="_blank" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-sky-500/10 hover:text-sky-400 hover:border-sky-500/30 transition-all"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><rect x="1" y="1" width="12" height="12" rx="3" stroke="currentColor" stroke-width="1.2"/><circle cx="7" cy="7" r="2.5" stroke="currentColor" stroke-width="1.2"/><circle cx="10.5" cy="3.5" r="0.5" fill="currentColor"/></svg></a>
                        <a href="https://linkedin.com" target="_blank" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-slate-400 hover:bg-sky-500/10 hover:text-sky-400 hover:border-sky-500/30 transition-all"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1.5 5h2.5v7.5H1.5V5zm1.25-1a1.25 1.25 0 100-2.5 1.25 1.25 0 000 2.5zm9.75 4c0-2.5-1.5-3.5-3-3.5S7 5.5 7 5.5V5H4.5v7.5H7V9c0-1 .5-2 2-2s1.5 1 1.5 2v3.5H13V8z" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 grid gap-4 sm:grid-cols-2 reveal delay-2">
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:border-sky-500/20 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-2xl bg-sky-500/10 border border-sky-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><img src="{{ asset('images/icons/icon-05.svg') }}" alt="" class="w-5 h-5 opacity-80"></div>
                    <h3 class="font-semibold text-white mb-2">Suporte técnico dedicado</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Assistência especializada para implantação e operação contínua do sistema.</p>
                </div>
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:border-sky-500/20 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-2xl bg-violet-500/10 border border-violet-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><img src="{{ asset('images/icons/icon-03.svg') }}" alt="" class="w-5 h-5 opacity-80"></div>
                    <h3 class="font-semibold text-white mb-2">Segurança de dados</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Proteção de dados e controle de acesso por níveis para sua operação.</p>
                </div>
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:border-sky-500/20 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><img src="{{ asset('images/icons/icon-04.svg') }}" alt="" class="w-5 h-5 opacity-80"></div>
                    <h3 class="font-semibold text-white mb-2">Alta performance</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Plataforma leve e otimizada para operações reais de WMS com alta demanda.</p>
                </div>
                <div class="p-6 rounded-3xl border border-white/10 bg-slate-900 hover:border-sky-500/20 transition-all duration-300 group">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform"><img src="{{ asset('images/icons/icon-01.svg') }}" alt="" class="w-5 h-5 opacity-80"></div>
                    <h3 class="font-semibold text-white mb-2">Treinamento incluso</h3>
                    <p class="text-sm text-slate-400 leading-relaxed">Capacitação completa da sua equipe para aproveitar ao máximo a plataforma.</p>
                </div>
            </div>
        </div>

        <div class="mt-10 text-center reveal">
            <a href="mailto:comercial@MystockMastersmart.com.br" class="inline-flex items-center gap-2 px-8 py-4 text-sm font-semibold rounded-full bg-sky-500 text-slate-950 hover:bg-sky-400 shadow-xl shadow-sky-500/25 transition-all duration-300 hover:-translate-y-0.5">
                Enviar mensagem
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M1 8l14-7-7 14V9H4L1 8z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </a>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-white/10 bg-slate-950">
        <div class="px-6 py-12 mx-auto max-w-7xl lg:px-8">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4 mb-10">
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-500 to-violet-600 flex items-center justify-center flex-shrink-0">
                            <svg width="16" height="16" viewBox="0 0 32 32" fill="none"><path d="M22.95 28.9H14.15V21.95C14.15 21.15 13.5 20.55 12.75 20.55H6.25V4C6.25 3.5 6.65 3.1 7.15 3.1H22.35C22.85 3.1 23.25 3.5 23.25 4V5.1C22.25 5.1 20.75 5.75 20.75 6.55L20.7 22.8L23.25 28.15V28.6C23.2 28.75 23.1 28.9 22.95 28.9Z" fill="white"/></svg>
                        </div>
                        <div><p class="text-sm font-semibold text-white">MystockMaster</p><p class="text-xs text-slate-400">WMS & Logística</p></div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-xs">Plataforma de gestão de armazém para transformar a produtividade e visibilidade das operações logísticas.</p>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Produto</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="#funcionalidades" class="hover:text-sky-400 transition-colors">Funcionalidades</a></li>
                        <li><a href="#solucoes" class="hover:text-sky-400 transition-colors">Soluções</a></li>
                        <li><a href="#origem" class="hover:text-sky-400 transition-colors">Nossa história</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-sky-400 transition-colors">Entrar na plataforma</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-white mb-4">Contato</h4>
                    <ul class="space-y-3 text-sm text-slate-400">
                        <li><a href="https://wa.me/+5562984306136" target="_blank" class="hover:text-sky-400 transition-colors">WhatsApp</a></li>
                        <li><a href="mailto:comercial@MystockMastersmart.com.br" class="hover:text-sky-400 transition-colors">Email comercial</a></li>
                        <li><a href="https://instagram.com" target="_blank" class="hover:text-sky-400 transition-colors">Instagram</a></li>
                        <li><a href="https://linkedin.com" target="_blank" class="hover:text-sky-400 transition-colors">LinkedIn</a></li>
                    </ul>
                </div>
            </div>
            <div class="flex flex-col gap-3 pt-8 border-t border-white/10 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-500">© 2025 MystockMaster Softwares. Todos os direitos reservados.</p>
                <p class="text-xs text-slate-600">Desenvolvimento de sistemas logísticos e WMS.</p>
            </div>
        </div>
    </footer>

    <script>
        // Header scroll effect
        const header = document.getElementById('site-header');
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY > 50;
            header.classList.toggle('header-scrolled', scrolled);
            // Remove initial gradient when solid bg kicks in
            if (scrolled) {
                header.style.backgroundImage = 'none';
            } else {
                header.style.backgroundImage = '';
            }
        }, { passive: true });

        // Mobile menu
        const btn = document.getElementById('menu-btn');
        const menu = document.getElementById('mobile-menu');
        const l1 = document.getElementById('l1'), l2 = document.getElementById('l2'), l3 = document.getElementById('l3');
        let open = false;
        btn.addEventListener('click', () => {
            open = !open;
            menu.classList.toggle('hidden', !open);
            l1.style.transform = open ? 'rotate(45deg) translate(4px, 4px)' : '';
            l2.style.opacity = open ? '0' : '1';
            l3.style.transform = open ? 'rotate(-45deg) translate(4px, -4px)' : '';
            l3.style.width = open ? '24px' : '';
        });
        document.querySelectorAll('#mobile-menu a').forEach(a => a.addEventListener('click', () => {
            open = false; menu.classList.add('hidden');
            l1.style.transform = l2.style.opacity = l3.style.transform = l3.style.width = '';
            l2.style.opacity = '1';
        }));

        // Scroll reveal
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
        }, { threshold: 0.12 });
        document.querySelectorAll('.reveal, .reveal-left, .reveal-right').forEach(el => obs.observe(el));
    </script>
</body>
</html>
