PRAGMA foreign_keys = ON;

/*
Auto Increment is used to automatically generate unique values for a column, 
usually a primary key, so that each record can be identified easily without manual input.
*/

-- USERS (members, trainers, admins)
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password TEXT NOT NULL,
    name TEXT NOT NULL,
    role TEXT CHECK(role IN ('member', 'trainer', 'admin')) NOT NULL DEFAULT 'member',
    profile_photo TEXT,
    active INTEGER DEFAULT 1
);

-- TRAINER PROFILE
CREATE TABLE trainer_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE,
    bio TEXT,
    specializations TEXT,
    certifications TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id)
);

-- FITNESS CLASSES
CREATE TABLE classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL
);

-- CLASS SCHEDULE
CREATE TABLE class_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER,
    trainer_id INTEGER,
    date TEXT,
    time TEXT,
    FOREIGN KEY(class_id) REFERENCES classes(id),
    FOREIGN KEY(trainer_id) REFERENCES users(id)
);

-- ENROLLMENTS
CREATE TABLE enrollments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    schedule_id INTEGER,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(schedule_id) REFERENCES class_schedule(id),
    UNIQUE(user_id, schedule_id)
);

-- EQUIPMENT
CREATE TABLE equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    total_quantity INTEGER NOT NULL
);

-- EQUIPMENT STATUS 
CREATE TABLE equipment_status (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    equipment_id INTEGER,
    available_quantity INTEGER,
    last_updated TEXT,
    FOREIGN KEY(equipment_id) REFERENCES equipment(id)
);

-- PERSONAL TRAINER BOOKINGS
CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    member_id INTEGER,
    trainer_id INTEGER,
    date TEXT,
    time TEXT,
    status TEXT CHECK(status IN ('booked', 'cancelled')) DEFAULT 'booked',
    FOREIGN KEY(member_id) REFERENCES users(id),
    FOREIGN KEY(trainer_id) REFERENCES users(id)
);

-- REVIEWS
CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    class_id INTEGER,
    rating INTEGER CHECK(rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id),
    FOREIGN KEY(class_id) REFERENCES classes(id)
);


-- Add schedule_id to reviews for specific class instances
ALTER TABLE reviews ADD COLUMN schedule_id INTEGER REFERENCES class_schedule(id);


-- Add timestamps for auditing
ALTER TABLE users ADD COLUMN created_at TEXT DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE enrollments ADD COLUMN created_at TEXT DEFAULT CURRENT_TIMESTAMP;


-- Indexes for performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_class_schedule_trainer ON class_schedule(trainer_id);
CREATE INDEX idx_enrollments_user ON enrollments(user_id);
CREATE INDEX idx_bookings_trainer ON bookings(trainer_id);