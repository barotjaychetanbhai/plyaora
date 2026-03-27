<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'user') {
        header("Location: player/index.php");
        exit();
    } elseif ($_SESSION['role'] === 'owner') {
        header("Location: owner/index.php");
        exit();
    }
}

require_once 'includes/db.php';
require_once 'includes/functions.php';

// 1. Fetch Active Sports
$sports_query = $conn->query("SELECT * FROM sports WHERE status = 'active' ORDER BY name ASC");
$sports = $sports_query->fetch_all(MYSQLI_ASSOC);

// 2. Fetch Trending Turfs (Top Rated)
$trending_query = $conn->query("
    SELECT t.*, s.name as sport_name, s.icon as sport_icon, c.name as city_name 
    FROM turfs t 
    JOIN sports s ON t.sport_id = s.id 
    JOIN cities c ON t.city_id = c.id 
    WHERE t.status = 'active' 
    ORDER BY t.rating DESC LIMIT 6
");
$trending_turfs = $trending_query->fetch_all(MYSQLI_ASSOC);

// 3. Fetch Nearby/Latest Turfs
$nearby_query = $conn->query("
    SELECT t.*, s.name as sport_name, s.icon as sport_icon, c.name as city_name 
    FROM turfs t 
    JOIN sports s ON t.sport_id = s.id 
    JOIN cities c ON t.city_id = c.id 
    WHERE t.status = 'active' 
    ORDER BY t.created_at DESC LIMIT 6
");
$nearby_turfs = $nearby_query->fetch_all(MYSQLI_ASSOC);

// 4. Counts for Tickers/Stats
$turf_count = $conn->query("SELECT COUNT(*) FROM turfs WHERE status = 'active'")->fetch_row()[0];
$player_count = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Playora — Book Sports Turfs Near You</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,900;1,400;1,700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />
    <script>
        tailwind.config = {
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
            --emerald: #10b981;
            --emerald-600: #059669;
            --cyan: #06b6d4;
            --purple: #8b5cf6;
            --void: #030304;

            /* Theme Variables - Default Dark */
            --bg: var(--void);
            --text: #ffffff;
            --text-muted: rgba(255, 255, 255, 0.6);
            --glass-bg: rgba(255, 255, 255, 0.04);
            --glass-border: rgba(255, 255, 255, 0.07);
            --nav-bg: rgba(3, 3, 4, 0.88);
            --card-bg: rgba(255, 255, 255, 0.03);
            --input-bg: rgba(255, 255, 255, 0.05);
            --border-muted: rgba(255, 255, 255, 0.06);
        }

        .light-mode {
            --bg: #f1f5f9;
            --text: #0f172a;
            --text-muted: #475569;
            --glass-bg: #ffffff;
            --glass-border: #cbd5e1;
            --nav-bg: rgba(255, 255, 255, 0.98);
            --card-bg: #ffffff;
            --input-bg: #f8fafc;
            --border-muted: #cbd5e1;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            transition: background 0.3s, color 0.3s;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #0a0a0b;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--emerald);
            border-radius: 2px;
        }

        /* Glow utilities */
        .glow-em {
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.18);
        }

        .glow-em-sm {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.12);
        }

        .glow-cyan {
            box-shadow: 0 0 40px rgba(6, 182, 212, 0.18);
        }

        .text-glow {
            text-shadow: 0 0 40px rgba(16, 185, 129, 0.5);
        }

        .text-glow-lg {
            text-shadow: 0 0 80px rgba(16, 185, 129, 0.4), 0 0 140px rgba(6, 182, 212, 0.2);
        }

        /* Glass Components - Now uses variables */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
        }

        .glass-dark {
            background: var(--nav-bg);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
        }

        .glass-em {
            background: var(--input-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* Navbar */
        #navbar {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #navbar.scrolled {
            background: var(--nav-bg);
            backdrop-filter: blur(28px);
            border-bottom: 1px solid var(--border-muted);
        }

        /* --- Theme Toggle & Location --- */
        .theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: var(--glass-bg);
            color: var(--text);
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            border-color: var(--emerald);
            background: var(--glass-border);
        }

        .nav-location {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--input-bg);
            padding: 8px 14px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            transition: all 0.3s;
        }

        .nav-location:hover {
            border-color: var(--emerald);
            background: var(--card-bg);
        }

        /* --- Search Bar Modern --- */
        .search-bar-container {
            max-width: 900px;
            margin: -40px auto 40px;
            padding: 0 16px;
            position: relative;
            z-index: 40;
        }

        .search-bar {
            background: var(--card-bg);
            backdrop-filter: blur(24px);
            border: 1px solid var(--glass-border);
            padding: 10px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .light-mode .search-bar {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #ffffff;
        }

        .search-field-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 10px 16px;
            border-radius: 14px;
            transition: background 0.2s;
            cursor: pointer;
        }

        .search-field-wrap:hover {
            background: var(--input-bg);
        }

        .search-field-wrap label {
            font-size: 10px;
            font-weight: 700;
            color: var(--emerald);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .search-field-wrap input,
        .search-field-wrap select {
            border: none;
            outline: none;
            background: transparent;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            font-weight: 500;
            width: 100%;
            cursor: pointer;
        }

        .search-field-wrap input::placeholder {
            color: var(--text-muted);
        }

        .search-divider {
            width: 1px;
            height: 32px;
            background: var(--glass-border);
            margin: 0 4px;
        }

        .search-btn {
            background: var(--emerald);
            color: white;
            border: none;
            border-radius: 14px;
            padding: 0 24px;
            height: 52px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-btn:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        /* --- Light Mode Refinements --- */
        .light-mode .glass,
        .light-mode .glass-dark,
        .light-mode .cat-card,
        .light-mode .turf-card,
        .light-mode .testi-card,
        .light-mode .booking-card,
        .light-mode .partner-card,
        .light-mode .search-bar,
        .light-mode .theme-toggle,
        .light-mode .nav-location {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05) !important;
        }

        .light-mode .cat-card:hover,
        .light-mode .turf-card:hover,
        .light-mode .testi-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1) !important;
            border-color: var(--emerald) !important;
        }

        /* Override Tailwind's white-border utilities in light mode */
        .light-mode [class*="border-white/"] {
            border-color: #cbd5e1 !important;
        }

        .light-mode .glass-em {
            background: #f0fdf4 !important;
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        /* Text refinements - Much darker for readability */
        .light-mode .text-white\/40,
        .light-mode .text-white\/50,
        .light-mode .text-white\/60,
        .light-mode .text-white\/70,
        .light-mode .text-white\/80,
        .light-mode .text-gray-400,
        .light-mode .text-gray-300 {
            color: #475569 !important;
        }
        
        .light-mode .text-gray-500,
        .light-mode .text-gray-600 {
            color: #334155 !important;
        }

        .light-mode .ticker-inner span {
            color: #64748b;
        }
        
        .light-mode .badge {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }
        
        .light-mode .confirmed-badge {
            background: #dcfce7;
            color: #166534;
        }

        .light-mode .card-loc, .light-mode .card-price {
            color: #1e293b;
        }

        /* Hero */
        .hero-bg {
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(16, 185, 129, 0.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 80% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse 40% 30% at 10% 70%, rgba(139, 92, 246, 0.06) 0%, transparent 50%),
                #030304;
        }

        /* Floating orbs */
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
            animation: orb-float 8s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.08), transparent);
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            animation-delay: 0s;
        }

        .orb-2 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(6, 182, 212, 0.06), transparent);
            bottom: 20%;
            right: 5%;
            animation-delay: -3s;
        }

        .orb-3 {
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.06), transparent);
            bottom: 30%;
            left: 5%;
            animation-delay: -6s;
        }

        @keyframes orb-float {

            0%,
            100% {
                transform: translateY(0) translateX(-50%);
            }

            50% {
                transform: translateY(-30px) translateX(-50%);
            }
        }

        .orb-2,
        .orb-3 {
            animation: orb-float2 10s ease-in-out infinite;
        }

        @keyframes orb-float2 {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Animations */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.3);
            }

            100% {
                box-shadow: 0 0 0 20px rgba(16, 185, 129, 0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -200% center;
            }

            100% {
                background-position: 200% center;
            }
        }

        @keyframes ticker {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .animate-fadeUp {
            animation: fadeUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease forwards;
        }

        .animate-slideRight {
            animation: slideRight 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .delay-100 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-200 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-300 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .delay-400 {
            animation-delay: 0.4s;
            opacity: 0;
        }

        .delay-500 {
            animation-delay: 0.5s;
            opacity: 0;
        }

        .delay-600 {
            animation-delay: 0.6s;
            opacity: 0;
        }

        .delay-700 {
            animation-delay: 0.7s;
            opacity: 0;
        }

        /* Buttons */
        .btn-em {
            background: linear-gradient(135deg, #10b981, #06b6d4);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-em::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease, opacity 0.6s ease;
            opacity: 0;
        }

        .btn-em:hover::before {
            width: 300px;
            height: 300px;
            opacity: 0;
        }

        .btn-em:active::before {
            width: 0;
            height: 0;
            opacity: 0.3;
            transition: none;
        }

        .btn-em:hover {
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4), 0 0 60px rgba(6, 182, 212, 0.2);
            transform: translateY(-1px);
        }

        .btn-ghost {
            border: 1px solid rgba(16, 185, 129, 0.4);
            transition: all 0.3s ease;
        }

        .btn-ghost:hover {
            border-color: rgba(16, 185, 129, 0.8);
            background: rgba(16, 185, 129, 0.08);
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.15);
        }

        /* Category cards */
        .cat-card {
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.2), 0 20px 40px rgba(0, 0, 0, 0.4);
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        .cat-card:hover .cat-icon {
            transform: scale(1.15);
            filter: drop-shadow(0 0 12px rgba(16, 185, 129, 0.5));
        }

        .cat-icon {
            transition: all 0.35s ease;
            font-size: 2.5rem;
            display: block;
        }

        /* Turf cards */
        /* --- Refined Turf Card Styles (from Screenshot) --- */
        .turf-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
        }

        .turf-img-bg {
            width: 100%;
            height: 180px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .turf-img-emoji {
            font-size: 56px;
            transition: transform 0.4s ease;
        }

        .turf-fav {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            color: #1a0e07;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .turf-body {
            padding: 24px;
        }

        .turf-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 10px;
        }

        .turf-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .turf-rating {
            display: flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            color: #f59e0b;
        }

        .turf-meta-dot {
            width: 3px;
            height: 3px;
            background: var(--glass-border);
            border-radius: 50%;
        }

        .turf-price {
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
        }

        .turf-price span {
            font-size: 13px;
            font-weight: 400;
            color: var(--text-muted);
        }

        .turf-slots {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .slot-chip {
            padding: 6px 14px;
            border-radius: 20px;
            background: var(--input-bg);
            border: 1px solid var(--glass-border);
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
            cursor: pointer;
            transition: all 0.2s;
        }

        .slot-chip:hover, .slot-chip.selected {
            background: var(--emerald);
            color: white;
            border-color: var(--emerald);
        }

        .turf-book-btn {
            width: 100%;
            padding: 14px;
            background: #23150d;
            color: white;
            border: none;
            border-radius: 14px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .turf-book-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
            background: #000000;
        }

        .light-mode .turf-book-btn {
            background: #1a0e07;
        }

        /* Sport backgrounds from lending-2.html */
        .bg-cricket { background: #5d4037; }
        .bg-football { background: #1a3a2a; }
        .bg-badminton { background: #311b92; }
        .bg-tennis { background: #827717; }
        .bg-box { background: #01579b; }

        .turf-card:hover .turf-img-emoji {
            transform: scale(1.1);
        }

        .turf-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.12), 0 24px 48px rgba(0, 0, 0, 0.5);
        }

        .turf-card:hover .turf-img {
            transform: scale(1.06);
        }

        .turf-img {
            transition: transform 0.5s ease;
        }

        /* Gradient text */
        .grad-text {
            background: linear-gradient(135deg, #10b981 0%, #06b6d4 50%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .grad-text-em {
            background: linear-gradient(135deg, #10b981, #34d399);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Shimmer badge */
        .shimmer-badge {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.1) 25%, rgba(16, 185, 129, 0.25) 50%, rgba(16, 185, 129, 0.1) 75%);
            background-size: 200% auto;
            animation: shimmer 2s linear infinite;
        }

        /* Step connector */
        .step-line {
            background: linear-gradient(90deg, #10b981, #06b6d4);
        }

        /* Scroll ticker */
        .ticker-wrap {
            overflow: hidden;
        }

        .ticker-inner {
            display: flex;
            width: max-content;
            animation: ticker 20s linear infinite;
        }

        /* Testimonial card */
        .testi-card {
            transition: all 0.3s ease;
        }

        .testi-card:hover {
            transform: translateY(-4px);
            border-color: rgba(16, 185, 129, 0.2) !important;
        }

        /* Owner section */
        .owner-bg {
            background:
                radial-gradient(ellipse 60% 80% at 0% 50%, rgba(16, 185, 129, 0.1) 0%, transparent 60%),
                radial-gradient(ellipse 40% 60% at 100% 50%, rgba(6, 182, 212, 0.08) 0%, transparent 50%),
                rgba(16, 185, 129, 0.03);
        }

        /* Mobile menu */
        #mobile-menu {
            transition: all 0.3s ease;
        }

        /* Search bar focus */
        .search-input:focus {
            outline: none;
        }

        .search-field {
            transition: all 0.2s ease;
        }

        .search-field:focus-within {
            border-color: rgba(16, 185, 129, 0.4);
        }

        /* Star */
        .star-filled {
            color: #f59e0b;
        }

        /* Horizontal scroll */
        .h-scroll {
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .h-scroll::-webkit-scrollbar {
            display: none;
        }

        /* Number counter glow */
        .num-glow {
            color: #10b981;
            text-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
        }

        /* Noise overlay */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            opacity: 0.4;
        }

        /* Grid lines background */
        .grid-bg {
            background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Carousel Styles */
        .carousel-outer {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 16px;
            user-select: none;
            margin-top: 80px;
            /* Offset for fixed navbar */
        }

        .slides-track {
            display: flex;
            transition: transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
            will-change: transform;
        }

        .slide {
            min-width: 100%;
            height: 380px;
            /* Increased from 180px for better hero impact */
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        /* Slide 1 — Book Now (Emerald) */
        .slide-1 {
            background: linear-gradient(125deg, #062917 0%, #0a3d22 35%, #115c34 60%, #1a7a48 100%);
        }

        .slide-1::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 80% at 85% 50%, rgba(16, 185, 129, 0.25) 0%, transparent 60%);
        }

        /* Slide 2 — Nearby Turf (Cyan/Blue) */
        .slide-2 {
            background: linear-gradient(125deg, #041b2e 0%, #072d4a 35%, #0d4a72 60%, #1265a0 100%);
        }

        .slide-2::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 80% at 85% 50%, rgba(6, 182, 212, 0.22) 0%, transparent 60%);
        }

        /* Slide 3 — Become a Partner (Purple/Amber) */
        .slide-3 {
            background: linear-gradient(125deg, #1a0b35 0%, #2d1060 35%, #44188a 60%, #5b22b0 100%);
        }

        .slide-3::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse 50% 80% at 85% 50%, rgba(139, 92, 246, 0.3) 0%, transparent 60%);
        }

        /* Noise overlay on each slide */
        .slide::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1;
        }

        .slide-content {
            position: relative;
            z-index: 2;
            padding: 0 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            height: 100%;
        }

        .slide-left {
            flex: 1;
            max-width: 55%;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            animation: blink 1.6s ease-in-out infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        .badge-1 {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 0.5px solid rgba(52, 211, 153, 0.3);
        }

        .badge-2 {
            background: rgba(6, 182, 212, 0.15);
            color: #67e8f9;
            border: 0.5px solid rgba(103, 232, 249, 0.3);
        }

        .badge-3 {
            background: rgba(167, 139, 250, 0.15);
            color: #c4b5fd;
            border: 0.5px solid rgba(196, 181, 253, 0.3);
        }

        .badge-dot-1 {
            background: #34d399;
        }

        .badge-dot-2 {
            background: #67e8f9;
        }

        .badge-dot-3 {
            background: #c4b5fd;
        }

        .slide-heading {
            font-size: clamp(24px, 4vw, 42px);
            font-weight: 600;
            color: #fff;
            line-height: 1.25;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
        }

        .slide-heading strong {
            font-weight: 600;
        }

        .highlight-1 {
            color: #34d399;
        }

        .highlight-2 {
            color: #67e8f9;
        }

        .highlight-3 {
            color: #c4b5fd;
        }

        .slide-sub {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 24px;
            line-height: 1.6;
            max-width: 400px;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
        }

        .cta-btn:hover {
            transform: translateY(-1px);
        }

        .cta-btn:active {
            transform: scale(0.97);
        }

        .btn-1 {
            background: #10b981;
            color: #fff;
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.35);
        }

        .btn-1:hover {
            box-shadow: 0 6px 28px rgba(16, 185, 129, 0.5);
        }

        .btn-2 {
            background: #0891b2;
            color: #fff;
            box-shadow: 0 4px 20px rgba(8, 145, 178, 0.35);
        }

        .btn-2:hover {
            box-shadow: 0 6px 28px rgba(8, 145, 178, 0.5);
        }

        .btn-3 {
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            color: #fff;
            box-shadow: 0 4px 20px rgba(124, 58, 237, 0.4);
        }

        .btn-3:hover {
            box-shadow: 0 6px 28px rgba(124, 58, 237, 0.55);
        }

        .btn-arrow {
            font-size: 14px;
            transition: transform 0.2s ease;
        }

        .cta-btn:hover .btn-arrow {
            transform: translateX(3px);
        }

        /* Right illustration area */
        .slide-right {
            position: absolute;
            right: 40px;
            top: 0;
            bottom: 0;
            width: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }

        /* Slide 1 illustration — booking card float */
        .illus-1 {
            position: relative;
            width: 240px;
            height: 180px;
        }

        .booking-card {
            position: absolute;
            background: rgba(255, 255, 255, 0.06);
            border: 0.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            backdrop-filter: blur(10px);
            padding: 12px 16px;
            color: #fff;
            font-size: 11px;
        }

        .booking-card-main {
            width: 180px;
            top: 10px;
            right: 0;
            animation: floatA 4s ease-in-out infinite;
        }

        .booking-card-mini {
            width: 140px;
            bottom: 0;
            left: 0;
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(52, 211, 153, 0.25);
            animation: floatB 5s ease-in-out infinite 0.5s;
        }

        @keyframes floatA {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-6px)
            }
        }

        @keyframes floatB {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(5px)
            }
        }

        .card-sport-icon {
            font-size: 18px;
            margin-bottom: 6px;
            display: block;
        }

        .card-name {
            font-weight: 500;
            font-size: 12px;
            color: #fff;
            margin-bottom: 2px;
        }

        .card-loc {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 6px;
        }

        .card-price {
            font-size: 13px;
            font-weight: 500;
            color: #34d399;
        }

        .confirmed-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
            font-size: 10px;
            padding: 3px 7px;
            border-radius: 20px;
            margin-top: 4px;
        }

        /* Slide 2 illustration — map pins */
        .illus-2 {
            position: relative;
            width: 240px;
            height: 180px;
        }

        .map-bg {
            width: 220px;
            height: 160px;
            background: rgba(255, 255, 255, 0.04);
            border: 0.5px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            position: relative;
            overflow: hidden;
        }

        .map-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(6, 182, 212, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(6, 182, 212, 0.06) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .map-pin {
            position: absolute;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: pinBounce 3s ease-in-out infinite;
        }

        .map-pin:nth-child(2) {
            animation-delay: 0.4s;
        }

        .map-pin:nth-child(3) {
            animation-delay: 0.8s;
        }

        .map-pin:nth-child(4) {
            animation-delay: 1.2s;
        }

        @keyframes pinBounce {

            0%,
            100% {
                transform: translateY(0)
            }

            50% {
                transform: translateY(-4px)
            }
        }

        .pin-head {
            width: 22px;
            height: 22px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pin-head span {
            transform: rotate(45deg);
            font-size: 9px;
            display: block;
            line-height: 1;
        }

        .pin-1 .pin-head {
            background: #0891b2;
            box-shadow: 0 2px 8px rgba(8, 145, 178, 0.5);
        }

        .pin-2 .pin-head {
            background: #10b981;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.5);
        }

        .pin-3 .pin-head {
            background: #7c3aed;
            box-shadow: 0 2px 8px rgba(124, 58, 237, 0.5);
        }

        .pin-4 .pin-head {
            background: #f59e0b;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.5);
        }

        .pin-tail {
            width: 2px;
            height: 6px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 1px;
        }

        .pin-label {
            position: absolute;
            top: -24px;
            left: 26px;
            background: rgba(0, 0, 0, 0.6);
            border: 0.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 6px;
            padding: 3px 7px;
            font-size: 9px;
            color: #fff;
            white-space: nowrap;
        }

        .distance-pill {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(6, 182, 212, 0.15);
            border: 0.5px solid rgba(103, 232, 249, 0.25);
            border-radius: 20px;
            padding: 3px 9px;
            font-size: 10px;
            color: #67e8f9;
        }

        /* Slide 3 illustration — partner growth */
        .illus-3 {
            position: relative;
            width: 240px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .partner-card {
            background: rgba(255, 255, 255, 0.05);
            border: 0.5px solid rgba(167, 139, 250, 0.2);
            border-radius: 14px;
            padding: 14px 18px;
            width: 200px;
            animation: floatA 4.5s ease-in-out infinite;
        }

        .partner-logo-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .partner-logo-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: linear-gradient(135deg, #7c3aed, #5b21b6);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }

        .partner-title {
            font-size: 11px;
            font-weight: 500;
            color: #fff;
        }

        .partner-sub {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.45);
        }

        .earn-row {
            display: flex;
            align-items: baseline;
            gap: 4px;
            margin-bottom: 8px;
        }

        .earn-amount {
            font-size: 20px;
            font-weight: 500;
            color: #c4b5fd;
            letter-spacing: -0.02em;
        }

        .earn-label {
            font-size: 10px;
            color: rgba(255, 255, 255, 0.45);
        }

        .mini-bar-row {
            display: flex;
            gap: 3px;
            align-items: flex-end;
            height: 24px;
        }

        .mini-bar {
            flex: 1;
            border-radius: 2px;
            background: rgba(167, 139, 250, 0.25);
            animation: barGrow 3s ease-in-out infinite;
        }

        .mini-bar:nth-child(1) {
            height: 35%;
            animation-delay: 0s;
        }

        .mini-bar:nth-child(2) {
            height: 55%;
            animation-delay: 0.1s;
        }

        .mini-bar:nth-child(3) {
            height: 45%;
            animation-delay: 0.2s;
        }

        .mini-bar:nth-child(4) {
            height: 70%;
            animation-delay: 0.3s;
        }

        .mini-bar:nth-child(5) {
            height: 60%;
            animation-delay: 0.4s;
        }

        .mini-bar:nth-child(6) {
            height: 85%;
            animation-delay: 0.5s;
            background: rgba(167, 139, 250, 0.5);
        }

        .mini-bar:nth-child(7) {
            height: 100%;
            animation-delay: 0.6s;
            background: rgba(167, 139, 250, 0.7);
        }

        @keyframes barGrow {

            0%,
            100% {
                opacity: 0.6;
            }

            50% {
                opacity: 1;
            }
        }

        /* Nav arrows */
        .nav-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.35);
            border: 0.5px solid rgba(255, 255, 255, 0.12);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease, transform 0.2s ease;
            color: rgba(255, 255, 255, 0.7);
            font-size: 20px;
            backdrop-filter: blur(8px);
        }

        .nav-arrow:hover {
            background: rgba(0, 0, 0, 0.6);
            color: #fff;
            transform: translateY(-50%) scale(1.08);
        }

        .nav-prev {
            left: 20px;
        }

        .nav-next {
            right: 20px;
        }

        /* Dots */
        .dots {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            width: 24px;
            background: rgba(255, 255, 255, 0.85);
        }

        /* progress bar */
        .progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 10;
            transform-origin: left;
            animation: progressAnim 4s linear infinite;
        }

        @keyframes progressAnim {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }
    </style>
