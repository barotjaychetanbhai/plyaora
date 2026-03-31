<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Playora – Book Sports Turfs Near You</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
  --brown-900: #1a0e07;
  --brown-800: #2d1a0e;
  --brown-700: #4a2c1a;
  --brown-600: #6b3d24;
  --brown-500: #8b5e3c;
  --brown-400: #a97c5b;
  --brown-300: #c9a07e;
  --brown-200: #e8c9a8;
  --brown-100: #f5ede3;
  --brown-50: #fdf8f3;
  --cream: #faf6f0;
  --white: #ffffff;
  --gold: #c9a84c;
  --gold-light: #e8c96a;
  --text-dark: #1a0e07;
  --text-mid: #4a2c1a;
  --text-light: #8b5e3c;
  --shadow-sm: 0 2px 8px rgba(26,14,7,0.08);
  --shadow-md: 0 8px 30px rgba(26,14,7,0.12);
  --shadow-lg: 0 20px 60px rgba(26,14,7,0.18);
  --radius: 14px;
  --radius-sm: 8px;
}

* { margin:0; padding:0; box-sizing:border-box; }

html { scroll-behavior: smooth; }

body {
  font-family: 'DM Sans', sans-serif;
  background: var(--cream);
  color: var(--text-dark);
  overflow-x: hidden;
}

/* SCROLLBAR */
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: var(--brown-100); }
::-webkit-scrollbar-thumb { background: var(--brown-400); border-radius: 3px; }

/* =========== NAVBAR =========== */
.navbar {
  position: sticky; top: 0; z-index: 1000;
  background: rgba(250,246,240,0.92);
  backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(201,160,126,0.25);
  padding: 0 40px;
  height: 68px;
  display: flex; align-items: center; justify-content: space-between;
  transition: all 0.3s ease;
}
.navbar.scrolled {
  background: rgba(250,246,240,0.98);
  box-shadow: var(--shadow-md);
}
.nav-logo {
  font-family: 'Playfair Display', serif;
  font-size: 26px; font-weight: 900;
  color: var(--brown-800);
  letter-spacing: -0.5px;
  display: flex; align-items: center; gap: 8px;
}
.nav-logo span { color: var(--gold); }
.logo-dot { width: 8px; height: 8px; background: var(--gold); border-radius: 50%; display: inline-block; margin-bottom: 2px; }

.nav-location {
  display: flex; align-items: center; gap: 6px;
  background: var(--brown-100);
  padding: 8px 14px; border-radius: 30px;
  cursor: pointer; font-size: 13.5px; font-weight: 500;
  color: var(--brown-700);
  border: 1.5px solid transparent;
  transition: all 0.2s;
}
.nav-location:hover { border-color: var(--brown-300); background: var(--white); }
.nav-location .loc-icon { font-size: 14px; }
#nav-city { max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.nav-links { display: flex; align-items: center; gap: 32px; }
.nav-links a {
  font-size: 14px; font-weight: 500;
  color: var(--brown-600);
  text-decoration: none;
  transition: color 0.2s;
  position: relative;
}
.nav-links a::after {
  content: '';
  position: absolute; bottom: -3px; left: 0;
  width: 0; height: 2px;
  background: var(--gold);
  transition: width 0.3s;
}
.nav-links a:hover { color: var(--brown-900); }
.nav-links a:hover::after { width: 100%; }

.nav-actions { display: flex; align-items: center; gap: 12px; }
.btn-outline {
  padding: 8px 20px; border-radius: 30px;
  border: 1.5px solid var(--brown-400);
  background: transparent;
  color: var(--brown-700); font-size: 14px; font-weight: 500;
  cursor: pointer; transition: all 0.2s; font-family: 'DM Sans', sans-serif;
}
.btn-outline:hover { background: var(--brown-100); border-color: var(--brown-600); }
.btn-primary {
  padding: 9px 22px; border-radius: 30px;
  background: var(--brown-800);
  color: var(--white); font-size: 14px; font-weight: 600;
  border: none; cursor: pointer; transition: all 0.25s;
  font-family: 'DM Sans', sans-serif;
}
.btn-primary:hover { background: var(--brown-900); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(26,14,7,0.25); }

