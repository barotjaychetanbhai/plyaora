<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="logo">Playora<span>.</span></div>
      <p>India's most loved sports turf booking platform. Find, book, and play at the best turfs near you.</p>
      <div class="socials">
        <a class="social-btn" href="#">𝕏</a>
        <a class="social-btn" href="#">📸</a>
        <a class="social-btn" href="#">💬</a>
        <a class="social-btn" href="#">▶</a>
      </div>
    </div>
    <div class="footer-col">
      <h4>Explore</h4>
      <ul>
        <li><a href="#">Find Turfs</a></li>
        <li><a href="#">All Sports</a></li>
        <li><a href="#">Cities</a></li>
        <li><a href="#">Trending</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Company</h4>
      <ul>
        <li><a href="#">About Us</a></li>
        <li><a href="#">Careers</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Press</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Support</h4>
      <ul>
        <li><a href="#">Help Center</a></li>
        <li><a href="#">Contact Us</a></li>
        <li><a href="#">Privacy Policy</a></li>
        <li><a href="#">Terms</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 Playora Technologies Pvt Ltd. All rights reserved.</p>
    <p>Made with ❤️ in India 🇮🇳</p>
  </div>
</footer>

<!-- BOTTOM MOBILE NAV -->
<div class="mobile-nav">
  <div class="mobile-nav-items">
    <div class="mnav-item active" onclick="loadPage('home'); document.querySelectorAll('.mnav-item').forEach(i=>i.classList.remove('active')); this.classList.add('active');">
      <div class="mnav-icon">🏠</div>Home
    </div>
    <div class="mnav-item" onclick="loadPage('explore'); document.querySelectorAll('.mnav-item').forEach(i=>i.classList.remove('active')); this.classList.add('active');">
      <div class="mnav-icon">🔍</div>Explore
    </div>
    <div class="mnav-item" onclick="alert('Login to view Bookings')">
      <div class="mnav-icon">📋</div>Bookings
    </div>
    <div class="mnav-item" onclick="alert('Login to view Profile')">
      <div class="mnav-icon">👤</div>Profile
    </div>
  </div>
</div>
<!-- TOAST -->
<div class="toast" id="toast"></div>
</body>
</html>