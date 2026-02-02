<header class="header"> 
    <div class="logo-container">
        {{-- Logo Image --}}
        <div class="logo-icon">
            <img src="{{ asset('images/resultIG.png') }}" alt="ResultIgniter Logo">
        </div>

        {{-- Logo Text --}}
        <div class="logo-text tangerine-regular">ResultIgniter</div>

        {{-- Sidebar Toggle --}}
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="user-info">
        {{-- SETTINGS DROPDOWN --}}
        <div class="account-dropdown settings-dropdown">
            <button class="account-btn settings-btn" aria-label="Toggle settings menu">
                <i class="fas fa-cog"></i>
                <span class="settings-label">Settings</span>
            </button>

            <div class="dropdown-content settings-dropdown-content">
                <div class="dropdown-title">
                    <i class="fas fa-cog dropdown-title-icon"></i>
                    Settings
                </div>

                <a href="#" class="dropdown-link">
                    <i class="fas fa-sliders-h dropdown-link-icon"></i>
                    <span>General Settings</span>
                </a>
                <a href="#" class="dropdown-link">
                    <i class="fas fa-graduation-cap dropdown-link-icon"></i>
                    <span>Academic Settings</span>
                </a>
                <a href="{{ route('grades.index') }}" class="dropdown-link">
                    <i class="fas fa-chart-line dropdown-link-icon"></i>
                    <span>Grading Configuration</span>
                </a>
                <a href="#" class="dropdown-link">
                    <i class="fas fa-user-shield dropdown-link-icon"></i>
                    <span>User Roles & Permissions</span>
                </a>
                <a href="#" class="dropdown-link">
                    <i class="fas fa-tools dropdown-link-icon"></i>
                    <span>System Preferences</span>
                </a>
                <a href="#" class="dropdown-link">
                    <i class="fas fa-database dropdown-link-icon"></i>
                    <span>Backup & Restore</span>
                </a>
            </div>
        </div>

        {{-- USER INFO --}}
        <div class="user-avatar">
            <i class="fas fa-user"></i>
        </div>

        <span>{{ Auth::user()->name ?? 'User' }}</span>

        {{-- ACCOUNT DROPDOWN --}}
        <div class="account-dropdown">
            <button class="account-btn" aria-label="Toggle account menu">
                <i class="fas fa-angle-down"></i>
            </button>

            <div class="dropdown-content">
                <div class="dropdown-title">Account</div>

                <a href="{{ route('profile') }}">Profile</a>
                <hr>

                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<style>
/* Logo container */
.logo-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Logo image */
.logo-icon img {
    height: 50px;
    width: auto;
    object-fit: contain;
}

/* User info */
.user-info {
    position: relative;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Settings button styling */
.settings-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 1px solid #dee2e6;
    border-radius: 8px;
    color: #495057;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.settings-btn:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-color: #adb5bd;
    color: #212529;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
}

.settings-btn:active {
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.settings-btn i {
    font-size: 16px;
    color: #6c757d;
    transition: all 0.2s ease;
}

.settings-btn:hover i {
    color: #495057;
    transform: rotate(15deg);
}

.settings-label {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Account button (angle only) */
.account-btn:not(.settings-btn) {
    background: transparent;
    color: #333;
    border: none;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 16px;
    transition: transform 0.3s ease, color 0.2s ease;
}

.account-btn:not(.settings-btn) i {
    transition: transform 0.3s ease;
}

/* Rotate icon when active */
.account-dropdown.active .account-btn:not(.settings-btn) i {
    transform: rotate(180deg);
}

/* Settings dropdown specific styling */
.settings-dropdown.active .settings-btn i {
    transform: rotate(30deg);
}

/* Dropdown container */
.account-dropdown {
    position: relative;
}

/* Dropdown content */
.dropdown-content {
    display: none;
    position: absolute;
    top: 110%;
    right: 0;
    width: 220px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    z-index: 100;
    overflow: hidden;
    animation: fadeIn 0.2s ease-in-out;
    border: 1px solid #e9ecef;
}

/* Settings dropdown content specific */
.settings-dropdown-content {
    width: 240px;
}

/* Dropdown title */
.dropdown-title {
    font-weight: 600;
    text-align: left;
    padding: 14px 16px;
    font-size: 14px;
    color: #495057;
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    border-bottom: 1px solid #e9ecef;
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.dropdown-title-icon {
    color: #6c757d;
    font-size: 14px;
}

/* Dropdown links & buttons */
.dropdown-content a,
.dropdown-content .logout-form button {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 12px 16px;
    text-align: left;
    font-size: 13.5px;
    color: #495057;
    background: none;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    gap: 10px;
}

.dropdown-link {
    display: flex !important;
    align-items: center;
    text-decoration: none;
    color: #495057 !important;
}

.dropdown-link-icon {
    width: 16px;
    text-align: center;
    font-size: 13px;
    color: #6c757d;
    transition: color 0.2s ease;
}

.dropdown-content a:hover,
.dropdown-content .logout-form button:hover {
    background: #f8f9fa;
    color: #212529;
    
}

.dropdown-content a:hover .dropdown-link-icon {
    color: #495057;
}

/* Separator */
.dropdown-content hr {
    margin: 0;
    border: 0;
    border-top: 1px solid #e9ecef;
}

/* Click-only dropdown */
.account-dropdown.active .dropdown-content {
    display: block;
}

/* Fade animation */
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translateY(-8px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

/* Tangerine font */
@font-face {
    font-family: 'Tangerine';
    src: url('/fonts/Tangerine-Regular.ttf') format('truetype');
    font-weight: 400;
}

@font-face {
    font-family: 'Tangerine';
    src: url('/fonts/Tangerine-Bold.ttf') format('truetype');
    font-weight: 700;
}

.tangerine-regular,
.tangerine-bold {
    font-family: "Tangerine", cursive;
    font-weight: 700;
    font-size: 32px;
}

/* User avatar */
.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #4dabf7, #339af0);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    margin-left: 10px;
}
</style>

<script>
/* Toggle dropdown on click */
document.querySelectorAll('.account-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.stopPropagation();
        const dropdown = this.parentElement;
        
        // Close all other dropdowns
        document.querySelectorAll('.account-dropdown').forEach(dd => {
            if (dd !== dropdown) {
                dd.classList.remove('active');
            }
        });
        
        // Toggle current dropdown
        dropdown.classList.toggle('active');
    });
});

/* Close dropdown when clicking outside */
document.addEventListener('click', function () {
    document.querySelectorAll('.account-dropdown').forEach(dd => {
        dd.classList.remove('active');
    });
});

/* Close dropdown when pressing Escape */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.account-dropdown').forEach(dd => {
            dd.classList.remove('active');
        });
    }
});
</script>