/* =========== HERO =========== */
.hero {
  position: relative;
  background: var(--brown-900);
  min-height: 540px;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 80px 40px 90px;
  overflow: hidden;
}
.hero-bg {
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 70% 60% at 20% 50%, rgba(201,160,126,0.12) 0%, transparent 70%),
    radial-gradient(ellipse 50% 80% at 80% 30%, rgba(201,168,76,0.08) 0%, transparent 70%);
}
.hero-pattern {
  position: absolute; inset: 0;
  opacity: 0.04;
  background-image: repeating-linear-gradient(0deg, transparent, transparent 40px, rgba(255,255,255,0.5) 40px, rgba(255,255,255,0.5) 41px),
    repeating-linear-gradient(90deg, transparent, transparent 40px, rgba(255,255,255,0.5) 40px, rgba(255,255,255,0.5) 41px);
}
.hero-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(201,168,76,0.15);
  border: 1px solid rgba(201,168,76,0.35);
  color: var(--gold-light);
  padding: 6px 16px; border-radius: 30px;
  font-size: 12.5px; font-weight: 500;
  margin-bottom: 24px; letter-spacing: 0.5px;
  animation: fadeDown 0.6s ease both;
}
.hero h1 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(38px, 6vw, 68px);
  font-weight: 900; color: var(--white);
  text-align: center; line-height: 1.1;
  letter-spacing: -1.5px;
  margin-bottom: 16px;
  animation: fadeDown 0.6s 0.1s ease both;
}
.hero h1 em { color: var(--gold); font-style: normal; }
.hero p {
  color: rgba(255,255,255,0.55);
  font-size: 16px; text-align: center;
  margin-bottom: 40px; font-weight: 400;
  animation: fadeDown 0.6s 0.2s ease both;
}

/* SEARCH BAR */
.search-bar {
  background: var(--white);
  border-radius: 16px;
  padding: 8px 8px 8px 8px;
  display: flex; align-items: stretch; gap: 4px;
  box-shadow: 0 20px 60px rgba(26,14,7,0.4);
  width: 100%; max-width: 840px;
  animation: fadeUp 0.6s 0.3s ease both;
}
.search-field {
  flex: 1; padding: 14px 18px;
  border: none; outline: none;
  background: transparent;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; color: var(--text-dark);
  cursor: pointer;
  transition: background 0.2s;
  border-radius: 10px;
  min-width: 0;
}
.search-field:hover { background: var(--brown-50); }
.search-field-wrap {
  flex: 1; display: flex; flex-direction: column;
  padding: 10px 16px;
  border-radius: 10px; cursor: pointer;
  transition: background 0.2s; min-width: 0;
}
.search-field-wrap:hover { background: var(--brown-50); }
.search-field-wrap label {
  font-size: 10px; font-weight: 600;
  color: var(--brown-400); letter-spacing: 0.8px;
  text-transform: uppercase; margin-bottom: 3px;
}
.search-field-wrap select, .search-field-wrap input[type="date"], .search-field-wrap input[type="time"] {
  border: none; outline: none; background: transparent;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; color: var(--text-dark); font-weight: 500;
  cursor: pointer; width: 100%;
}
.search-divider { width: 1px; background: var(--brown-100); margin: 8px 0; flex-shrink: 0; }
.search-btn {
  background: var(--brown-800);
  color: var(--white); border: none;
  padding: 0 28px; border-radius: 12px;
  font-size: 15px; font-weight: 600;
  cursor: pointer; transition: all 0.25s;
  font-family: 'DM Sans', sans-serif;
  white-space: nowrap;
  display: flex; align-items: center; gap: 8px;
}
.search-btn:hover { background: var(--brown-900); transform: scale(1.02); }
.search-stats {
  display: flex; gap: 32px;
  margin-top: 28px;
  animation: fadeUp 0.6s 0.45s ease both;
}
.stat-item { text-align: center; }
.stat-num {
  font-family: 'Playfair Display', serif;
  font-size: 22px; font-weight: 700;
  color: var(--gold);
}
.stat-lbl { font-size: 12px; color: rgba(255,255,255,0.45); margin-top: 2px; }

/* =========== SECTION COMMONS =========== */
section { padding: 72px 40px; }
.section-header { margin-bottom: 36px; }
.section-tag {
  display: inline-block;
  font-size: 11px; font-weight: 700;
  letter-spacing: 1.5px; text-transform: uppercase;
  color: var(--brown-500); margin-bottom: 8px;
}
.section-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(26px, 4vw, 38px);
  font-weight: 800; color: var(--brown-900);
  letter-spacing: -0.5px; line-height: 1.15;
}
.section-title em { color: var(--gold); font-style: normal; }
.section-sub {
  font-size: 15px; color: var(--text-light);
  margin-top: 8px; font-weight: 400;
}
.section-row { display: flex; justify-content: space-between; align-items: flex-end; }
.see-all {
  color: var(--brown-600); font-size: 14px; font-weight: 600;
  text-decoration: none; display: flex; align-items: center; gap: 4px;
  transition: color 0.2s;
}
.see-all:hover { color: var(--brown-900); }

