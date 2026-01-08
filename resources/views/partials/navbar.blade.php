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
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 22L2 22"/>
                <path d="M2 11L10.1259 4.49931C11.2216 3.62279 12.7784 3.62279 13.8741 4.49931L22 11"/>
                <path d="M15.5 5.5V3.5C15.5 3.22386 15.7239 3 16 3H18.5C18.7761 3 19 3.22386 19 3.5V8.5"/>
                <path d="M4 22V9.5"/>
                <path d="M20 22V9.5"/>
                <path d="M15 22V17C15 15.5858 15 14.8787 14.5607 14.4393C14.1213 14 13.4142 14 12 14C10.5858 14 9.87868 14 9.43934 14.4393C9 14.8787 9 15.5858 9 17V22"/>
                <path d="M14 9.5C14 10.6046 13.1046 11.5 12 11.5C10.8954 11.5 10 10.6046 10 9.5C10 8.39543 10.8954 7.5 12 7.5C13.1046 7.5 14 8.39543 14 9.5Z"/>
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
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32" fill="currentColor">
                <path d="M9.069 2.672v14.928h-6.397c0 0 0 6.589 0 8.718s1.983 3.010 3.452 3.010c1.469 0 16.26 0 20.006 0 1.616 0 3.199-1.572 3.199-3.199 0-1.175 0-23.457 0-23.457h-20.259zM6.124 28.262c-0.664 0-2.385-0.349-2.385-1.944v-7.652h5.331v7.192c0 0.714-0.933 2.404-2.404 2.404h-0.542zM28.262 26.129c0 1.036-1.096 2.133-2.133 2.133h-17.113c0.718-0.748 1.119-1.731 1.119-2.404v-22.12h18.126v22.391z"/>
                <path d="M12.268 5.871h13.861v1.066h-13.861v-1.066z"/>
                <path d="M12.268 20.265h13.861v1.066h-13.861v-1.066z"/>
                <path d="M12.268 23.997h13.861v1.066h-13.861v-1.066z"/>
                <path d="M26.129 9.602h-13.861v7.997h13.861v-7.997zM25.063 16.533h-11.729v-5.864h11.729v5.864z"/>
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
