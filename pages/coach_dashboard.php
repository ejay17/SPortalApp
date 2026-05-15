<?php
session_start();
require_once '../assets/backend/functions/auth_middleware.php';
requireRole('2');

require '../assets/backend/connection/conn.php';

$user_id = $_SESSION['user_id'];

// Get user data
$user_query = "SELECT * FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

// Get coach's sports
$sports_query = "SELECT sport_id, sport_name FROM sports WHERE user_id = '$user_id'";
$sports_result = mysqli_query($conn, $sports_query);
$coach_sports = [];
while ($s = mysqli_fetch_assoc($sports_result)) {
    $coach_sports[] = $s;
}

// Get coach's tryouts with participant count
$sport_ids = array_column($coach_sports, 'sport_id');
$sport_id_list = !empty($sport_ids) ? implode(',', $sport_ids) : '0';

$tryouts_query = "SELECT t.*, s.sport_name,
    (SELECT COUNT(*) FROM player_activity pa WHERE pa.tryout_id = t.tryout_id) as participant_count
    FROM tryouts t
    JOIN sports s ON t.sport_id = s.sport_id
    WHERE t.sport_id IN ($sport_id_list)
    ORDER BY t.date DESC";
$tryouts_result = mysqli_query($conn, $tryouts_query);
$tryouts = [];
while ($t = mysqli_fetch_assoc($tryouts_result)) {
    $tryouts[] = $t;
}

// Get total players across coach's sports
$total_players_query = "SELECT COUNT(DISTINCT u.user_id) as total 
    FROM users u 
    JOIN sports s ON u.user_id = s.user_id 
    WHERE s.sport_name IN (SELECT sport_name FROM sports WHERE user_id = '$user_id') AND u.role_id = '1'";
$total_players_result = mysqli_query($conn, $total_players_query);
$total_players_row = mysqli_fetch_assoc($total_players_result);
$total_players = $total_players_row['total'];

// Get announcements
$announcements_query = "SELECT * FROM announcements WHERE user_id = '$user_id' ORDER BY created_at DESC";
$announcements_result = mysqli_query($conn, $announcements_query);
$announcements = [];
while ($a = mysqli_fetch_assoc($announcements_result)) {
    $announcements[] = $a;
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

$unread_query = "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = '$user_id' AND is_read = 0";
$unread_result = mysqli_query($conn, $unread_query);
$unread_row = mysqli_fetch_assoc($unread_result);
$unread_count = $unread_row['cnt'];

// Get tryout for editing if specified
$edit_tryout = null;
if (isset($_GET['edit_tryout'])) {
    $edit_id = intval($_GET['edit_tryout']);
    $edit_query = "SELECT * FROM tryouts WHERE tryout_id = '$edit_id'";
    $edit_result = mysqli_query($conn, $edit_query);
    if (mysqli_num_rows($edit_result) > 0) {
        $edit_tryout = mysqli_fetch_assoc($edit_result);
    }
}

// Get tryout participants if specified
$view_participants = null;
$participants = [];
if (isset($_GET['view_participants'])) {
    $view_tryout_id = intval($_GET['view_participants']);
    $vp_query = "SELECT t.name as tryout_name FROM tryouts t WHERE t.tryout_id = '$view_tryout_id'";
    $vp_result = mysqli_query($conn, $vp_query);
    $view_participants = mysqli_fetch_assoc($vp_result);

    $players_query = "SELECT u.user_id, u.given_name, u.middle_name, u.last_name, u.suffix, u.student_id, u.year_level, u.institute_campus, u.sex, u.contact_number
        FROM users u
        JOIN player_activity pa ON u.user_id = pa.user_id
        WHERE pa.tryout_id = '$view_tryout_id' AND u.role_id = '1'
        ORDER BY u.last_name, u.given_name";
    $players_result = mysqli_query($conn, $players_query);
    while ($p = mysqli_fetch_assoc($players_result)) {
        $participants[] = $p;
    }
}

$page = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPortal - Coach Dashboard</title>
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
            background-color: #92400e;
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
            background: #92400e;
            border-radius: 2px;
        }
    </style>
</head>

