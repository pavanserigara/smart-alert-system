# Architecture

## Design Pattern: Hybrid Microservices
The system uses a decoupled architecture where the heavy lifting (weather intelligence, solar potential logic, API aggregation) is offloaded to a **Python Backend**, while the user interface and content management are handled by a **PHP Frontend**.

### 1. Data Flow
- **User Action**: User searches for a city or loads a dashboard.
- **Client Request**: Frontend (JS) sends an async fetch request to the Python Engine.
- **Engine Processing**: Python Engine calls OpenWeatherMap, applies agro/energy logic (e.g., solar potential), and returns JSON.
- **UI Update**: Frontend renders JSON data into glassmorphic cards.

### 2. State Management
- **Persistence**: MySQL database stores persistent data (Users, Blogs, Alerts).
- **Session**: PHP `$_SESSION` manages authentication and role-based access.
- **Transient Data**: Live weather data is not stored; it is fetched and rendered on-demand for real-time accuracy.

### 3. Security Architecture
- **RBAC**: Role-Based Access Control enforced at the PHP level.
- **Admin Command Core**: Dedicated `admin.php` for critical system actions (broadcasts, user termination).
- **Backend Isolation**: Python engine exposes only read-only status/weather endpoints to the public.
