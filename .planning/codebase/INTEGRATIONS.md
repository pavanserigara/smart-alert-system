# Integrations

## External Services
- **OpenWeatherMap API**: Primary source for real-time atmospheric data.
  - **Endpoints**: `/weather`, `/forecast`
  - **Usage**: Temperature, humidity, wind speed, visibility, and 5-day predictive data.

## Internal Bridges
- **Python-to-PHP Bridge**:
  - The Python `weather_engine.py` acts as a microservice on port 5000.
  - PHP dashboards fetch data via asynchronous JS `fetch()` calls.
  - CORS is enabled in Python to allow cross-origin requests from the PHP server.

- **PHP-to-Database Bridge**:
  - Standardized PDO connection in `db_connect.php`.
  - Used for authentication, blog management, and alert broadcasting.

- **System Heartbeat**:
  - Real-time telemetry logs written to `processing_logs` table by various system actions.
  - Visualized in the Admin HUD via terminal-style consoles.
