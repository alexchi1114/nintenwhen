# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Nintenwhen is a Nintendo game release tracking and prediction platform built with Laravel 12 and Bootstrap 5. It tracks franchise "health" based on release patterns and uses OpenAI GPT-4.1 to predict upcoming game announcements.

## Common Commands

```bash
# Development (Vite)
npm run dev          # Start Vite dev server with HMR
npm run build        # Production build

# Laravel
php artisan migrate  # Run database migrations
php artisan tinker   # Interactive PHP shell
php artisan cache:clear  # Clear application cache

# Testing
./vendor/bin/phpunit              # Run all tests
./vendor/bin/phpunit tests/Unit   # Run unit tests only
./vendor/bin/phpunit --filter=TestName  # Run single test
```

## Architecture

### Core Business Logic

The key concept is **franchise health** - a calculation that determines how "overdue" a franchise is for a new release:

- `Franchise::getStatus()` returns "good", "neutral", "bad", or "dead" based on release frequency
- `Franchise::getR()` calculates risk: `(days_since_last / avg_days_between) * predict_multiplier`
- `Franchise::getFranchisesToWatch()` finds franchises likely to get announcements (neutral/bad status with no pending releases)

### Data Model Relationships

```
Franchise (1) → (M) Game
Franchise (1) → (M) Franchise (self-join via parent_franchise_id)
Game (M) → (M) Tag (pivot: game_tag)
Game (M) → (M) System (pivot: game_system)
```

### Controllers

- **FranchiseController** - Franchise listing, search, and AI analysis endpoints (`/franchise-analysis`, `/franchise-analysis/stream`)
- **HomeController** - Homepage with countdown timers; auto-removes games released >14 days ago
- **GameController** - Game management

### AI Integration

- Uses OpenAI GPT-4.1 via `openai-php/client` with web_search tool
- Endpoints: `/franchise-analysis` (cached JSON) and `/franchise-analysis/stream` (SSE streaming)
- Results cached for 1 day using Laravel's file cache

### Frontend

- Axios for API calls, Bootstrap 5 for layout
- `resources/js/countdown.js` handles release countdown timers
- SASS stylesheets compiled via Vite (`resources/sass/app.scss`)
