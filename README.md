# PG Spotter Project

A web application for finding and listing PG accommodations.

## Project Structure

```
pg_spotter_project/
├── assets/
│   ├── css/
│   │   ├── header.css      # Styles for header and navigation
│   │   └── pg-details.css  # Styles for PG details page
│   └── js/
│       └── script.js       # Main JavaScript functionality
├── includes/
│   ├── config.php         # Database and site configuration
│   ├── header.php         # Common header template
│   └── footer.php         # Common footer template
└── uploads/
    └── pg_photos/         # PG listing photos
```

## User id & Passwords
  Admin:- admin@pgspotter.com         Password:- '123456'
  Users:-     userid                  Password
          1. doman@gmail.com          123456
          2. aman@gmail.com           123456
## Code Organization

### CSS
- `header.css`: Contains styles for the site header, navigation menu, and user profile components
- `pg-details.css`: Contains styles for the gallery, amenities, and other PG listing details

### JavaScript
- `script.js`: Contains global site functionality including:
  - Gallery controls and image navigation
  - Alert message handling
  - Common UI interactions

### PHP Templates
- Header and footer templates are in the includes directory
- Each page-specific PHP file contains its own business logic
- Database operations are centralized in config.php

## Development Guidelines

1. Keep JavaScript modular and well-commented
2. Use external CSS files instead of inline styles
3. Follow consistent naming conventions
4. Keep PHP logic separated from presentation
5. Use prepared statements for database queries
6. Maintain proper security checks and validations