<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @auth
        @if(auth()->user()->role === 'client')
            <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 128 128'%3E%3Cpath fill='%2342b883' d='M78.8,10L64,35.4L49.2,10H0l64,110l64-110H78.8z'/%3E%3Cpath fill='%2335495e' d='M78.8,10L64,35.4L49.2,10H25.6L64,76l38.4-66H78.8z'/%3E%3C/svg%3E">
        @endif
    @endauth
    <title>{{ config('app.name', 'Laravel') }}</title>
    @routes
    @vite(['resources/js/app.js'])
    @inertiaHead
    {{-- Apply dark instantly before page loads (prevents white flash) --}}
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme: dark)').matches))document.documentElement.classList.add('dark');})();</script>
    <style>
        /* =============================================
           DARK MODE: Full page coverage
           Targets all Tailwind + shadcn/ui patterns
           ============================================= */

        /* Body & root backgrounds */
        html.dark body { background-color: #09090b !important; color: #f4f4f5 !important; }
        html.dark main { background-color: #09090b !important; }
        html.dark div[class*="h-screen"] { background-color: #09090b !important; }

        /* All white backgrounds → dark */
        html.dark [class*="bg-white"] { background-color: #18181b !important; }
        html.dark [class*="bg-zinc-50"] { background-color: #121214 !important; }
        html.dark [class*="bg-zinc-100"] { background-color: #1c1c1f !important; }
        html.dark [class*="bg-gray-50"] { background-color: #121214 !important; }
        html.dark [class*="bg-gray-100"] { background-color: #1c1c1f !important; }
        html.dark [class*="bg-slate-50"] { background-color: #121214 !important; }

        /* Gradient backgrounds used in ClientPortal */
        html.dark [class*="from-blue-50"] { --tw-gradient-from: #0f172a !important; }
        html.dark [class*="to-white"] { --tw-gradient-to: #18181b !important; }

        /* Cards — shadcn Card component renders as div */
        html.dark [data-slot="card"],
        html.dark .rounded-xl,
        html.dark .rounded-2xl,
        html.dark .rounded-3xl {
            background-color: #18181b;
        }
        /* Only target cards that don't already have explicit bg-zinc-900 (dark wallet card) */
        html.dark .shadow-sm:not([class*="bg-zinc-9"]):not([class*="bg-gradient"]) {
            background-color: #1c1c1f !important;
        }
        html.dark .shadow-2xl:not([class*="bg-zinc-9"]):not([style*="background"]) {
            background-color: #1c1c1f !important;
        }

        /* Text: headings */
        html.dark h1, html.dark h2, html.dark h3, html.dark h4, html.dark h5, html.dark h6 {
            color: #f4f4f5 !important;
        }
        /* Named text colour classes */
        html.dark .text-black { color: #f4f4f5 !important; }
        html.dark .text-zinc-900 { color: #f4f4f5 !important; }
        html.dark .text-zinc-800 { color: #e4e4e7 !important; }
        html.dark .text-zinc-700 { color: #d4d4d8 !important; }
        html.dark .text-zinc-600 { color: #a1a1aa !important; }
        html.dark .text-zinc-500 { color: #71717a !important; }
        html.dark .text-gray-900 { color: #f4f4f5 !important; }
        html.dark .text-gray-800 { color: #e4e4e7 !important; }
        html.dark .text-gray-700 { color: #d4d4d8 !important; }
        html.dark .text-gray-600 { color: #a1a1aa !important; }

        /* Borders */
        html.dark [class*="border-zinc-200"] { border-color: #3f3f46 !important; }
        html.dark [class*="border-zinc-100"] { border-color: #27272a !important; }
        html.dark [class*="border-gray-200"] { border-color: #3f3f46 !important; }
        html.dark [class*="border-gray-100"] { border-color: #27272a !important; }
        html.dark [class*="border-blue-100"] { border-color: #1e3a5f !important; }

        /* Inputs, textareas, selects */
        html.dark input:not([type="checkbox"]):not([type="radio"]),
        html.dark textarea,
        html.dark select {
            background-color: #27272a !important;
            color: #f4f4f5 !important;
            border-color: #3f3f46 !important;
        }
        html.dark input::placeholder,
        html.dark textarea::placeholder { color: #71717a !important; }

        /* Tabs list  */
        html.dark [role="tablist"] { background-color: #27272a !important; }

        /* Tables */
        html.dark thead, html.dark thead tr { background-color: #1c1c1f !important; }
        html.dark tbody tr { border-color: #27272a !important; }
        html.dark tbody tr td { color: #d4d4d8 !important; }
        html.dark tbody tr:hover { background-color: #1c1c1f !important; }

        /* Notification / hover states */
        html.dark [class*="hover:bg-zinc-50"]:hover { background-color: #1c1c1f !important; }
        html.dark [class*="bg-blue-50"] { background-color: #172554 !important; }
        html.dark [class*="bg-yellow-100"] { background-color: #422006 !important; }
        html.dark [class*="text-zinc-800"]:is(h4) { color: #e4e4e7 !important; }

        /* =============================================
           FLOATING DARK MODE TOGGLE BUTTON
           ============================================= */
        #dmtoggle {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            border: 1.5px solid #e4e4e7;
            background: #ffffff;
            font-size: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            line-height: 1;
        }
        #dmtoggle:hover { transform: scale(1.12); box-shadow: 0 6px 22px rgba(0,0,0,0.22); }
        html.dark #dmtoggle { background: #27272a; border-color: #52525b; }
    </style>
</head>
<body class="font-sans antialiased">
    @inertia

    {{-- Floating dark/light toggle (works without any rebuild) --}}
    <button id="dmtoggle" title="Toggle Dark / Light Mode" aria-label="Toggle Dark Mode">🌙</button>

    <script>
        (function() {
            var btn = document.getElementById('dmtoggle');
            // Set correct initial emoji
            if (document.documentElement.classList.contains('dark')) {
                btn.textContent = '\u2600\uFE0F'; // ☀️
            }
            btn.addEventListener('click', function() {
                var dark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', dark ? 'dark' : 'light');
                btn.textContent = dark ? '\u2600\uFE0F' : '\uD83C\uDF19'; // ☀️ or 🌙
            });
        })();
    </script>
</body>
</html>