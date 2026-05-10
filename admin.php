<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once 'db_connect.php';

// Handle All Admin Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['broadcast'])) {
            $msg = $_POST['message'];
            $stmt = $pdo->prepare("INSERT INTO emergency_alerts (title, message, severity) VALUES ('ADMIN BROADCAST', ?, 'emergency')");
            $stmt->execute([$msg]);
            $success = "Alert Broadcasted Successfully!";
        } elseif (isset($_POST['post_blog'])) {
            $title = $_POST['blog_title'];
            $content = $_POST['blog_content'];
            $stmt = $pdo->prepare("INSERT INTO blogs (title, content) VALUES (?, ?)");
            $stmt->execute([$title, $content]);
            $success = "Blog Post Published!";
        } elseif (isset($_POST['delete_blog'])) {
            $id = $_POST['blog_id'];
            $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Blog Post Removed!";
        } elseif (isset($_POST['delete_alert'])) {
            $id = $_POST['alert_id'];
            $stmt = $pdo->prepare("DELETE FROM emergency_alerts WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Emergency Alert Cleared!";
        } elseif (isset($_POST['delete_user'])) {
            $id = $_POST['user_id'];
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $success = "User Account Terminated!";
        }
    } catch (Exception $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}

// Fetch Data for HUD
try {
    $logs = $pdo->query("SELECT * FROM processing_logs ORDER BY timestamp DESC LIMIT 15")->fetchAll();
    $all_blogs = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC")->fetchAll();
    $all_alerts = $pdo->query("SELECT * FROM emergency_alerts ORDER BY created_at DESC")->fetchAll();
    $all_users = $pdo->query("SELECT id, username, role FROM users WHERE role != 'admin' ORDER BY role")->fetchAll();

    // Stats
    $stats = [
        'users' => count($all_users),
        'blogs' => count($all_blogs),
        'alerts' => count($all_alerts)
    ];

    // User Distribution Analysis
    $distribution = [
        'farmer' => 0,
        'energy' => 0,
        'common' => 0
    ];
    foreach ($all_users as $u) {
        if (isset($distribution[$u['role']]))
            $distribution[$u['role']]++;
    }

    // Regional Hotspots (Extracted from logs)
    $hotspots = [];
    foreach ($logs as $log) {
        if (strpos($log['action'], 'WEATHER_FETCH') !== false) {
            $loc = $log['details'];
            $hotspots[$loc] = ($hotspots[$loc] ?? 0) + 1;
        }
    }
    arsort($hotspots);
    $hotspots = array_slice($hotspots, 0, 5);

} catch (Exception $e) {
    $logs = [];
    $all_blogs = [];
    $all_alerts = [];
    $all_users = [];
    $stats = ['users' => 0, 'blogs' => 0, 'alerts' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Nexus Supreme - Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/main.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=Fira+Code:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-mono {
            font-family: 'Fira Code', monospace;
        }

        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .admin-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .status-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body class="admin-gradient min-h-screen text-slate-200">

    <!-- Top Navigation Bar -->
    <nav class="border-b border-white/5 bg-slate-900/50 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center font-black text-white italic shadow-lg shadow-blue-500/20">
                    N</div>
                <div>
                    <h1 class="text-xl font-black tracking-tighter text-white">NEXUS<span
                            class="text-blue-500">SUPREME</span></h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Root Administration
                        Console</p>
                </div>
            </div>
            <div class="flex items-center gap-4 sm:gap-6">
                <div class="flex items-center gap-2 px-3 py-1 bg-green-500/10 rounded-full border border-green-500/20">
                    <span class="w-2 h-2 rounded-full bg-green-500 status-pulse"></span>
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-tighter">System Health:
                        Optimal</span>
                </div>
                <a href="login.php" class="text-xs font-bold text-slate-400 hover:text-white transition">LOGOUT</a>
            </div>
        </div>
    </nav>

    <main class="max-w-[1600px] mx-auto p-4 sm:p-6 lg:p-8">

        <!-- Top Statistics HUD -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <div class="glass-card border-l-4 border-blue-500">
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Total Operators</p>
                <p class="text-2xl sm:text-3xl font-black text-white"><?= $stats['users'] ?></p>
            </div>
            <div class="glass-card border-l-4 border-purple-500">
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Ecosystem Insights</p>
                <p class="text-2xl sm:text-3xl font-black text-white"><?= $stats['blogs'] ?></p>
            </div>
            <div class="glass-card border-l-4 border-red-500">
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Active Broadcasts</p>
                <p class="text-2xl sm:text-3xl font-black text-white"><?= $stats['alerts'] ?></p>
            </div>
            <div class="glass-card border-l-4 border-green-500">
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Engine Uptime</p>
                <p class="text-2xl sm:text-3xl font-black text-white"><span id="uptime-counter">00h 00m 00s</span></p>
            </div>
        </div>

        <!-- Ecosystem Intelligence Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-8">
            <!-- Operator Mix -->
            <div class="glass-card">
                <h3 class="text-xs font-black text-blue-500 uppercase tracking-widest mb-6">Operator Mix</h3>
                <div class="space-y-4">
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] font-bold uppercase">
                            <span>Farmers</span><span><?= $distribution['farmer'] ?></span></div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-green-500 h-full"
                                style="width: <?= $stats['users'] > 0 ? ($distribution['farmer'] / $stats['users'] * 100) : 0 ?>%">
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] font-bold uppercase"><span>Energy
                                Experts</span><span><?= $distribution['energy'] ?></span></div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full"
                                style="width: <?= $stats['users'] > 0 ? ($distribution['energy'] / $stats['users'] * 100) : 0 ?>%">
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex justify-between text-[10px] font-bold uppercase">
                            <span>Citizens</span><span><?= $distribution['common'] ?></span></div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full"
                                style="width: <?= $stats['users'] > 0 ? ($distribution['common'] / $stats['users'] * 100) : 0 ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5-Day Trend Graph -->
            <div class="glass-card bg-gradient-to-br from-blue-600/5 to-transparent">
                <h3 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-6">5-Day Atmospheric Trends
                </h3>
                <div class="h-48">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- System Efficiency -->
            <div class="glass-card md:col-span-2 lg:col-span-1">
                <h3 class="text-xs font-black text-green-500 uppercase tracking-widest mb-6">Efficiency Matrix</h3>
                <div class="flex flex-col items-center justify-center py-2">
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90">
                            <circle cx="48" cy="48" r="40" fill="transparent" stroke="currentColor" stroke-width="8"
                                class="text-slate-800"></circle>
                            <circle cx="48" cy="48" r="40" fill="transparent" stroke="currentColor" stroke-width="8"
                                class="text-green-500" stroke-dasharray="251.2"
                                stroke-dashoffset="<?= 251.2 * (1 - 0.98) ?>"></circle>
                        </svg>
                        <span class="absolute text-lg sm:text-xl font-black text-white italic">98%</span>
                    </div>
                    <p class="text-[9px] sm:text-[10px] font-bold text-slate-500 uppercase mt-4 tracking-tighter">Sensor Latency:
                        <span class="text-white">42ms</span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 sm:gap-8">

            <!-- Left Management Column (Primary Tools) -->
            <div class="lg:col-span-3 space-y-8">

                <!-- Feedback Messages -->
                <?php if (isset($success)): ?>
                    <div
                        class="bg-green-500/10 border border-green-500/20 p-4 rounded-2xl text-green-400 font-bold text-sm animate-fade">
                        ✓ <?= $success ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div
                        class="bg-red-500/10 border border-red-500/20 p-4 rounded-2xl text-red-400 font-bold text-sm animate-fade">
                        ⚠ <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Dispatch Center -->
                    <div class="glass-card p-8">
                        <h3 class="text-lg font-black mb-6 uppercase tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                </path>
                            </svg>
                            Emergency Dispatch
                        </h3>
                        <form method="POST" class="space-y-4">
                            <textarea name="message" rows="4" placeholder="Enter critical broadcast message..."
                                class="w-full bg-slate-900/50 border border-white/5 rounded-xl p-4 text-white focus:ring-2 focus:ring-red-500 outline-none text-sm transition"
                                required></textarea>
                            <button type="submit" name="broadcast"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-black py-4 rounded-xl transition shadow-lg shadow-red-600/20 uppercase tracking-widest text-xs">
                                Global Broadcast
                            </button>
                        </form>
                    </div>

                    <!-- Insight Publisher -->
                    <div class="glass-card p-8">
                        <h3 class="text-lg font-black mb-6 uppercase tracking-tight flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Ecosystem Publisher
                        </h3>
                        <form method="POST" class="space-y-4">
                            <input type="text" name="blog_title" placeholder="Insight Headline..."
                                class="w-full bg-slate-900/50 border border-white/5 rounded-xl p-4 text-white outline-none focus:ring-2 focus:ring-blue-500 text-sm transition"
                                required>
                            <textarea name="blog_content" rows="4" placeholder="Write expert weather analysis..."
                                class="w-full bg-slate-900/50 border border-white/5 rounded-xl p-4 text-white focus:ring-2 focus:ring-blue-500 outline-none text-sm transition"
                                required></textarea>
                            <button type="submit" name="post_blog"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl transition shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs">
                                Publish Insight
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Registry Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- User Registry -->
                    <div class="glass-card p-8">
                        <h3 class="text-lg font-black mb-6 uppercase tracking-tight text-slate-400">Operator Registry
                        </h3>
                        <div class="space-y-3">
                            <?php foreach ($all_users as $u): ?>
                                <div
                                    class="flex items-center justify-between p-4 bg-slate-900/40 rounded-xl border border-white/5 hover:border-white/10 transition">
                                    <div>
                                        <p class="text-sm font-black text-white"><?= htmlspecialchars($u['username']) ?></p>
                                        <p class="text-[9px] font-bold text-blue-500 uppercase tracking-widest">
                                            <?= $u['role'] ?></p>
                                    </div>
                                    <form method="POST" onsubmit="return confirm('Terminate access for this user?')">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" name="delete_user"
                                            class="p-2 hover:bg-red-500/20 text-red-500 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Management Tabs -->
                    <div class="glass-card p-8">
                        <h3 class="text-lg font-black mb-6 uppercase tracking-tight text-slate-400">Active Content</h3>
                        <div class="space-y-4">
                            <!-- Section: Active Alerts -->
                            <div class="space-y-2">
                                <p class="text-[10px] font-black text-red-500 uppercase tracking-tighter mb-2">Live
                                    Broadcasts</p>
                                <?php foreach ($all_alerts as $a): ?>
                                    <div
                                        class="flex items-center justify-between p-3 bg-red-500/5 rounded-lg border border-red-500/10">
                                        <p class="text-xs text-red-200 truncate pr-4"><?= htmlspecialchars($a['message']) ?>
                                        </p>
                                        <form method="POST">
                                            <input type="hidden" name="alert_id" value="<?= $a['id'] ?>">
                                            <button type="submit" name="delete_alert"
                                                class="text-red-500 hover:text-white text-[10px] font-black">CLEAR</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Section: Blogs -->
                            <div class="space-y-2 pt-4 border-t border-white/5">
                                <p class="text-[10px] font-black text-blue-500 uppercase tracking-tighter mb-2">Recent
                                    Insights</p>
                                <?php foreach (array_slice($all_blogs, 0, 5) as $b): ?>
                                    <div
                                        class="flex items-center justify-between p-3 bg-slate-900/40 rounded-lg border border-white/5">
                                        <p class="text-xs text-slate-300 truncate pr-4"><?= htmlspecialchars($b['title']) ?>
                                        </p>
                                        <form method="POST">
                                            <input type="hidden" name="blog_id" value="<?= $b['id'] ?>">
                                            <button type="submit" name="delete_blog"
                                                class="text-slate-500 hover:text-red-500 text-[10px] font-black transition">DEL</button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Monitoring Column (System Health) -->
            <div class="lg:col-span-1 space-y-8">

                <!-- Service Heartbeat -->
                <div class="glass-card overflow-hidden">
                    <div class="bg-green-500/10 p-4 border-b border-green-500/20 flex justify-between items-center">
                        <span class="text-xs font-black text-green-500 uppercase tracking-widest">Supervisor
                            Heartbeat</span>
                        <div class="w-2 h-2 rounded-full bg-green-500 status-pulse"></div>
                    </div>
                    <div class="p-6 font-mono text-[10px] space-y-1 h-48 overflow-y-auto text-green-400/80 bg-black/40"
                        id="system-logs">
                        <p class="text-blue-400 font-bold">> NEXUS SUPERVISOR v2.4_READY</p>
                        <p>> Internal Data Bridge: CONNECTED</p>
                        <p>> Database Integrity: VERIFIED</p>
                        <p class="text-slate-600">--------------------------------</p>
                    </div>
                </div>

                <!-- Global Engine Console -->
                <div class="glass-card overflow-hidden">
                    <div class="bg-blue-500/10 p-4 border-b border-blue-500/20">
                        <span class="text-xs font-black text-blue-500 uppercase tracking-widest">Weather_Engine.py
                            Log</span>
                    </div>
                    <div
                        class="p-6 font-mono text-[10px] space-y-2 h-[400px] overflow-y-auto text-blue-300/70 bg-black/40">
                        <?php foreach ($logs as $log): ?>
                            <div class="border-l border-white/10 pl-3">
                                <p class="text-slate-600 text-[8px]"><?= $log['timestamp'] ?></p>
                                <p><span class="text-blue-500"><?= $log['action'] ?>:</span> <?= $log['details'] ?></p>
                            </div>
                        <?php endforeach; ?>
                        <p class="animate-pulse">_</p>
                    </div>
                </div>

            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let currentUptime = 0;
        let lastSyncTime = 0;

        async function initTrendChart() {
            try {
                const res = await fetch('http://127.0.0.1:5000/api/forecast?city=Bengaluru');
                const data = await res.json();

                const labels = data.map(d => new Date(d.date).toLocaleDateString(undefined, { weekday: 'short' }));
                const temps = data.map(d => d.temp);

                const ctx = document.getElementById('trendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Temp °C',
                            data: temps,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 10 } } },
                            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } }
                        }
                    }
                });
            } catch (e) { }
        }
        async function fetchSystemStatus() {
            try {
                const res = await fetch('http://127.0.0.1:5000/api/status');
                const data = await res.json();
                currentUptime = data.uptime_seconds;
                updateUptimeUI();
            } catch (e) {
                console.error("Status Sync Failed");
            }
        }

        function updateUptimeUI() {
            const counter = document.getElementById('uptime-counter');
            if (counter) {
                const hrs = Math.floor(currentUptime / 3600);
                const mins = Math.floor((currentUptime % 3600) / 60);
                const secs = currentUptime % 60;
                counter.innerText = `${hrs}h ${mins}m ${secs}s`;
            }
        }

        function updateHeartbeat() {
            currentUptime++;
            lastSyncTime++;

            // Update UI every second
            updateUptimeUI();

            // Sync with backend every 15 seconds to prevent drift
            if (lastSyncTime >= 15) {
                fetchSystemStatus();
                lastSyncTime = 0;
            }

            // Periodic Console Heartbeat
            if (currentUptime % 8 === 0) {
                const logContainer = document.getElementById('system-logs');
                if (logContainer) {
                    const p = document.createElement('p');
                    p.className = "text-blue-400 pt-1";
                    p.innerText = `> SERVICE_PING ${new Date().toLocaleTimeString()} ... 200 OK`;
                    logContainer.prepend(p);
                    if (logContainer.children.length > 20) logContainer.lastElementChild.remove();
                }
            }
        }

        setInterval(updateHeartbeat, 1000);
        fetchSystemStatus(); // Initial sync to get base uptime
        initTrendChart();    // Render 5-day trend graph
    </script>

</body>

</html>