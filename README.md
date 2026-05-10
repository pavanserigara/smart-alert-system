# Smart Weather & Energy Ecosystem

A dual-stack (Python/PHP) application that provides localized weather intelligence, agricultural alerts, and solar energy estimation.

## 🚀 Setup Instructions

### 1. Backend (Python)
- **Install Dependencies**:
  ```bash
  python3 -m venv venv
  source venv/bin/activate
  pip install -r requirements.txt
  ```
- **Run the Engine**:
  ```bash
  python weather_engine.py
  ```
  **Run the php server**:
  ```bash
  php -S localhost:8000
  ```
   
  The backend will run on `http://localhost:5000` and `http://localhost:8000` for php.

### 2. Database (MySQL)
- Create a database named `weather_eco`.
- Import the `schema.sql` file:
  ```bash
  mysql -u root -p weather_eco < schema.sql
  ```
- Configure connection details in `db_connect.php`.

### 3. Frontend (PHP)
- Place the project in your web server root (e.g., `htdocs` for XAMPP).
- Open `login.php` in your browser.
- **Demo Credentials**:
  - Username: `citizen_joe` | `farmer_ted` | `solar_sam` | `admin_main`
  - Password: `password123`

## 📁 Project Structure
- `weather_engine.py`: Flask API & Weather Logic.
- `index.php`: Main Dashboard (Role-based).
- `login.php`: Authentication & Session Management.
- `db_connect.php`: Database connection settings.
- `schema.sql`: Database schema & demo data.
- `requirements.txt`: Python libraries.

## 💡 Smart Logic Features
- **Solar Potential**: Calculated using cloud cover and latitude-based zenith approximation.
- **Agricultural Alerts**: Triggers "Fungal Risk" based on rain/temperature correlations.
- **Travel Suitability**: Suggests indoor/outdoor activities based on visibility and wind speed.
