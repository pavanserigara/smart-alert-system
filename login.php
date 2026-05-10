<?php
session_start();
require_once 'db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];
        
        // Log the login action
        $log_stmt = $pdo->prepare("INSERT INTO processing_logs (user_id, action, details) VALUES (?, 'LOGIN', ?)");
        $log_stmt->execute([$user['id'], "User logged into dashboard as " . $user['role']]);

        // Route to specialized dashboard files
        $dashboard = $user['role'] . ".php";
        header("Location: " . $dashboard);
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Login - Weather & Energy Ecosystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); }
        .login-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
    <div class="login-card p-8 rounded-3xl shadow-2xl w-full max-w-md relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-500"></div>
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black mx-auto mb-6 text-2xl italic shadow-lg shadow-blue-500/20">N</div>
            <h1 class="text-2xl font-black text-white tracking-tighter uppercase">Nexus <span class="text-blue-500">Access</span></h1>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Environmental Command Interface</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl mb-6 text-xs font-bold uppercase tracking-tight animate-pulse">
                ⚠ <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Operator ID</label>
                <input type="text" name="username" required class="w-full bg-slate-900/50 border border-white/5 px-4 py-3 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm">
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Access Key</label>
                <input type="password" name="password" required class="w-full bg-slate-900/50 border border-white/5 px-4 py-3 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:outline-none transition text-sm">
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-xl transition shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs">
                Initialize Session
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-white/5 text-center">
            <p class="text-[9px] font-black text-slate-600 uppercase tracking-widest mb-3">Authorized Nodes Only</p>
            <div class="grid grid-cols-2 gap-2 text-[9px] font-bold">
                <div class="bg-white/5 p-2 rounded-lg text-slate-400 border border-white/5">citizen_joe</div>
                <div class="bg-white/5 p-2 rounded-lg text-slate-400 border border-white/5">farmer_ted</div>
                <div class="bg-white/5 p-2 rounded-lg text-slate-400 border border-white/5">solar_sam</div>
                <div class="bg-white/5 p-2 rounded-lg text-slate-400 border border-white/5">admin_main</div>
            </div>
            <p class="text-[9px] text-slate-700 mt-3 font-mono">KEY: password123</p>
        </div>
    </div>
</body>
</html>
