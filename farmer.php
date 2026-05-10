<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'farmer') {
    header("Location: login.php");
    exit;
}
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriIntel - Farmer Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>

<body class="p-4 md:p-8">

    <!-- Global Emergency Alert (Pushed by Admin) -->
    <div id="emergency-bar" class="hidden max-w-7xl mx-auto mb-8 animate-fade">
        <div class="bg-red-500/20 border border-red-500/50 p-4 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="badge-emergency">Emergency</span>
                <p id="alert-text" class="text-red-200 font-semibold"></p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-white">✕</button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <header class="flex flex-col lg:flex-row justify-between items-start lg:items-end mb-12 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-blue-400 font-bold tracking-widest uppercase text-[10px] sm:text-sm">Agricultural Intelligence</h2>
                    <span id="live-clock" class="text-slate-500 text-[10px] sm:text-xs font-mono border-l border-white/10 pl-3"></span>
                </div>
                <h1 class="text-4xl sm:text-5xl font-black gradient-text">Field Command</h1>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                <div class="search-container w-full sm:w-auto flex-1">
                    <svg class="w-5 h-5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="cityInput" placeholder="Search for location weather..."
                        onchange="updateDashboard()" class="search-input w-full">
                    <button onclick="useMyLocation()" class="text-blue-400 hover:text-white transition shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                        </svg>
                    </button>
                </div>
                <div class="flex items-center justify-between w-full sm:w-auto gap-4">
                    <p class="text-slate-400 font-medium text-[10px] sm:text-xs whitespace-nowrap">Operator: <span
                            class="text-white font-bold tracking-widest uppercase"><?= $_SESSION['username'] ?></span>
                    </p>
                    <a href="login.php"
                        class="bg-red-500/10 hover:bg-red-500/20 text-red-500 px-3 py-1 rounded-lg text-[10px] font-bold border border-red-500/20 transition">LOGOUT</a>
                </div>
            </div>
        </header>

        <!-- Main Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="glass-card p-8 col-span-1 md:col-span-2">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-bold">Atmospheric Analysis</h3>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6" id="weather-stats">
                    <!-- Dynamic -->
                </div>
                <div class="mt-10 p-6 bg-slate-900/50 rounded-2xl border border-white/5">
                    <h4 class="text-blue-400 font-bold text-xs uppercase mb-3">Automated Field Report</h4>
                    <p id="field-report" class="text-slate-300 text-sm italic leading-relaxed"></p>
                </div>
            </div>

            <div class="glass-card p-8 bg-gradient-to-br from-blue-600/20 to-purple-600/20">
                <h3 class="text-xl font-bold mb-4">Risk Assessment</h3>
                <div id="risk-content" class="space-y-4">
                    <!-- Dynamic -->
                </div>
            </div>
        </div>

        <!-- Agricultural Logic Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Existing Cards ... -->
            <div class="glass-card p-6">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center text-green-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                        </path>
                    </svg>
                </div>
                <h4 class="font-bold text-lg mb-2">Irrigation Control</h4>
                <p id="irrigation-logic" class="text-slate-400 text-sm leading-relaxed">Analyzing soil moisture
                    saturation...</p>
            </div>

            <div class="glass-card p-6">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </div>
                <h4 class="font-bold text-lg mb-2">Precipitation Shield</h4>
                <p id="rain-logic" class="text-slate-400 text-sm leading-relaxed">Scanning cloud density vectors...</p>
            </div>

            <div class="glass-card p-6">
                <div
                    class="w-12 h-12 bg-purple-500/20 rounded-xl flex items-center justify-center text-purple-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                </div>
                <h4 class="font-bold text-lg mb-2">Fungal Protection</h4>
                <p id="fungal-logic" class="text-slate-400 text-sm leading-relaxed">Correlating humidity and temp...</p>
            </div>
        </div>        <!-- NEW: Predictive Forecast Strip -->
        <div class="glass-card mb-8 sm:mb-12">
            <h3 class="text-xl font-bold mb-8">5-Day Field Outlook</h3>
            <div id="forecast-strip" class="flex justify-between items-center gap-4 overflow-x-auto pb-4 hide-scrollbar">
                <!-- Forecast items will be injected here -->
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8 mb-12">
            <!-- Smart Nexus (Scifi Upgrade) -->
            <div class="lg:col-span-1 scifi-iot-card p-6 sm:p-8 rounded-3xl border-l-4 border-amber-500 relative overflow-hidden">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-lg sm:text-xl font-black tracking-tighter scifi-glow-text uppercase">Agri-Sync Command</h3>
                    <span class="text-[9px] sm:text-[10px] bg-amber-500/20 text-amber-500 px-2 py-1 rounded font-black uppercase tracking-tighter border border-amber-500/30">v4.2-PRO</span>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 sm:p-4 bg-white/5 rounded-2xl opacity-40">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="scifi-status-ring">
                                <svg class="w-3 h-3 sm:w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-white">Irrigation Core</p>
                                <p class="text-[7px] sm:text-[8px] font-bold text-slate-500 uppercase tracking-tighter">Sub-Surface: OFFLINE</p>
                            </div>
                        </div>
                        <div class="w-8 h-4 sm:w-10 sm:h-5 bg-slate-800 rounded-full border border-white/10"></div>
                    </div>

                    <div class="flex justify-between items-center p-3 sm:p-4 bg-white/5 rounded-2xl opacity-40">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="scifi-status-ring">
                                <svg class="w-3 h-3 sm:w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-white">Nebula Drone</p>
                                <p class="text-[7px] sm:text-[8px] font-bold text-slate-500 uppercase tracking-tighter">Payload: LOCKED</p>
                            </div>
                        </div>
                        <div class="w-8 h-4 sm:w-10 sm:h-5 bg-slate-800 rounded-full border border-white/10"></div>
                    </div>

                    <div class="flex justify-between items-center p-3 sm:p-4 bg-white/5 rounded-2xl opacity-40">
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="scifi-status-ring">
                                <svg class="w-3 h-3 sm:w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs font-black uppercase tracking-widest text-white">Soil Bio-Scanner</p>
                                <p class="text-[7px] sm:text-[8px] font-bold text-slate-500 uppercase tracking-tighter">Link: STANDBY</p>
                            </div>
                        </div>
                        <div class="w-8 h-4 sm:w-10 sm:h-5 bg-slate-800 rounded-full border border-white/10"></div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-white/5 text-center">
                    <p class="text-[8px] sm:text-[9px] font-bold text-slate-600 uppercase tracking-[0.2em] animate-pulse">Establishing Deep-Space Link...</p>
                </div>
            </div>

            <!-- Ecosystem Feed (Blogs) -->
            <div class="md:col-span-2 lg:col-span-2 glass-card p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Ecosystem Insights</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] text-slate-500 font-mono font-bold uppercase tracking-widest">Sensors
                            Online</span>
                    </div>
                </div>
                <div id="blog-feed" class="space-y-4 mb-8">
                    <!-- Blogs will be injected here -->
                </div>

                <!-- NEW: Software Heartbeat Console -->
                <div class="bg-black/50 rounded-xl border border-white/5 p-4 font-mono text-[10px] overflow-hidden">
                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-white/5">
                        <span class="text-blue-400 font-bold tracking-widest uppercase">System_Heartbeat.log</span>
                        <span class="text-slate-600">STABLE_v2.0</span>
                    </div>
                    <div id="system-logs" class="space-y-1 h-24 overflow-y-auto text-green-500/70">
                        <p>> Initializing local sensor handshake...</p>
                        <p>> Connection established with 127.0.0.1:5000</p>
                        <p class="text-blue-400">> GET /api/weather 200 OK</p>
                        <p class="text-blue-400">> GET /api/forecast 200 OK</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').innerText = now.toLocaleTimeString() + " | " + now.toLocaleDateString();
        }
        setInterval(updateClock, 1000);
        updateClock();

        function useMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    updateDashboard(lat, lon);
                }, () => {
                    updateDashboard(); // Fallback to default city if permission denied
                });
            } else {
                updateDashboard();
            }
        }

        async function updateDashboard(lat = null, lon = null) {
            const logContainer = document.getElementById('system-logs');
            if (logContainer) {
                const p = document.createElement('p');
                p.className = "text-blue-400";
                p.innerText = `> GET /api/weather ${new Date().toLocaleTimeString()} ... 200 OK`;
                logContainer.prepend(p);
                if (logContainer.children.length > 8) logContainer.lastElementChild.remove();
            }

            const cityInput = document.getElementById('cityInput').value;
            let baseUrl = `http://127.0.0.1:5000/api`;
            let params = lat && lon ? `?lat=${lat}&lon=${lon}` : `?city=${cityInput}`;

            try {
                // Fetch Current
                const resCurrent = await fetch(`${baseUrl}/weather${params}`);
                const data = await resCurrent.json();
                render(data);

                // Fetch Forecast
                const resForecast = await fetch(`${baseUrl}/forecast${params}`);
                const forecastData = await resForecast.json();
                renderForecast(forecastData);
            } catch (e) { console.error(e); }
        }

        function render(data) {
            document.getElementById('cityInput').value = data.location;
            document.getElementById('weather-stats').innerHTML = `
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Temperature</p><p class="stat-value text-white">${data.raw.temp}°C</p></div>
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Cloud Density</p><p class="stat-value text-blue-400">${data.raw.clouds}%</p></div>
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Wind Speed</p><p class="stat-value text-green-400">${data.raw.wind}m/s</p></div>
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Visibility</p><p class="stat-value text-purple-400">${(data.raw.visibility / 1000).toFixed(1)}k</p></div>
            `;

            // NEW: Enhanced Crop Intelligence
            let advice = "";
            let needs = "";

            if (data.raw.temp > 30) {
                advice = "CRITICAL: High heat stress detected. Recommend heat-resistant crops like Sorghum or Sunflower.";
                needs = "Increased Nitrogen (N) for stem strength and 2x irrigation cycles.";
            } else if (data.raw.temp < 15) {
                advice = "COOL TEMPS: Ideal for Wheat, Peas, or Mustard.";
                needs = "Potassium (K) enrichment for frost resistance and controlled moisture.";
            } else {
                advice = "OPTIMAL RANGE: Perfect for Rice, Maize, or Cotton.";
                needs = "Standard balanced N-P-K fertilization and regular weeding.";
            }

            if (data.raw.clouds > 70) {
                needs += " | Low sunlight: Reduce water to prevent root rot.";
            }

            document.getElementById('field-report').innerHTML = `
                <div class="space-y-4">
                    <p class="text-blue-400 font-bold tracking-widest text-[10px] uppercase">Live Field Analysis</p>
                    <p class="text-slate-200 leading-relaxed">${data.location} is currently at ${data.raw.temp}°C with ${data.raw.condition.toLowerCase()} skies.</p>
                    <div class="p-4 bg-blue-500/10 rounded-xl border border-blue-500/20">
                        <p class="text-white font-bold text-sm mb-1">Strategic Advice</p>
                        <p class="text-slate-300 text-xs">${advice}</p>
                    </div>
                    <div class="p-4 bg-green-500/10 rounded-xl border border-green-500/20">
                        <p class="text-white font-bold text-sm mb-1">Plant Vitality Needs</p>
                        <p class="text-slate-300 text-xs">${needs}</p>
                    </div>
                </div>
            `;

            const agri = data.insights.agri;
            document.getElementById('risk-content').innerHTML = agri.alerts.map(a => `
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-900/50 border border-white/5">
                    <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    <span class="text-sm font-medium text-slate-200">${a}</span>
                </div>
            `).join('');

            document.getElementById('irrigation-logic').innerText = data.raw.temp > 25 ? "High evaporation detected. Increasing water cycles." : "Moisture levels stable. Scheduled watering active.";
            document.getElementById('rain-logic').innerText = data.raw.clouds > 60 ? "Heavy cloud cover. Secure harvested yields immediately." : "Clear horizons. Optimal for field operations.";
            document.getElementById('fungal-logic').innerText = agri.status === 'Alert' ? "CRITICAL: Warm/Wet detected. Deploy bio-defenses." : "Low risk. Pathogen monitoring inactive.";
        }

        function renderForecast(forecast) {
            const container = document.getElementById('forecast-strip');
            if (!container) return;
            container.innerHTML = forecast.map(day => `
                <div class="flex-shrink-0 text-center px-6 border-r border-white/5 last:border-none">
                    <p class="text-xs font-bold text-slate-500 uppercase mb-2">${new Date(day.date).toLocaleDateString(undefined, { weekday: 'short' })}</p>
                    <div class="text-2xl mb-2">${getWeatherIcon(day.condition)}</div>
                    <p class="text-xl font-bold text-white">${Math.round(day.temp)}°</p>
                    <p class="text-[10px] text-slate-500 uppercase font-bold mt-1">${day.condition}</p>
                </div>
            `).join('');
        }

        function getWeatherIcon(condition) {
            const icons = { 'Clear': '☀️', 'Clouds': '☁️', 'Rain': '🌧️', 'Sunny': '☀️' };
            return icons[condition] || '⛅';
        }

        async function fetchBlogs() {
            try {
                const res = await fetch('get_blogs.php');
                const blogs = await res.json();
                const container = document.getElementById('blog-feed');
                if (!container) return;
                if (blogs.length === 0) {
                    container.innerHTML = `<p class="text-slate-500 italic text-sm">No new insights from the Admin yet.</p>`;
                    return;
                }
                container.innerHTML = blogs.map(b => `
                    <div class="p-4 bg-slate-900/50 rounded-xl border border-white/5">
                        <h4 class="text-blue-400 font-bold mb-1">${b.title}</h4>
                        <p class="text-slate-300 text-sm leading-relaxed">${b.content}</p>
                        <span class="text-[10px] text-slate-500 font-mono mt-2 block">${b.created_at}</span>
                    </div>
                `).join('');
            } catch (e) { }
        }

        async function checkAlerts() {
            try {
                const res = await fetch('get_alerts.php');
                const alerts = await res.json();
                if (alerts.length > 0) {
                    document.getElementById('emergency-bar').classList.remove('hidden');
                    document.getElementById('alert-text').innerText = alerts[0].message;
                }
            } catch (e) { }
        }

        // AUTO-LOCATION ON LOAD
        window.onload = () => {
            useMyLocation();
            checkAlerts();
            fetchBlogs();
            setInterval(updateDashboard, 60000);
        };
    </script>
</body>

</html>