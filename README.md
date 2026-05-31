# Ladybug's Gym

A full-stack web application for managing a fitness gym — class schedules, trainer profiles, memberships, equipment tracking, nutrition plans, and administrative dashboards.

## Second Delivery

This is the second delivery, featuring a fully functional PHP + SQLite application with AJAX interactions, role-based dashboards, and a polished black-and-white/red design system.

## Pages

| Page | Description |
|------|-------------|
| `index.php` | Homepage with hero, plan teasers, equipment/nutrition carousels, feedback, Q&A |
| `pages/schedule.php` | Weekly calendar with class modals, AJAX enroll/unenroll, filter bar |
| `pages/plans.php` | Membership plans with live filter, tiered cards (Starter → Elite) |
| `pages/equipment.php` | Equipment availability with search and status filter |
| `pages/nutrition.php` | Nutrition plans with goal/trainer filter, custom plan requests |
| `pages/trainers.php` | Trainer directory with search and detail modals |
| `pages/login.php` | Login form with centered premium card design |
| `pages/register.php` | Registration form matching login page design |
| `pages/profile.php` | User profile with avatar, stats, and edit form |
| `pages/dashboard.php` | Admin/trainer dashboard with user/class/equipment management |

## How to Run

```bash
# Initialize the database (if not already done)
sqlite3 database/database.db < database/database.sql

# Start the development server
php -S localhost:9000
```

Then open `http://localhost:9000` in your browser.

## Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | `adminuser` | `admin123` |
| Trainer | `migueltrainer` | `trainer123` |
| Trainer | `sofiatrainer` | `trainer123` |
| Trainer | `carlostrainer` | `trainer123` |
| Trainer | `anatrai` | `trainer123` |
| Trainer | `pedrotrainer` | `trainer123` |
| Trainer | `lenatrainer` | `trainer123` |
| Member | `joaosilva` | `hashedpass1` |
| Member | `anacosta` | `hashedpass1` |

All trainers share the password `trainer123`. Member passwords are stored as bcrypt hashes.

## Database Schema

The database consists of 11 tables:

- **users** — Members, trainers, and admins with role-based access
- **plans** — Tiered membership plans (Starter, Basic, Premium, Elite, Annual)
- **classes** — Fitness class catalog with difficulty levels and max capacity
- **class_schedule** — Individual class sessions with trainer assignments and timestamps
- **enrollments** — Member enrollment tracking with capacity enforcement
- **reviews** — Post-class ratings and reviews
- **trainer_profiles** — Extended trainer info (bio, specializations, certifications)
- **equipment** — Gym equipment inventory with availability status
- **nutrition_plans** — Member nutrition plan requests linked to trainers
- **feedback** — General gym feedback with ratings
- **bookings** — Personal trainer booking records

## Design System

- **Palette:** Black, dark gray, medium gray, white, and red (#c41e3a) accents only — no green
- **Cards:** `.card` component with 18px border-radius, subtle shadows, hover lift
- **Filters:** Unified `.schedule-filters` bar with gradient background used across all listing pages
- **Auth pages:** Centered single-card layout with smooth input focus transitions
- **Plan cards:** Progressive tier styling — Starter (muted), Premium (red top accent + "Most Popular"), Elite (glowing border + gradient badge), Annual (gold accent + "Best Value")
- **Toast messages:** Top-right auto-dismiss notifications (2s)

## Technical Highlights

- **AJAX enrollment/unenrollment** with XHR, no page reloads
- **CSRF protection** on all state-changing POST actions
- **Session regeneration** after login to prevent fixation
- **AbortController** for race-condition-safe week navigation
- **Live client-side filtering** on plans, equipment, nutrition, and trainers pages
- **Role-based dashboards** with inline editing for admins and trainers
- **Responsive design** with breakpoints at 900px and 500px

## CSS Organization

| File | Purpose |
|------|---------|
| `base.css` | Variables, resets, base typography |
| `components.css` | Reusable components (cards, badges, buttons, forms) |
| `layout.css` | Header, footer, grid containers, navigation |
| `pages.css` | Page-specific styles (plans, equipment, nutrition, etc.) |
| `shared.css` | Shared patterns (page headers, keyframes, helpers) |
| `cards.css` | Card component variants (team, equipment, news, etc.) |
| `schedule.css` | Calendar grid, filter bar, class cards |
| `profile.css` | Profile sidebar and edit form |
| `login.css` | Login page centered card design |
| `register.css` | Registration page (matches login design) |
| `trainers.css` | Trainer directory grid and modals |
| `home.css` | Homepage sections (hero, carousels, teasers) |