<body class="bg-orange-50/30 min-h-screen">

    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black/50 z-30 hidden" onclick="toggleMobileMenu()"></div>

    <!-- Mobile Sidebar -->
    <aside id="mobile-sidebar" class="mobile-menu fixed left-0 top-0 h-full w-64 bg-amber-800 z-40 flex flex-col lg:hidden">
        <div class="p-5 flex items-center gap-3 border-b border-amber-700">
            <img src="../assets/images/S lang - SPortal Logo.svg" alt="" width="32">
            <span class="text-xl font-extrabold text-white">SPortal</span>
        </div>
        <nav class="flex-1 p-3 flex flex-col gap-1">
            <a href="?page=home" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'home' ? 'active' : ''; ?>">
                <i class="fas fa-home w-5"></i> Home
            </a>
            <a href="?page=tryouts" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'tryouts' ? 'active' : ''; ?>">
                <i class="fas fa-trophy w-5"></i> Tryouts
            </a>
            <a href="?page=players" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'players' ? 'active' : ''; ?>">
                <i class="fas fa-users w-5"></i> Players
            </a>
            <a href="?page=announcements" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'announcements' ? 'active' : ''; ?>">
                <i class="fas fa-bullhorn w-5"></i> Announcements
            </a>
            <a href="?page=notifications" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'notifications' ? 'active' : ''; ?>">
                <i class="fas fa-bell w-5"></i> Notifications
                <?php if ($unread_count > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="?page=profile" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'profile' ? 'active' : ''; ?>">
                <i class="fas fa-user w-5"></i> Profile
            </a>
        </nav>
        <div class="p-3 border-t border-amber-700">
            <a href="../assets/backend/functions/logout.php" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 hover:bg-red-600">
                <i class="fas fa-sign-out-alt w-5"></i> Logout
            </a>
        </div>
    </aside>

    <div class="flex min-h-screen">
        <!-- Desktop Sidebar -->
        <aside class="hidden lg:flex w-64 bg-amber-800 flex-col fixed h-full">
            <div class="p-5 flex items-center gap-3 border-b border-amber-700">
                <img src="../assets/images/S lang - SPortal Logo.svg" alt="" width="32">
                <span class="text-xl font-extrabold text-white">SPortal</span>
            </div>
            <div class="p-4 border-b border-amber-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                        <?php echo strtoupper(substr($user['given_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm"><?php echo htmlspecialchars($user['given_name'] . ' ' . $user['last_name']); ?></p>
                        <p class="text-amber-300 text-xs">Coach</p>
                    </div>
                </div>
            </div>
            <nav class="flex-1 p-3 flex flex-col gap-1">
                <a href="?page=home" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'home' ? 'active' : ''; ?>">
                    <i class="fas fa-home w-5"></i> Home
                </a>
                <a href="?page=tryouts" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'tryouts' ? 'active' : ''; ?>">
                    <i class="fas fa-trophy w-5"></i> Tryouts
                </a>
                <a href="?page=players" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'players' ? 'active' : ''; ?>">
                    <i class="fas fa-users w-5"></i> Players
                </a>
                <a href="?page=announcements" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'announcements' ? 'active' : ''; ?>">
                    <i class="fas fa-bullhorn w-5"></i> Announcements
                </a>
                <a href="?page=notifications" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'notifications' ? 'active' : ''; ?>">
                    <i class="fas fa-bell w-5"></i> Notifications
                    <?php if ($unread_count > 0): ?>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full badge-pulse"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a>
                <a href="?page=profile" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 <?php echo $page == 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user w-5"></i> Profile
                </a>
            </nav>
            <div class="p-3 border-t border-amber-700">
                <a href="../assets/backend/functions/logout.php" class="sidebar-link px-4 py-3 rounded-lg text-amber-200 font-semibold flex items-center gap-3 hover:bg-red-600">
                    <i class="fas fa-sign-out-alt w-5"></i> Logout
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-amber-100 sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 lg:px-8 py-4">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleMobileMenu()" class="lg:hidden text-amber-700 text-xl">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="text-lg lg:text-xl font-bold text-amber-700">
                            <?php
                            $page_titles = [
                                'home' => 'Coach Dashboard',
                                'tryouts' => 'Manage Tryouts',
                                'players' => 'Players',
                                'announcements' => 'Announcements',
                                'notifications' => 'Notifications',
                                'profile' => 'My Profile'
                            ];
                            echo $page_titles[$page] ?? 'Dashboard';
                            ?>
                        </h1>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="?page=notifications" class="relative text-amber-700 hover:text-amber-600">
                            <i class="fas fa-bell text-xl"></i>
                            <?php if ($unread_count > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-4 h-4 rounded-full flex items-center justify-center badge-pulse"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="w-8 h-8 bg-amber-800 rounded-full flex items-center justify-center text-white font-bold text-sm">
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
                    <div class="bg-gradient-to-r from-amber-700 to-orange-700 rounded-2xl p-6 lg:p-8 text-white">
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                            <div>
                                <p class="text-amber-200 text-sm">Welcome back, Coach</p>
                                <h2 class="text-3xl font-extrabold"><?php echo htmlspecialchars($user['given_name'] . ' ' . $user['last_name']); ?>!</h2>
                                <p class="text-amber-200 mt-1"><?php echo htmlspecialchars($user['institute_campus']); ?></p>
                            </div>
                            <div class="flex gap-2 flex-wrap">
                                <?php foreach ($coach_sports as $sport): ?>
                                    <span class="bg-white/20 backdrop-blur px-3 py-1 rounded-full text-sm font-semibold"><?php echo htmlspecialchars($sport['sport_name']); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-white rounded-xl p-5 border border-amber-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-trophy text-amber-700 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo count($tryouts); ?></p>
                                    <p class="text-sm text-gray-500">Total Tryouts</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-amber-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-users text-green-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo $total_players; ?></p>
                                    <p class="text-sm text-gray-500">Total Players</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-amber-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-futbol text-blue-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo count($coach_sports); ?></p>
                                    <p class="text-sm text-gray-500">My Sports</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-xl p-5 border border-amber-100 card-hover">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-bullhorn text-purple-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-800"><?php echo count($announcements); ?></p>
                                    <p class="text-sm text-gray-500">Announcements</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Recent Tryouts -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl border border-amber-100 p-6">
                            <h3 class="font-bold text-amber-700 mb-4"><i class="fas fa-bolt mr-2"></i>Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="?page=tryouts" class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                                    <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center text-white"><i class="fas fa-plus"></i></div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Create Tryout</p>
                                        <p class="text-xs text-gray-500">Schedule a new tryout session</p>
                                    </div>
                                </a>
                                <a href="?page=announcements" class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                                    <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center text-white"><i class="fas fa-bullhorn"></i></div>
                                    <div>
                                        <p class="font-semibold text-gray-800">Post Announcement</p>
                                        <p class="text-xs text-gray-500">Send message to players</p>
                                    </div>
                                </a>
                                <a href="?page=players" class="flex items-center gap-3 p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition">
                                    <div class="w-10 h-10 bg-amber-600 rounded-lg flex items-center justify-center text-white"><i class="fas fa-users"></i></div>
                                    <div>
                                        <p class="font-semibold text-gray-800">View Players</p>
                                        <p class="text-xs text-gray-500">See registered players</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Recent Tryouts -->
                        <div class="lg:col-span-2 bg-white rounded-xl border border-amber-100 overflow-hidden">
                            <div class="p-5 border-b border-amber-50 flex items-center justify-between">
                                <h3 class="font-bold text-amber-700"><i class="fas fa-trophy mr-2"></i>My Tryouts</h3>
                                <a href="?page=tryouts" class="text-sm text-amber-600 hover:underline">View all</a>
                            </div>
                            <div class="p-5 space-y-3 max-h-80 overflow-y-auto scrollbar-thin">
                                <?php if (empty($tryouts)): ?>
                                    <p class="text-gray-400 text-center py-8">No tryouts created yet. <a href="?page=tryouts" class="text-amber-600 underline">Create one now</a>.</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($tryouts, 0, 5) as $tryout): ?>
                                    <div class="flex items-center justify-between p-3 bg-amber-50/50 rounded-lg">
                                        <div>
                                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($tryout['name']); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo date('M d, Y', strtotime($tryout['date'])); ?> at <?php echo date('h:i A', strtotime($tryout['time'])); ?> • <?php echo $tryout['participant_count']; ?> player(s)</p>
                                        </div>
                                        <a href="?page=tryouts&view_participants=<?php echo $tryout['tryout_id']; ?>" class="text-amber-600 hover:text-amber-800 text-sm font-semibold"><i class="fas fa-eye mr-1"></i>View</a>
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
                    <!-- Create Tryout Button -->
                    <div class="flex justify-between items-center">
                        <p class="text-gray-600"><?php echo count($tryouts); ?> tryout(s) total</p>
                        <button onclick="document.getElementById('create-tryout-modal').classList.add('show')" class="px-6 py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Create Tryout
                        </button>
                    </div>

                    <?php if ($edit_tryout): ?>
                    <!-- Edit Tryout Form -->
                    <div class="bg-white rounded-xl border border-amber-100 p-6">
                        <h3 class="text-lg font-bold text-amber-700 mb-4"><i class="fas fa-edit mr-2"></i>Edit Tryout</h3>
                        <form action="../assets/backend/functions/tryout_update.php" method="POST">
                            <input type="hidden" name="tryout_id" value="<?php echo $edit_tryout['tryout_id']; ?>">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Tryout Name</label>
                                    <input type="text" name="tryout_name" value="<?php echo htmlspecialchars($edit_tryout['name']); ?>" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Location</label>
                                    <input type="text" name="location" value="<?php echo htmlspecialchars($edit_tryout['location']); ?>" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Date</label>
                                    <input type="date" name="date" value="<?php echo $edit_tryout['date']; ?>" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Time</label>
                                    <input type="time" name="time" value="<?php echo $edit_tryout['time']; ?>" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-amber-700">Description</label>
                                    <textarea name="description" required rows="3"
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1"><?php echo htmlspecialchars($edit_tryout['description']); ?></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-amber-700">Notes (Optional)</label>
                                    <textarea name="notes" rows="2"
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1"><?php echo htmlspecialchars($edit_tryout['notes'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex gap-3">
                                <button type="submit" name="update_tryout" class="px-8 py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                                <a href="?page=tryouts" class="px-8 py-3 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition">Cancel</a>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                    <?php if ($view_participants): ?>
                    <!-- View Participants -->
                    <div class="bg-white rounded-xl border border-amber-100 overflow-hidden">
                        <div class="p-5 border-b border-amber-50 flex items-center justify-between">
                            <h3 class="font-bold text-amber-700"><i class="fas fa-users mr-2"></i>Participants for "<?php echo htmlspecialchars($view_participants['tryout_name']); ?>"</h3>
                            <a href="?page=tryouts" class="text-sm text-amber-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i>Back to Tryouts</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-amber-50">
                                    <tr>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Name</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Student ID</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Sex</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Year Level</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Institute/Campus</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($participants)): ?>
                                        <tr><td colspan="6" class="text-center py-8 text-gray-400">No participants yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($participants as $p): ?>
                                        <tr class="border-t border-amber-50 hover:bg-amber-50/50">
                                            <td class="p-4 text-sm font-semibold text-gray-800">
                                                <?php echo htmlspecialchars($p['given_name'] . ' ' . ($p['middle_name'] ? $p['middle_name'][0] . '. ' : '') . $p['last_name'] . ($p['suffix'] ? ' ' . $p['suffix'] : '')); ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['student_id'] ?? '-'); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['sex']); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['year_level'] ?? '-'); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['institute_campus']); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['contact_number'] ?? '-'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Tryout Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                        <?php if (empty($tryouts)): ?>
                            <div class="col-span-full text-center py-16">
                                <i class="fas fa-trophy text-6xl text-amber-200 mb-4"></i>
                                <p class="text-gray-400 text-lg">No tryouts created yet.</p>
                                <button onclick="document.getElementById('create-tryout-modal').classList.add('show')" class="mt-4 px-6 py-3 bg-amber-700 text-white font-bold rounded-full hover:bg-amber-800 transition">Create Your First Tryout</button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($tryouts as $tryout): ?>
                            <div class="bg-white rounded-xl border border-amber-100 card-hover overflow-hidden">
                                <div class="p-5">
                                    <div class="flex items-start justify-between mb-3">
                                        <span class="bg-amber-100 text-amber-700 text-xs font-bold px-3 py-1 rounded-full"><?php echo htmlspecialchars($tryout['sport_name']); ?></span>
                                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-full"><i class="fas fa-users mr-1"></i><?php echo $tryout['participant_count']; ?></span>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($tryout['name']); ?></h3>
                                    <p class="text-sm text-gray-500 mb-4"><?php echo htmlspecialchars($tryout['description']); ?></p>
                                    
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <p><i class="fas fa-calendar-alt w-5 text-amber-500"></i> <?php echo date('F d, Y', strtotime($tryout['date'])); ?></p>
                                        <p><i class="fas fa-clock w-5 text-amber-500"></i> <?php echo date('h:i A', strtotime($tryout['time'])); ?></p>
                                        <p><i class="fas fa-map-marker-alt w-5 text-amber-500"></i> <?php echo htmlspecialchars($tryout['location']); ?></p>
                                    </div>
                                </div>
                                <div class="px-5 pb-5 flex gap-2">
                                    <a href="?page=tryouts&view_participants=<?php echo $tryout['tryout_id']; ?>" class="flex-1 py-2 bg-blue-50 text-blue-600 font-semibold rounded-lg hover:bg-blue-100 transition text-center text-sm border border-blue-200">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <a href="?page=tryouts&edit_tryout=<?php echo $tryout['tryout_id']; ?>" class="flex-1 py-2 bg-amber-50 text-amber-700 font-semibold rounded-lg hover:bg-amber-100 transition text-center text-sm border border-amber-200">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form action="../assets/backend/functions/tryout_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this tryout? All registered players will be notified.');">
                                        <input type="hidden" name="tryout_id" value="<?php echo $tryout['tryout_id']; ?>">
                                        <button type="submit" name="delete_tryout" class="py-2 px-3 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition text-sm border border-red-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- PLAYERS PAGE -->
                <?php elseif ($page == 'players'): ?>
                <div class="space-y-6">
                    <?php
                    // Get all players for coach's sports
                    $sport_names = array_column($coach_sports, 'sport_name');
                    $sport_name_list = !empty($sport_names) ? "'" . implode("','", $sport_names) . "'" : "''";

                    $all_players_query = "SELECT DISTINCT u.*, GROUP_CONCAT(s.sport_name) as sports
                        FROM users u
                        JOIN sports s ON u.user_id = s.user_id
                        WHERE s.sport_name IN ($sport_name_list) AND u.role_id = '1'
                        GROUP BY u.user_id
                        ORDER BY u.last_name, u.given_name";
                    $all_players_result = mysqli_query($conn, $all_players_query);
                    $all_players = [];
                    while ($ap = mysqli_fetch_assoc($all_players_result)) {
                        $all_players[] = $ap;
                    }
                    ?>

                    <div class="flex items-center justify-between">
                        <p class="text-gray-600"><?php echo count($all_players); ?> player(s) in your sports</p>
                    </div>

                    <div class="bg-white rounded-xl border border-amber-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-amber-50">
                                    <tr>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Name</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Student ID</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Sex</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Year Level</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Institute/Campus</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Sports</th>
                                        <th class="text-left p-4 text-sm font-semibold text-amber-700">Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($all_players)): ?>
                                        <tr><td colspan="7" class="text-center py-8 text-gray-400">No players registered in your sports yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($all_players as $p): ?>
                                        <tr class="border-t border-amber-50 hover:bg-amber-50/50">
                                            <td class="p-4 text-sm font-semibold text-gray-800">
                                                <?php echo htmlspecialchars($p['given_name'] . ' ' . ($p['middle_name'] ? $p['middle_name'][0] . '. ' : '') . $p['last_name'] . ($p['suffix'] ? ' ' . $p['suffix'] : '')); ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['student_id'] ?? '-'); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['sex']); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['year_level'] ?? '-'); ?></td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['institute_campus']); ?></td>
                                            <td class="p-4 text-sm">
                                                <?php foreach (explode(',', $p['sports']) as $sp): ?>
                                                    <span class="inline-block bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded-full mr-1"><?php echo htmlspecialchars(trim($sp)); ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td class="p-4 text-sm text-gray-600"><?php echo htmlspecialchars($p['contact_number'] ?? '-'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ANNOUNCEMENTS PAGE -->
                <?php elseif ($page == 'announcements'): ?>
                <div class="space-y-6">
                    <!-- Create Announcement Button -->
                    <div class="flex justify-end">
                        <button onclick="document.getElementById('create-announcement-modal').classList.add('show')" class="px-6 py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition flex items-center gap-2">
                            <i class="fas fa-plus-circle"></i> Post Announcement
                        </button>
                    </div>

                    <!-- Announcements List -->
                    <?php if (empty($announcements)): ?>
                        <div class="text-center py-16">
                            <i class="fas fa-bullhorn text-6xl text-amber-200 mb-4"></i>
                            <p class="text-gray-400 text-lg">No announcements posted yet.</p>
                            <button onclick="document.getElementById('create-announcement-modal').classList.add('show')" class="mt-4 px-6 py-3 bg-amber-700 text-white font-bold rounded-full hover:bg-amber-800 transition">Post Your First Announcement</button>
                        </div>
                    <?php else: ?>
                        <?php foreach ($announcements as $ann): ?>
                        <div class="bg-white rounded-xl border border-amber-100 p-6 card-hover">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($ann['title']); ?></h3>
                                    <p class="text-sm text-gray-500">Posted on <?php echo date('F d, Y h:i A', strtotime($ann['created_at'])); ?> • <span class="text-amber-600"><?php echo htmlspecialchars($ann['sport_name']); ?></span></p>
                                </div>
                                <form action="../assets/backend/functions/announcement_delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    <input type="hidden" name="announcement_id" value="<?php echo $ann['announcement_id']; ?>">
                                    <button type="submit" name="delete_announcement" class="text-red-400 hover:text-red-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
                            <button type="submit" class="text-sm text-amber-600 hover:underline font-semibold"><i class="fas fa-check-double mr-1"></i> Mark all as read</button>
                        </form>
                    <?php endif; ?>

                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-16">
                            <i class="fas fa-bell-slash text-6xl text-amber-200 mb-4"></i>
                            <p class="text-gray-400 text-lg">No notifications yet.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($notifications as $notif): ?>
                        <div class="bg-white rounded-xl border <?php echo $notif['is_read'] == 0 ? 'border-amber-300 bg-amber-50/50' : 'border-amber-100'; ?> p-5 card-hover">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?php echo $notif['is_read'] == 0 ? 'bg-amber-600 text-white' : 'bg-gray-200 text-gray-500'; ?>">
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
                    <div class="bg-white rounded-xl border border-amber-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-amber-700 to-orange-700 p-6 flex items-center gap-4">
                            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-extrabold">
                                <?php echo strtoupper(substr($user['given_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                            </div>
                            <div class="text-white">
                                <h2 class="text-xl font-extrabold"><?php echo htmlspecialchars($user['given_name'] . ' ' . ($user['middle_name'] ? $user['middle_name'][0] . '. ' : '') . $user['last_name'] . ($user['suffix'] ? ' ' . $user['suffix'] : '')); ?></h2>
                                <p class="text-amber-200 text-sm">@<?php echo htmlspecialchars($user['username']); ?> • Coach</p>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Form -->
                    <div class="bg-white rounded-xl border border-amber-100 p-6">
                        <h3 class="text-lg font-bold text-amber-700 mb-4"><i class="fas fa-edit mr-2"></i>Edit Profile</h3>
                        <form action="../assets/backend/functions/edit_profile.php" method="POST">
                            <input type="hidden" name="edit_profile" value="1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Given Name</label>
                                    <input type="text" name="given_name" value="<?php echo htmlspecialchars($user['given_name']); ?>" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Middle Name</label>
                                    <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Last Name</label>
                                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Suffix</label>
                                    <input type="text" name="suffix" value="<?php echo htmlspecialchars($user['suffix'] ?? ''); ?>" placeholder="ex. Jr."
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Contact Number</label>
                                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($user['contact_number'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Social Media Link</label>
                                    <input type="text" name="social_media" value="<?php echo htmlspecialchars($user['social_media'] ?? ''); ?>"
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-sm font-semibold text-amber-700">Institute/Campus</label>
                                    <select name="institute_campus" class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
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
                                <button type="submit" class="px-8 py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition">
                                    <i class="fas fa-save mr-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="bg-white rounded-xl border border-amber-100 p-6">
                        <h3 class="text-lg font-bold text-amber-700 mb-4"><i class="fas fa-lock mr-2"></i>Change Password</h3>
                        <form action="../assets/backend/functions/edit_profile.php" method="POST">
                            <input type="hidden" name="change_password" value="1">
                            <div class="space-y-4 max-w-md">
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Current Password</label>
                                    <input type="password" name="current_password" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">New Password</label>
                                    <input type="password" name="new_password" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-amber-700">Confirm New Password</label>
                                    <input type="password" name="confirm_password" required
                                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                                </div>
                                <button type="submit" class="px-8 py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition">
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

    <!-- Create Tryout Modal -->
    <div id="create-tryout-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="p-6 border-b border-amber-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-amber-700"><i class="fas fa-plus-circle mr-2"></i>Create Tryout</h3>
                <button onclick="document.getElementById('create-tryout-modal').classList.remove('show')" class="text-gray-400 hover:text-gray-600 text-xl"><i class="fas fa-times"></i></button>
            </div>
            <form action="../assets/backend/functions/tryout_create.php" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-amber-700">Sport</label>
                    <select name="sport_name" required class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
                        <?php foreach ($coach_sports as $sport): ?>
                            <option value="<?php echo htmlspecialchars($sport['sport_name']); ?>"><?php echo htmlspecialchars($sport['sport_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-amber-700">Tryout Name</label>
                    <input type="text" name="tryout_name" required placeholder="e.g., Basketball Tryout Screening"
                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-amber-700">Description</label>
                    <textarea name="description" required rows="2" placeholder="Brief description of the tryout"
                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1"></textarea>
                </div>
                <div>
                    <label class="text-sm font-semibold text-amber-700">Notes (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="Additional notes for players"
                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-amber-700">Date</label>
                        <input type="date" name="date" required class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-amber-700">Time</label>
                        <input type="time" name="time" required class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
                    </div>
                </div>
                <div>
                    <label class="text-sm font-semibold text-amber-700">Location</label>
                    <input type="text" name="location" required placeholder="e.g., University Gymnasium"
                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                </div>
                <div class="pt-2">
                    <button type="submit" name="create_tryout" class="w-full py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition">
                        <i class="fas fa-plus-circle mr-2"></i>Create Tryout
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Announcement Modal -->
    <div id="create-announcement-modal" class="modal-overlay fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto shadow-2xl">
            <div class="p-6 border-b border-amber-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-amber-700"><i class="fas fa-bullhorn mr-2"></i>Post Announcement</h3>
                <button onclick="document.getElementById('create-announcement-modal').classList.remove('show')" class="text-gray-400 hover:text-gray-600 text-xl"><i class="fas fa-times"></i></button>
            </div>
            <form action="../assets/backend/functions/announcement_create.php" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-semibold text-amber-700">Title</label>
                    <input type="text" name="title" required placeholder="Announcement title"
                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1">
                </div>
                <div>
                    <label class="text-sm font-semibold text-amber-700">Sport</label>
                    <select name="sport_name" required class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1 bg-white">
                        <?php foreach ($coach_sports as $sport): ?>
                            <option value="<?php echo htmlspecialchars($sport['sport_name']); ?>"><?php echo htmlspecialchars($sport['sport_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="text-sm font-semibold text-amber-700">Content</label>
                    <textarea name="content" required rows="5" placeholder="Write your announcement here..."
                        class="w-full p-3 rounded-lg border border-amber-200 outline-amber-600 mt-1"></textarea>
                </div>
                <div class="pt-2">
                    <button type="submit" name="create_announcement" class="w-full py-3 bg-amber-700 text-white font-bold rounded-lg hover:bg-amber-800 transition">
                        <i class="fas fa-bullhorn mr-2"></i>Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const sidebar = document.getElementById('mobile-sidebar');
            const overlay = document.getElementById('mobile-overlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        }

        // Close modals when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });
    </script>

</body>

</html>
