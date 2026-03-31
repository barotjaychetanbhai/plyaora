<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

// Fetch Trending Turfs
$trending_query = $conn->query("
    SELECT t.*, s.name as sport_name, c.name as city_name
    FROM turfs t
    JOIN sports s ON t.sport_id = s.id
    JOIN cities c ON t.city_id = c.id
    WHERE t.status = 'active'
    ORDER BY t.rating DESC LIMIT 4
");
$trending_turfs = $trending_query ? $trending_query->fetch_all(MYSQLI_ASSOC) : [];

// Fetch Nearby Turfs (Ahmedabad Default)
$nearby_query = $conn->query("
    SELECT t.*, s.name as sport_name, c.name as city_name
    FROM turfs t
    JOIN sports s ON t.sport_id = s.id
    JOIN cities c ON t.city_id = c.id
    WHERE t.status = 'active' AND c.name = 'Ahmedabad'
    ORDER BY t.created_at DESC LIMIT 4
");
$nearby_turfs = $nearby_query ? $nearby_query->fetch_all(MYSQLI_ASSOC) : [];

// Fetch Sports
$sports_query = $conn->query("SELECT * FROM sports WHERE status = 'active' ORDER BY name ASC");
$sports_list = $sports_query ? $sports_query->fetch_all(MYSQLI_ASSOC) : [];
?>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-pattern"></div>
  <div class="hero-badge">🏆 India's #1 Sports Turf Booking Platform</div>
  <h1>Find & Book <em>Sports Turfs</em><br>Near You</h1>
  <p>Discover premium turfs in your city · Book in under 60 seconds</p>

  <div class="search-bar">
    <div class="search-field-wrap">
      <label>📍 Location</label>
      <input type="text" id="search-location" placeholder="Surat, Gujarat" style="border:none;outline:none;background:transparent;font-family:'DM Sans',sans-serif;font-size:14px;color:var(--text-dark);font-weight:500;width:100%;">
    </div>
    <div class="search-divider"></div>
    <div class="search-field-wrap">
      <label>⚽ Sport</label>
      <select>
        <option>Any Sport</option>
        <option>Football</option>
        <option>Cricket</option>
        <option>Badminton</option>
        <option>Box Cricket</option>
      </select>
    </div>
    <div class="search-divider"></div>
    <div class="search-field-wrap">
      <label>📅 Date</label>
      <input type="date" id="search-date">
    </div>
    <div class="search-divider"></div>
    <div class="search-field-wrap">
      <label>⏰ Time</label>
      <input type="time">
    </div>
    <button class="search-btn" onclick="handleSearch()">🔍 Search Turfs</button>
  </div>

  <div class="search-stats">
    <div class="stat-item"><div class="stat-num">2,400+</div><div class="stat-lbl">Turfs Listed</div></div>
    <div class="stat-item"><div class="stat-num">48</div><div class="stat-lbl">Cities</div></div>
    <div class="stat-item"><div class="stat-num">1.2L+</div><div class="stat-lbl">Bookings Done</div></div>
    <div class="stat-item"><div class="stat-num">4.8⭐</div><div class="stat-lbl">Avg Rating</div></div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="categories-section">
  <div class="section-header">
    <div class="section-tag">Browse by Sport</div>
    <div class="section-title">What do you <em>want to play?</em></div>
  </div>
  <div class="categories-grid">
    <div class="cat-card active" onclick="loadPage('search_result', 'sport_name=Football')">
      <div class="cat-icon-wrap">⚽</div>
      <div class="cat-name">Football</div>
      <div class="cat-count">640 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Cricket')">
      <div class="cat-icon-wrap">🏏</div>
      <div class="cat-name">Cricket</div>
      <div class="cat-count">420 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Badminton')">
      <div class="cat-icon-wrap">🏸</div>
      <div class="cat-name">Badminton</div>
      <div class="cat-count">380 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Box+Cricket')">
      <div class="cat-icon-wrap">🏟</div>
      <div class="cat-name">Box Cricket</div>
      <div class="cat-count">280 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Volleyball')">
      <div class="cat-icon-wrap">🏐</div>
      <div class="cat-name">Volleyball</div>
      <div class="cat-count">190 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Basketball')">
      <div class="cat-icon-wrap">🏀</div>
      <div class="cat-name">Basketball</div>
      <div class="cat-count">160 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Tennis')">
      <div class="cat-icon-wrap">🎾</div>
      <div class="cat-name">Tennis</div>
      <div class="cat-count">110 turfs</div>
    </div>
    <div class="cat-card" onclick="loadPage('search_result', 'sport_name=Pickleball')">
      <div class="cat-icon-wrap">🏓</div>
      <div class="cat-name">Pickleball</div>
      <div class="cat-count">85 turfs</div>
    </div>
  </div>
</section>

<!-- TRENDING TURFS -->
<section id="trending" style="background: var(--cream);">
  <div class="section-row">
    <div class="section-header" style="margin-bottom:0">
      <div class="section-tag">🔥 Hot Right Now</div>
      <div class="section-title">Trending <em>Turfs</em></div>
      <div class="section-sub">Most booked this week in your city</div>
    </div>
    <a href="#" class="see-all" onclick="loadPage('search_result')">View all →</a>
  </div>
  <div style="height:30px"></div>
  <div class="turfs-grid" id="trending-grid">
  <?php foreach($trending_turfs as $turf): ?>
    <div class="turf-card" onclick="loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">
      <div class="turf-img">
        <div class="turf-img-bg football"></div>
        <div class="turf-emoji">⚽</div>
        <div class="turf-badge">Top Rated</div>
        <button class="fav-btn">🤍</button>
      </div>
      <div class="turf-info">
        <div class="turf-header-flex">
          <div class="turf-title"><?= htmlspecialchars($turf['name']) ?></div>
          <div class="turf-rating">⭐ <?= htmlspecialchars($turf['rating']) ?></div>
        </div>
        <div class="turf-meta">📍 <?= htmlspecialchars($turf['city_name']) ?> &nbsp;•&nbsp; 🏏 <?= htmlspecialchars($turf['sport_name']) ?></div>
        <div class="turf-price-flex">
          <div class="turf-price">₹<?= htmlspecialchars($turf['price_per_hour']) ?><span>/hr</span></div>
          <button class="book-btn-sm" onclick="event.stopPropagation(); loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">Book</button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if(empty($trending_turfs)): ?>
    <div style="grid-column: 1/-1; text-align: center; color: var(--brown-400);">No trending turfs found.</div>
  <?php endif; ?>
</div>
</section>

<!-- NEARBY TURFS -->
<section class="nearby-section">
  <div class="section-row">
    <div class="section-header" style="margin-bottom:0">
      <div class="section-tag">📍 Near You</div>
      <div class="section-title">Turfs <em>Nearby</em></div>
      <div class="section-sub" id="nearby-sub">Based on your current location</div>
    </div>
    <a href="#" class="see-all" onclick="loadPage('search_result')">View all →</a>
  </div>
  <div style="height:30px"></div>
  <div class="turfs-grid" id="nearby-grid">
  <?php foreach($nearby_turfs as $turf): ?>
    <div class="turf-card" onclick="loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">
      <div class="turf-img">
        <div class="turf-img-bg cricket"></div>
        <div class="turf-emoji">🏏</div>
        <div class="turf-badge">Nearby</div>
        <button class="fav-btn">🤍</button>
      </div>
      <div class="turf-info">
        <div class="turf-header-flex">
          <div class="turf-title"><?= htmlspecialchars($turf['name']) ?></div>
          <div class="turf-rating">⭐ <?= htmlspecialchars($turf['rating']) ?></div>
        </div>
        <div class="turf-meta">📍 <?= htmlspecialchars($turf['city_name']) ?> &nbsp;•&nbsp; 🏏 <?= htmlspecialchars($turf['sport_name']) ?></div>
        <div class="turf-price-flex">
          <div class="turf-price">₹<?= htmlspecialchars($turf['price_per_hour']) ?><span>/hr</span></div>
          <button class="book-btn-sm" onclick="event.stopPropagation(); loadPage('turf_detail', 'id=<?= $turf['id'] ?>')">Book</button>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if(empty($nearby_turfs)): ?>
    <div style="grid-column: 1/-1; text-align: center; color: var(--brown-400);">No nearby turfs found in Ahmedabad.</div>
  <?php endif; ?>
</div>
</section>

<!-- HOW IT WORKS -->
<section class="how-section">
  <div class="section-header" style="text-align:center">
    <div class="section-tag">How It Works</div>
    <div class="section-title">Book in <em>3 Simple Steps</em></div>
    <div class="section-sub" style="color:rgba(255,255,255,0.45)">No calls. No deposits. Instant confirmation.</div>
  </div>
  <div class="steps-row">
    <div class="step-card">
      <div class="step-num">01</div>
      <div class="step-icon">🔍</div>
      <div class="step-title">Search Your Turf</div>
      <div class="step-desc">Browse 2,400+ verified turfs by sport, location, price, and time slot. Filter by amenities and ratings.</div>
    </div>
    <div class="step-card">
      <div class="step-num">02</div>
      <div class="step-icon">📅</div>
      <div class="step-title">Select Your Slot</div>
      <div class="step-desc">Pick a date and time that works for you. See real-time availability and book your preferred slot instantly.</div>
    </div>
    <div class="step-card">
      <div class="step-num">03</div>
      <div class="step-icon">🏃</div>
      <div class="step-title">Show Up & Play</div>
      <div class="step-desc">Get instant confirmation on WhatsApp & email. Show your QR code at the turf and start playing!</div>
    </div>
  </div>
</section>

<!-- OWNER CTA -->
<section class="owner-section">
  <div class="owner-bg"></div>
  <div class="owner-content">
    <div class="owner-text">
      <div class="owner-tag">FOR TURF OWNERS</div>
      <div class="owner-title">Own a Turf?<br><em>Earn More</em> with Playora</div>
      <div class="owner-desc">Join 1,200+ turf owners already growing their business on Playora. Zero upfront cost. Keep 94% of every booking.</div>
      <div class="owner-perks">
        <div class="perk">
          <div class="perk-icon">💰</div>
          <div class="perk-text">Zero Commission<br>First 3 Months</div>
        </div>
        <div class="perk">
          <div class="perk-icon">📊</div>
          <div class="perk-text">Real-time<br>Dashboard</div>
        </div>
        <div class="perk">
          <div class="perk-icon">📣</div>
          <div class="perk-text">Free Marketing<br>Support</div>
        </div>
      </div>
      <button class="btn-gold" onclick="openModal('partner')">List Your Turf Free →</button>
    </div>
    <div class="owner-visual">
      🏟
      <div class="owner-stat-chip chip1">
        <div class="chip-num">₹2.4L</div>
        <div class="chip-lbl">Avg Monthly Revenue</div>
      </div>
      <div class="owner-stat-chip chip2">
        <div class="chip-num">+47%</div>
        <div class="chip-lbl">Booking Growth</div>
      </div>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testi-section">
  <div class="section-header" style="text-align:center">
    <div class="section-tag">What People Say</div>
    <div class="section-title">Loved by <em>Players & Owners</em></div>
  </div>
  <div class="testi-grid">
    <div class="testi-card">
      <div class="testi-stars">★★★★★</div>
      <div class="testi-quote">"</div>
      <div class="testi-text">Playora completely changed how we book turfs. Found an amazing 5-a-side ground near Vesu in minutes. The slot booking is super smooth!</div>
      <div class="testi-author">
        <div class="testi-avatar">👨</div>
        <div>
          <div class="testi-name">Aryan Shah</div>
          <div class="testi-role">Football Player • Surat</div>
        </div>
      </div>
    </div>
    <div class="testi-card">
      <div class="testi-stars">★★★★★</div>
      <div class="testi-quote">"</div>
      <div class="testi-text">Our turf bookings went from 12 to 28 per week after listing on Playora. The dashboard is clean and payouts are always on time.</div>
      <div class="testi-author">
        <div class="testi-avatar">👨‍💼</div>
        <div>
          <div class="testi-name">Rajan Mehta</div>
          <div class="testi-role">Turf Owner • Ahmedabad</div>
        </div>
      </div>
    </div>
    <div class="testi-card">
      <div class="testi-stars">★★★★★</div>
      <div class="testi-quote">"</div>
      <div class="testi-text">I love the nearby feature — it detected my location perfectly and showed turfs within 3 km. Booked a badminton court for Sunday morning!</div>
      <div class="testi-author">
        <div class="testi-avatar">👩</div>
        <div>
          <div class="testi-name">Priya Patel</div>
          <div class="testi-role">Badminton Player • Surat</div>
        </div>
      </div>
    </div>
  </div>
</section>



</div><!-- end landing-page -->

<!-- ============ EXPLORE PAGE ============ -->
<div id="explore-page" class="explore-page">
  <div class="explore-header">
    <div class="explore-search-row">
      <input type="text" class="explore-input" placeholder="🔍 Search turf name, area..." value="">
      <select class="explore-input" style="max-width:140px">
        <option>All Sports</option>
        <option>Football ⚽</option>
        <option>Cricket 🏏</option>
        <option>Badminton 🏸</option>
        <option>Box Cricket</option>
      </select>
      <input type="date" class="explore-input" style="max-width:160px" id="explore-date">
      <input type="time" class="explore-input" style="max-width:130px">
      <button class="filter-btn">🎛 Filters</button>
    </div>
  </div>
  <div class="explore-body">
    <div class="filter-sidebar">
      <div class="filter-group">
        <div class="filter-group-title">Sport</div>
        <div class="filter-option"><input type="checkbox" id="f1" checked><label for="f1">Football ⚽</label><span>640</span></div>
        <div class="filter-option"><input type="checkbox" id="f2"><label for="f2">Cricket 🏏</label><span>420</span></div>
        <div class="filter-option"><input type="checkbox" id="f3"><label for="f3">Badminton 🏸</label><span>380</span></div>
        <div class="filter-option"><input type="checkbox" id="f4"><label for="f4">Box Cricket</label><span>280</span></div>
      </div>
      <div class="filter-group">
        <div class="filter-group-title">Price Range (₹/hr)</div>
        <div class="price-range-wrap">
          <input type="range" min="200" max="3000" value="1500" oninput="this.nextElementSibling.children[1].textContent='₹'+this.value">
          <div class="price-labels"><span>₹200</span><span>₹1500</span><span>₹3000</span></div>
        </div>
      </div>
      <div class="filter-group">
        <div class="filter-group-title">Rating</div>
        <div class="filter-option"><input type="radio" name="rating" id="r1"><label for="r1">4.5+ ⭐⭐⭐⭐⭐</label></div>
        <div class="filter-option"><input type="radio" name="rating" id="r2" checked><label for="r2">4.0+ ⭐⭐⭐⭐</label></div>
        <div class="filter-option"><input type="radio" name="rating" id="r3"><label for="r3">3.5+</label></div>
      </div>
      <div class="filter-group">
        <div class="filter-group-title">Distance</div>
        <div class="filter-option"><input type="radio" name="dist" id="d1" checked><label for="d1">Under 2 km</label></div>
        <div class="filter-option"><input type="radio" name="dist" id="d2"><label for="d2">Under 5 km</label></div>
        <div class="filter-option"><input type="radio" name="dist" id="d3"><label for="d3">Under 10 km</label></div>
      </div>
      <div class="filter-group">
        <div class="filter-group-title">Amenities</div>
        <div class="filter-option"><input type="checkbox" id="a1" checked><label for="a1">Parking 🚗</label></div>
        <div class="filter-option"><input type="checkbox" id="a2"><label for="a2">Washroom 🚿</label></div>
        <div class="filter-option"><input type="checkbox" id="a3"><label for="a3">Floodlights 💡</label></div>
        <div class="filter-option"><input type="checkbox" id="a4"><label for="a4">Cafeteria ☕</label></div>
        <div class="filter-option"><input type="checkbox" id="a5"><label for="a5">Equipment 🎽</label></div>
      </div>
    </div>
    <div class="explore-results">
      <div class="results-meta">
        <span class="results-count">Showing 24 turfs near <span id="explore-city">you</span></span>
        <select class="sort-select">
          <option>Sort: Relevance</option>
          <option>Sort: Rating ↓</option>
          <option>Sort: Price ↑</option>
          <option>Sort: Distance ↑</option>
        </select>
      </div>
      <div class="explore-grid" id="explore-grid"></div>
    </div>
    <div class="map-panel">
      <div class="map-placeholder">
        <div class="map-icon">🗺</div>
        <div style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:white;margin-bottom:8px">Map View</div>
        <p>Turf locations shown on map.<br>Hover cards to highlight.</p>
        <div class="map-dots">
          <div class="map-dot"></div><div class="map-dot"></div><div class="map-dot"></div><div class="map-dot"></div>
          <div class="map-dot"></div><div class="map-dot"></div><div class="map-dot"></div><div class="map-dot"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ============ TURF DETAIL PAGE ============ -->
<div id="detail-page" class="detail-page">
  <button class="back-btn" onclick="loadPage('search_result')">← Back to Results</button>
  <div style="padding: 0 40px;" id="gallery-wrap">
    <div class="detail-gallery">
      <div class="gallery-main" id="gallery-main">🏟</div>
      <div class="gallery-side">
        <div class="gallery-thumb">⚽</div>
        <div class="gallery-thumb">🌿</div>
      </div>
    </div>
  </div>
  <div class="detail-layout">
    <div class="detail-left">
      <div class="detail-title" id="detail-name">Arena Sports Club</div>
      <div class="detail-meta-row">
        <div class="turf-rating"><span class="star">★</span> <span id="detail-rating">4.7</span> <span style="color:var(--brown-400);font-weight:400">(128 reviews)</span></div>
        <div class="turf-meta-dot"></div>
        <div class="turf-dist" id="detail-dist">📍 2.1 km away</div>
        <div class="turf-meta-dot"></div>
        <div class="turf-sport" id="detail-sport">⚽ Football</div>
      </div>
      <div class="detail-amenities" id="detail-amenities">
        <div class="amenity-chip">🚗 Parking</div>
        <div class="amenity-chip">💡 Floodlights</div>
        <div class="amenity-chip">🚿 Washroom</div>
        <div class="amenity-chip">☕ Cafeteria</div>
        <div class="amenity-chip">🎽 Equipment</div>
        <div class="amenity-chip">🏆 Certified</div>
      </div>
      <div class="detail-desc">
        Arena Sports Club offers a premium FIFA-quality artificial turf spread over 7,500 sq ft. The facility features high-intensity LED floodlights for night games, a covered spectator stand, modern changing rooms, and a fully equipped sports cafe. Perfect for 5-a-side and 7-a-side football matches.
      </div>
      <div class="section-h3">Select Time Slot</div>
      <div style="margin-bottom:12px">
        <input type="date" class="panel-input" id="detail-date" style="margin:0">
      </div>
      <div class="slots-grid" id="detail-slots"></div>
      <div class="section-h3">Reviews</div>
      <div id="detail-reviews"></div>
    </div>
    <div>
      <div class="book-panel">
        <div class="book-price" id="panel-price">₹800 <span>/ hour</span></div>
        <div class="book-rating">
          <span style="color:var(--gold)">★</span>
          <span style="font-weight:600;color:var(--brown-800)" id="panel-rating">4.7</span>
          <span style="font-size:13px;color:var(--brown-400)">(128 reviews)</span>
        </div>
        <div class="panel-label">Date</div>
        <input type="date" class="panel-input" id="panel-date">
        <div class="panel-label">Selected Slot</div>
        <input type="text" class="panel-input" id="panel-slot" placeholder="Choose a time slot above" readonly>
        <div class="panel-summary">
          <div class="summary-row"><span class="key">Turf Rate</span><span class="val" id="sum-rate">₹800 × 1 hr</span></div>
          <div class="summary-row"><span class="key">Platform Fee</span><span class="val">₹20</span></div>
          <div class="summary-row"><span class="key">Total</span><span class="val" id="sum-total">₹820</span></div>
        </div>
        <button class="book-now-btn" onclick="openModal('login')">Book Now →</button>
        <p style="font-size:12px;color:var(--brown-400);text-align:center;margin-top:10px">Free cancellation up to 2 hours before</p>
      </div>
    </div>
  </div>
</div>

<!-- ============ MODAL ============ -->
<div class="modal-overlay" id="modal-overlay" onclick="handleOverlayClick(event)">
  <div class="modal-box" id="modal-box">
    <button class="modal-close" onclick="closeModal()">✕</button>
    <div id="modal-content"></div>
  </div>
</div>

<!-- MOBILE NAV -->
<div class="mobile-nav">
  <div class="mobile-nav-items">
    <div class="mnav-item active" onclick="loadPage('landing');setActiveNav(this)">
      <div class="mnav-icon">🏠</div>Home
    </div>
    <div class="mnav-item" onclick="loadPage('explore');setActiveNav(this)">
      <div class="mnav-icon">🔍</div>Explore
    </div>
    <div class="mnav-item" onclick="openModal('login')">
      <div class="mnav-icon">📋</div>Bookings
    </div>
    <div class="mnav-item" onclick="openModal('login')">
      <div class="mnav-icon">👤</div>Profile
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>


</body>
</html>
<script>
function handleSearch() {
    const loc = document.getElementById('search-location') ? document.getElementById('search-location').value : '';
    const sportSelect = document.querySelector('.search-bar select');
    const sport = sportSelect && sportSelect.selectedIndex > 0 ? sportSelect.options[sportSelect.selectedIndex].text : '';
    const date = document.getElementById('search-date') ? document.getElementById('search-date').value : '';

    let params = `location=${encodeURIComponent(loc)}`;
    if (sport !== 'Any Sport') params += `&sport_name=${encodeURIComponent(sport)}`;
    if (date) params += `&date=${encodeURIComponent(date)}`;

    loadPage('search_result', params);
}
</script>
