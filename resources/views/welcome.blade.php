<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KONEK — CMU Talent Network</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f7f8f4] text-[#1d3029]">
    <header class="landing-header px-5 py-6 sm:px-8">
        <div class="mx-auto flex max-w-6xl items-center justify-between">
            <a href="/" class="flex items-center gap-3">
                <x-application-logo class="h-10 w-10 text-[#176b4d]" />
                <div>
                    <div class="text-sm font-bold tracking-[0.16em] text-[#143d30]">KONEK</div>
                    <div class="mt-0.5 text-[10px] text-slate-400">CMU Talent Network</div>
                </div>
            </a>

            <nav class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="landing-primary-button">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="landing-text-link">Log in</a>
                    <a href="{{ route('register') }}" class="landing-primary-button">Get started</a>
                @endauth
            </nav>
        </div>
    </header>

    <main>
        <section class="mx-auto max-w-6xl px-5 pb-20 pt-16 text-center sm:px-8 sm:pb-28 sm:pt-24">
            <div class="mx-auto max-w-4xl">
                <p class="landing-hero-item mb-6 text-xs font-semibold uppercase tracking-[0.2em] text-[#2f7d5f]" style="--enter-delay: 100ms">Work and talent, connected locally</p>
                <h1 class="landing-hero-item text-[2.8rem] font-semibold leading-[1.08] tracking-[-0.045em] text-[#143d30] sm:text-6xl lg:text-7xl" style="--enter-delay: 180ms">
                    Opportunities within<br class="hidden sm:block"> the CMU community.
                </h1>
                <p class="landing-hero-item mx-auto mt-7 max-w-2xl text-base leading-7 text-slate-500 sm:text-lg sm:leading-8" style="--enter-delay: 280ms">
                    A straightforward place for clients to find capable people and for freelancers to discover meaningful work.
                </p>
                <div class="landing-hero-item mt-9 flex flex-wrap justify-center gap-3" style="--enter-delay: 360ms">
                    <a href="{{ route('register') }}" class="landing-primary-button px-6 py-3">Create an account</a>
                    <a href="{{ route('login') }}" class="landing-secondary-button px-6 py-3">Sign in</a>
                </div>
            </div>

            <div class="landing-preview landing-hero-item mx-auto mt-16 max-w-5xl rounded-[28px] border border-[#dce5de] bg-white p-3 shadow-[0_24px_70px_rgba(31,65,50,0.08)] sm:mt-20 sm:p-5" style="--enter-delay: 470ms">
                <div class="overflow-hidden rounded-[20px] border border-[#e6ebe7] bg-[#f8faf8] text-left">
                    <div class="flex items-center justify-between border-b border-[#e6ebe7] bg-white px-5 py-4 sm:px-7">
                        <div class="flex items-center gap-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-[#35a678]"></div>
                            <span class="text-sm font-semibold text-[#24483a]">Latest opportunities</span>
                        </div>
                        <span class="text-xs text-slate-400">Updated today</span>
                    </div>
                    <div class="grid divide-y divide-[#e6ebe7] sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                        <article class="landing-opportunity p-5 sm:p-7">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#388166]">Development</p>
                            <h2 class="mt-3 text-base leading-6 text-[#183c30]">Laravel application developer</h2>
                            <p class="mt-2 text-sm text-slate-500">Full-time · Expert</p>
                            <p class="mt-5 text-sm font-semibold text-[#176b4d]">₱20k–₱35k</p>
                        </article>
                        <article class="landing-opportunity p-5 sm:p-7">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#388166]">Design</p>
                            <h2 class="mt-3 text-base leading-6 text-[#183c30]">Mobile product UI/UX designer</h2>
                            <p class="mt-2 text-sm text-slate-500">Part-time · Intermediate</p>
                            <p class="mt-5 text-sm font-semibold text-[#176b4d]">₱8k–₱15k</p>
                        </article>
                        <article class="landing-opportunity p-5 sm:p-7">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-[#388166]">Content</p>
                            <h2 class="mt-3 text-base leading-6 text-[#183c30]">Technical content writer</h2>
                            <p class="mt-2 text-sm text-slate-500">Internship · Entry</p>
                            <p class="mt-5 text-sm font-semibold text-[#176b4d]">₱300–₱500/hr</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-[#dfe6df] bg-white">
            <div class="mx-auto grid max-w-6xl gap-10 px-5 py-16 sm:px-8 md:grid-cols-3">
                <div class="landing-reveal" data-reveal>
                    <div class="feature-number">01</div>
                    <h2 class="mt-5 text-lg text-[#183c30]">Post opportunities</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Create clear job posts with budgets, requirements, skills, and deadlines.</p>
                </div>
                <div class="landing-reveal" data-reveal style="--reveal-delay: 100ms">
                    <div class="feature-number">02</div>
                    <h2 class="mt-5 text-lg text-[#183c30]">Discover talent</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Review applications from people already connected to the CMU community.</p>
                </div>
                <div class="landing-reveal" data-reveal style="--reveal-delay: 200ms">
                    <div class="feature-number">03</div>
                    <h2 class="mt-5 text-lg text-[#183c30]">Build experience</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500">Find relevant work, submit focused applications, and grow your portfolio.</p>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-6xl px-5 py-20 sm:px-8 sm:py-24">
            <div class="landing-cta landing-reveal grid gap-8 rounded-[28px] bg-[#183f32] p-8 text-white sm:p-12 lg:grid-cols-[1fr_auto] lg:items-center" data-reveal>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#8ed5b5]">Start connecting</p>
                    <h2 class="mt-4 max-w-2xl text-3xl leading-tight text-white sm:text-4xl">Your next collaborator may already be here.</h2>
                    <p class="mt-4 max-w-xl text-sm leading-6 text-white/60">Join KONEK and access opportunities built around the CMU network.</p>
                </div>
                <a href="{{ route('register') }}" class="inline-flex self-start rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#176b4d] hover:bg-[#edf7f1]">Create an account</a>
            </div>
        </section>
    </main>

    <footer class="border-t border-[#dfe6df] px-5 py-8 sm:px-8">
        <div class="mx-auto flex max-w-6xl flex-col gap-2 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <span>© {{ date('Y') }} KONEK</span>
            <span>Central Mindanao University Talent Network</span>
        </div>
    </footer>
    <script>
        const revealItems = document.querySelectorAll('[data-reveal]');

        if ('IntersectionObserver' in window) {
            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.18 });

            revealItems.forEach((item) => revealObserver.observe(item));
        } else {
            revealItems.forEach((item) => item.classList.add('is-visible'));
        }
    </script>
</body>
</html>
