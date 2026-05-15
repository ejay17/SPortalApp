<?php
session_start();
require_once '../assets/backend/functions/auth_middleware.php';
requireRole('1');

require '../assets/backend/connection/conn.php';

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = "SELECT * FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get user's sports
$sports_query = "SELECT sport_name FROM sports WHERE user_id = '$user_id'";
$sports_result = mysqli_query($conn, $sports_query);
$user_sports = [];
while ($s = mysqli_fetch_assoc($sports_result)) {
    $user_sports[] = $s['sport_name'];
}

// Get available tryouts for player's sports
$sport_list = "'" . implode("','", $user_sports) . "'";
$tryouts_query = "SELECT t.*, s.sport_name, 
    (SELECT COUNT(*) FROM player_activity pa WHERE pa.tryout_id = t.tryout_id) as participant_count,
    (SELECT COUNT(*) FROM player_activity pa WHERE pa.tryout_id = t.tryout_id AND pa.user_id = '$user_id') as joined
    FROM tryouts t
    JOIN sports s ON t.sport_id = s.sport_id
    WHERE s.sport_name IN ($sport_list)
    ORDER BY t.date ASC";
$tryouts_result = mysqli_query($conn, $tryouts_query);
$available_tryouts = [];
while ($t = mysqli_fetch_assoc($tryouts_result)) {
    $available_tryouts[] = $t;
}

// Get player's joined tryouts
$my_tryouts_query = "SELECT t.*, s.sport_name, pa.player_act_id
    FROM tryouts t
    JOIN player_activity pa ON t.tryout_id = pa.tryout_id
    JOIN sports s ON t.sport_id = s.sport_id
    WHERE pa.user_id = '$user_id'
    ORDER BY t.date ASC";
$my_tryouts_result = mysqli_query($conn, $my_tryouts_query);
$my_tryouts = [];
while ($mt = mysqli_fetch_assoc($my_tryouts_result)) {
    $my_tryouts[] = $mt;
}

// Get announcements for player's sports
$announcements_query = "SELECT a.*, u.given_name, u.last_name FROM announcements a 
    JOIN users u ON a.user_id = u.user_id 
    WHERE a.sport_name IN ($sport_list)
    ORDER BY a.created_at DESC LIMIT 10";
$announcements_result = mysqli_query($conn, $announcements_query);
$announcements = [];
while ($ann = mysqli_fetch_assoc($announcements_result)) {
    $announcements[] = $ann;
}

// Get notifications
$notif_query = "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY created_at DESC LIMIT 20";
$notif_result = mysqli_query($conn, $notif_query);
$notifications = [];
$unread_count = 0;
while ($n = mysqli_fetch_assoc($notif_result)) {
    $notifications[] = $n;
    if ($n['is_read'] == 0) $unread_count++;
}

