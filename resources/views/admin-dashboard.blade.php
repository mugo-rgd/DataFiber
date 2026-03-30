<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Kenya Power Dark Fibre Leasing</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --kp-yellow: #FFD700;
            --kp-blue: #0066B3;
            --kp-green: #009639;
            --kp-dark: #003f20;
            --kp-light: #f8f9fa;
            --kp-gray: #6c757d;
            --kp-white: #ffffff;
            --border-radius: 8px;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.07);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            background: linear-gradient(to right, var(--kp-blue), var(--kp-green));
            color: white;
            padding: 0.8rem 0;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo img {
            height: 50px;
            width: auto;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
        }

        .logo-main {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .logo-sub {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--kp-yellow);
            color: var(--kp-dark);
        }

        .btn-primary:hover {
            background: #e6c300;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: var(--kp-green);
            color: white;
        }

        .btn-secondary:hover {
            background: #00802c;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-outline {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Dashboard Layout */
        .dashboard-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: var(--shadow-sm);
            padding: 1.5rem 0;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 0 1.5rem 1.5rem;
            border-bottom: 1px solid #eee;
            margin-bottom: 1rem;
        }

        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--kp-blue);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sidebar-menu {
            list-style: none;
            flex: 1;
        }

        .menu-section {
            margin-bottom: 1.5rem;
        }

        .menu-heading {
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--kp-gray);
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
        }

        .menu-item {
            margin-bottom: 0.25rem;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.7rem 1.5rem;
            color: #555;
            text-decoration: none;
            transition: var(--transition);
            border-left: 3px solid transparent;
        }

        .menu-link:hover, .menu-link.active {
            background: rgba(0, 102, 179, 0.05);
            color: var(--kp-blue);
            border-left-color: var(--kp-blue);
        }

        .menu-link i {
            width: 20px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 1rem 1.5rem 0;
            border-top: 1px solid #eee;
            margin-top: auto;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f5f7fa;
            overflow-y: auto;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--kp-dark);
        }

        .page-subtitle {
            color: var(--kp-gray);
            margin-top: 0.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border-top: 4px solid var(--kp-yellow);
        }

        .stat-card:nth-child(2) {
            border-top-color: var(--kp-blue);
        }

        .stat-card:nth-child(3) {
            border-top-color: var(--kp-green);
        }

        .stat-card:nth-child(4) {
            border-top-color: #ff6b6b;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-weight: 600;
            color: #555;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 102, 179, 0.1);
            color: var(--kp-blue);
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
        }

        .stat-change {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 0.85rem;
            color: var(--kp-green);
            margin-top: 0.5rem;
        }

        .dashboard-section {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #eee;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--kp-blue);
        }

        .view-all {
            color: var(--kp-blue);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .activity-list, .lease-list {
            list-style: none;
        }

        .activity-item, .lease-item {
            display: flex;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child, .lease-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--kp-blue);
            flex-shrink: 0;
        }

        .activity-content, .lease-content {
            flex: 1;
        }

        .activity-title, .lease-title {
            font-weight: 500;
            margin-bottom: 0.3rem;
            color: #333;
        }

        .activity-time, .lease-details {
            font-size: 0.85rem;
            color: var(--kp-gray);
        }

        .lease-status {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: rgba(0, 150, 57, 0.1);
            color: var(--kp-green);
        }

        .status-pending {
            background: rgba(255, 215, 0, 0.1);
            color: #b38f00;
        }

        .status-expired {
            background: rgba(239, 71, 111, 0.1);
            color: #ef476f;
        }

        /* Responsive Design */
        @media (max-width: 900px) {
            .dashboard-container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                order: 2;
            }

            .main-content {
                order: 1;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .user-menu {
                width: 100%;
                justify-content: center;
            }

            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                   <img src="/images/logo.png" alt="Kenya Power Logo">
                    <div class="logo-text">
                        <span class="logo-main">KENYA POWER</span>
                        <span class="logo-sub">Dark Fibre Leasing System</span>
                    </div>
                </div>

                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">A</div>
                        <div class="user-details">
                            <span class="user-name">John Mwangi</span>
                            <span class="user-role">Network Administrator</span>
                        </div>
                    </div>
                    <a href="#" class="btn btn-outline"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Dashboard Layout -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title"><i class="fas fa-th-large"></i> Dashboard</h2>
            </div>

            <ul class="sidebar-menu">
                <li class="menu-section">
                    <div class="menu-heading">Administration</div>
                    <ul>
                        <li class="menu-item">
                            <a href="#" class="menu-link active">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-network-wired"></i> Network Management
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-users-cog"></i> User Management
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-file-contract"></i> Lease Applications
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-section">
                    <div class="menu-heading">Monitoring</div>
                    <ul>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-chart-line"></i> Analytics
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-map-marked-alt"></i> Network Map
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-server"></i> Infrastructure
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-section">
                    <div class="menu-heading">Support</div>
                    <ul>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-ticket-alt"></i> Support Tickets
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-file-invoice"></i> Billing
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="menu-link">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="#" class="btn btn-secondary" style="width: 100%;">
                    <i class="fas fa-cog"></i> Admin Panel
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Dashboard Header -->
            <div class="dashboard-header">
                <div>
                    <h1 class="page-title">Network Administration Dashboard</h1>
                    <p class="page-subtitle">Monitor and manage Kenya Power's dark fibre network</p>
                </div>
                <div>
                    <a href="#" class="btn btn-primary"><i class="fas fa-plus"></i> New Lease</a>
                    <a href="#" class="btn btn-outline" style="color: var(--kp-blue); border-color: var(--kp-blue);">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Active Leases</h3>
                        <div class="stat-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                    </div>
                    <div class="stat-value">142</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i> 8 new this month
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Pending Requests</h3>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value">24</div>
                    <div class="stat-change">
                        <i class="fas fa-exclamation-circle"></i> 12 require attention
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Network Uptime</h3>
                        <div class="stat-icon">
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                    <div class="stat-value">99.7%</div>
                    <div class="stat-change">
                        <i class="fas fa-check-circle"></i> All systems operational
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <h3 class="stat-title">Revenue</h3>
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <div class="stat-value">KSh 4.2M</div>
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i> 12% from last month
                    </div>
                </div>
            </div>

            <!-- Recent Activity Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Recent Activity</h2>
                    <a href="#" class="view-all">View All</a>
                </div>

                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="activity-content">
                            <h3 class="activity-title">New lease application received</h3>
                            <p>From TechSolutions Ltd for Westlands area</p>
                            <span class="activity-time">2 hours ago</span>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="activity-content">
                            <h3 class="activity-title">Lease application approved</h3>
                            <p>Application #4298 for Thika Road has been approved</p>
                            <span class="activity-time">Yesterday</span>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div class="activity-content">
                            <h3 class="activity-title">Maintenance completed</h3>
                            <p>Scheduled maintenance for Mombasa route completed successfully</p>
                            <span class="activity-time">2 days ago</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Lease Applications Section -->
            <div class="dashboard-section">
                <div class="section-header">
                    <h2 class="section-title">Recent Lease Applications</h2>
                    <a href="#" class="view-all">View All</a>
                </div>

                <ul class="lease-list">
                    <li class="lease-item">
                        <div class="activity-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="lease-content">
                            <h3 class="lease-title">Uchumi Enterprises</h3>
                            <p>New connection request for Industrial Area</p>
                            <div class="lease-details">
                                <span class="lease-status status-pending">Pending Review</span>
                                <span>Submitted: 2 days ago</span>
                            </div>
                        </div>
                    </li>
                    <li class="lease-item">
                        <div class="activity-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="lease-content">
                            <h3 class="lease-title">Kenya Commercial Bank</h3>
                            <p>Bandwidth upgrade for Thika Road branch</p>
                            <div class="lease-details">
                                <span class="lease-status status-active">In Progress</span>
                                <span>Submitted: 5 days ago</span>
                            </div>
                        </div>
                    </li>
                    <li class="lease-item">
                        <div class="activity-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="lease-content">
                            <h3 class="lease-title">Nairobi Hospital</h3>
                            <p>Redundant connection request</p>
                            <div class="lease-details">
                                <span class="lease-status status-active">Approved</span>
                                <span>Submitted: 1 week ago</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </main>
    </div>

    <script>
        // Menu activation
        document.addEventListener('DOMContentLoaded', function() {
            const menuLinks = document.querySelectorAll('.menu-link');
            menuLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    menuLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>
</body>
</html>
