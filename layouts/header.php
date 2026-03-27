<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playora - Turf Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        void: '#030304',
                        emerald: { DEFAULT: '#10b981', 400: '#34d399', 500: '#10b981', 600: '#059669' },
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        body: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-dark: #030304;
            --text-dark: #ffffff;
            --glass-bg-dark: rgba(255, 255, 255, 0.05);
            --glass-border-dark: rgba(255, 255, 255, 0.1);

            --bg-light: #ffffff;
            --text-light: #111827;
            --glass-bg-light: rgba(0, 0, 0, 0.02);
            --glass-border-light: rgba(0, 0, 0, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Dark Mode Default */
        body { background-color: var(--bg-dark); color: var(--text-dark); }
        .glass { background: var(--glass-bg-dark); backdrop-filter: blur(20px); border: 1px solid var(--glass-border-dark); }

        /* Light Mode overrides via Tailwind 'dark:' class system where possible, but base CSS here */
        html:not(.dark) body { background-color: var(--bg-light); color: var(--text-light); }
        html:not(.dark) .glass { background: var(--glass-bg-light); border: 1px solid var(--glass-border-light); }

        .grad-text {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
    <script>
        // Check theme immediately to prevent flash
        if (localStorage.getItem('theme') === 'light' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: light)').matches)) {
            document.documentElement.classList.remove('dark')
        } else {
            document.documentElement.classList.add('dark')
        }
    </script>
</head>
<body class="antialiased min-h-screen flex flex-col relative overflow-x-hidden">