// Get unread count
$unread_query = "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = '$user_id' AND is_read = 0";
$unread_result = mysqli_query($conn, $unread_query);
$unread_row = mysqli_fetch_assoc($unread_result);
$unread_count = $unread_row['cnt'];

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPortal - Player Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        @font-face {
            font-family: 'Nunito';
            src: url('../fonts/Nunito-Regular.ttf') format('truetype');
        }

        body {
            font-family: 'Nunito';
        }

        .sidebar-link {
            transition: all 0.2s ease;
        }

        .sidebar-link:hover,
        .sidebar-link.active {
            background-color: #ca8a04;
            color: white;
        }

        .card-hover {
            transition: all 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .badge-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .mobile-menu {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Modal styles */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .modal-overlay.show {
            opacity: 1;
            pointer-events: all;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: #fbbf24;
            border-radius: 2px;
        }
    </style>
</head>

<body class="bg-orange-50/30 min-h-screen">

    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden" onclick="toggleMobileMenu()"></div>

    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar" class="mobile-menu fixed left-0 top-0 h-full w-64 bg-yellow-600 z-40 flex flex-col lg:hidden">
        <div class="p-5 flex items-center gap-3 border-b border-yellow-500">
            <img src="../assets/images/S lang - SPortal Logo.svg" alt="" width="32">
            <span class="text-xl font-extrabold text-white">SPortal</span>
        </div>
        <nav class="flex-1 p-3 flex flex-col gap-1">
            <a href="?page=home" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'home' ? 'active' : ''; ?>">
                <i class="fas fa-home w-5"></i> Home
            </a>
            <a href="?page=tryouts" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'tryouts' ? 'active' : ''; ?>">
                <i class="fas fa-trophy w-5"></i> Tryouts
            </a>
            <a href="?page=my_tryouts" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'my_tryouts' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check w-5"></i> My Tryouts
            </a>
            <a href="?page=announcements" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'announcements' ? 'active' : ''; ?>">
                <i class="fas fa-bullhorn w-5"></i> Announcements
            </a>
            <a href="?page=notifications" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'notifications' ? 'active' : ''; ?>">
                <i class="fas fa-bell w-5"></i> Notifications
                <?php if ($unread_count > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="?page=profile" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user w-5"></i> Profile
            </a>
        </nav>
        <div class="p-3 border-t border-yellow-500">
            <a href="../assets/backend/functions/logout.php" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 hover:bg-red-600">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>

    <div class="flex min-h-screen">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex w-64 bg-yellow-600 flex-col fixed h-full">
            <div class="p-5 flex items-center gap-3 border-b border-yellow-500">
                <img src="../assets/images/S lang - SPortal Logo.svg" alt="" width="32">
                <span class="text-xl font-extrabold text-white">SPortal</span>
            </div>
            <div class="p-4 border-b border-yellow-500">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        <?php echo strtoupper(substr($user['given_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm"><?php echo htmlspecialchars($user['given_name'] . ' ' . $user['last_name']); ?></p>
                        <p class="text-yellow-200 text-xs">Player</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-3 flex flex-col gap-1">
                <a href="?page=home" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'home' ? 'active' : ''; ?>">
                    <i class="fas fa-home w-5"></i> Home
                </a>
                <a href="?page=tryouts" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'tryouts' ? 'active' : ''; ?>">
                    <i class="fas fa-trophy w-5"></i> Tryouts
                </a>
                <a href="?page=my_tryouts" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'my_tryouts' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check w-5"></i> My Tryouts
                </a>
                <a href="?page=announcements" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'announcements' ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn w-5"></i> Announcements
                </a>
                <a href="?page=notifications" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'notifications' ? 'active' : ''; ?>">
                    <i class="fas fa-bell w-5"></i> Notifications
                    <?php if ($unread_count > 0): ?>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full badge-pulse"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <a href="?page=profile" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 <?php echo $page == 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user w-5"></i> Profile
                </a>
            </nav>
            <div class="p-3 border-t border-yellow-500">
                <a href="../assets/backend/functions/logout.php" class="sidebar-link px-4 py-3 rounded-lg text-yellow-100 font-semibold flex items-center gap-3 hover:bg-red-600">
                    <i class="fas fa-sign-out-alt w-5"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-yellow-100 sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleMobileMenu()" class="lg:hidden text-yellow-700 text-xl">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-lg lg:text-xl font-bold text-yellow-700">
                            <?php
                            $page_titles = [
                                'home' => 'Dashboard',
                                'tryouts' => 'Available Tryouts',
                                'my_tryouts' => 'My Tryouts',
                                'announcements' => 'Announcements',
                                'notifications' => 'Notifications',
                                'profile' => 'My Profile'
                            ];
                            echo $page_titles[$page] ?? 'Dashboard';
                            ?>
                        </h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="?page=notifications" class="relative text-yellow-700 hover:text-yellow-600">
                            <i class="fas fa-bell text-xl"></i>
                            <?php if ($unread_count > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center badge-pulse"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            <?php echo strtoupper(substr($user['given_name'], 0, 1)); ?>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="mx-4 lg:mx-8 mt-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['error']) . '</div>';
                unset($_SESSION['error']);
            }
            if (isset($_SESSION['success'])) {
                echo '<div class="mx-4 lg:mx-8 mt-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg text-sm flex items-center gap-2"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['success']) . '</div>';
                unset($_SESSION['success']);
            }
            ?>

            <!-- Page Content -->
            <div class="p-4 lg:p-8">

                <!-- HOME PAGE -->
                <?php if ($page == 'home'): ?>
                <div class="space-y-6">
                    <!-- Welcome Card -->
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-2xl p-6 lg:p-8 text-white">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div>
                                <p class="text-yellow-100 text-sm">Welcome back,</p>
                                <h2 class="text-3xl font-extrabold"><?php echo htmlspecialchars($user['given_name'] . ' ' . $user['last_name']); ?>!</h2>
                                <p class="text-yellow-100 mt-1"><?php echo htmlspecialchars($user['institute_campus']); ?> • <?php echo htmlspecialchars($user['year_level'] ?? ''); ?></p>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <?php foreach ($user_sports as $sport): ?>
                                    <span class="bg-white/20 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold"><?php echo htmlspecialchars($sport); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl p-5 border border-yellow-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo count($available_tryouts); ?></p>
                                    <p class="text-sm text-gray-500">Available Tryouts</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-yellow-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo count($my_tryouts); ?></p>
                                    <p class="text-sm text-gray-500">Joined Tryouts</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-yellow-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-futbol text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo count($user_sports); ?></p>
                                    <p class="text-sm text-gray-500">My Sports</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-yellow-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-bell text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo $unread_count; ?></p>
                                    <p class="text-sm text-gray-500">Unread Notifications</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Tryouts & Announcements -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Upcoming Tryouts -->
                        <div class="bg-white rounded-xl border border-yellow-100 overflow-hidden">
                            <div class="p-5 border-b border-yellow-50 flex items-center justify-between">
                                <h3 class="font-bold text-yellow-700"><i class="fas fa-trophy mr-2"></i>Upcoming Tryouts</h3>
                                <a href="?page=tryouts" class="text-sm text-yellow-600 hover:underline">View all</a>
                            </div>
                            <div class="p-5 space-y-3 max-h-80 overflow-y-auto scrollbar-thin">
                                <?php if (empty($available_tryouts)): ?>
                                    <p class="text-gray-400 text-center py-8">No upcoming tryouts for your sports.</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($available_tryouts, 0, 5) as $tryout): ?>
                                    <div class="flex items-center justify-between p-3 bg-yellow-50/50 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($tryout['name']); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($tryout['date'])); ?> at <?php echo date('h:i A', strtotime($tryout['time'])); ?> • <?php echo htmlspecialchars($tryout['location']); ?></p>
                                        </div>
                                        <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full"><?php echo htmlspecialchars($tryout['sport_name']); ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Recent Announcements -->
                        <div class="bg-white rounded-xl border border-yellow-100 overflow-hidden">
                            <div class="p-5 border-b border-yellow-50 flex items-center justify-between">
                                <h3 class="font-bold text-yellow-700"><i class="fas fa-bullhorn mr-2"></i>Recent Announcements</h3>
                                <a href="?page=announcements" class="text-sm text-yellow-600 hover:underline">View all</a>
                            </div>
                            <div class="p-5 space-y-3 max-h-80 overflow-y-auto scrollbar-thin">
                                <?php if (empty($announcements)): ?>
                                    <p class="text-gray-400 text-center py-8">No announcements yet.</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($announcements, 0, 5) as $ann): ?>
                                    <div class="p-3 bg-yellow-50/50 rounded-lg">
                                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($ann['title']); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">By Coach <?php echo htmlspecialchars($ann['given_name'] . ' ' . $ann['last_name']); ?> • <?php echo date('M d, Y', strtotime($ann['created_at'])); ?></p>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TRYOUTS PAGE -->
                <?php elseif ($page == 'tryouts'): ?>
                <div class="space-y-6">
                    <!-- Filter -->
                    <div class="bg-white rounded-xl p-4 border border-yellow-100 flex flex-wrap gap-2">
                        <button onclick="filterTryouts('all')" class="filter-btn px-4 py-2 rounded-full text-sm font-semibold bg-yellow-600 text-white">All</button>
                        <?php foreach ($user_sports as $sport): ?>
                            <button onclick="filterTryouts('<?php echo htmlspecialchars($sport); ?>')" class="filter-btn px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-700 hover:bg-yellow-200"><?php echo htmlspecialchars($sport); ?></button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Tryout Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4" id="tryout-cards">
                        <?php if (empty($available_tryouts)): ?>
                            <div class="col-span-full text-center py-16">
                                <i class="fas fa-trophy text-6xl text-yellow-200 mb-4"></i>
                                <p class="text-gray-400 text-lg">No tryouts available for your sports yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($available_tryouts as $tryout): ?>
                            <div class="bg-white rounded-xl border border-yellow-100 card-hover overflow-hidden tryout-card" data-sport="<?php echo htmlspecialchars($tryout['sport_name']); ?>">
                                <div class="p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full"><?php echo htmlspecialchars($tryout['sport_name']); ?></span>
                                        <?php if ($tryout['joined'] > 0): ?>
                                            <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full"><i class="fas fa-check mr-1"></i>Joined</span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($tryout['name']); ?></h3>
                                    <p class="text-sm text-gray-500 mb-4"><?php echo htmlspecialchars($tryout['description']); ?></p>
                                    
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p><i class="fas fa-calendar-alt w-5 text-yellow-500"></i> <?php echo date('F d, Y', strtotime($tryout['date'])); ?></p>
                                        <p><i class="fas fa-clock w-5 text-yellow-500"></i> <?php echo date('h:i A', strtotime($tryout['time'])); ?></p>
                                        <p><i class="fas fa-map-marker-alt w-5 text-yellow-500"></i> <?php echo htmlspecialchars($tryout['location']); ?></p>
                                        <p><i class="fas fa-users w-5 text-yellow-500"></i> <?php echo $tryout['participant_count']; ?> participant(s)</p>
                                    </div>

                                    <?php if ($tryout['notes']): ?>
                                        <div class="mt-3 p-2 bg-yellow-50 rounded-lg text-xs text-yellow-700">
                                            <i class="fas fa-sticky-note mr-1"></i> <?php echo htmlspecialchars($tryout['notes']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($tryout['joined'] == 0): ?>
                                <div class="px-5 pb-5">
                                    <form action="../assets/backend/functions/tryout_join.php" method="POST">
                                        <input type="hidden" name="tryout_id" value="<?php echo $tryout['tryout_id']; ?>">
                                        <button type="submit" name="join_tryout" class="w-full py-3 bg-yellow-600 text-white font-bold rounded-lg hover:bg-yellow-700 transition flex items-center justify-center gap-2">
                                            <i class="fas fa-plus-circle"></i> Join Tryout
                                        </button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- MY TRYOUTS PAGE -->
                <?php elseif ($page == 'my_tryouts'): ?>
                <div class="space-y-6">
                    <?php if (empty($my_tryouts)): ?>
                        <div class="text-center py-16">
                            <i class="fas fa-calendar-check text-6xl text-yellow-200 mb-4"></i>
                            <p class="text-gray-400 text-lg">You haven't joined any tryouts yet.</p>
                            <a href="?page=tryouts" class="mt-4 inline-block px-6 py-3 bg-yellow-600 text-white font-bold rounded-full hover:bg-yellow-700 transition">Browse Tryouts</a>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                            <?php foreach ($my_tryouts as $tryout): ?>
                            <div class="bg-white rounded-xl border border-yellow-100 card-hover overflow-hidden">
                                <div class="bg-green-500 text-white text-center py-2 text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1"></i> Registered
                                </div>
                                <div class="p-5">
                                    <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full"><?php echo htmlspecialchars($tryout['sport_name']); ?></span>
                                    <h3 class="text-lg font-bold text-gray-800 mt-3 mb-1"><?php echo htmlspecialchars($tryout['name']); ?></h3>
                                    <p class="text-sm text-gray-500 mb-4"><?php echo htmlspecialchars($tryout['description']); ?></p>
                                    
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p><i class="fas fa-calendar-alt w-5 text-yellow-500"></i> <?php echo date('F d, Y', strtotime($tryout['date'])); ?></p>
                                        <p><i class="fas fa-clock w-5 text-yellow-500"></i> <?php echo date('h:i A', strtotime($tryout['time'])); ?></p>
                                        <p><i class="fas fa-map-marker-alt w-5 text-yellow-500"></i> <?php echo htmlspecialchars($tryout['location']); ?></p>
                                    </div>
                                </div>
                                <div class="px-5 pb-5">
                                    <form action="../assets/backend/functions/tryout_leave.php" method="POST" onsubmit="return confirm('Are you sure you want to leave this tryout?');">
                                        <input type="hidden" name="tryout_id" value="<?php echo $tryout['tryout_id']; ?>">
                                        <button type="submit" name="leave_tryout" class="w-full py-3 bg-red-50 text-red-600 font-bold rounded-lg hover:bg-red-100 transition flex items-center justify-center gap-2 border border-red-200">
                                            <i class="fas fa-times-circle"></i> Leave Tryout
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ANNOUNCEMENTS PAGE -->
                <?php elseif ($page == 'announcements'): ?>
                <div class="space-y-4">
                    <?php if (empty($announcements)): ?>
                        <div class="text-center py-16">
                            <i class="fas fa-bullhorn text-6xl text-yellow-200 mb-4"></i>
                            <p class="text-gray-400 text-lg">No announcements yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($announcements as $ann): ?>
                        <div class="bg-white rounded-xl border border-yellow-100 p-6 card-hover">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($ann['title']); ?></h3>
                                    <p class="text-sm text-gray-500">By Coach <?php echo htmlspecialchars($ann['given_name'] . ' ' . $ann['last_name']); ?> • <?php echo date('F d, Y h:i A', strtotime($ann['created_at'])); ?></p>
                                </div>
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full"><?php echo htmlspecialchars($ann['sport_name']); ?></span>
                            </div>
                            <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($ann['content'])); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- NOTIFICATIONS PAGE -->
                <?php elseif ($page == 'notifications'): ?>
                <div class="space-y-4">
                    <?php if ($unread_count > 0): ?>
                        <form action="../assets/backend/functions/notifications.php" method="POST" class="mb-2">
                            <input type="hidden" name="mark_read" value="1">
                            <button type="submit" class="text-sm text-yellow-600 hover:underline font-semibold"><i class="fas fa-check-double mr-1"></i> Mark all as read</button>
                        </form>
                    <?php endif; ?>

                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-16">
                            <i class="fas fa-bell-slash text-6xl text-yellow-200 mb-4"></i>
                            <p class="text-gray-400 text-lg">No notifications yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                        <div class="bg-white rounded-xl border <?php echo $notif['is_read'] == 0 ? 'border-yellow-300 bg-yellow-50/50' : 'border-yellow-100'; ?> p-5 card-hover">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?php echo $notif['is_read'] == 0 ? 'bg-yellow-500 text-white' : 'bg-gray-200 text-gray-500'; ?>">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <h4 class="font-bold text-gray-800"><?php echo htmlspecialchars($notif['title']); ?></h4>
                                        <span class="text-xs text-gray-400 ml-2 flex-shrink-0"><?php echo date('M d, h:i A', strtotime($notif['created_at'])); ?></span>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($notif['message']); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- PROFILE PAGE -->
                <?php elseif ($page == 'profile'): ?>
                <div class="space-y-6 max-w-3xl">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl border border-yellow-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-6 flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-extrabold">
                                <?php echo strtoupper(substr($user['given_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                            </div>
                            <div class="text-white">
                                <h2 class="text-xl font-extrabold"><?php echo htmlspecialchars($user['given_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'][0] . '. ' : '') . $user['last_name'] . ($user['suffix'] ? ' ' . $user['suffix'] : '')); ?></h2>
                                <p class="text-yellow-100 text-sm">@<?php echo htmlspecialchars($user['username']); ?> • Player</p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Form -->
                    <div class="bg-white rounded-xl border border-yellow-100 p-6">
                        <h3 class="text-lg font-bold text-yellow-700 mb-4"><i class="fas fa-edit mr-2"></i>Edit Profile</h3>
                        <form action="../assets/backend/functions/edit_profile.php" method="POST">
                            <input type="hidden" name="edit_profile" value="1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Given Name</label>
                                    <input type="text" name="given_name" value="<?php echo htmlspecialchars($user['given_name']); ?>" required
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Middle Name</label>
                                    <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Last Name</label>
                                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Suffix</label>
                                    <input type="text" name="suffix" value="<?php echo htmlspecialchars($user['suffix'] ?? ''); ?>" placeholder="ex. Jr."
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Student ID</label>
                                    <input type="text" name="student_id" value="<?php echo htmlspecialchars($user['student_id'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Year Level</label>
                                    <select name="year_level" class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1 bg-white">
                                        <option value="1st Year" <?php echo ($user['year_level'] ?? '') == '1st Year' ? 'selected' : ''; ?>>1st Year</option>
                                        <option value="2nd Year" <?php echo ($user['year_level'] ?? '') == '2nd Year' ? 'selected' : ''; ?>>2nd Year</option>
                                        <option value="3rd Year" <?php echo ($user['year_level'] ?? '') == '3rd Year' ? 'selected' : ''; ?>>3rd Year</option>
                                        <option value="4th Year" <?php echo ($user['year_level'] ?? '') == '4th Year' ? 'selected' : ''; ?>>4th Year</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Contact Number</label>
                                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Social Media Link</label>
                                    <input type="text" name="social_media" value="<?php echo htmlspecialchars($user['social_media'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-yellow-700">Institute/Campus</label>
                                    <select name="institute_campus" class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1 bg-white">
                                        <option value="Balagtas Technical Vocational College" <?php echo $user['institute_campus'] == 'Balagtas Technical Vocational College' ? 'selected' : ''; ?>>Balagtas Technical Vocational College</option>
                                        <option value="College Of Agriculture" <?php echo $user['institute_campus'] == 'College Of Agriculture' ? 'selected' : ''; ?>>College Of Agriculture</option>
                                        <option value="College Of Education" <?php echo $user['institute_campus'] == 'College Of Education' ? 'selected' : ''; ?>>College Of Education</option>
                                        <option value="College Of Engineering And Technology" <?php echo $user['institute_campus'] == 'College Of Engineering And Technology' ? 'selected' : ''; ?>>College Of Engineering And Technology</option>
                                        <option value="College Of Management" <?php echo $user['institute_campus'] == 'College Of Management' ? 'selected' : ''; ?>>College Of Management</option>
                                        <option value="Fortunato F. Halili National Agricultural School" <?php echo $user['institute_campus'] == 'Fortunato F. Halili National Agricultural School' ? 'selected' : ''; ?>>Fortunato F. Halili National Agricultural School</option>
                                        <option value="Institute Of Arts And Sciences" <?php echo $user['institute_campus'] == 'Institute Of Arts And Sciences' ? 'selected' : ''; ?>>Institute Of Arts And Sciences</option>
                                        <option value="Insitute Of Computer Studies" <?php echo $user['institute_campus'] == 'Insitute Of Computer Studies' ? 'selected' : ''; ?>>Insitute Of Computer Studies</option>
                                        <option value="Institute Of Veterinary Medicine" <?php echo $user['institute_campus'] == 'Institute Of Veterinary Medicine' ? 'selected' : ''; ?>>Institute Of Veterinary Medicine</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" class="px-8 py-3 bg-yellow-600 text-white font-bold rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white rounded-xl border border-yellow-100 p-6">
                        <h3 class="text-lg font-bold text-yellow-700 mb-4"><i class="fas fa-lock mr-2"></i>Change Password</h3>
                        <form action="../assets/backend/functions/edit_profile.php" method="POST">
                            <input type="hidden" name="change_password" value="1">
                            <div class="space-y-4 max-w-md">
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Current Password</label>
                                    <input type="password" name="current_password" required
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">New Password</label>
                                    <input type="password" name="new_password" required
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-yellow-700">Confirm New Password</label>
                                    <input type="password" name="confirm_password" required
                                        class="w-full p-3 rounded-lg border border-yellow-200 outline-yellow-600 mt-1">
                                </div>
                                <button type="submit" class="px-8 py-3 bg-yellow-600 text-white font-bold rounded-lg hover:bg-yellow-700 transition">
                                    <i class="fas fa-key mr-2"></i>Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <?php endif; ?>

            </div>
        </main>
    </div>

    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }

        // Tryout filter
        function filterTryouts(sport) {
            const cards = document.querySelectorAll('.tryout-card');
            const btns = document.querySelectorAll('.filter-btn');

            btns.forEach(btn => {
                btn.classList.remove('bg-yellow-600', 'text-white');
                btn.classList.add('bg-yellow-100', 'text-yellow-700');
            });
            event.target.classList.remove('bg-yellow-100', 'text-yellow-700');
            event.target.classList.add('bg-yellow-600', 'text-white');

            cards.forEach(card => {
                if (sport === 'all' || card.dataset.sport === sport) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

</body>

</html>
