@section('css')
<style>
.sidebar-dark {
    background-color: #032127 !important;
    color: white !important;
    width: 280px !important;
    height: 100vh !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 1040 !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    flex-direction: column !important;
}
.sidebar-dark .sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}
.sidebar-dark .sidebar-title {
    color: white;
    font-size: 18px;
    font-weight: 600;
    margin: 0;
}
.sidebar-dark .close-btn {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
}
.sidebar-dark .sidebar-menu {
    padding: 0;
    margin: 0;
    flex: 1;
    display: block !important;
    overflow-y: auto;
}
.sidebar-dark .nav-link {
    color: white !important;
    padding: 15px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex !important;
    align-items: center;
    text-decoration: none;
    transition: background-color 0.3s ease;
    width: 100%;
    box-sizing: border-box;
    margin: 0;
    min-height: 50px;
}
.sidebar-dark .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
.sidebar-dark .nav-link.active {
    background-color: rgba(127, 215, 225, 0.2);
}
.sidebar-dark .nav-icon {
    width: 20px;
    height: 20px;
    margin-right: 15px;
    color: white;
    flex-shrink: 0;
    display: inline-block !important;
}
.sidebar-dark .nav-text {
    font-size: 14px;
    font-weight: 500;
    flex-grow: 1;
    display: inline-block !important;
}
.sidebar-dark .logout-btn {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    display: flex !important;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background-color 0.3s ease;
    width: calc(100% - 40px);
}
.sidebar-dark .logout-btn:hover {
    background-color: #c82333;
}
.sidebar-dark .menu-item {
    display: block !important;
    width: 100%;
}
.sidebar-dark .logout-section {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    display: block !important;
}
/* Force visibility */
.sidebar-dark * {
    visibility: visible !important;
    display: block !important;
}
.sidebar-dark .nav-link {
    visibility: visible !important;
    display: flex !important;
}
.sidebar-dark .nav-icon {
    visibility: visible !important;
    display: inline-block !important;
}
.sidebar-dark .nav-text {
    visibility: visible !important;
    display: inline-block !important;
}
.sidebar-dark .logout-btn {
    visibility: visible !important;
    display: flex !important;
}
</style>
@endsection
<aside class="sidenav sidebar-dark" id="sidenav-main">
    <div class="sidebar-header">
        <h2 class="sidebar-title">Driver Portal</h2>
        <button class="close-btn" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-item">
            <a class="nav-link" href="{{ url('/driver-profile') }}">
                <i class="fas fa-user-cog nav-icon"></i>
                <span class="nav-text">Profile Settings</span>
            </a>
        </div>
    </div>

    <div class="logout-section">
        <button class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </button>
    </div>

    <form id="logout-form" action="{{ route('unified.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</aside>