/* =========== CATEGORIES =========== */
.categories-section { background: var(--white); }
.categories-grid {
  display: flex; gap: 16px; flex-wrap: wrap;
}
.cat-card {
  flex: 1; min-width: 120px; max-width: 200px;
  background: var(--brown-50);
  border: 1.5px solid var(--brown-100);
  border-radius: 16px;
  padding: 28px 20px;
  display: flex; flex-direction: column;
  align-items: center; gap: 12px;
  cursor: pointer; transition: all 0.3s;
  text-align: center;
}
.cat-card:hover {
  background: var(--brown-800);
  border-color: var(--brown-800);
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(26,14,7,0.2);
}
.cat-card:hover .cat-name { color: var(--white); }
.cat-card:hover .cat-icon-wrap { background: rgba(255,255,255,0.15); }
.cat-icon-wrap {
  width: 58px; height: 58px;
  background: var(--brown-100);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 28px;
  transition: background 0.3s;
}
.cat-name { font-size: 13.5px; font-weight: 600; color: var(--brown-700); transition: color 0.3s; }
.cat-count { font-size: 11.5px; color: var(--brown-400); transition: color 0.3s; }
.cat-card:hover .cat-count { color: rgba(255,255,255,0.6); }
.cat-card.active {
  background: var(--brown-800);
  border-color: var(--brown-800);
}
.cat-card.active .cat-name { color: var(--white); }
.cat-card.active .cat-icon-wrap { background: rgba(255,255,255,0.15); }
.cat-card.active .cat-count { color: rgba(255,255,255,0.6); }

/* =========== TURF CARDS =========== */
.turfs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
  gap: 22px;
}
.turf-card {
  background: var(--white);
  border-radius: 18px;
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  border: 1px solid rgba(201,160,126,0.15);
  transition: all 0.35s;
  cursor: pointer;
  position: relative;
}
.turf-card:hover {
  transform: translateY(-6px);
  box-shadow: var(--shadow-lg);
  border-color: var(--brown-200);
}
.turf-img {
  width: 100%; height: 180px;
  object-fit: cover;
  background: var(--brown-200);
  display: flex; align-items: center; justify-content: center;
  font-size: 56px;
  position: relative; overflow: hidden;
}
.turf-img-bg {
  width: 100%; height: 180px;
  position: relative; overflow: hidden;
}
.turf-img-emoji {
  width: 100%; height: 100%;
  display: flex; align-items: center; justify-content: center;
  font-size: 56px;
  transition: transform 0.4s;
}
.turf-card:hover .turf-img-emoji { transform: scale(1.08); }
.turf-badge {
  position: absolute; top: 12px; left: 12px;
  background: var(--brown-800);
  color: var(--white); font-size: 11px; font-weight: 600;
  padding: 4px 10px; border-radius: 20px;
}
.turf-fav {
  position: absolute; top: 12px; right: 12px;
  width: 32px; height: 32px;
  background: rgba(255,255,255,0.9);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; cursor: pointer;
  transition: all 0.2s;
}
.turf-fav:hover { background: #fff; transform: scale(1.1); }
.turf-body { padding: 16px 18px 18px; }
.turf-name {
  font-family: 'Playfair Display', serif;
  font-size: 17px; font-weight: 700;
  color: var(--brown-900); margin-bottom: 6px;
}
.turf-meta { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
.turf-rating {
  display: flex; align-items: center; gap: 4px;
  font-size: 13px; font-weight: 600; color: var(--brown-700);
}
.turf-rating .star { color: var(--gold); }
.turf-dist, .turf-sport {
  font-size: 12.5px; color: var(--brown-400); font-weight: 400;
}
.turf-meta-dot { width: 3px; height: 3px; background: var(--brown-200); border-radius: 50%; }
.turf-price {
  font-size: 15px; font-weight: 700; color: var(--brown-800);
}
.turf-price span { font-size: 12px; font-weight: 400; color: var(--brown-400); }
.turf-slots { display: flex; gap: 6px; margin: 12px 0; flex-wrap: wrap; }
.slot-chip {
  padding: 5px 12px; border-radius: 20px;
  background: var(--brown-50);
  border: 1.5px solid var(--brown-100);
  font-size: 12px; font-weight: 600;
  color: var(--brown-600); cursor: pointer;
  transition: all 0.2s;
}
.slot-chip:hover, .slot-chip.selected {
  background: var(--brown-800); color: var(--white); border-color: var(--brown-800);
}
.turf-book-btn {
  width: 100%; padding: 11px;
  background: var(--brown-800);
  color: var(--white); border: none;
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; font-weight: 600;
  cursor: pointer; transition: all 0.25s;
  margin-top: 4px;
}
.turf-book-btn:hover { background: var(--brown-900); box-shadow: 0 6px 20px rgba(26,14,7,0.25); }

/* =========== NEARBY SECTION =========== */
.nearby-section { background: var(--brown-50); }

/* =========== HOW IT WORKS =========== */
.how-section { background: var(--brown-900); color: var(--white); }
.how-section .section-title { color: var(--white); }
.how-section .section-tag { color: var(--gold); }
.steps-row {
  display: grid; grid-template-columns: repeat(3, 1fr);
  gap: 30px; margin-top: 48px;
}
.step-card {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 20px;
  padding: 36px 28px;
  text-align: center;
  transition: all 0.3s;
  position: relative; overflow: hidden;
}
.step-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--gold), var(--brown-400));
  transform: scaleX(0); transform-origin: left;
  transition: transform 0.4s;
}
.step-card:hover { background: rgba(255,255,255,0.08); transform: translateY(-4px); }
.step-card:hover::before { transform: scaleX(1); }
.step-num {
  font-family: 'Playfair Display', serif;
  font-size: 52px; font-weight: 900;
  color: rgba(201,168,76,0.2);
  line-height: 1; margin-bottom: 12px;
}
.step-icon { font-size: 40px; margin-bottom: 16px; }
.step-title {
  font-family: 'Playfair Display', serif;
  font-size: 20px; font-weight: 700;
  color: var(--white); margin-bottom: 10px;
}
.step-desc { font-size: 14px; color: rgba(255,255,255,0.55); line-height: 1.6; }

