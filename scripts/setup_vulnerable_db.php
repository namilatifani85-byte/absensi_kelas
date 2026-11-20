<?php

$dbDir = dirname(__DIR__) . '/database';
$vulnDb = $dbDir . '/database_vuln.sqlite';

$pdo = new PDO("sqlite:" . $vulnDb);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS classes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code TEXT NOT NULL,
    name TEXT NOT NULL,
    lecturer_id INTEGER,
    room TEXT,
    day TEXT,
    time_start TEXT,
    time_end TEXT,
    is_active INTEGER DEFAULT 1,
    created_at TEXT,
    updated_at TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    role TEXT NOT NULL,
    password TEXT NOT NULL,
    created_at TEXT,
    updated_at TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    nim TEXT UNIQUE NOT NULL,
    full_name TEXT NOT NULL,
    study_program TEXT,
    created_at TEXT,
    updated_at TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS attendances (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    class_id INTEGER NOT NULL,
    student_id INTEGER NOT NULL,
    attendance_date TEXT NOT NULL,
    status TEXT NOT NULL,
    excuse_notes TEXT,
    medical_note_encrypted TEXT,
    checked_in_at TEXT,
    verified_by INTEGER,
    created_at TEXT,
    updated_at TEXT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS attendance_notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    attendance_id INTEGER NOT NULL,
    author_id INTEGER NOT NULL,
    notes TEXT NOT NULL,
    created_at TEXT,
    updated_at TEXT
)");

// Cek apakah data sudah ada
$stmt = $pdo->query("SELECT COUNT(*) FROM classes");
if ((int) $stmt->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO classes (code, name, room, day, time_start, time_end, is_active) VALUES
        ('TIF-301', 'Keamanan Sistem Komputer', 'Lab Siber 2', 'Senin', '08:00', '10:30', 1),
        ('TIF-204', 'Rekayasa Perangkat Lunak', 'Ruang Teori 3', 'Rabu', '13:00', '15:30', 1)
    ");
}

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ((int) $stmt->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO users (id, name, email, role, password) VALUES
        (1, 'Dr. Ir. Hendra Wijaya, M.Kom', 'hendra.wijaya@univ.ac.id', 'lecturer', 'secret'),
        (2, 'Budi Santoso', 'budi.santoso@student.univ.ac.id', 'student', 'secret'),
        (3, 'Siti Aminah', 'siti.aminah@student.univ.ac.id', 'student', 'secret')
    ");
}

$stmt = $pdo->query("SELECT COUNT(*) FROM students");
if ((int) $stmt->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO students (id, user_id, nim, full_name, study_program) VALUES
        (1, 2, '230170001', 'Budi Santoso', 'Teknik Informatika'),
        (2, 3, '230170002', 'Siti Aminah', 'Teknik Informatika')
    ");
}

$stmt = $pdo->query("SELECT COUNT(*) FROM attendances");
if ((int) $stmt->fetchColumn() === 0) {
    $pdo->exec("INSERT INTO attendances (id, class_id, student_id, attendance_date, status, excuse_notes) VALUES
        (1, 1, 1, '2026-11-20', 'present', 'Hadir tepat waktu'),
        (2, 1, 2, '2026-11-20', 'sick', 'Izin demam dan istirahat')
    ");
}

echo "Berhasil menginisialisasi skema dan data database_vuln.sqlite\n";
