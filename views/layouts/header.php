<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../helpers/security_helper.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(getNamaToko()) ?> - <?= htmlspecialchars($pageTitle ?? 'Dashboard', ENT_QUOTES) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;       /* Premium Indigo */
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --border-color: #e2e8f0;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --sidebar-width: 270px;
        }
        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            overflow-x: hidden;
        }
        /* Layout Structure */
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        /* Sidebar Styling */
        #sidebar {
            min-width: var(--sidebar-width);
            max-width: var(--sidebar-width);
            background: var(--bg-surface);
            transition: all 0.3s;
            border-right: 1px solid var(--border-color);
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: fixed;
            z-index: 1000;
        }
        #sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        .sidebar-header {
            padding: 1.5rem 1.8rem;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            min-height: 80px;
        }
        .sidebar-header h3 {
            margin: 0;
            font-weight: 800;
            color: var(--primary);
            font-size: 1.8rem;
            letter-spacing: 0px;
        }
        ul.components {
            padding: 1rem 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        ul.components li a {
            padding: 0.8rem 1.5rem;
            display: flex;
            align-items: center;
            font-size: 1.05rem;
            color: var(--secondary);
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            margin: 0.25rem 1.25rem;
            border-radius: 12px;
        }
        ul.components li a i {
            margin-right: 14px;
            font-size: 1.25rem;
        }
        ul.components li a:hover,
        ul.components li.active > a {
            color: var(--primary);
            background: rgba(79, 70, 229, 0.08); /* Transparent Indigo Hover */
        }
        ul.components li.active > a {
            font-weight: 600;
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }
        /* Navigating Categories (Subheadings) */
        .sidebar-heading {
            padding: 10px 1.8rem 5px;
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        /* Page Content Area */
        #content {
            width: calc(100% - var(--sidebar-width));
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }
        #content.active {
            width: 100%;
            margin-left: 0;
        }
        /* Top Navbar */
        .top-navbar {
            background: var(--bg-surface);
            padding: 0 2rem;
            min-height: 80px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .btn-toggle {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 10px;
            transition: 0.2s;
        }
        .btn-toggle:hover {
            background: #f1f5f9;
            color: var(--primary);
        }
        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }
        .user-avatar {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .user-info .name {
            font-weight: 600;
            font-size: 0.95rem;
            margin: 0;
            color: var(--text-main);
        }
        .user-info .role {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
        }
        /* Content Container */
        .main-content {
            padding: 2.5rem;
            flex-grow: 1;
        }
        /* Premium Dashboard Widgets / Cards */
        .stat-card {
            background: var(--bg-surface);
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            box-shadow: 0 5px 20px rgba(0,0,0,0.02);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-title {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
            margin-top: 1.2rem;
            margin-bottom: 0.2rem;
        }
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0;
        }
        @media (max-width: 991.98px) {
            #sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            #sidebar.active {
                margin-left: 0;
            }
            #content {
                width: 100%;
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
