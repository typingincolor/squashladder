# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 10 squash ladder management system with session-based authentication. Players can log in, view ladder standings, challenge other players, accept/reject challenges, and enter match results. The system automatically updates player rankings based on match outcomes.

## Development Commands

### Running the Application
```bash
# Start development server
php artisan serve

# Access at http://localhost:8000
```

### Database Management
```bash
# Run migrations
php artisan migrate

# Refresh database and seed test data (14 players)
php artisan migrate:fresh --seed

# Use SQLite for local development (already configured in .env)
DB_CONNECTION=sqlite
DB_DATABASE=/full/path/to/database/database.sqlite
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/LadderTest.php

# Run specific test method
php artisan test --filter testPlayerCanViewLadder

# Tests use in-memory SQLite database (configured in phpunit.xml)
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Clear application caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Tinker (REPL)
```bash
# Useful for testing models and relationships
php artisan tinker

# Example: Get first player with user relationship
App\Models\Player::with('user')->first()
```

## Architecture

### Authentication & Authorization

**One-to-One User-Player Relationship:**
- `User` model handles authentication (Laravel's built-in Authenticatable)
- `Player` model contains game-related data (rank, match history)
- Each player has exactly one user account
- All test players use password: `password`

**Access Control:**
- Guests can only access login and password reset pages
- Authenticated users are redirected to dashboard (not a generic ladder view)
- Players can only edit their own profile
- Only challengers can enter results for accepted challenges
- Only opponents can accept/reject challenges

### Challenge Workflow State Machine

Challenges follow this state flow:
1. **PENDING**: Created, waiting for opponent response
2. **ACCEPTED**: Opponent accepted, waiting for challenger to enter result
3. **REJECTED**: Opponent rejected, can be deleted by either party
4. **COMPLETED**: Result entered, challenge hidden from both players

Key rules:
- Only the **challenger** can enter the result (not the opponent)
- Only the **opponent** can accept/reject a pending challenge
- Completed challenges automatically disappear from both players' views
- When entering a result from a challenge, player dropdowns are locked/readonly

### Ranking System

**Rank Swapping Logic** (`Player::swapRankWith()`):
- Uses database transactions to ensure atomicity
- Temporarily uses negative rank values (-1, -2) to avoid unique constraint violations
- Only swaps ranks when a lower-ranked player beats a higher-ranked player
- Result processing is automatic via `Result::processRankings()`

**Result Descriptions:**
- ID 1: "beat" (player1 won)
- ID 2: "drew with" (draw, no rank change)

When player1 beats player2 and player2.rank < player1.rank, ranks swap.

### Database Schema

**Core Tables:**
- `users`: Authentication (email, password, remember_token)
- `players`: Game data (forename, surname, email, rank, user_id FK)
- `results`: Match results (player1_id, player2_id, result_description_id, match_date)
- `challenges`: Challenge state (challenger_id, opponent_id, message, status, result_id FK)
- `result_descriptions`: "beat" or "drew with"

**Important Indexes:**
- `players.rank` (unique) - enables efficient ladder queries
- `players.user_id` (FK with cascade delete)
- `challenges.challenger_id`, `challenges.opponent_id`, `challenges.status`

### Key Eloquent Relationships

**Player Model:**
```php
// User relationship (authentication)
player->user()

// Challenge relationships
player->challengesSent()      // as challenger
player->challengesReceived()  // as opponent

// Results relationships
player->resultsAsPlayer1()
player->resultsAsPlayer2()
player->allResults()          // combined, ordered by date desc
```

**Challenge Model:**
```php
challenge->challenger()  // Player who sent challenge
challenge->opponent()    // Player who received challenge
challenge->result()      // Linked Result (when completed)
```

### Frontend Architecture

**No JavaScript Framework:**
- Pure Blade templates with server-side rendering
- Forms use standard POST/PUT/PATCH/DELETE with CSRF tokens
- Uses Laravel's `@auth` directive for conditional rendering

**Styling:**
- Modern gradient design with purple theme
- Fully responsive (desktop → tablet → mobile)
- Custom CSS in `public/css/app.css` (no build process)
- Tables transform to card layout on mobile (<480px)

**Navigation:**
- Site title is clickable and returns to dashboard
- Only "My Profile" and "Logout" in nav (no redundant Dashboard link)
- Challenge buttons appear directly on ladder table

### Configuration

**App Name:**
- Configurable via `APP_NAME` in `.env` (default: "Club Squash Ladder")
- Used throughout views via `config('app.name')`

**Mail:**
- Development uses `MAIL_MAILER=log` (emails written to logs)
- Challenge notifications sent via `Mail::raw()` when challenges are created

### Testing Strategy

**Factory Pattern:**
- `PlayerFactory` automatically creates linked User accounts
- Use `Player::factory()->rank(1)->create()` for ranked players
- `ChallengeFactory` has state methods: `pending()`, `accepted()`, `rejected()`, `completed()`

**Database:**
- Tests use `RefreshDatabase` trait with in-memory SQLite
- Seeders create 14 players with random names via Faker

### Common Gotchas

1. **Rank Swapping:** Always use `Player::swapRankWith()` in transactions, never update ranks directly
2. **Challenge Authorization:** Check both `challenger_id` and `opponent_id` - they have different permissions
3. **Form Classes:** All inputs must have `form-control` class for consistent styling
4. **Route Names:** Use `dashboard` not `ladder.index` (old route removed)
5. **Player Selection in Results:** When challenge_id is present, render readonly inputs instead of dropdowns

### File Structure

```
app/
├── Models/
│   ├── User.php              # Authentication model
│   ├── Player.php            # Game model with rank logic
│   ├── Challenge.php         # State machine with accept/reject/complete
│   └── Result.php            # With automatic rank processing
├── Http/Controllers/
│   ├── Auth/                 # Login, password reset
│   ├── DashboardController   # Main authenticated view
│   ├── ChallengeController   # Create, accept, reject, delete
│   ├── ResultController      # Enter results, link to challenges
│   └── PlayerController      # Profile editing only
resources/views/
├── layouts/app.blade.php     # Master layout with sticky header
├── auth/                     # Login, password reset forms
├── dashboard.blade.php       # Ladder + challenges + results
├── challenges/create.blade.php
├── results/create.blade.php  # Handles both manual and challenge results
└── profile/edit.blade.php
database/
├── migrations/               # 9 total migrations
├── seeders/
│   ├── PlayerSeeder         # Creates 14 random players with users
│   └── ResultDescriptionSeeder
└── factories/               # Player, User, Challenge, Result factories
```