/* =========== OWNER CTA =========== */
.owner-section {
  background: linear-gradient(135deg, var(--brown-800) 0%, var(--brown-900) 100%);
  position: relative; overflow: hidden;
}
.owner-bg {
  position: absolute; inset: 0;
  background: radial-gradient(ellipse 60% 80% at 90% 50%, rgba(201,168,76,0.12) 0%, transparent 70%);
}
.owner-content {
  position: relative; z-index: 1;
  display: flex; align-items: center;
  justify-content: space-between; gap: 40px;
}
.owner-text .owner-tag {
  display: inline-block;
  background: rgba(201,168,76,0.2);
  color: var(--gold-light);
  padding: 6px 16px; border-radius: 30px;
  font-size: 12px; font-weight: 600;
  letter-spacing: 1px; margin-bottom: 20px;
}
.owner-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(32px, 5vw, 50px);
  font-weight: 900; color: var(--white);
  line-height: 1.1; letter-spacing: -1px;
  margin-bottom: 16px;
}
.owner-title em { color: var(--gold); font-style: normal; }
.owner-desc { font-size: 16px; color: rgba(255,255,255,0.6); max-width: 420px; line-height: 1.7; margin-bottom: 32px; }
.owner-perks { display: flex; gap: 24px; margin-bottom: 36px; }
.perk { display: flex; align-items: center; gap: 10px; }
.perk-icon { width: 36px; height: 36px; background: rgba(201,168,76,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.perk-text { font-size: 13px; color: rgba(255,255,255,0.7); font-weight: 500; }
.btn-gold {
  padding: 14px 32px; border-radius: 12px;
  background: var(--gold);
  color: var(--brown-900); font-size: 15px; font-weight: 700;
  border: none; cursor: pointer; transition: all 0.25s;
  font-family: 'DM Sans', sans-serif;
}
.btn-gold:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: 0 10px 30px rgba(201,168,76,0.35); }
.owner-visual {
  flex-shrink: 0;
  width: 340px; height: 300px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 24px;
  display: flex; align-items: center; justify-content: center;
  font-size: 120px;
  position: relative; overflow: hidden;
}
.owner-stat-chip {
  position: absolute;
  background: var(--white); border-radius: 12px;
  padding: 10px 16px; box-shadow: var(--shadow-md);
}
.owner-stat-chip.chip1 { bottom: 24px; left: -16px; }
.owner-stat-chip.chip2 { top: 24px; right: -16px; }
.chip-num { font-size: 18px; font-weight: 800; color: var(--brown-900); }
.chip-lbl { font-size: 11px; color: var(--brown-400); }

/* =========== TESTIMONIALS =========== */
.testi-section { background: var(--white); }
.testi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px;
}
.testi-card {
  background: var(--brown-50);
  border: 1px solid var(--brown-100);
  border-radius: 18px; padding: 28px;
  transition: all 0.3s;
}
.testi-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-md);
  background: var(--white);
}
.testi-stars { color: var(--gold); font-size: 14px; margin-bottom: 14px; letter-spacing: 2px; }
.testi-text {
  font-size: 14.5px; line-height: 1.7;
  color: var(--brown-700); margin-bottom: 20px;
  font-style: italic;
}
.testi-quote {
  font-size: 48px; color: var(--brown-200);
  font-family: 'Playfair Display', serif;
  line-height: 0.5; margin-bottom: 10px;
}
.testi-author { display: flex; align-items: center; gap: 12px; }
.testi-avatar {
  width: 42px; height: 42px; border-radius: 50%;
  background: var(--brown-200);
  display: flex; align-items: center; justify-content: center;
  font-size: 18px;
  border: 2px solid var(--brown-300);
}
.testi-name { font-size: 14px; font-weight: 700; color: var(--brown-900); }
.testi-role { font-size: 12px; color: var(--brown-400); }

