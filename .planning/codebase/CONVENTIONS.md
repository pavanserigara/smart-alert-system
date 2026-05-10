# Conventions

## Python (Backend)
- **Naming**: `snake_case` for all functions and variables.
- **Documentation**: Triple-quote docstrings for all logic functions (`calculate_solar_potential`, etc.).
- **Response Format**: Strict JSON responses via `jsonify`.
- **Config**: Constants defined at the top of the file (e.g., `API_KEY`, `SOLAR_CONSTANT`).

## PHP (Frontend)
- **Database**: Use PDO with Prepared Statements for all queries.
- **Security**: Mandatory `session_start()` and role verification at the top of every dashboard.
- **UI Architecture**: Tailwind CSS for layout, Custom CSS for the "Sci-Fi/Glassmorphic" look.
- **Async**: JavaScript `fetch()` for all dynamic weather data to keep the UI non-blocking.

## Version Control
- **Commits**: Focused, atomic commits per feature or fix.
- **Planning**: Documentation-first workflow via the `.planning/` system.
