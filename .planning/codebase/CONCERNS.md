# Concerns

## Security
- **API Key Exposure**: The OpenWeatherMap `API_KEY` is currently hardcoded in `weather_engine.py`. It should be moved to an `.env` file.
- **CSRF Protection**: Dashboard forms (Admin alert/blog forms) currently lack CSRF tokens.
- **Input Validation**: While PDO handles SQL injection, additional server-side validation for form inputs (e.g., blog length, character types) is minimal.

## Performance
- **Synchronous Backend**: The Python Flask engine handles requests synchronously. Under heavy load, this could lead to latency.
- **Lack of Caching**: Every search triggers a fresh API call. Implementing a 10-minute cache for city weather would reduce API usage and improve speed.
- **DB Connection**: The database connection in `db_connect.php` uses `die()` on failure, which provides a poor user experience in production.

## Reliability
- **Internet Dependency**: The system has no offline fallback if the OpenWeatherMap API is unreachable.
- **Python Engine Dependency**: If the Python engine (Port 5000) crashes, all dashboards will fail to show weather data, though the PHP pages will still load.