/* =========== FOOTER =========== */
footer {
  background: var(--brown-900);
  color: rgba(255,255,255,0.7);
  padding: 60px 40px 30px;
}
.footer-grid {
  display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 50px; margin-bottom: 48px;
}
.footer-brand .logo {
  font-family: 'Playfair Display', serif;
  font-size: 24px; font-weight: 900;
  color: var(--white); margin-bottom: 14px;
}
.footer-brand .logo span { color: var(--gold); }
.footer-brand p { font-size: 13.5px; line-height: 1.7; max-width: 260px; color: rgba(255,255,255,0.5); }
.footer-brand .socials { display: flex; gap: 10px; margin-top: 20px; }
.social-btn {
  width: 36px; height: 36px; border-radius: 10px;
  background: rgba(255,255,255,0.08);
  display: flex; align-items: center; justify-content: center;
  font-size: 15px; cursor: pointer; transition: all 0.2s;
  text-decoration: none; color: white;
}
.social-btn:hover { background: var(--gold); transform: translateY(-2px); }
.footer-col h4 {
  font-size: 13px; font-weight: 700;
  letter-spacing: 1px; text-transform: uppercase;
  color: var(--white); margin-bottom: 18px;
}
.footer-col ul { list-style: none; }
.footer-col li { margin-bottom: 10px; }
.footer-col a {
  font-size: 13.5px; color: rgba(255,255,255,0.5);
  text-decoration: none; transition: color 0.2s;
}
.footer-col a:hover { color: var(--gold); }
.footer-bottom {
  border-top: 1px solid rgba(255,255,255,0.08);
  padding-top: 24px;
  display: flex; justify-content: space-between; align-items: center;
}
.footer-bottom p { font-size: 12.5px; color: rgba(255,255,255,0.35); }

/* =========== MODAL =========== */
.modal-overlay {
  position: fixed; inset: 0; z-index: 2000;
  background: rgba(26,14,7,0.7);
  backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  opacity: 0; pointer-events: none; transition: opacity 0.3s;
}
.modal-overlay.open { opacity: 1; pointer-events: all; }
.modal-box {
  background: var(--white);
  border-radius: 24px;
  padding: 40px;
  width: 100%; max-width: 440px;
  transform: translateY(20px) scale(0.97);
  transition: transform 0.3s;
  box-shadow: var(--shadow-lg);
}
.modal-overlay.open .modal-box { transform: translateY(0) scale(1); }
.modal-title {
  font-family: 'Playfair Display', serif;
  font-size: 26px; font-weight: 800;
  color: var(--brown-900); margin-bottom: 6px;
}
.modal-sub { font-size: 14px; color: var(--brown-500); margin-bottom: 28px; }
.modal-input {
  width: 100%; padding: 12px 16px;
  border: 1.5px solid var(--brown-200);
  border-radius: 10px; margin-bottom: 14px;
  font-family: 'DM Sans', sans-serif;
  font-size: 14px; color: var(--brown-900);
  outline: none; transition: border-color 0.2s;
  background: var(--brown-50);
}
.modal-input:focus { border-color: var(--brown-500); background: var(--white); }
.modal-btn {
  width: 100%; padding: 13px;
  background: var(--brown-800); color: var(--white);
  border: none; border-radius: 10px;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px; font-weight: 700;
  cursor: pointer; transition: all 0.25s; margin-top: 4px;
}
.modal-btn:hover { background: var(--brown-900); }
.modal-close {
  position: absolute; top: 16px; right: 16px;
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--brown-100); border: none;
  cursor: pointer; font-size: 16px;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.2s;
}
.modal-close:hover { background: var(--brown-200); }
.modal-box { position: relative; }
.modal-divider { display: flex; align-items: center; gap: 12px; margin: 20px 0; }
.modal-divider::before, .modal-divider::after { content: ''; flex: 1; height: 1px; background: var(--brown-100); }
.modal-divider span { font-size: 12px; color: var(--brown-400); }