</head>

<body class="antialiased">

    <!-- ═══════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════ -->
    <?php include "includes/header.php"; ?>


    <!-- ═══════════════════════════════════════════
     HERO CAROUSEL
═══════════════════════════════════════════ -->
    <section class="max-w-7xl mx-auto px-4 lg:px-6 pt-4">
        <div class="carousel-outer">
            <div class="slides-track" id="track">

                <!-- SLIDE 1: Book Now -->
                <div class="slide slide-1">
                    <div class="slide-content">
                        <div class="slide-left">
                            <div class="badge badge-1">
                                <span class="badge-dot badge-dot-1"></span>
                                Instant Booking
                            </div>
                            <div class="slide-heading">
                                Book a Turf,<br><span class="highlight-1">Play Today</span>
                            </div>
                            <div class="slide-sub">Find and reserve top-rated turfs near you in under 60 seconds. No
                                calls,
                                no waiting.</div>
                            <a href="?book_turf=1" class="cta-btn btn-1">
                                Book Now
                                <span class="btn-arrow">→</span>
                            </a>
                        </div>

                        <div class="slide-right">
                            <div class="illus-1">
                                <div class="turf-card !border-none !shadow-none !w-[180px] pointer-events-none">
                                    <div class="turf-img-bg bg-football !h-28">
                                        <div class="turf-img-emoji !text-4xl">⚽</div>
                                    </div>
                                    <div class="turf-body !p-3 bg-void/40 backdrop-blur-md">
                                        <div class="turf-name !text-sm !mb-1">Champions Arena</div>
                                        <div class="turf-meta !mb-2 !text-[10px]">
                                            <div class="turf-rating">★ 4.9</div>
                                            <div class="turf-meta-dot"></div>
                                            <div class="turf-dist">📍 0.8 km</div>
                                        </div>
                                        <div class="confirmed-badge !mt-0 scale-90 origin-left">
                                            <span style="width:5px;height:5px;background:#34d399;border-radius:50%;display:inline-block"></span>
                                            Confirmed
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-card booking-card-mini">
                                    <div style="font-size:10px;color:rgba(255,255,255,0.5);margin-bottom:2px">Tonight ·
                                        7:00
                                        PM</div>
                                    <div style="font-size:11px;font-weight:500;color:#fff">Football · 5-a-side</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 2: Nearby Turf -->
                <div class="slide slide-2">
                    <div class="slide-content">
                        <div class="slide-left">
                            <div class="badge badge-2">
                                <span class="badge-dot badge-dot-2"></span>
                                Near You
                            </div>
                            <div class="slide-heading">
                                Turfs Within<br><span class="highlight-2">Your Reach</span>
                            </div>
                            <div class="slide-sub">Discover the best sports grounds in your neighborhood. Filter by
                                sport,
                                distance, and availability.</div>
                            <button class="cta-btn btn-2" onclick="sendPrompt('Show me nearby turfs on Playora')">
                                Find Nearby
                                <span class="btn-arrow">→</span>
                            </button>
                        </div>

                        <div class="slide-right">
                            <div class="illus-2">
                                <div class="map-bg">
                                    <div class="map-grid"></div>

                                    <div class="map-pin pin-1" style="left:40px;top:30px">
                                        <div style="position:relative">
                                            <div class="pin-head"><span>⚽</span></div>
                                            <div class="pin-label">0.8 km</div>
                                        </div>
                                        <div class="pin-tail"></div>
                                    </div>

                                    <div class="map-pin pin-2" style="left:90px;top:55px">
                                        <div style="position:relative">
                                            <div class="pin-head"><span>🏏</span></div>
                                            <div class="pin-label">1.2 km</div>
                                        </div>
                                        <div class="pin-tail"></div>
                                    </div>

                                    <div class="map-pin pin-3" style="left:55px;top:80px">
                                        <div style="position:relative">
                                            <div class="pin-head"><span>🏸</span></div>
                                        </div>
                                        <div class="pin-tail"></div>
                                    </div>

                                    <div class="map-pin pin-4" style="left:120px;top:35px">
                                        <div style="position:relative">
                                            <div class="pin-head"><span>🥊</span></div>
                                        </div>
                                        <div class="pin-tail"></div>
                                    </div>

                                    <div class="distance-pill">4 turfs nearby</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLIDE 3: Become a Partner -->
                <div class="slide slide-3">
                    <div class="slide-content">
                        <div class="slide-left">
                            <div class="badge badge-3">
                                <span class="badge-dot badge-dot-3"></span>
                                For Turf Owners
                            </div>
                            <div class="slide-heading">
                                List Your Turf,<br><span class="highlight-3">Earn More</span>
                            </div>
                            <div class="slide-sub">Join <?= $turf_count ?>+ owners earning ₹30K–80K/month. Zero setup fees. Instant
                                payouts.
                                Smart dashboard.</div>
                            <button class="cta-btn btn-3"
                                onclick="sendPrompt('How do I become a Playora partner and list my turf?')">
                                Become a Partner
                                <span class="btn-arrow">→</span>
                            </button>
                        </div>

                        <div class="slide-right">
                            <div class="illus-3">
                                <div class="partner-card">
                                    <div class="partner-logo-row">
                                        <div class="partner-logo-icon">🏟</div>
                                        <div>
                                            <div class="partner-title">Green Arena</div>
                                            <div class="partner-sub">Partner since 2024</div>
                                        </div>
                                    </div>
                                    <div class="earn-row">
                                        <div class="earn-amount">₹62K</div>
                                        <div class="earn-label">this month</div>
                                    </div>
                                    <div class="mini-bar-row">
                                        <div class="mini-bar"></div>
                                        <div class="mini-bar"></div>
                                        <div class="mini-bar"></div>
                                        <div class="mini-bar"></div>
                                        <div class="mini-bar"></div>
                                        <div class="mini-bar"></div>
                                        <div class="mini-bar"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Nav arrows -->
            <div class="nav-arrow nav-prev" id="prev">&#8249;</div>
            <div class="nav-arrow nav-next" id="next">&#8250;</div>

            <!-- Dots -->
            <div class="dots" id="dots">
                <div class="dot active" data-i="0"></div>
                <div class="dot" data-i="1"></div>
                <div class="dot" data-i="2"></div>
            </div>

            <!-- Progress bar -->
            <div class="progress-bar" id="progress"></div>
        </div>
    </section><br><br><br><br>

    <!-- ═══════════════════════════════════════════
     SEARCH BAR 🕵️‍♂️
