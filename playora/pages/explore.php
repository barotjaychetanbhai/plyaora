<?php
require_once '../config/db.php';

// Fetch Sports for filter
$sports_query = $conn->query("SELECT * FROM sports WHERE status = 'active' ORDER BY name ASC");
$sports_list = $sports_query ? $sports_query->fetch_all(MYSQLI_ASSOC) : [];

// Fetch Cities for filter
$cities_query = $conn->query("SELECT * FROM cities ORDER BY name ASC");
$cities_list = $cities_query ? $cities_query->fetch_all(MYSQLI_ASSOC) : [];
?>

<div id="explore-page" style="padding-top: 100px; padding-bottom: 80px; max-width: 1200px; margin: 0 auto; width: 90%;">
    <div class="section-title">Explore <em>Turfs</em></div>

    <!-- Filters Container -->
    <div style="background: var(--white); border-radius: var(--radius); padding: 20px; margin-bottom: 30px; box-shadow: var(--shadow-sm); display: flex; gap: 20px; flex-wrap: wrap;">
        <select id="filter-city" onchange="fetchExploreTurfs()" style="padding: 10px; border: 1px solid var(--brown-200); border-radius: var(--radius-sm); outline: none;">
            <option value="">All Locations</option>
            <?php foreach($cities_list as $city): ?>
                <option value="<?= htmlspecialchars($city['name']) ?>"><?= htmlspecialchars($city['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select id="filter-sport" onchange="fetchExploreTurfs()" style="padding: 10px; border: 1px solid var(--brown-200); border-radius: var(--radius-sm); outline: none;">
            <option value="">All Sports</option>
            <?php foreach($sports_list as $sport): ?>
                <option value="<?= htmlspecialchars($sport['id']) ?>"><?= htmlspecialchars($sport['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <select id="filter-price" onchange="fetchExploreTurfs()" style="padding: 10px; border: 1px solid var(--brown-200); border-radius: var(--radius-sm); outline: none;">
            <option value="">Any Price</option>
            <option value="0-500">Under ₹500/hr</option>
            <option value="500-1000">₹500 - ₹1000/hr</option>
            <option value="1000-plus">Above ₹1000/hr</option>
        </select>
    </div>

    <!-- Turfs Grid Container -->
    <div class="turfs-grid" id="explore-results">
        <div style="grid-column: 1/-1; text-align: center; color: var(--brown-500); padding: 40px;">
            Loading turfs...
        </div>
    </div>
</div>

<script>
function fetchExploreTurfs() {
    const city = document.getElementById('filter-city').value;
    const sport = document.getElementById('filter-sport').value;
    const price = document.getElementById('filter-price').value;
    const container = document.getElementById('explore-results');

    container.innerHTML = `
        <div style="grid-column: 1/-1; text-align: center; color: var(--brown-500); padding: 40px;">
            Loading turfs...
        </div>
    `;

    // Ensure parameters are parsed or empty
    const params = new URLSearchParams(window.location.search);
    const urlSport = params.get('sport') && !sport ? params.get('sport') : '';

    let url = `ajax/get_turfs.php?city=${encodeURIComponent(city)}&sport=${encodeURIComponent(sport || urlSport)}&price=${encodeURIComponent(price)}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; color: var(--brown-400); padding: 40px;">No turfs found matching your criteria.</div>`;
                return;
            }

            let html = '';
            data.forEach(turf => {
                html += `
                    <div class="turf-card" onclick="loadPage('turf_detail', 'id=${turf.id}')">
                        <div class="turf-img">
                            <div class="turf-img-bg football"></div>
                            <div class="turf-emoji">⚽</div>
                        </div>
                        <div class="turf-info">
                            <div class="turf-header-flex">
                                <div class="turf-title">${turf.name}</div>
                                <div class="turf-rating">⭐ ${turf.rating || 'N/A'}</div>
                            </div>
                            <div class="turf-meta">📍 ${turf.city_name || 'N/A'} &nbsp;•&nbsp; 🏏 ${turf.sport_name || 'N/A'}</div>
                            <div class="turf-price-flex">
                                <div class="turf-price">₹${turf.price_per_hour}<span>/hr</span></div>
                                <button class="book-btn-sm" onclick="event.stopPropagation(); loadPage('turf_detail', 'id=${turf.id}')">Book</button>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error fetching turfs:', error);
            container.innerHTML = `<div style="grid-column: 1/-1; text-align: center; color: red; padding: 40px;">Error loading turfs.</div>`;
        });
}

// Check if there are params passed from home page (e.g. clicking a sport chip)
const urlParams = new URLSearchParams(window.location.search);
const sportParam = urlParams.get('sport');
if(sportParam) {
    // If we have a name, we might need to select it, but we use ID in explore filter normally.
    // For simplicity, we just trigger fetch and pass param.
}

fetchExploreTurfs();
</script>