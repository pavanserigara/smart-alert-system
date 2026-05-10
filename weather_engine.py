import requests
import math
import time
from flask import Flask, jsonify, request
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Configuration - OpenWeatherMap API Key
API_KEY = "50f3dc95f2ac14ee5ed65251e73c8c5b"
SOLAR_CONSTANT = 1361  # W/m^2
START_TIME = time.time()

@app.route('/api/status', methods=['GET'])
def get_status():
    uptime = int(time.time() - START_TIME)
    return jsonify({
        "status": "online",
        "uptime_seconds": uptime,
        "version": "2.4.0-stable"
    })

def calculate_solar_potential(cloud_cover, lat, dt, sunrise, sunset):
    """
    Enhanced Solar Potential Calculation
    Accounts for cloud cover and Time of Day (Day/Night)
    """
    # 1. Check if it's night
    if dt < sunrise or dt > sunset:
        return 0
        
    # 2. Simplified Zenith approximation based on latitude
    zenith_deg = abs(lat) 
    zenith_rad = math.radians(zenith_deg)
    
    # 3. Calculate potential based on atmospheric attenuation
    potential = (SOLAR_CONSTANT * math.cos(zenith_rad)) * (1 - (cloud_cover / 100))
    
    # Ensure a small minimum during the day even if cloudy
    return round(max(50, potential), 2)

def get_agri_alerts(temp, weather_main, pop):
    """
    Logic for Agricultural Alerts
    - Fungal Risk: Rain + Temp > 20C
    - Crop Security: Rain Probability > 70%
    """
    alerts = []
    if "Rain" in weather_main and temp > 20:
        alerts.append("High Fungal Risk: Warm and wet conditions detected.")
    
    if pop > 0.7:
        alerts.append("Crop Security Warning: High precipitation probability (>70%). Secure harvested crops.")
    elif pop < 0.2 and temp > 25:
        alerts.append("Irrigation Needed: Dry conditions and high heat.")
    else:
        alerts.append("Conditions Stable: No immediate action required.")
        
    return alerts

def get_travel_insights(wind_speed, visibility):
    """
    Logic for Travel Suitability
    """
    if wind_speed > 10 or visibility < 2000:
        return {
            "status": "Cautious",
            "suggestion": "Indoor activities recommended due to high wind/low visibility.",
            "type": "Indoor"
        }
    return {
        "status": "Excellent",
        "suggestion": "Perfect weather for outdoor exploration and travel.",
        "type": "Outdoor"
    }

@app.route('/')
def index():
    return "<h1>Weather Engine API is Running</h1><p>Visit <b>http://localhost:8000/login.php</b> for the Dashboard UI.</p>"

@app.route('/api/weather', methods=['GET'])
def get_weather_data():
    city = request.args.get('city')
    lat_param = request.args.get('lat')
    lon_param = request.args.get('lon')
    
    # 1. Build URL based on available params
    if lat_param and lon_param:
        url = f"https://api.openweathermap.org/data/2.5/weather?lat={lat_param}&lon={lon_param}&appid={API_KEY}&units=metric"
    else:
        city = city if city else "New York"
        url = f"https://api.openweathermap.org/data/2.5/weather?q={city}&appid={API_KEY}&units=metric"
    
    try:
        response = requests.get(url)
        data = response.json()
        if response.status_code != 200:
            return jsonify({"error": f"API Error: {data.get('message', 'Unknown error')}"}), response.status_code
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    
    # 2. Extract Base Metrics
    temp = round(data['main']['temp'])
    cloud_cover = data['clouds']['all']
    weather_main = data['weather'][0]['main']
    wind_speed = data['wind']['speed']
    visibility = data.get('visibility', 10000)
    lat = data['coord']['lat']
    
    # Time Data for Day/Night Check
    dt = data.get('dt', 0)
    sunrise = data.get('sys', {}).get('sunrise', 0)
    sunset = data.get('sys', {}).get('sunset', 0)
    
    # Simulated POP (Probability of Precipitation)
    pop = 0.8 if "Rain" in weather_main else 0.1

    # 3. Process Smart Logic
    solar_potential = calculate_solar_potential(cloud_cover, lat, dt, sunrise, sunset)
    agri_alerts = get_agri_alerts(temp, weather_main, pop)
    travel = get_travel_insights(wind_speed, visibility)

    # 4. Return Structured Insights
    return jsonify({
        "location": data['name'],
        "is_demo": False,
        "raw": {
            "temp": temp,
            "clouds": cloud_cover,
            "condition": weather_main,
            "wind": wind_speed,
            "visibility": visibility
        },
        "insights": {
            "solar": {
                "potential_wm2": solar_potential,
                "status": "Optimal" if solar_potential > 600 else ("Moderate" if solar_potential > 0 else "Night")
            },
            "agri": {
                "alerts": agri_alerts,
                "status": "Alert" if "Risk" in str(agri_alerts) else "Healthy"
            },
            "travel": travel
        }
    })

@app.route('/api/forecast', methods=['GET'])
def get_forecast():
    city = request.args.get('city', 'New York')
    lat = request.args.get('lat')
    lon = request.args.get('lon')
    
    if lat and lon:
        url = f"https://api.openweathermap.org/data/2.5/forecast?lat={lat}&lon={lon}&appid={API_KEY}&units=metric"
    else:
        url = f"https://api.openweathermap.org/data/2.5/forecast?q={city}&appid={API_KEY}&units=metric"
        
    try:
        response = requests.get(url)
        data = response.json()
        
        if response.status_code != 200:
            return jsonify({"error": "Forecast API Error"}), response.status_code
        
        # Process 5-day forecast (3-hour intervals) into daily summaries
        forecast_list = []
        seen_days = set()
        
        for item in data['list']:
            date = item['dt_txt'].split(' ')[0]
            if date not in seen_days:
                seen_days.add(date)
                forecast_list.append({
                    "date": date,
                    "temp": round(item['main']['temp']),
                    "condition": item['weather'][0]['main'],
                    "icon": item['weather'][0]['icon']
                })
        
        return jsonify(forecast_list[:6])
    except Exception as e:
        return jsonify({"error": str(e)}), 500

if __name__ == '__main__':
    print("Weather Engine starting on http://localhost:5000")
    app.run(host='0.0.0.0', port=5000, debug=True)