/* =========== MOBILE BOTTOM NAV =========== */
.mobile-nav {
  display: none;
  position: fixed; bottom: 0; left: 0; right: 0; z-index: 900;
  background: var(--white);
  border-top: 1px solid var(--brown-100);
  padding: 10px 0 16px;
  box-shadow: 0 -8px 30px rgba(26,14,7,0.1);
}
.mobile-nav-items { display: flex; justify-content: space-around; }
.mnav-item {
  display: flex; flex-direction: column; align-items: center; gap: 4px;
  cursor: pointer; padding: 4px 12px;
  color: var(--brown-400); font-size: 11px; font-weight: 600;
  transition: color 0.2s;
}
.mnav-item.active { color: var(--brown-800); }
.mnav-icon { font-size: 22px; }

/* =========== EXPLORE PAGE =========== */
.explore-page { display: none; }
.explore-page.active { display: flex; flex-direction: column; min-height: 100vh; }
.explore-header {
  background: var(--white);
  padding: 16px 24px;
  border-bottom: 1px solid var(--brown-100);
  position: sticky; top: 68px; z-index: 100;
}
.explore-search-row { display: flex; gap: 10px; align-items: center; }
.explore-input {
  flex: 1; padding: 10px 16px;
  border: 1.5px solid var(--brown-200);
  border-radius: 10px;
  font-family: 'DM Sans', sans-serif; font-size: 14px;
  color: var(--brown-900); background: var(--brown-50);
  outline: none; transition: border-color 0.2s;
}
.explore-input:focus { border-color: var(--brown-500); background: var(--white); }
.filter-btn {
  padding: 10px 18px; border-radius: 10px;
  border: 1.5px solid var(--brown-200);
  background: var(--white); color: var(--brown-700);
  font-family: 'DM Sans', sans-serif; font-size: 14px; font-weight: 500;
  cursor: pointer; transition: all 0.2s;
  display: flex; align-items: center; gap: 6px;
}
.filter-btn:hover { border-color: var(--brown-500); background: var(--brown-50); }
.explore-body { display: flex; flex: 1; }
.filter-sidebar {
  width: 270px; flex-shrink: 0;
  background: var(--white);
  border-right: 1px solid var(--brown-100);
  padding: 24px 20px;
  overflow-y: auto;
}
.filter-group { margin-bottom: 28px; }
.filter-group-title {
  font-size: 12px; font-weight: 700;
  letter-spacing: 1px; text-transform: uppercase;
  color: var(--brown-500); margin-bottom: 12px;
}
.filter-option {
  display: flex; align-items: center; gap: 10px;
  padding: 8px 12px; border-radius: 8px;
  cursor: pointer; transition: background 0.2s; margin-bottom: 4px;
}
.filter-option:hover { background: var(--brown-50); }
.filter-option input[type="checkbox"] { accent-color: var(--brown-700); width: 16px; height: 16px; }
.filter-option label { font-size: 13.5px; color: var(--brown-700); cursor: pointer; flex: 1; }
.filter-option span { font-size: 12px; color: var(--brown-400); }
.price-range-wrap { display: flex; flex-direction: column; gap: 8px; }
.price-range-wrap input[type="range"] { accent-color: var(--brown-700); width: 100%; }
.price-labels { display: flex; justify-content: space-between; font-size: 12px; color: var(--brown-500); }
.explore-results {
  flex: 1; padding: 24px;
  overflow-y: auto;
  background: var(--brown-50);
}
.results-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.results-count { font-size: 14px; color: var(--brown-600); font-weight: 500; }
.sort-select {
  padding: 8px 14px; border-radius: 8px;
  border: 1.5px solid var(--brown-200);
  background: var(--white); color: var(--brown-700);
  font-family: 'DM Sans', sans-serif; font-size: 13px;
  outline: none; cursor: pointer;
}
.explore-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 18px;
}
.map-panel {
  width: 380px; flex-shrink: 0;
  background: var(--brown-800);
  position: sticky; top: 68px;
  height: calc(100vh - 130px);
  display: flex; align-items: center; justify-content: center;
  flex-direction: column; gap: 16px;
  color: var(--white);
}
.map-placeholder {
  text-align: center;
}
.map-placeholder .map-icon { font-size: 56px; margin-bottom: 12px; }
.map-placeholder p { font-size: 14px; color: rgba(255,255,255,0.5); }
.map-dots {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
  margin-top: 16px;
}
.map-dot {
  width: 12px; height: 12px; border-radius: 50%;
  background: var(--gold); opacity: 0.6;
  animation: pulse 2s infinite;
}
.map-dot:nth-child(2n) { animation-delay: 0.4s; }
.map-dot:nth-child(3n) { animation-delay: 0.8s; }