═══════════════════════════════════════════ -->
    <div class="search-bar-container">
        <div class="search-bar">
            <!-- Location -->
            <div class="search-field-wrap">
                <label>📍 Location</label>
                <input type="text" id="search-location" placeholder="Detecting location...">
            </div>

            <div class="search-divider"></div>

            <!-- Sport -->
            <div class="search-field-wrap">
                <label>⚽ Sport</label>
                <select id="search-sport">
                    <option>Any Sport</option>
                    <?php foreach ($sports as $sport): ?>
                        <option value="<?= $sport['id'] ?>"><?= htmlspecialchars($sport['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="search-divider"></div>

            <!-- Date -->
            <div class="search-field-wrap">
                <label>📅 Date</label>
                <input type="date" id="search-date">
            </div>

            <div class="search-divider"></div>

            <!-- Time -->
            <div class="search-field-wrap">
                <label>⏰ Time</label>
                <input type="time" id="search-time">
            </div>

            <button class="search-btn" onclick="showPage('explore')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Search Turfs
            </button>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════
     TICKER / SOCIAL PROOF
═══════════════════════════════════════════ -->
    <div class="py-4 border-y border-white/5 overflow-hidden relative">
        <div class="absolute left-0 top-0 bottom-0 w-16 bg-gradient-to-r from-void to-transparent z-10"></div>
        <div class="absolute right-0 top-0 bottom-0 w-16 bg-gradient-to-l from-void to-transparent z-10"></div>
        <div class="ticker-inner gap-12 items-center">
            <?php foreach (array_merge($sports, $sports) as $sport): 
                $icon = $icons[$sport['name']] ?? '🏟';
                $colors = ['Football'=>'text-gray-500', 'Cricket'=>'text-emerald-500', 'Badminton'=>'text-gray-500', 'Box Cricket'=>'text-cyan-500'];
                $color = $colors[$sport['name']] ?? 'text-gray-500';
            ?>
            <span class="text-xs <?= $color ?> uppercase tracking-widest whitespace-nowrap"><?= $icon ?> <?= htmlspecialchars($sport['name']) ?>
                &nbsp;&nbsp;•</span>
            <?php endforeach; ?>
        </div>
    </div>


    <!-- ═══════════════════════════════════════════
     CATEGORIES SECTION
═══════════════════════════════════════════ -->
    <section id="categories" class="py-24 px-4 max-w-7xl mx-auto">
        <div class="text-center mb-14">
            <div class="text-emerald-500 text-sm font-semibold tracking-widest uppercase mb-3">Browse by Sport</div>
            <h2 class="font-display font-bold text-4xl lg:text-5xl">Pick Your <span class="grad-text">Game</span></h2>
            <p class="text-gray-500 mt-3 text-base">Choose from a range of sports and find the perfect turf</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            <?php 
            $icons = ['Cricket'=>'🏏', 'Football'=>'⚽', 'Badminton'=>'🏸', 'Tennis'=>'🎾', 'Box Cricket'=>'🥊', 'Basketball'=>'🏀', 'Volleyball'=>'🏐', 'Hockey'=>'🏑'];
            foreach (array_slice($sports, 0, 8) as $sport): 
                $icon = $icons[$sport['name']] ?? '🏟';
            ?>
            <div onclick="window.location.href='auth/login.php'"
                class="cat-card glass rounded-3xl p-8 border border-white/8 cursor-pointer text-center group relative overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-emerald-500/0 to-emerald-500/0 group-hover:from-emerald-500/5 group-hover:to-cyan-500/5 transition-all duration-500 rounded-3xl">
                </div>
                <span class="cat-icon"><?= $icon ?></span>
                <div class="font-display font-semibold text-lg mt-4 mb-1"><?= htmlspecialchars($sport['name']) ?></div>
                <div class="text-xs text-gray-500">Explore Now</div>
                <div
                    class="mt-4 text-xs text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-1">
                    Explore <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════
     TRENDING TURFS
═══════════════════════════════════════════ -->
    <section class="py-16 px-4 relative">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <div class="text-emerald-500 text-sm font-semibold tracking-widest uppercase mb-2">Hot Right Now
                    </div>
                    <h2 class="font-display font-bold text-3xl lg:text-4xl">Trending <span
                            class="grad-text">Turfs</span></h2>
                </div>
                <a href="#"
                    class="text-sm text-emerald-400 hover:text-emerald-300 flex items-center gap-1 transition-colors hidden sm:flex">
                    View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="h-scroll flex gap-5 pb-4">
                <?php 
                $bg_classes = ['Cricket'=>'bg-cricket', 'Football'=>'bg-football', 'Badminton'=>'bg-badminton', 'Tennis'=>'bg-tennis', 'Box Cricket'=>'bg-box'];
                foreach ($trending_turfs as $turf): 
                    $bg = $bg_classes[$turf['sport_name']] ?? 'bg-football';
                    $icon = $icons[$turf['sport_name']] ?? '⚽';
                ?>
                <div class="turf-card flex-shrink-0 w-72 cursor-pointer" onclick="window.location.href='turf-details.php?id=<?= $turf['id'] ?>'">
                    <div class="turf-img-bg <?= $bg ?>">
                        <div class="turf-img-emoji"><?= $icon ?></div>
                        <div class="turf-fav">🤍</div>
                    </div>
                    <div class="turf-body">
                        <div class="turf-name"><?= htmlspecialchars($turf['name']) ?></div>
                        <div class="turf-meta">
                            <div class="turf-rating">★ <?= $turf['rating'] ?></div>
                            <div class="turf-meta-dot"></div>
                            <div class="turf-dist">📍 <?= $turf['city_name'] ?></div>
                            <div class="turf-meta-dot"></div>
                            <div class="turf-sport"><?= $icon ?> <?= htmlspecialchars($turf['sport_name']) ?></div>
                        </div>
                        <div class="turf-price">₹<?= number_format($turf['price']) ?><span>/hr</span></div>
                        <div class="turf-slots">
                            <div class="slot-chip">6PM</div>
                            <div class="slot-chip">7PM</div>
                            <div class="slot-chip">9PM</div>
                        </div>
                        <a href="turf-details.php?id=<?= $turf['id'] ?>" class="turf-book-btn inline-block text-center">Book Now</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════
     NEARBY TURFS
═══════════════════════════════════════════ -->
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <div class="text-emerald-500 text-sm font-semibold tracking-widest uppercase mb-2">📍 Near You</div>
                    <h2 class="font-display font-bold text-3xl lg:text-4xl">Turfs <span class="grad-text">Nearby</span>
                    </h2>
                </div>
                <a href="#"
                    class="text-sm text-emerald-400 hover:text-emerald-300 flex items-center gap-1 transition-colors hidden sm:flex">
                    See more <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <?php 
                $gradient_classes = ['Cricket'=>'from-yellow-900/60', 'Football'=>'from-emerald-900/60', 'Badminton'=>'from-blue-900/60', 'Tennis'=>'from-lime-900/60', 'Box Cricket'=>'from-orange-900/60'];
                foreach ($nearby_turfs as $turf): 
                    $grad = $gradient_classes[$turf['sport_name']] ?? 'from-emerald-900/60';
                    $icon = $icons[$turf['sport_name']] ?? '🏟';
                ?>
                <div class="turf-card glass rounded-3xl overflow-hidden border border-white/8 group">
                    <div class="relative overflow-hidden h-48">
                        <div
                            class="turf-img w-full h-full bg-gradient-to-br <?= $grad ?> to-void flex items-center justify-center">
                            <div class="text-7xl opacity-50"><?= $icon ?></div>
                        </div>
                        <div
                            class="absolute top-3 left-3 bg-emerald-500/20 border border-emerald-500/30 text-xs font-semibold px-2.5 py-1 rounded-full text-emerald-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> New Launch
                        </div>
                        <div class="absolute top-3 right-3 glass px-2 py-1 rounded-full text-xs text-yellow-400">⭐ <?= $turf['rating'] ?>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-void/80 to-transparent">
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <div class="font-display font-semibold text-base"><?= htmlspecialchars($turf['name']) ?></div>
                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                    <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    0.8 km away · <?= htmlspecialchars($turf['city_name']) ?>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            <span
                                class="text-xs bg-emerald-500/10 text-emerald-400 px-2 py-0.5 rounded-full"><?= htmlspecialchars($turf['sport_name']) ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div><span class="text-emerald-400 font-bold text-lg">₹<?= number_format($turf['price']) ?></span><span
                                    class="text-gray-600 text-xs">/hr</span></div>
                            <a href="turf-details.php?id=<?= $turf['id'] ?>" class="btn-em text-xs font-semibold px-4 py-2 rounded-xl text-white inline-block">Book
                                Now</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════
     HOW IT WORKS
═══════════════════════════════════════════ -->
    <section id="how" class="py-28 px-4 relative overflow-hidden">
        <div
            class="absolute inset-0 bg-gradient-to-b from-transparent via-emerald-950/10 to-transparent pointer-events-none">
        </div>
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-16">
                <div class="text-emerald-500 text-sm font-semibold tracking-widest uppercase mb-3">Simple Process</div>
                <h2 class="font-display font-bold text-4xl lg:text-5xl">How It <span class="grad-text">Works</span></h2>
                <p class="text-gray-500 mt-3">Get on the field in 3 easy steps</p>
            </div>

            <div class="relative flex flex-col lg:flex-row items-center gap-8 lg:gap-0">
                <!-- Connecting line (desktop) -->
                <div
                    class="hidden lg:block absolute top-16 left-[calc(16.66%+24px)] right-[calc(16.66%+24px)] h-px step-line opacity-30">
                </div>

                <!-- Step 1 -->
                <div class="flex-1 flex flex-col items-center text-center px-6 relative group">
                    <div
                        class="w-16 h-16 rounded-2xl glass-em border border-emerald-500/25 flex items-center justify-center text-2xl mb-5 transition-all duration-300 group-hover:glow-em group-hover:border-emerald-500/50 relative">
                        🔍
                        <div
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-emerald-500 text-void text-xs font-black flex items-center justify-center">
                            1</div>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-2">Search</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Enter your city, sport, date and preferred time to
                        find available turfs near you.</p>
                </div>

                <!-- Arrow -->
                <div class="lg:hidden text-emerald-500 opacity-40">↓</div>

                <!-- Step 2 -->
                <div class="flex-1 flex flex-col items-center text-center px-6 relative group">
                    <div
                        class="w-16 h-16 rounded-2xl glass-em border border-cyan-500/25 flex items-center justify-center text-2xl mb-5 transition-all duration-300 group-hover:glow-cyan group-hover:border-cyan-500/50 relative">
                        📅
                        <div
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-cyan-500 text-void text-xs font-black flex items-center justify-center">
                            2</div>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-2">Book</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Pick your turf, select a time slot, and pay
                        securely in under 60 seconds.</p>
                </div>

                <!-- Arrow -->
                <div class="lg:hidden text-emerald-500 opacity-40">↓</div>

                <!-- Step 3 -->
                <div class="flex-1 flex flex-col items-center text-center px-6 relative group">
                    <div
                        class="w-16 h-16 rounded-2xl glass-em border border-purple-500/25 flex items-center justify-center text-2xl mb-5 transition-all duration-300 group-hover:shadow-[0_0_30px_rgba(139,92,246,0.25)] group-hover:border-purple-500/50 relative">
                        🏃
                        <div
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-purple-500 text-white text-xs font-black flex items-center justify-center">
                            3</div>
                    </div>
                    <h3 class="font-display font-semibold text-xl mb-2">Play</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">Show up at the turf, get your confirmation, and
                        enjoy your game. That's it!</p>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════
     OWNER CTA
═══════════════════════════════════════════ -->
    <section class="py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div
                class="owner-bg rounded-3xl border border-emerald-500/15 p-10 lg:p-16 relative overflow-hidden glow-em-sm">
                <!-- Background glow blob -->
                <div
                    class="absolute -top-20 -left-20 w-80 h-80 rounded-full bg-emerald-500/5 filter blur-3xl pointer-events-none">
                </div>
                <div
                    class="absolute -bottom-20 -right-20 w-80 h-80 rounded-full bg-cyan-500/5 filter blur-3xl pointer-events-none">
                </div>

                <div class="relative grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left -->
                    <div>
                        <div
                            class="inline-flex items-center gap-2 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold px-3 py-1.5 rounded-full mb-5">
                            🏟 For Turf Owners
                        </div>
                        <h2 class="font-display font-bold text-3xl lg:text-5xl leading-tight mb-5">
                            Own a Turf?<br />
                            <span class="grad-text">Grow Your Business</span><br />with Playora
                        </h2>
                        <p class="text-gray-400 mb-8 leading-relaxed">Join 500+ turf owners who are earning more with
                            Playora's smart booking platform. Zero setup cost, instant payments.</p>
                        <a href="partner.php" class="btn-em font-semibold px-8 py-3.5 rounded-2xl text-white flex items-center gap-2 w-fit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            List Your Turf — It's Free
                        </a>
                    </div>

                    <!-- Right: Benefits -->
                    <div class="space-y-4">
                        <div
                            class="glass rounded-2xl p-5 flex items-start gap-4 hover:border-emerald-500/20 border border-white/5 transition-all duration-300">
                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-500/15 flex items-center justify-center text-lg shrink-0">
                                💰</div>
                            <div>
                                <div class="font-semibold text-sm mb-1">Earn ₹30,000–₹80,000/month</div>
                                <div class="text-gray-500 text-xs">Maximize occupancy with smart slot management and
                                    automated reminders</div>
                            </div>
                        </div>
                        <div
                            class="glass rounded-2xl p-5 flex items-start gap-4 hover:border-cyan-500/20 border border-white/5 transition-all duration-300">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-500/15 flex items-center justify-center text-lg shrink-0">
                                📊</div>
                            <div>
                                <div class="font-semibold text-sm mb-1">Real-time Dashboard</div>
                                <div class="text-gray-500 text-xs">Track bookings, revenue, and customer reviews in one
                                    beautiful dashboard</div>
                            </div>
                        </div>
                        <div
                            class="glass rounded-2xl p-5 flex items-start gap-4 hover:border-purple-500/20 border border-white/5 transition-all duration-300">
                            <div
                                class="w-10 h-10 rounded-xl bg-purple-500/15 flex items-center justify-center text-lg shrink-0">
                                ⚡</div>
                            <div>
                                <div class="font-semibold text-sm mb-1">Instant Payouts</div>
                                <div class="text-gray-500 text-xs">Get your earnings transferred directly — no delay, no
                                    hassle</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════
     TESTIMONIALS
═══════════════════════════════════════════ -->
    <section class="py-28 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-14">
                <div class="text-emerald-500 text-sm font-semibold tracking-widest uppercase mb-3">Player Reviews</div>
                <h2 class="font-display font-bold text-4xl lg:text-5xl">What Players <span class="grad-text">Say</span>
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- T1 -->
                <div class="testi-card glass rounded-3xl p-7 border border-white/7">
                    <div class="flex gap-1 mb-4">
                        <span class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">"Booked a football turf in under 2 minutes!
                        The slot was confirmed instantly and the turf was spotless. Playora is now my go-to app for
                        weekend matches."</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-cyan-500 flex items-center justify-center font-bold text-sm text-void">
                            RK</div>
                        <div>
                            <div class="font-semibold text-sm">Rohan Kapoor</div>
                            <div class="text-gray-600 text-xs">Football Enthusiast · Mumbai</div>
                        </div>
                    </div>
                </div>

                <!-- T2 -->
                <div class="testi-card glass rounded-3xl p-7 border border-white/7">
                    <div class="flex gap-1 mb-4">
                        <span class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">"As a cricket team captain, I used to struggle
                        finding good turfs. Playora has made it so easy — filters, ratings, pricing all in one place.
                        Game changer!"</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center font-bold text-sm text-white">
                            AP</div>
                        <div>
                            <div class="font-semibold text-sm">Arjun Patel</div>
                            <div class="text-gray-600 text-xs">Cricket Captain · Ahmedabad</div>
                        </div>
                    </div>
                </div>

                <!-- T3 -->
                <div class="testi-card glass rounded-3xl p-7 border border-white/7">
                    <div class="flex gap-1 mb-4">
                        <span class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span><span class="star-filled">★</span><span
                            class="text-gray-600">★</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">"The badminton courts on Playora are amazing!
                        I found a court 500m from my house I didn't even know existed. Super smooth booking experience."
                    </p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center font-bold text-sm text-void">
                            PS</div>
                        <div>
                            <div class="font-semibold text-sm">Priya Sharma</div>
                            <div class="text-gray-600 text-xs">Badminton Player · Bangalore</div>
                        </div>
                    </div>
                </div>

                <!-- T4 -->
                <div class="testi-card glass rounded-3xl p-7 border border-white/7 md:col-span-1 lg:col-span-1">
                    <div class="flex gap-1 mb-4">
                        <span class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">"Listed my turf on Playora 3 months ago.
                        Bookings increased by 70%! The owner dashboard is incredible — I can manage everything from my
                        phone."</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-500 to-yellow-500 flex items-center justify-center font-bold text-sm text-void">
                            SM</div>
                        <div>
                            <div class="font-semibold text-sm">Suresh Mehta</div>
                            <div class="text-gray-600 text-xs">Turf Owner · Hyderabad</div>
                        </div>
                    </div>
                </div>

                <!-- T5 -->
                <div class="testi-card glass rounded-3xl p-7 border border-white/7">
                    <div class="flex gap-1 mb-4">
                        <span class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span><span class="star-filled">★</span><span
                            class="star-filled">★</span>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed mb-5">"Our corporate team building events are now
                        sorted! Playora has large turfs for groups. Booking for 20 people was just as easy as for 5."
                    </p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-600 to-teal-400 flex items-center justify-center font-bold text-sm text-void">
                            NR</div>
                        <div>
                            <div class="font-semibold text-sm">Neha Rao</div>
                            <div class="text-gray-600 text-xs">HR Manager · Pune</div>
                        </div>
                    </div>
                </div>

                <!-- T6 — CTA card -->
                <div
                    class="testi-card glass-em rounded-3xl p-7 border border-emerald-500/15 flex flex-col justify-between">
                    <div>
                        <div class="text-4xl mb-4">🏆</div>
                        <div class="font-display font-bold text-2xl mb-2">Join 50K+ Players</div>
                        <p class="text-gray-500 text-sm">Be part of India's fastest growing sports turf booking
                            community.</p>
                    </div>
                    <button class="btn-em font-semibold text-sm px-6 py-3 rounded-2xl text-white mt-6 w-full">Get
                        Started Free</button>
                </div>
            </div>
        </div>
    </section>


    <!-- ═══════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════ -->
    <?php include "includes/footer.php"; ?>


    <script>
        // Navbar scroll blur
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        });

        // Mobile menu toggle
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            const h1 = document.getElementById('h1');
            const h2 = document.getElementById('h2');
            const h3 = document.getElementById('h3');
            const isOpen = !menu.classList.contains('hidden');
            menu.classList.toggle('hidden');
            if (!isOpen) {
                h1.style.transform = 'rotate(45deg) translateY(8px)';
                h2.style.opacity = '0';
                h3.style.transform = 'rotate(-45deg) translateY(-8px)';
                h3.style.width = '20px';
            } else {
                h1.style.transform = '';
                h2.style.opacity = '';
                h3.style.transform = '';
                h3.style.width = '';
            }
        }

        // Ripple effect on btn-em buttons
        document.querySelectorAll('.btn-em').forEach(btn => {
            btn.addEventListener('click', function (e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height) * 2;
                ripple.style.cssText = `
        position:absolute; border-radius:50%; background:rgba(255,255,255,0.25);
        width:${size}px; height:${size}px;
        left:${e.clientX - rect.left - size / 2}px;
        top:${e.clientY - rect.top - size / 2}px;
        animation: ripple-anim 0.6s ease-out forwards; pointer-events:none;
      `;
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });

        // Add ripple keyframe
        const style = document.createElement('style');
        style.textContent = `@keyframes ripple-anim { from { transform:scale(0); opacity:1; } to { transform:scale(1); opacity:0; } }`;
        document.head.appendChild(style);

        // Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadeUp');
                    entry.target.style.opacity = '1';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.cat-card, .turf-card, .testi-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(el);
        });

        const cardObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 80 * i);
                    cardObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.cat-card, .turf-card, .testi-card').forEach(el => {
            cardObserver.observe(el);
        });

        // Carousel Logic
        const track = document.getElementById('track');
        const dots = document.querySelectorAll('.dot');
        const progress = document.getElementById('progress');
        let current = 0;
        const total = 3;
        let timer;

        function goTo(n, resetTimer = true) {
            current = (n + total) % total;
            track.style.transform = `translateX(-${current * 100}%)`;
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
            if (resetTimer) {
                clearInterval(timer);
                restartProgress();
                timer = setInterval(() => goTo(current + 1), 4000);
            }
        }

        function restartProgress() {
            progress.style.animation = 'none';
            progress.offsetWidth;
            progress.style.animation = 'progressAnim 4s linear forwards';
        }

        document.getElementById('prev').onclick = () => goTo(current - 1);
        document.getElementById('next').onclick = () => goTo(current + 1);
        dots.forEach(d => d.onclick = () => goTo(+d.dataset.i));

        restartProgress();
        timer = setInterval(() => goTo(current + 1), 4000);

        // Touch/swipe support
        let startX = 0;
        track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - startX;
            if (Math.abs(dx) > 40) goTo(dx < 0 ? current + 1 : current - 1);
        });

        // sendPrompt Stub
        function sendPrompt(prompt) {
            console.log("Prompt sent:", prompt);
            // Implement prompt handling or redirection as needed
            window.location.href = '#categories'; // For now, scroll to explore
        }

        // --- Theme Toggle Logic ---
        function toggleTheme() {
            const body = document.body;
            const icon = document.getElementById('theme-icon');
            body.classList.toggle('light-mode');
            const isLight = body.classList.contains('light-mode');
            localStorage.setItem('theme', isLight ? 'light' : 'dark');
            updateThemeIcon(isLight);
        }

        function updateThemeIcon(isLight) {
            const icon = document.getElementById('theme-icon');
            if (isLight) {
                // Moon Icon
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />`;
            } else {
                // Sun Icon
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />`;
            }
        }

        // Initialize Theme
        if (localStorage.getItem('theme') === 'light') {
            document.body.classList.add('light-mode');
            updateThemeIcon(true);
        }

        // --- Location Logic ---
        function getLocation() {
            const navCity = document.getElementById('nav-city');
            const searchLoc = document.getElementById('search-location');

            navCity.textContent = "Detecting...";

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        const { latitude, longitude } = pos.coords;
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${latitude}&lon=${longitude}&format=json`)
                            .then(r => r.json())
                            .then(data => {
                                const city = data.address.city || data.address.town || data.address.village || data.address.county || 'Your City';
                                const state = data.address.state || '';
                                navCity.textContent = city;
                                if (searchLoc) searchLoc.value = city + (state ? ', ' + state : '');
                                console.log(`📍 Location detected: ${city}`);
                            })
                            .catch(() => setFallbackCity());
                    },
                    () => setFallbackCity()
                );
            } else {
                setFallbackCity();
            }
        }

        function setFallbackCity() {
            document.getElementById('nav-city').textContent = 'Surat';
            const searchLoc = document.getElementById('search-location');
            if (searchLoc) searchLoc.value = 'Surat, Gujarat';
        }

        // --- Navigation ---
        function showPage(page) {
            if (page === 'explore') {
                const el = document.getElementById('categories');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Set default date
        const dateInput = document.getElementById('search-date');
        if (dateInput) {
            dateInput.value = new Date().toISOString().split('T')[0];
        }

        // Run on load
        getLocation();
    </script>
</body>

</html>