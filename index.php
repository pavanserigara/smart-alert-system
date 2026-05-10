<?php
// Simple View Routing for MVP
$view = isset($_GET['view']) ? $_GET['view'] : 'common';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus - Smart Weather & Energy Ecosystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%); font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="min-h-screen text-slate-200">
    <!-- Nexus Splash Screen -->
    <div id="nexus-splash" class="scifi-splash">
        <div class="scifi-loader">
            <div class="scifi-text-glitch" data-text="NEXUS_SUPREME">NEXUS_SUPREME</div>
            <div class="scifi-sub-text">SYSTEM INITIALIZING...</div>
            <div class="scifi-progress-bar">
                <div class="scifi-progress-fill"></div>
            </div>
            <div class="scifi-scanline"></div>
        </div>
    </div>
    
    <script>
        window.addEventListener('load', () => {
            setTimeout(() => {
                const splash = document.getElementById('nexus-splash');
                splash.style.opacity = '0';
                setTimeout(() => splash.remove(), 1000);
            }, 2500);
        });
    </script>

    <!-- Navigation -->
    <nav class="bg-slate-900/50 backdrop-blur-xl border-b border-white/5 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row justify-between py-4 sm:h-16 items-center gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black italic shadow-lg shadow-blue-500/20">N</div>
                    <span class="text-xl font-black text-white tracking-tighter uppercase">Nexus <span class="text-blue-500">Core</span></span>
                </div>
                <div class="flex space-x-6 text-[10px] font-black uppercase tracking-widest">
                    <a href="?view=common" class="<?= $view == 'common' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-slate-500 hover:text-white' ?> pb-1 transition">Common</a>
                    <a href="?view=farmer" class="<?= $view == 'farmer' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-slate-500 hover:text-white' ?> pb-1 transition">Farmer</a>
                    <a href="?view=energy" class="<?= $view == 'energy' ? 'text-blue-400 border-b-2 border-blue-400' : 'text-slate-500 hover:text-white' ?> pb-1 transition">Energy</a>
                    <a href="?view=admin" class="<?= $view == 'admin' ? 'text-red-500 border-b-2 border-red-500' : 'text-slate-500 hover:text-red-400' ?> pb-1 transition">Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        
        <!-- Search Header -->
        <div class="mb-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight capitalize"><?= $view ?> Intelligence</h1>
                <p class="text-slate-500 mt-1 text-sm font-medium">Real-time environmental telemetry node.</p>
            </div>
            <div class="flex gap-2 w-full lg:w-auto">
                <input type="text" id="cityInput" placeholder="Enter target city..." class="px-4 py-3 rounded-xl bg-slate-900/50 border border-white/5 text-white focus:ring-2 focus:ring-blue-500 focus:outline-none flex-1 lg:w-64 transition">
                <button onclick="fetchWeather()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl transition font-black uppercase tracking-widest text-xs shadow-lg shadow-blue-600/20">Sync</button>
            </div>
        </div>

        <!-- Dashboard Content -->
        <div id="loading" class="text-center py-20 hidden">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-[10px] font-black text-slate-500 uppercase tracking-widest animate-pulse">Scanning Atmospheric Vectors...</p>
        </div>

        <div id="content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Dynamic Widgets injected by JS -->
        </div>
            <!-- Dynamic Widgets injected by JS -->
        </div>

    </main>

    <script>
        const view = "<?= $view ?>";
        
        async function fetchWeather() {
            const city = document.getElementById('cityInput').value;
            const loading = document.getElementById('loading');
            const content = document.getElementById('content');
            
            loading.classList.remove('hidden');
            content.classList.add('opacity-50');

            try {
                const response = await fetch(`http://127.0.0.1:5000/api/weather?city=${city}`);
                const data = await response.json();
                
                if (!response.ok) {
                    alert("Backend Error: " + (data.error || "Unknown error"));
                    return;
                }
                
                renderDashboard(data);
            } catch (err) {
                console.error(err);
                alert("Connection failed! Please ensure weather_engine.py is running.");
            } finally {
                loading.classList.add('hidden');
                content.classList.remove('opacity-50');
            }
        }

        function renderDashboard(data) {
            const container = document.getElementById('content');
            container.innerHTML = '';

            // 1. Common Weather Overview (Visible to all)
            const baseCard = createCard("Current Conditions", `
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-4xl font-bold text-slate-900">${data.raw.temp}°C</p>
                        <p class="text-slate-500 font-medium">${data.raw.condition} in ${data.location}</p>
                    </div>
                    <div class="text-blue-500">
                         <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t flex justify-between text-sm text-slate-500">
                    <span>Wind: ${data.raw.wind} m/s</span>
                    <span>Clouds: ${data.raw.clouds}%</span>
                </div>
            `);
            container.appendChild(baseCard);

            // 2. View Specific Logic
            if (view === 'common') {
                const travel = data.insights.travel;
                container.appendChild(createCard("Travel Suitability", `
                    <div class="bg-${travel.type === 'Outdoor' ? 'green' : 'amber'}-50 p-4 rounded-xl mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-${travel.type === 'Outdoor' ? 'green' : 'amber'}-600">${travel.status}</span>
                        <p class="text-slate-700 mt-1">${travel.suggestion}</p>
                    </div>
                    <p class="text-sm text-slate-500 italic">Travel Tip: Visibility is currently ${(data.raw.visibility / 1000).toFixed(1)}km.</p>
                `));
            }

            if (view === 'farmer') {
                const agri = data.insights.agri;
                container.appendChild(createCard("Agricultural Status", `
                    <div class="space-y-3">
                        ${agri.alerts.map(alert => `
                            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg border-l-4 border-blue-500">
                                <span class="text-sm text-slate-700">${alert}</span>
                            </div>
                        `).join('')}
                    </div>
                `));
            }

            if (view === 'energy') {
                const solar = data.insights.solar;
                container.appendChild(createCard("Solar Power Estimator", `
                    <div class="text-center py-4">
                        <p class="text-xs font-semibold text-slate-500 mb-1 uppercase">Instant Potential</p>
                        <p class="text-5xl font-black text-amber-500">${solar.potential_wm2}<span class="text-lg"> W/m²</span></p>
                        <div class="mt-6 p-3 bg-amber-50 rounded-xl text-sm text-amber-800">
                            <strong>System Logic:</strong> Estimates based on ${data.raw.clouds}% cloud cover.
                        </div>
                    </div>
                `));
            }

            if (view === 'admin') {
                container.appendChild(createCard("System Health", `
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">API Gateway</span>
                            <span class="text-green-600 font-bold">Online</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Python Engine</span>
                            <span class="text-green-600 font-bold">Active</span>
                        </div>
                        <div class="mt-4 bg-slate-900 rounded-lg p-3 text-xs text-green-400 font-mono">
                            [LOG] GET /api/weather 200 OK<br>
                            [LOG] Processing Solar Logic...<br>
                            [LOG] Dispatching Agri Alerts...
                        </div>
                    </div>
                `));
            }
        }

        function createCard(title, html) {
            const div = document.createElement('div');
            div.className = "glass p-6 sm:p-8 rounded-3xl border border-white/5 hover:border-blue-500/30 transition duration-500 group";
            div.innerHTML = `
                <h3 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em] mb-6 group-hover:text-blue-400 transition">${title}</h3>
                <div class="text-slate-200">${html}</div>
            `;
            return div;
        }

        // Initial Load
        window.onload = fetchWeather;
    </script>
</body>
</html>
