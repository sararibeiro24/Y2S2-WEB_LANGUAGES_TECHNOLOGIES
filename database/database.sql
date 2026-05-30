PRAGMA foreign_keys = OFF;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS enrollments;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS nutrition_plans;
DROP TABLE IF EXISTS class_schedule;
DROP TABLE IF EXISTS trainer_profiles;
DROP TABLE IF EXISTS equipment_status;
DROP TABLE IF EXISTS equipment;
DROP TABLE IF EXISTS classes;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS plans;

PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS plans (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price REAL NOT NULL,
    billing_cycle TEXT CHECK(billing_cycle IN ('weekly', 'monthly', 'yearly')) DEFAULT 'weekly',
    features TEXT
);

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

CREATE TABLE IF NOT EXISTS trainer_profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER UNIQUE NOT NULL,
    bio TEXT,
    specializations TEXT,
    certifications TEXT,
    years_experience INTEGER DEFAULT 0,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    capacity INTEGER NOT NULL CHECK(capacity > 0),
    difficulty TEXT DEFAULT 'Beginner'
);

CREATE TABLE IF NOT EXISTS class_schedule (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    scheduled_at TEXT NOT NULL,
    FOREIGN KEY(class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS enrollments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    schedule_id INTEGER NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(schedule_id) REFERENCES class_schedule(id) ON DELETE CASCADE,
    UNIQUE(user_id, schedule_id)
);

CREATE TABLE IF NOT EXISTS equipment (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    total_quantity INTEGER NOT NULL CHECK(total_quantity >= 0)
);

CREATE TABLE IF NOT EXISTS equipment_status (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    equipment_id INTEGER NOT NULL,
    available_quantity INTEGER NOT NULL CHECK(available_quantity >= 0),
    last_updated TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    member_id INTEGER NOT NULL,
    trainer_id INTEGER NOT NULL,
    scheduled_at TEXT NOT NULL,
    status TEXT CHECK(status IN ('booked', 'cancelled')) NOT NULL DEFAULT 'booked',
    FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(trainer_id) REFERENCES users(id) ON DELETE CASCADE
);

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

-- MEMBERSHIP PLAN DATA
INSERT OR IGNORE INTO plans (id, name, price, billing_cycle, features) VALUES
(1, 'Starter Weekly', 9.99, 'weekly', 'Gym floor access, 1 class per week, Basic support'),
(2, 'Basic Monthly', 19.99, 'monthly', 'Access to gym floor, 1 class per week, Standard support'),
(3, 'Premium Weekly', 24.99, 'weekly', 'Unlimited classes, Equipment reservations, Nutrition consultation'),
(4, 'Premium Monthly', 39.99, 'monthly', 'Unlimited classes, Equipment reservations, Nutrition consultation'),
(5, 'Elite Weekly', 34.99, 'weekly', 'Personal trainer booking, Nutrition plan, Priority support'),
(6, 'Elite Monthly', 59.99, 'monthly', 'Personal trainer booking, Nutrition plan, Priority support'),
(7, 'Annual Pass', 599.99, 'yearly', 'Unlimited access, premium support, guest passes, exclusive perks');

-- USERS
INSERT OR IGNORE INTO users (id, username, email, password_hash, name, role, profile_photo, plan_id) VALUES
(1, 'joaosilva', 'joao@gmail.com', '$2y$10$QzIUyiOTZjwt96HtvDmEYOCPDY3DJwIPO/LtYdOQkyA60Y4sZdi3i', 'João Silva', 'member', NULL, 1),
(2, 'anacosta', 'ana@gmail.com', '$2y$10$fxu8L8V1kPOim0r/Qs5aZ.cTtkhTOvk2/XEVCBdN7.HkVYt0oV2OW', 'Ana Costa', 'member', NULL, 2),
(3, 'migueltrainer', 'miguel@gmail.com', '$2y$12$IA/O.cCyW426nJmSRot/juyIj.SaYHc319fW8gjpwT.QLUubQjhs2', 'Miguel Ferreira', 'trainer', NULL, NULL),
(4, 'sofiatrainer', 'sofia@gmail.com', '$2y$12$IA/O.cCyW426nJmSRot/juyIj.SaYHc319fW8gjpwT.QLUubQjhs2', 'Sofia Martins', 'trainer', NULL, NULL),
(5, 'adminuser', 'admin@gmail.com', '$2y$12$X2JgpAQ.iTJ8up4R9xEXD.YGLLO7SmRGr1hnuwdJmCjtgWoQI2orC', 'Admin User', 'admin', NULL, NULL),
(6, 'carlostrainer', 'carlos@gmail.com', '$2y$12$IA/O.cCyW426nJmSRot/juyIj.SaYHc319fW8gjpwT.QLUubQjhs2', 'Carlos Santos', 'trainer', 'boxing_trainer.jpg', NULL),
(7, 'anatrai', 'ana.t@mail.com', '$2y$12$IA/O.cCyW426nJmSRot/juyIj.SaYHc319fW8gjpwT.QLUubQjhs2', 'Ana Rodrigues', 'trainer', 'female_trainer2.jpg', NULL),
(8, 'pedrotrainer', 'pedro@mail.com', '$2y$12$IA/O.cCyW426nJmSRot/juyIj.SaYHc319fW8gjpwT.QLUubQjhs2', 'Pedro Alves', 'trainer', NULL, NULL),
(9, 'lenatrainer', 'lena@mail.com', '$2y$12$IA/O.cCyW426nJmSRot/juyIj.SaYHc319fW8gjpwT.QLUubQjhs2', 'Elena Kovač', 'trainer', 'female_trainer4.jpg', NULL);

-- TRAINER PROFILES
INSERT OR IGNORE INTO trainer_profiles (user_id, bio, specializations, certifications, years_experience) VALUES
(3, 'Strength and conditioning coach with 8+ years of experience transforming athletes.', 'Strength Training, HIIT, Powerlifting', 'NASM Certified', 8),
(4, 'Dedicated yoga and pilates instructor helping you find balance and flexibility.', 'Yoga, Pilates, Meditation', 'ACE Certified', 6),
(6, 'Professional boxer turned coach. Get ready to sweat and learn real striking technique.', 'Boxing, Kickboxing, HIIT', 'IBF Certified', 10),
(7, 'Holistic wellness coach specializing in mobility and functional training for all levels.', 'Pilates, Yoga, Recovery', 'Yoga Alliance RYT-500', 7),
(8, 'Strength and hypertrophy specialist. Whether you want to build muscle or get stronger, I have you covered.', 'Strength Training, Bodybuilding, Calisthenics', 'NSCA Certified', 5),
(9, 'Dance and cardio expert who makes fitness fun. Expect high energy and great music!', 'Zumba, Dance Cardio, HIIT', 'ACE Group Fitness', 4);

-- FITNESS CLASSES
INSERT OR IGNORE INTO classes (id, name, description, capacity, difficulty) VALUES
(1, 'Yoga', 'Relaxing yoga sessions focused on flexibility.', 4, 'Beginner'),
(2, 'HIIT', 'High intensity interval training workouts.', 2, 'Advanced'),
(3, 'Pilates', 'Core and posture improvement classes.', 3, 'Intermediate'),
(4, 'Strength Training', 'Resistance and muscle building workouts.', 2, 'Intermediate'),
(5, 'Boxing', 'High-energy boxing and striking workouts.', 3, 'Advanced'),
(6, 'Spinning', 'Indoor cycling for endurance and leg strength.', 4, 'Intermediate'),
(7, 'Zumba', 'Dance-based cardio for all fitness levels.', 5, 'Beginner'),
(8, 'CrossFit', 'Functional movements at high intensity.', 2, 'Advanced');

-- CLASS SCHEDULE
INSERT OR IGNORE INTO class_schedule (id, class_id, trainer_id, scheduled_at) VALUES
(1,  1, 4,  '2026-05-25 09:00:00'),
(2,  2, 3,  '2026-05-25 11:00:00'),
(3,  5, 6,  '2026-05-25 14:00:00'),
(4,  3, 4,  '2026-05-26 09:00:00'),
(5,  4, 3,  '2026-05-26 11:00:00'),
(6,  6, 7,  '2026-05-26 14:00:00'),
(7,  5, 6,  '2026-05-27 09:00:00'),
(8,  1, 4,  '2026-05-27 11:00:00'),
(9,  2, 3,  '2026-05-27 14:00:00'),
(10, 7, 9,  '2026-05-28 09:00:00'),
(11, 3, 7,  '2026-05-28 11:00:00'),
(12, 4, 8,  '2026-05-28 14:00:00'),
(13, 1, 4,  '2026-05-29 09:00:00'),
(14, 8, 6,  '2026-05-29 11:00:00'),
(15, 6, 7,  '2026-05-29 14:00:00'),
(16, 2, 3,  '2026-05-30 09:00:00'),
(17, 7, 9,  '2026-05-30 11:00:00'),
(18, 5, 6,  '2026-05-30 14:00:00'),
(19, 3, 4,  '2026-05-31 09:00:00'),
(20, 1, 7,  '2026-05-31 11:00:00'),
(21, 1, 4,  '2026-06-01 09:00:00'),
(22, 4, 8,  '2026-06-01 11:00:00'),
(23, 2, 3,  '2026-06-01 14:00:00'),
(24, 5, 6,  '2026-06-02 09:00:00'),
(25, 3, 7,  '2026-06-02 11:00:00'),
(26, 6, 7,  '2026-06-02 14:00:00'),
(27, 7, 9,  '2026-06-03 09:00:00'),
(28, 8, 6,  '2026-06-03 11:00:00'),
(29, 1, 4,  '2026-06-03 14:00:00'),
(30, 4, 8,  '2026-06-04 09:00:00'),
(31, 2, 3,  '2026-06-04 11:00:00'),
(32, 3, 4,  '2026-06-04 14:00:00'),
(33, 5, 6,  '2026-06-05 09:00:00'),
(34, 7, 9,  '2026-06-05 11:00:00'),
(35, 6, 7,  '2026-06-05 14:00:00'),
(36, 1, 4,  '2026-06-06 09:00:00'),
(37, 8, 6,  '2026-06-06 11:00:00'),
(38, 4, 8,  '2026-06-06 14:00:00'),
(39, 2, 3,  '2026-06-07 09:00:00'),
(40, 3, 7,  '2026-06-07 11:00:00');

-- ENROLLMENTS
INSERT OR IGNORE INTO enrollments (user_id, schedule_id) VALUES
(1, 1),
(1, 4),
(2, 2),
(2, 4),
(2, 6);

-- EQUIPMENT
INSERT OR IGNORE INTO equipment (id, name, total_quantity) VALUES
(1, 'Dumbbells', 50),
(2, 'Yoga Mats', 30),
(3, 'Treadmills', 10),
(4, 'Resistance Bands', 40);

-- EQUIPMENT STATUS
INSERT OR IGNORE INTO equipment_status (equipment_id, available_quantity) VALUES
(1, 45),
(2, 25),
(3, 8),
(4, 35);

-- NUTRITION PLANS DATA
INSERT OR IGNORE INTO nutrition_plans (user_id, trainer_id, target_calories, goal, meal_details) VALUES
(1, 3, 1800, 'Weight Loss', 'High protein breakfast, light lunch, balanced dinner.'),
(2, 3, 2250, 'Muscle Gain', 'Lean protein, smarter carbs, and recovery-focused eating.'),
(1, 4, 2000, 'Maintenance', 'Three meals with healthy fats and vegetables.'),
(2, 4, 2500, 'Muscle Gain', 'Calorie surplus with lean proteins and carbs.'),
(1, 6, 2100, 'Weight Loss', 'Protein-focused meals with lots of vegetables and hydration.'),
(2, 6, 2150, 'Maintenance', 'Simple meal structure with mindful calories and good recovery.'),
(1, 7, 2300, 'Muscle Gain', 'A calorie surplus with clean carbs, protein, and recovery fats.'),
(2, 7, 2400, 'Muscle Gain', 'High-performance fueling with more protein and recovery nutrients.'),
(1, 8, 2050, 'Maintenance', 'Balanced portions for daily energy, mobility, and recovery.'),
(2, 8, 2200, 'Weight Loss', 'Smart portions with protein-first meals and steady hydration.'),
(1, 9, 1900, 'Weight Loss', 'Portion-controlled meals, low sugar, and steady protein intake.'),
(2, 9, 2350, 'Maintenance', 'Balanced nutrition for recovery, stamina, and everyday energy.');

-- BOOKINGS
INSERT OR IGNORE INTO bookings (member_id, trainer_id, scheduled_at, status) VALUES
(1, 3, '2026-05-22 14:00:00', 'booked'),
(2, 4, '2026-05-22 16:00:00', 'booked'),
(1, 4, '2026-05-25 10:00:00', 'cancelled');

-- REVIEWS
INSERT OR IGNORE INTO reviews (user_id, class_id, schedule_id, rating, comment) VALUES
(1, 5, 5, 5, 'Amazing yoga session!'),
(2, 2, 6, 4, 'Very intense but enjoyable workout.'),
(1, 4, 10, 5, 'Excellent trainer and atmosphere.');