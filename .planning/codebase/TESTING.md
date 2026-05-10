# Testing

## Current Strategy: Manual UAT
The system is currently verified through manual User Acceptance Testing (UAT):
- **Weather Accuracy**: Verifying search results against the OpenWeatherMap dashboard.
- **Admin Control**: Manual verification of alert broadcasting and blog posting.
- **Cross-Role Access**: Ensuring login redirects and role-based permissions function as intended.

## Real-Time Verification
- **System Heartbeat**: Every dashboard features a console showing `200 OK` pings for every API request.
- **Engine Logs**: The `processing_logs` table provides a historical record of all critical backend actions.

## Future Recommendations
- **Python**: Implement `pytest` for unit testing atmospheric logic (e.g., solar potential calculations).
- **PHP**: Implement `PHPUnit` for database model verification.
- **E2E**: Use Playwright or Selenium to verify the full weather search-to-render flow.
