PRAGMA foreign_keys = ON;

/*
Auto Increment is used to automatically generate unique values for a column, 
usually a primary key, so that each record can be identified easily without manual input.
*/

-- USERS (members, trainers, admins)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash TEXT NOT NULL,
    name TEXT NOT NULL,
    role TEXT CHECK(role IN ('member', 'trainer', 'admin')) NOT NULL DEFAULT 'member',
    profile_photo TEXT,
    active INTEGER NOT NULL DEFAULT 1 CHECK(active IN (0,1)),
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    plan_id INTEGER REFERENCES plans(id) ON DELETE SET NULL
);

-- TRAINER PROFILE
CREATE TABLE IF NOT EXISTS trainer_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE NOT NULL,
    bio TEXT,
    specializations TEXT,
    certifications TEXT,
    years_experience INTEGER DEFAULT 0,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- FITNESS CLASSES
CREATE TABLE IF NOT EXISTS classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL CHECK(capacity > 0),
    difficulty TEXT DEFAULT 'Beginner'
);

-- CLASS SCHEDULE
CREATE TABLE IF NOT EXISTS class_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    scheduled_at TEXT NOT NULL,
    FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ENROLLMENTS
CREATE TABLE IF NOT EXISTS enrollments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    schedule_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(schedule_id) REFERENCES class_schedule(id) ON DELETE CASCADE,
    UNIQUE(user_id, schedule_id)
);

-- EQUIPMENT
CREATE TABLE IF NOT EXISTS equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    total_quantity INTEGER NOT NULL CHECK(total_quantity >= 0)
);

