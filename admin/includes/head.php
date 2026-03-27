<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Playora - Admin Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=JetBrains+Mono&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        void: '#030304',
                        paper: '#1a1a1c',
                        subtle: '#2a2a2d',
                        border: 'rgba(255, 255, 255, 0.08)',
                    },
                    animation: {
                        'gradient-flow': 'gradient-flow 6s ease infinite',
                        'pulse-glow': 'pulse-glow 8s ease-in-out infinite',
                    },
                    keyframes: {
                        'gradient-flow': {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        },
                        'pulse-glow': {
                            '0%, 100%': { transform: 'scale(1)', opacity: '0.5' },
                            '50%': { transform: 'scale(1.1)', opacity: '0.8' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #030304; 
            color: #f9f9fa;
        }
        .glass-card {
            background: rgba(26, 26, 28, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 0.75rem;
        }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #030304; }
        ::-webkit-scrollbar-thumb { background: #2a2a2d; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
</head>
<body class="font-sans antialiased bg-void text-subtle overflow-hidden flex h-screen relative">
