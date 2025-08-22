<aside class="sidebar expanded" id="sidebar">
  <nav class="sidebar-nav">
    <!-- Header -->
    <header class="sidebar-header">
      <div class="sidebar-toggle-container">
        <button class="sidebar-toggle-btn" id="sidebarToggle" aria-label="Toggle sidebar">
          <svg class="sidebar-toggle-icon" id="toggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>

      <div class="sidebar-profile">
        <div class="sidebar-avatar">
          <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Ccircle cx='20' cy='20' r='20' fill='%234f46e5'/%3E%3Ctext x='20' y='26' text-anchor='middle' fill='white' font-family='Arial' font-size='16' font-weight='bold'%3EC%3C/text%3E%3C/svg%3E" alt="User avatar" class="sidebar-avatar-img" />
        </div>
        <div class="sidebar-profile-info" id="profileInfo">
          <p class="sidebar-username">VLACKSTER</p>
          <p class="sidebar-user-role">Coder</p>
        </div>
      </div>
    </header>
      <?php 
        if(isset($_SESSION['manager_id'])) {
            echo "<p>Manager</p>";
        } else {
            echo "<p>Employee</p>";
        }
      ?>
    <!-- Navigation Lists -->
    <div class="sidebar-content">
      <!-- Primary Navigation -->
      <div class="sidebar-section">
        <h2 class="sidebar-section-title" id="generalTitle">General</h2>
        <ul class="sidebar-list">
          <li>
            <div class="sidebar-link-wrapper">

              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <span class="sidebar-link-text">Inbox</span>
              </a>
              <div class="sidebar-tooltip">Inbox</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <span class="sidebar-link-text">Favourite</span>
              </a>
              <div class="sidebar-tooltip">Favourite</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span class="sidebar-link-text">Sent</span>
              </a>
              <div class="sidebar-tooltip">Sent</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <span class="sidebar-link-text">Draft</span>
              </a>
              <div class="sidebar-tooltip">Draft</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <span class="sidebar-link-text">Archive</span>
              </a>
              <div class="sidebar-tooltip">Archive</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span class="sidebar-link-text">Trash</span>
              </a>
              <div class="sidebar-tooltip">Trash</div>
            </div>
          </li>
        </ul>
      </div>

      <!-- Secondary Navigation -->
      <div class="sidebar-section">
        <h2 class="sidebar-section-title" id="accountTitle">Account</h2>
        <ul class="sidebar-list">
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="sidebar-link-text">Profile</span>
              </a>
              <div class="sidebar-tooltip">Profile</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="sidebar-link-text">Settings</span>
              </a>
              <div class="sidebar-tooltip">Settings</div>
            </div>
          </li>
          <li>
            <div class="sidebar-link-wrapper">
              <a href="#" class="sidebar-link">
                <svg class="sidebar-link-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <span class="sidebar-link-text">Logout</span>
              </a>
              <div class="sidebar-tooltip">Logout</div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</aside>