/* =========== TURF DETAIL PAGE =========== */
.detail-page { display: none; }
.detail-page.active { display: block; }
.detail-gallery {
  display: grid; grid-template-columns: 2fr 1fr;
  grid-template-rows: 260px;
  gap: 10px; margin-bottom: 32px;
}
.gallery-main {
  background: var(--brown-200);
  border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 80px; grid-row: span 1;
}
.gallery-side { display: flex; flex-direction: column; gap: 10px; }
.gallery-thumb {
  flex: 1;
  background: var(--brown-300);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 36px; cursor: pointer;
  transition: opacity 0.2s;
}
.gallery-thumb:hover { opacity: 0.8; }
.detail-layout {
  display: grid; grid-template-columns: 1fr 340px; gap: 36px;
  padding: 0 40px 60px;
}
.detail-left {}
.detail-title {
  font-family: 'Playfair Display', serif;
  font-size: 32px; font-weight: 800;
  color: var(--brown-900); margin-bottom: 8px;
}
.detail-meta-row {
  display: flex; align-items: center; gap: 16px;
  margin-bottom: 20px; flex-wrap: wrap;
}
.detail-amenities {
  display: flex; gap: 10px; flex-wrap: wrap; margin: 20px 0;
}
.amenity-chip {
  padding: 7px 16px; border-radius: 30px;
  background: var(--brown-50);
  border: 1.5px solid var(--brown-100);
  font-size: 13px; color: var(--brown-600); font-weight: 500;
}
.detail-desc {
  font-size: 14.5px; line-height: 1.8;
  color: var(--brown-600); margin-bottom: 28px;
}
.section-h3 {
  font-family: 'Playfair Display', serif;
  font-size: 20px; font-weight: 700;
  color: var(--brown-900); margin-bottom: 16px;
}
.slots-grid {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;
  margin-bottom: 28px;
}
.slot-btn {
  padding: 10px 8px; border-radius: 10px;
  border: 1.5px solid var(--brown-200);
  background: var(--white);
  font-family: 'DM Sans', sans-serif;
  font-size: 12.5px; font-weight: 600;
  color: var(--brown-600); cursor: pointer;
  transition: all 0.2s; text-align: center;
}
.slot-btn:hover, .slot-btn.sel {
  background: var(--brown-800); color: var(--white); border-color: var(--brown-800);
}
.slot-btn.full { background: var(--brown-50); color: var(--brown-300); border-color: var(--brown-100); cursor: not-allowed; text-decoration: line-through; }
.book-panel {
  background: var(--white);
  border: 1px solid var(--brown-100);
  border-radius: 20px;
  padding: 28px 24px;
  position: sticky; top: 90px;
  box-shadow: var(--shadow-md);
  height: fit-content;
}
.book-price {
  font-family: 'Playfair Display', serif;
  font-size: 30px; font-weight: 800;
  color: var(--brown-900);
}
.book-price span { font-size: 15px; font-weight: 400; color: var(--brown-400); }
.book-rating { display: flex; align-items: center; gap: 6px; margin-bottom: 24px; }
.panel-label {
  font-size: 11.5px; font-weight: 700;
  letter-spacing: 0.8px; text-transform: uppercase;
  color: var(--brown-400); margin-bottom: 8px;
}
.panel-input {
  width: 100%; padding: 11px 14px;
  border: 1.5px solid var(--brown-200);
  border-radius: 10px; margin-bottom: 14px;
  font-family: 'DM Sans', sans-serif; font-size: 14px;
  color: var(--brown-900); background: var(--brown-50);
  outline: none; transition: border-color 0.2s;
}
.panel-input:focus { border-color: var(--brown-500); background: var(--white); }
.panel-summary {
  background: var(--brown-50);
  border-radius: 12px; padding: 16px;
  margin-bottom: 16px;
}
.summary-row {
  display: flex; justify-content: space-between;
  font-size: 13.5px; margin-bottom: 8px;
}
.summary-row:last-child { margin: 0; padding-top: 8px; border-top: 1px solid var(--brown-100); font-weight: 700; }
.summary-row .key { color: var(--brown-500); }
.summary-row .val { color: var(--brown-900); }
.book-now-btn {
  width: 100%; padding: 14px;
  background: var(--brown-800); color: var(--white);
  border: none; border-radius: 12px;
  font-family: 'DM Sans', sans-serif;
  font-size: 15px; font-weight: 700;
  cursor: pointer; transition: all 0.25s;
}
.book-now-btn:hover { background: var(--brown-900); box-shadow: 0 8px 24px rgba(26,14,7,0.3); }
.back-btn {
  display: inline-flex; align-items: center; gap: 8px;
  padding: 8px 16px; border-radius: 30px;
  background: var(--brown-50); border: none;
  color: var(--brown-700); font-size: 14px; font-weight: 500;
  cursor: pointer; margin: 20px 40px;
  font-family: 'DM Sans', sans-serif; transition: all 0.2s;
}
.back-btn:hover { background: var(--brown-100); }

