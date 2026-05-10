# Technology Stack

## Backend
- **Language**: Python 3.x
- **Framework**: Flask
- **Core Libraries**: 
  - `requests`: External API communication
  - `flask-cors`: Cross-Origin Resource Sharing for PHP/Python communication
  - `math`: Solar and atmospheric calculations
  - `time`: System uptime and log tracking

## Frontend
- **Language**: PHP 8.x
- **Styling**: Tailwind CSS (CDN-based)
- **Scripting**: Vanilla JavaScript (ES6+)
- **Design System**: Custom Glassmorphic UI (defined in `assets/css/main.css`)
- **Visuals**: Google Fonts (Inter, Roboto, Outfit), Chart.js for analytics

## Database
- **Engine**: MySQL / MariaDB
- **Interface**: PHP Data Objects (PDO)
- **Schema**: 
  - `users`: Role-based access control (Admin, Farmer, Energy, Citizen)
  - `blogs`: Ecosystem insights feed
  - `emergency_alerts`: Global broadcast system
  - `processing_logs`: Real-time system telemetry

## Development Environment
- **Server**: PHP Built-in Server (Port 8000), Flask Dev Server (Port 5000)
- **Management**: Git for versioning