-- EQUIPMENT STATUS
CREATE TABLE IF NOT EXISTS equipment_status (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    equipment_id INTEGER NOT NULL,
    available_quantity INTEGER NOT NULL CHECK(available_quantity >= 0),
    last_updated TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

-- PERSONAL TRAINER BOOKINGS
CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    member_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    scheduled_at TEXT NOT NULL,
    status TEXT CHECK(status IN ('booked', 'cancelled')) NOT NULL DEFAULT 'booked',
    FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- REVIEWS
CREATE TABLE IF NOT EXISTS reviews (
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

-- MEMBERSHIP PLANS
CREATE TABLE IF NOT EXISTS plans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price REAL NOT NULL,
    billing_cycle TEXT CHECK(billing_cycle IN ('weekly', 'monthly', 'yearly')) DEFAULT 'weekly',
    features TEXT
);

-- NUTRITION PLANS
CREATE TABLE IF NOT EXISTS nutrition_plans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    target_calories INTEGER,
    goal TEXT CHECK(goal IN ('Weight Loss', 'Muscle Gain', 'Maintenance')),
    meal_details TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_users_username ON users(username);
CREATE INDEX IF NOT EXISTS idx_class_schedule_trainer ON class_schedule(trainer_id);
CREATE INDEX IF NOT EXISTS idx_schedule_datetime ON class_schedule(scheduled_at);
CREATE INDEX IF NOT EXISTS idx_enrollments_user ON enrollments(user_id);
CREATE INDEX IF NOT EXISTS idx_bookings_trainer ON bookings(trainer_id);
CREATE INDEX IF NOT EXISTS idx_bookings_member ON bookings(member_id);

-- Populate 

-- USERS
INSERT OR IGNORE INTO users (username, email, password_hash, name, role, profile_photo) VALUES

('joaosilva', 'joao@gmail.com', '$2y$10$QzIUyiOTZjwt96HtvDmEYOCPDY3DJwIPO/LtYdOQkyA60Y4sZdi3i', 'João Silva', 'member'),
('anacosta', 'ana@gmail.com', '$2y$10$fxu8L8V1kPOim0r/Qs5aZ.cTtkhTOvk2/XEVCBdN7.HkVYt0oV2OW', 'Ana Costa', 'member'),
('migueltrainer', 'miguel@gmail.com', '$2y$10$pu4RO98YESWtqJgpqMQdr.y0z4VcKKIhzHJlloDe2KchqwejJAa9C', 'Miguel Ferreira', 'trainer'),
('sofiatrainer', 'sofia@gmail.com', '$2y$10$.cOjn2hP7/.CPu4QKe6u.Otld1fWYlzuLwgmx1EcXAU7snQfHn2Ay', 'Sofia Martins', 'trainer'),
('adminuser', 'admin@gmail.com', '$2y$10$JfG.ZWCgLbrOjQMcBGrjLu92oNsSa9OQgTyxbzRKQVd6l5wiH1omm', 'Admin User', 'admin');

-- Additional trainers
INSERT OR IGNORE INTO users (username, email, password_hash, name, role, profile_photo) VALUES
('carlostrainer', 'carlos@gmail.com', '$2y$10$pu4RO98YESWtqJgpqMQdr.y0z4VcKKIhzHJlloDe2KchqwejJAa9C', 'Carlos Santos', 'trainer', 'boxing_trainer.jpg'),
('anatrai', 'ana.t@mail.com', '$2y$10$pu4RO98YESWtqJgpqMQdr.y0z4VcKKIhzHJlloDe2KchqwejJAa9C', 'Ana Rodrigues', 'trainer', 'female_trainer2.jpg'),
('pedrotrainer', 'pedro@mail.com', '$2y$10$pu4RO98YESWtqJgpqMQdr.y0z4VcKKIhzHJlloDe2KchqwejJAa9C', 'Pedro Alves', 'trainer', NULL),
('lenatrainer', 'lena@mail.com', '$2y$10$pu4RO98YESWtqJgpqMQdr.y0z4VcKKIhzHJlloDe2KchqwejJAa9C', 'Elena Kovač', 'trainer', 'female_trainer4.jpg');

-- TRAINER PROFILES
INSERT OR IGNORE INTO trainer_profiles (user_id, bio, specializations, certifications, years_experience) VALUES
(3, 'Strength and conditioning coach with 8+ years of experience transforming athletes.', 'Strength Training, HIIT, Powerlifting', 'NASM Certified', 8),
(4, 'Dedicated yoga and pilates instructor helping you find balance and flexibility.', 'Yoga, Pilates, Meditation', 'ACE Certified', 6),
(5, 'Professional boxer turned coach. Get ready to sweat and learn real striking technique.', 'Boxing, Kickboxing, HIIT', 'IBF Certified', 10),
(6, 'Holistic wellness coach specializing in mobility and functional training for all levels.', 'Pilates, Yoga, Recovery', 'Yoga Alliance RYT-500', 7),
(7, 'Strength and hypertrophy specialist. Whether you want to build muscle or get stronger, I have you covered.', 'Strength Training, Bodybuilding, Calisthenics', 'NSCA Certified', 5),
(8, 'Dance and cardio expert who makes fitness fun. Expect high energy and great music!', 'Zumba, Dance Cardio, HIIT', 'ACE Group Fitness', 4);

-- FITNESS CLASSES
INSERT OR IGNORE INTO classes (name, description, capacity, difficulty) VALUES
('Yoga', 'Relaxing yoga sessions focused on flexibility.', 4, 'Beginner'),
('HIIT', 'High intensity interval training workouts.', 2, 'Advanced'),
('Pilates', 'Core and posture improvement classes.', 3, 'Intermediate'),
('Strength Training', 'Resistance and muscle building workouts.', 2, 'Intermediate'),
('Boxing', 'High-energy boxing and striking workouts.', 3, 'Advanced'),
('Spinning', 'Indoor cycling for endurance and leg strength.', 4, 'Intermediate'),
('Zumba', 'Dance-based cardio for all fitness levels.', 5, 'Beginner'),
('CrossFit', 'Functional movements at high intensity.', 2, 'Advanced');

-- Trainers lookup table for schedule inserts
-- Miguel Ferreira = 3, Sofia Martins = 4, Carlos Santos = 9, Ana Rodrigues = 10, Pedro Alves = 11, Elena Kovač = 12
-- NOTE: IDs may shift if additional users exist; adjust as needed.

-- CLASS SCHEDULE
INSERT OR IGNORE INTO class_schedule (class_id, trainer_id, scheduled_at) VALUES
-- Week of May 25 - May 31
(1, 4, '2026-05-25 09:00:00'),
(2, 3, '2026-05-25 11:00:00'),
(3, 4, '2026-05-26 10:00:00'),
(4, 3, '2026-05-26 18:00:00'),
(5, 9, '2026-05-27 07:00:00'),
(1, 10, '2026-05-27 09:00:00'),
(2, 3, '2026-05-27 11:00:00'),
(6, 10, '2026-05-28 08:00:00'),
(3, 4, '2026-05-28 10:00:00'),
(4, 3, '2026-05-28 18:00:00'),
(1, 4, '2026-05-29 08:00:00'),
(2, 9, '2026-05-29 17:00:00'),
(7, 12, '2026-05-30 10:00:00'),
(8, 9, '2026-05-30 15:00:00'),
-- Week of June 1 - June 7
(1, 4, '2026-06-01 09:00:00'),
(4, 11, '2026-06-01 17:00:00'),
(5, 9, '2026-06-02 07:00:00'),
(3, 10, '2026-06-02 10:00:00'),
(2, 3, '2026-06-02 18:00:00'),
(6, 10, '2026-06-03 08:00:00'),
(7, 12, '2026-06-03 12:00:00'),
(8, 9, '2026-06-04 07:00:00'),
(1, 4, '2026-06-04 09:00:00'),
(4, 11, '2026-06-04 18:00:00');

-- ENROLLMENTS
INSERT OR IGNORE INTO enrollments (user_id, schedule_id) VALUES
(1, 1),
(1, 4),
(2, 2),
(2, 4),
(2, 6);


-- EQUIPMENT
INSERT OR IGNORE INTO equipment (name, total_quantity) VALUES
('Dumbbells', 50),
('Yoga Mats', 30),
('Treadmills', 10),
('Resistance Bands', 40);

-- EQUIPMENT STATUS
INSERT OR IGNORE INTO equipment_status (equipment_id, available_quantity) VALUES
(1, 45),
(2, 25),
(3, 8),
(4, 35);

-- BOOKINGS
INSERT OR IGNORE INTO bookings (member_id, trainer_id, scheduled_at, status) VALUES
(1, 3, '2026-05-22 14:00:00', 'booked'),
(2, 4, '2026-05-22 16:00:00', 'booked'),
(1, 4, '2026-05-25 10:00:00', 'cancelled');

-- REVIEWS
INSERT OR IGNORE INTO reviews (user_id, class_id, schedule_id, rating, comment) VALUES
(1, 1, 5, 5, 'Amazing yoga session!'),
(2, 2, 6, 4, 'Very intense but enjoyable workout.'),
(1, 4, 8, 5, 'Excellent trainer and atmosphere.');