/* =========== SKELETON =========== */
.skeleton {
  background: linear-gradient(90deg, var(--brown-100) 25%, var(--brown-50) 50%, var(--brown-100) 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  border-radius: 8px;
}
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* =========== ANIMATIONS =========== */
@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes pulse {
  0%, 100% { transform: scale(1); opacity: 0.6; }
  50% { transform: scale(1.4); opacity: 1; }
}

/* =========== RESPONSIVE =========== */
@media (max-width: 1024px) {
  .map-panel { display: none; }
  .footer-grid { grid-template-columns: 1fr 1fr; }
  .owner-visual { width: 260px; height: 240px; }
}
@media (max-width: 768px) {
  .navbar { padding: 0 20px; }
  .nav-links { display: none; }
  section { padding: 48px 20px; }
  .hero { padding: 60px 20px 70px; }
  .search-bar { flex-direction: column; gap: 0; }
  .search-divider { display: none; }
  .search-field-wrap { border-bottom: 1px solid var(--brown-100); border-radius: 0; }
  .search-field-wrap:first-child { border-radius: 12px 12px 0 0; }
  .search-btn { border-radius: 0 0 12px 12px; padding: 14px; width: 100%; justify-content: center; }
  .search-stats { gap: 20px; }
  .steps-row { grid-template-columns: 1fr; }
  .testi-grid { grid-template-columns: 1fr; }
  .owner-content { flex-direction: column; }
  .owner-visual { width: 100%; }
  .detail-layout { grid-template-columns: 1fr; padding: 0 20px 60px; }
  .detail-gallery { grid-template-columns: 1fr; grid-template-rows: auto; }
  .gallery-side { display: none; }
  .slots-grid { grid-template-columns: repeat(3, 1fr); }
  .footer-grid { grid-template-columns: 1fr; gap: 30px; }
  .mobile-nav { display: block; }
  body { padding-bottom: 70px; }
  .filter-sidebar { display: none; }
}

/* =========== TOAST =========== */
.toast {
  position: fixed; bottom: 90px; left: 50%; transform: translateX(-50%) translateY(20px);
  background: var(--brown-800); color: var(--white);
  padding: 12px 24px; border-radius: 30px;
  font-size: 14px; font-weight: 500; font-family: 'DM Sans', sans-serif;
  box-shadow: var(--shadow-lg);
  z-index: 3000; opacity: 0; transition: all 0.3s;
  pointer-events: none;
}
.toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }

/* Color coding for different turf types */
.turf-img-bg.football { background: linear-gradient(135deg, #2d5a1b 0%, #3d7a26 100%); }
.turf-img-bg.cricket { background: linear-gradient(135deg, #5a3a1b 0%, #7a5226 100%); }
.turf-img-bg.badminton { background: linear-gradient(135deg, #1b3a5a 0%, #265a7a 100%); }
.turf-img-bg.box { background: linear-gradient(135deg, #3a1b5a 0%, #52267a 100%); }
</style>
</head>
<body>

<!-- ============ NAVBAR ============ -->
<nav class="navbar" id="navbar">
  <div class="nav-logo">
    <span class="logo-dot"></span>
    Playora<span>.</span>
  </div>
  <div class="nav-location" >
    <span class="loc-icon">📍</span>
    <span id="nav-city">Detecting...</span>
    <span>▾</span>
  </div>
  <div class="nav-links">
      <a href="#" onclick="loadPage('home'); return false;" class="nav-link active">Home</a>
      <a href="#" onclick="loadPage('explore'); return false;" class="nav-link">Explore</a>
    </div>

</nav>
