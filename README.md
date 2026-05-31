# Ladybug's Gym

A web application for managing fitness classes, trainers, members, and equipment at a gym.

## First Delivery

This is the first delivery focusing on **HTML + CSS** for some pages of the application.

## How to View the Project

### Quick Start (Static HTML)

The HTML files can be accessed directly in any web browser. The project pages are located in the `src/html/` directory:

- **Home**: `src/html/index.html`
- **Classes Schedule**: `src/html/schedule.html`
- **Profile**: `src/html/profile.html`
- **Login**: `src/html/login.html`


Once opened, the navigation menu in the header allows browsing between the different pages.

### CSS Organization

The styling is organized into four modular files following a layered approach:

 1. **base.css** — Foundation


 2. **components.css** — Reusable Components


3. **layout.css** — Structural Layout


4. **pages.css** — Page-Specific Styles

## Features

**All users:**
- [X] Register a new account.
- [X] Log in and out.
- [X] Edit their profile, including name, username, password, and profile photo.

**Members:**
- [X] Browse the schedule of available fitness classes, filtering by type, trainer, day, or time.
- [X] Enroll in and cancel enrollment from upcoming classes, subject to capacity limits.
- [X] View trainer profiles, including their specializations and the classes they teach.
- [X] Check the current availability of equipment in the main training area.
- [X] Leave ratings and reviews for classes they have attended.

**Trainers:**
- [X] Manage their public profile, including bio, specializations, and certifications.
- [X] View the roster of members enrolled in their classes.
- [X] Track and manage their assigned class schedule.

**Admins:**
- [X] Manage members and trainers (create, update, and deactivate accounts).
- [X] Manage the class catalog (create, edit, and remove classes) and assign trainers to them.
- [X] Manage equipment in the main training area (add, update availability status, and remove items).
- [X] Elevate a user to admin status.
- [X] Oversee and ensure the smooth operation of the entire system.

**Extra:**
- [X] Define tiered membership plans (e.g., basic, premium) with different access levels, and allow members to subscribe to or upgrade their plan.
- [X] Member can request nutrition plans to trainers.
- [X] Users can give feedback to whole gym.

## Running

    sqlite3 database/database.db < database/database.sql
    php -S localhost:9000

## Credentials

**Admin:**
- User: adminuser
- Pass: admin123

**Trainer:**
- User: migueltrainer
- Pass: trainer123

**Normal User:**
- User: joaosilva
- Pass: hashedpass1
