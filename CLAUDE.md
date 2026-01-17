# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Nintenwhen is a Nintendo game release tracking and prediction platform built with Laravel 8 and Vue.js. It tracks franchise "health" based on release patterns and uses OpenAI GPT-4.1 to predict upcoming game announcements.

## Common Commands

```bash
# Development
npm run dev          # Build assets for development
npm run watch        # Watch and rebuild on changes
npm run prod         # Production build

# Laravel
php artisan migrate  # Run database migrations
php artisan tinker   # Interactive PHP shell
php artisan cache:clear  # Clear application cache

# Testing
./vendor/bin/phpunit  # Run PHPUnit tests
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

- **FranchiseController** (`app/Http/Controllers/FranchiseController.php`) - Main controller handling franchise listing, search, and AI analysis endpoints
- **HomeController** - Homepage with countdown timers; auto-removes games released >14 days ago
- **GameController** - Game management

### AI Integration

- Uses OpenAI GPT-4.1 with web_search tool enabled
- Endpoints: `/franchise-analysis` (cached JSON) and `/franchise-analysis/stream` (SSE)
- Results cached for 1 day using Laravel's file cache

### Frontend

- jQuery for DOM manipulation, Axios for API calls
- Bootstrap 4 responsive layout
- `resources/js/countdown.js` handles release countdown timers
- SASS stylesheets compiled via Laravel Mix
