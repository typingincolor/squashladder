# Club Squash Ladder

A modern web application for managing squash club ladders, built with Laravel 10.

## Features

- **User Authentication** - Secure registration and login system with password reset
- **Player Rankings** - Dynamic ladder system with automatic rank updates
- **Challenge System** - Players can challenge opponents and track challenge status
- **Result Entry** - Record match results with automatic rank swapping for winners
- **Profile Management** - Players can update their information and contact details
- **Responsive Design** - Modern, mobile-friendly interface that works on all devices

## Requirements

- PHP 8.1 or higher
- Composer
- SQLite (or MySQL/PostgreSQL)

## Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd squashladder
```

2. Install dependencies:
```bash
composer install
```

3. Copy the environment file:
```bash
cp .env.example .env
```

4. Generate application key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate
```

6. (Optional) Seed the database with sample data:
```bash
php artisan db:seed
```

7. Start the development server:
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Usage

### Getting Started

1. Register a new account or log in
2. Complete your player profile with contact information
3. View the ladder rankings on the dashboard
4. Challenge other players by clicking the "Challenge" button next to their name
5. Accept or reject challenges from your dashboard
6. Enter match results after completing a game

### Challenge Workflow

- **Pending**: Challenge has been sent and awaits opponent's response
- **Accepted**: Opponent has accepted, match can be played
- **Rejected**: Opponent has declined the challenge
- **Completed**: Result has been entered and ranks updated (if applicable)

### Ranking System

- Players are ranked from 1 (highest) downward
- When entering results, choose from: "beat", "drew with", or "lost to"
- Ranks swap when an upset occurs (lower-ranked player wins)
- Rankings remain unchanged for expected outcomes or draws
- Rankings are automatically updated when results are entered

## Development

### Running Tests

```bash
php artisan test
```

### Code Structure

- `app/Http/Controllers/` - Application controllers
- `app/Models/` - Eloquent models (User, Player, Challenge, Result)
- `resources/views/` - Blade templates
- `routes/web.php` - Application routes
- `database/migrations/` - Database schema

### Documentation

See `CLAUDE.md` for detailed development documentation and architecture notes.

## License

This project is open-source software.

## Support

For issues and questions, please open an issue on the GitHub repository.
