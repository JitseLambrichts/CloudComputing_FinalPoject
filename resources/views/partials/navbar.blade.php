{{-- Sidebar Navbar --}}
<aside class="sidebar collapsed" id="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-title">Dashboard</span>
        <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <a href="{{ url('/') }}" class="nav-item {{ request()->is('/') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            <span class="nav-text">Home</span>
        </a>
        
        <a href="{{ route('matches') }}" class="nav-item {{ request()->is('matches') ? 'active' : '' }}">
            <svg fill="currentColor" width="20" height="20" viewBox="0 0 420.746 420.746" xmlns="http://www.w3.org/2000/svg">
                <g>
                    <path d="M397.194,31.776h-31.402h-19.624h-86.356h-27.475h-31.406h-23.55v25.295v310.521v25.303h23.55h31.406h27.475h86.356h19.624h31.402h23.552v-25.303V57.071V31.776H397.194z M342.24,55.328v39.25h-86.352v-39.25H342.24z M200.931,55.328h31.406v35.325v27.477h27.475h86.356h19.624V90.653V55.328h31.402v141.306h-48.405c-5.41-24.658-27.399-43.176-53.648-43.176c-26.26,0-48.245,18.518-53.65,43.176h-40.56V55.328z M266.067,196.634c4.677-11.483,15.917-19.626,29.073-19.626c13.141,0,24.386,8.143,29.055,19.626H266.067z M324.195,220.184c-4.669,11.493-15.914,19.628-29.055,19.628c-13.156,0-24.396-8.135-29.073-19.628H324.195z M255.889,369.344v-39.253h86.352v39.253H255.889z M397.194,369.344h-31.402v-3.927v-39.253v-19.625h-19.624h-86.356h-27.475v19.625v39.253v3.927h-31.406v-149.16h40.56c5.405,24.657,27.391,43.18,53.65,43.18c26.249,0,48.238-18.522,53.648-43.18h48.405V369.344z"/>
                    <circle cx="23.55" cy="51.402" r="23.551"/>
                    <rect x="59.625" y="39.627" width="62.802" height="23.552"/>
                    <circle cx="23.55" cy="114.205" r="23.552"/>
                    <rect x="59.625" y="102.429" width="62.802" height="23.552"/>
                    <circle cx="23.55" cy="177.007" r="23.549"/>
                    <rect x="59.625" y="165.233" width="62.802" height="23.55"/>
                    <circle cx="23.55" cy="239.812" r="23.552"/>
                    <rect x="59.625" y="228.034" width="62.802" height="23.552"/>
                    <circle cx="23.55" cy="302.616" r="23.552"/>
                    <rect x="59.625" y="290.838" width="62.802" height="23.552"/>
                    <circle cx="23.55" cy="365.417" r="23.552"/>
                    <rect x="59.625" y="353.643" width="62.802" height="23.552"/>
                </g>
            </svg>
            <span class="nav-text">Matches</span>
        </a>
        
        <a href="{{ route('news') }}" class="nav-item {{ request()->is('news') ? 'active' : '' }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"></path>
                <path d="M18 14h-8"></path>
                <path d="M15 18h-5"></path>
                <path d="M10 6h8v4h-8V6Z"></path>
            </svg>
            <span class="nav-text">Nieuws</span>
        </a>
    </nav>
</aside>

{{-- Mobile overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

{{-- Mobile toggle button (visible when sidebar is closed) --}}
<button class="mobile-menu-btn" id="mobileMenuBtn" onclick="openSidebar()">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }
    
    function openSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('mobile-open');
        overlay.classList.add('show');
    }
    
    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
    }
</script>
