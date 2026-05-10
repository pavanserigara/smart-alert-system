<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'energy') {
    header("Location: login.php");
    exit;
}
require_once 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Helios - Energy Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body class="p-4 md:p-8">
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

    <!-- Global Emergency Alert -->
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
        <header class="flex flex-col lg:flex-row justify-between items-center mb-12 gap-6 sm:gap-8">
            <div class="text-center lg:text-left">
                <h2 class="text-amber-400 font-bold tracking-widest uppercase text-[10px] sm:text-xs mb-1">Solar Analytics Engine</h2>
                <h1 class="text-3xl sm:text-4xl font-black gradient-text">Energy Nexus</h1>
            </div>
            <div class="search-container flex-1 w-full max-w-xl">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="cityInput" placeholder="Search for location weather..." onchange="updateDashboard()" class="search-input w-full">
                <button onclick="useMyLocation()" class="text-amber-500 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                </button>
            </div>
            <div class="flex items-center justify-between w-full lg:w-auto gap-6">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-tighter">Node: <?= $_SESSION['username'] ?></p>
                <a href="login.php" class="bg-red-500/10 hover:bg-red-500/20 text-red-500 px-3 py-1 rounded-lg text-[10px] font-bold border border-red-500/20 transition">LOGOUT</a>
            </div>
        </header>

        <!-- Main Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="glass-card p-8 col-span-1 md:col-span-2">
                <div class="flex justify-between items-start mb-6">
                    <h3 class="text-xl font-bold">Atmospheric Irradiance</h3>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6" id="weather-stats">
                    <!-- Dynamic Stats -->
                </div>
                <div class="mt-10 p-6 bg-slate-900/50 rounded-2xl border border-white/5" id="generation-report">
                    <h4 class="text-amber-400 font-bold text-xs uppercase mb-3">Yield Intelligence Report</h4>
                    <p id="yield-analysis" class="text-slate-300 text-sm italic leading-relaxed">Analyzing luminosity vectors...</p>
                </div>
            </div>

            <div class="glass-card p-8 bg-gradient-to-br from-amber-600/20 to-orange-600/20">
                <h3 class="text-xl font-bold mb-4">Instant Potential</h3>
                <div class="flex flex-col items-center justify-center py-4">
                    <p class="text-6xl font-black text-white" id="solar-value">--</p>
                    <p class="text-xs font-bold text-amber-500 uppercase mt-2">Watts / m²</p>
                    <span id="efficiency-badge" class="mt-4 px-3 py-1 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded-full text-[10px] font-bold tracking-widest">Calculating...</span>
                </div>
            </div>
        </div>

        <!-- Solar Logic Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="glass-card p-6">
                <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 3v1m0 16v1m9-9h-1M4 9h-1m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h4 class="font-bold text-lg mb-2">Peak Estimate</h4>
                <p id="peak-est" class="text-slate-400 text-sm leading-relaxed">-- kWh hourly peak est.</p>
            </div>

            <div class="glass-card p-6">
                <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h4 class="font-bold text-lg mb-2">Grid Offset</h4>
                <p id="grid-offset" class="text-slate-400 text-sm leading-relaxed">--% estimated household reduction.</p>
            </div>

            <div class="glass-card p-6">
                <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center text-green-400 mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h4 class="font-bold text-lg mb-2">System Health</h4>
                <p class="text-slate-400 text-sm leading-relaxed">Dynamic Zenith Correction Active.</p>
            </div>
        </div>

        <!-- 7-Day Forecast Strip -->
        <div class="glass-card p-8 mb-12">
            <h3 class="text-xl font-bold mb-8 text-amber-400">7-Day Generation Outlook</h3>
            <div id="forecast-strip" class="flex justify-between items-center gap-4 overflow-x-auto pb-4">
                <!-- Forecast items injected -->
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Smart Nexus (Coming Soon) -->
            <div class="lg:col-span-1 glass-card p-8 border-l-4 border-amber-500">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Grid Nexus</h3>
                    <span class="text-[10px] bg-amber-500/20 text-amber-500 px-2 py-1 rounded font-bold uppercase tracking-tighter">Coming Soon</span>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-3 bg-slate-900/50 rounded-xl opacity-50 grayscale">
                        <span class="text-sm font-medium">Solar Tracker Motors</span>
                        <div class="w-10 h-5 bg-slate-700 rounded-full"></div>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-slate-900/50 rounded-xl opacity-50 grayscale">
                        <span class="text-sm font-medium">Battery Dispatch</span>
                        <div class="w-10 h-5 bg-slate-700 rounded-full"></div>
                    </div>
                </div>
            </div>

            <!-- Ecosystem Feed (Blogs) -->
            <div class="md:col-span-2 lg:col-span-2 glass-card p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-amber-400">Energy Insights</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="text-[10px] text-slate-500 font-mono font-bold uppercase tracking-widest">Grid Connected</span>
                    </div>
                </div>
                <div id="blog-feed" class="space-y-4 mb-8">
                    <!-- Blogs injected -->
                </div>

                <!-- NEW: Software Heartbeat Console -->
                <div class="bg-black/50 rounded-xl border border-white/5 p-4 font-mono text-[10px] overflow-hidden">
                    <div class="flex items-center justify-between mb-2 pb-2 border-b border-white/5">
                        <span class="text-amber-500 font-bold tracking-widest uppercase">Grid_Monitor.log</span>
                        <span class="text-slate-600">STABLE_v2.0</span>
                    </div>
                    <div id="system-logs" class="space-y-1 h-24 overflow-y-auto text-amber-500/70">
                        <p>> Handshake with Solar Array Matrix...</p>
                        <p>> Telemetry stream initialized 127.0.0.1:5000</p>
                        <p class="text-white">> GET /api/weather 200 OK</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function useMyLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    updateDashboard(lat, lon); 
                }, () => updateDashboard());
            } else {
                updateDashboard();
            }
        }

        async function updateDashboard(lat = null, lon = null) {
            const logContainer = document.getElementById('system-logs');
            if (logContainer) {
                const p = document.createElement('p');
                p.className = "text-white";
                p.innerText = `> GET /api/weather ${new Date().toLocaleTimeString()} ... 200 OK`;
                logContainer.prepend(p);
                if (logContainer.children.length > 8) logContainer.lastElementChild.remove();
            }

            const cityInput = document.getElementById('cityInput').value;
            let baseUrl = `http://127.0.0.1:5000/api`;
            let params = lat && lon ? `?lat=${lat}&lon=${lon}` : `?city=${cityInput}`;
            
            try {
                const resCurrent = await fetch(`${baseUrl}/weather${params}`);
                const data = await resCurrent.json();
                render(data);

                const resForecast = await fetch(`${baseUrl}/forecast${params}`);
                const forecastData = await resForecast.json();
                renderForecast(forecastData);
            } catch (e) { console.error(e); }
        }

        function render(data) {
            document.getElementById('cityInput').value = data.location;
            const solar = data.insights.solar;
            const potential = solar.potential_wm2;
            
            document.getElementById('solar-value').innerText = Math.round(potential);
            document.getElementById('efficiency-badge').innerText = potential > 600 ? "HIGH EFFICIENCY" : "LOW IRRADIANCE";
            
            document.getElementById('weather-stats').innerHTML = `
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Temp</p><p class="stat-value text-white">${data.raw.temp}°C</p></div>
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Clouds</p><p class="stat-value text-amber-400">${data.raw.clouds}%</p></div>
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Wind</p><p class="stat-value text-green-400">${data.raw.wind}m/s</p></div>
                <div><p class="text-slate-400 text-xs font-bold uppercase mb-1">Viz</p><p class="stat-value text-purple-400">${(data.raw.visibility/1000).toFixed(1)}k</p></div>
            `;

            const kwh = (potential / 1000).toFixed(2);
            document.getElementById('peak-est').innerText = kwh + " kWh hourly peak est.";
            document.getElementById('grid-offset').innerText = Math.round((kwh * 5.2 / 30) * 100) + "% estimated household reduction.";
            
            document.getElementById('yield-analysis').innerText = `Atmospheric density at ${data.raw.clouds}% allows for ${potential}W/m² irradiance. ${potential > 700 ? 'Optimal for battery charging.' : 'Maintain grid bypass for essential loads.'}`;
        }

        function renderForecast(forecast) {
            const container = document.getElementById('forecast-strip');
            container.innerHTML = forecast.map(day => `
                <div class="flex-shrink-0 text-center px-6 border-r border-white/5 last:border-none">
                    <p class="text-xs font-bold text-slate-500 uppercase mb-2">${new Date(day.date).toLocaleDateString(undefined, {weekday: 'short'})}</p>
                    <div class="text-2xl mb-2">${getWeatherIcon(day.condition)}</div>
                    <p class="text-xl font-bold text-white">${Math.round(day.temp)}°</p>
                    <p class="text-[10px] text-amber-500 uppercase font-bold mt-1">${day.condition}</p>
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
                if (blogs.length === 0) {
                    container.innerHTML = `<p class="text-slate-500 italic text-sm">No new insights from the Admin yet.</p>`;
                    return;
                }
                container.innerHTML = blogs.map(b => `
                    <div class="p-4 bg-slate-900/50 rounded-xl border border-white/5">
                        <h4 class="text-amber-400 font-bold mb-1">${b.title}</h4>
                        <p class="text-slate-300 text-sm leading-relaxed">${b.content}</p>
                    </div>
                `).join('');
            } catch (e) {}
        }

        async function checkAlerts() {
            try {
                const res = await fetch('get_alerts.php');
                const alerts = await res.json();
                if (alerts.length > 0) {
                    document.getElementById('emergency-bar').classList.remove('hidden');
                    document.getElementById('alert-text').innerText = alerts[0].message;
                }
            } catch (e) {}
        }

        window.onload = () => {
            useMyLocation();
            fetchBlogs();
            checkAlerts();
            setInterval(updateDashboard, 60000);
        };
    </script>
</body>
</html>
