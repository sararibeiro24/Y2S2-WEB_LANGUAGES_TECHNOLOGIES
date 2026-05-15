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
    password_hash TEXT NOT NULL,
    name TEXT NOT NULL,
    role TEXT CHECK(role IN ('member', 'trainer', 'admin')) NOT NULL DEFAULT 'member',
    profile_photo TEXT,
    active INTEGER NOT NULL DEFAULT 1 CHECK(active IN (0,1)),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
);

-- TRAINER PROFILE
CREATE TABLE trainer_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE NOT NULL,
    bio TEXT,
    specializations TEXT,
    certifications TEXT,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- FITNESS CLASSES
CREATE TABLE classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL CHECK(capacity > 0)
);

-- CLASS SCHEDULE
CREATE TABLE class_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    scheduled_at TEXT NOT NULL,
    FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ENROLLMENTS
CREATE TABLE enrollments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    schedule_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(schedule_id) REFERENCES class_schedule(id) ON DELETE CASCADE,
    UNIQUE(user_id, schedule_id)
);

-- EQUIPMENT
CREATE TABLE equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    total_quantity INTEGER NOT NULL CHECK(total_quantity >= 0)
);

-- EQUIPMENT STATUS
CREATE TABLE equipment_status (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    equipment_id INTEGER NOT NULL,
    available_quantity INTEGER NOT NULL CHECK(available_quantity >= 0),
    last_updated TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- PERSONAL TRAINER BOOKINGS
CREATE TABLE bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    member_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    scheduled_at TEXT NOT NULL,
    status TEXT CHECK(status IN ('booked', 'cancelled')) NOT NULL DEFAULT 'booked',
    FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- REVIEWS
CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    class_id INTEGER NOT NULL,
    schedule_id INTEGER,
    rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY(schedule_id) REFERENCES class_schedule(id) ON DELETE CASCADE,
    UNIQUE(user_id, schedule_id)
);

-- Indexes for performance
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_class_schedule_trainer ON class_schedule(trainer_id);
CREATE INDEX idx_schedule_datetime ON class_schedule(scheduled_at);
CREATE INDEX idx_enrollments_user ON enrollments(user_id);
CREATE INDEX idx_bookings_trainer ON bookings(trainer_id);
CREATE INDEX idx_bookings_member ON bookings(member_id);

-- Populate 

-- USERS
INSERT INTO users (username, email, password_hash, name, role) VALUES
('joaosilva', 'joao@gmail.com', 'hashedpass1', 'João Silva', 'member'),
('anacosta', 'ana@gmail.com', 'hashedpass2', 'Ana Costa', 'member'),
('migueltrainer', 'miguel@gmail.com', 'hashedpass3', 'Miguel Ferreira', 'trainer'),
('sofiatrainer', 'sofia@gmail.com', 'hashedpass4', 'Sofia Martins', 'trainer'),
('adminuser', 'admin@gmail.com', 'hashedadmin', 'Admin User', 'admin');

-- TRAINER PROFILES
INSERT INTO trainer_profiles (user_id, bio, specializations, certifications) VALUES
(3, 'Experienced trainer focused on strength and conditioning.', 'Strength Training, HIIT', 'NASM Certified'),
(4, 'Yoga and pilates instructor with years of experience.', 'Yoga, Pilates', 'ACE Certified');

-- FITNESS CLASSES
INSERT INTO classes (name, description, capacity) VALUES
('Yoga', 'Relaxing yoga sessions focused on flexibility.', 20),
('HIIT', 'High intensity interval training workouts.', 15),
('Pilates', 'Core and posture improvement classes.', 18),
('Strength Training', 'Resistance and muscle building workouts.', 12);

-- CLASS SCHEDULE
INSERT INTO class_schedule (class_id, trainer_id, scheduled_at) VALUES
(1, 4, '2026-05-20 09:00:00'),
(2, 3, '2026-05-20 11:00:00'),
(3, 4, '2026-05-21 10:00:00'),
(4, 3, '2026-05-21 18:00:00');

-- ENROLLMENTS
INSERT INTO enrollments (user_id, schedule_id) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 3);

-- EQUIPMENT
INSERT INTO equipment (name, total_quantity) VALUES
('Dumbbells', 50),
('Yoga Mats', 30),
('Treadmills', 10),
('Resistance Bands', 40);

-- EQUIPMENT STATUS
INSERT INTO equipment_status (equipment_id, available_quantity) VALUES
(1, 45),
(2, 25),
(3, 8),
(4, 35);

-- BOOKINGS
INSERT INTO bookings (member_id, trainer_id, scheduled_at, status) VALUES
(1, 3, '2026-05-22 14:00:00', 'booked'),
(2, 4, '2026-05-22 16:00:00', 'booked'),
(1, 4, '2026-05-25 10:00:00', 'cancelled');

-- REVIEWS
INSERT INTO reviews (user_id, class_id, schedule_id, rating, comment) VALUES
(1, 1, 1, 5, 'Amazing yoga session!'),
(2, 2, 2, 4, 'Very intense but enjoyable workout.'),
(1, 4, 4, 5, 'Excellent trainer and atmosphere.');