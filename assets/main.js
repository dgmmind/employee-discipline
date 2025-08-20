// Sidebar Dashboard JS
// DOM Elements
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const mobileMenuBtn = document.getElementById('mobileMenuBtn');
const sidebarOverlay = document.getElementById('sidebarOverlay');
const mainContent = document.getElementById('mainContent');
const toggleIcon = document.getElementById('toggleIcon');
const profileInfo = document.getElementById('profileInfo');
const generalTitle = document.getElementById('generalTitle');
const accountTitle = document.getElementById('accountTitle');

// State
let sidebarOpen = true;
let isCollapsed = false;

// Check if mobile
function isMobile() {
  return window.innerWidth < 1024;
}

// Update toggle icon
function updateToggleIcon() {
  if (sidebarOpen && !isCollapsed) {
    toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
  } else {
    toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
  }
}

// Update sidebar state
function updateSidebar() {
  const mobile = isMobile();

  if (mobile) {
    // Mobile behavior
    sidebar.classList.remove('collapsed');
    sidebar.classList.toggle('expanded', sidebarOpen);
    sidebarOverlay.classList.toggle('active', sidebarOpen);
    mainContent.classList.remove('shifted', 'collapsed-shifted');

    // Show/hide text elements
    profileInfo.style.display = 'block';
    generalTitle.style.display = 'block';
    accountTitle.style.display = 'block';
    document.querySelectorAll('.sidebar-link-text').forEach(el => {
      el.style.display = 'block';
    });
  } else {
    // Desktop behavior
    sidebar.classList.add('expanded');
    sidebar.classList.toggle('collapsed', isCollapsed);
    sidebarOverlay.classList.remove('active');

    if (isCollapsed) {
      mainContent.classList.remove('shifted');
      mainContent.classList.add('collapsed-shifted');

      // Hide text elements when collapsed
      profileInfo.style.display = 'none';
      generalTitle.style.display = 'none';
      accountTitle.style.display = 'none';
      document.querySelectorAll('.sidebar-link-text').forEach(el => {
        el.style.display = 'none';
      });
    } else {
      mainContent.classList.add('shifted');
      mainContent.classList.remove('collapsed-shifted');

      // Show text elements when expanded
      profileInfo.style.display = 'block';
      generalTitle.style.display = 'block';
      accountTitle.style.display = 'block';
      document.querySelectorAll('.sidebar-link-text').forEach(el => {
        el.style.display = 'block';
      });
    }
  }

  updateToggleIcon();
}

// Toggle sidebar
function toggleSidebar() {
  if (isMobile()) {
    sidebarOpen = !sidebarOpen;
  } else {
    isCollapsed = !isCollapsed;
  }
  updateSidebar();
}

// Event listeners
sidebarToggle.addEventListener('click', toggleSidebar);
mobileMenuBtn.addEventListener('click', toggleSidebar);
sidebarOverlay.addEventListener('click', () => {
  if (isMobile()) {
    sidebarOpen = false;
    updateSidebar();
  }
});

// Handle window resize
window.addEventListener('resize', () => {
  updateSidebar();
});

// Initialize
updateSidebar();
