<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 3600); // 1 hour
ini_set('session.use_strict_mode', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security headers
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; style-src 'self' https://cdn.jsdelivr.net https://fonts.googleapis.com 'unsafe-inline'; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data:;");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Database configuration
define('DB_PATH', __DIR__ . '/../database/todo.db');
define('HASH_ALGO', PASSWORD_DEFAULT);

// Create database directory if it doesn't exist
if (!file_exists(__DIR__ . '/../database')) {
    mkdir(__DIR__ . '/../database', 0755, true);
}

// Initialize database if it doesn't exist
if (!file_exists(DB_PATH)) {
    try {
        $db = new SQLite3(DB_PATH);
        
        // Create users table
        $db->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
        
        // Create tasks table
        $db->exec('
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                description TEXT,
                status TEXT DEFAULT "active" CHECK(status IN ("active", "completed")),
                priority TEXT DEFAULT "medium" CHECK(priority IN ("low", "medium", "high")),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ');
        
        // Create subtasks table
        $db->exec('
            CREATE TABLE IF NOT EXISTS subtasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                task_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                is_completed BOOLEAN DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
            )
        ');
        
        // Create indexes
        $db->exec('CREATE INDEX IF NOT EXISTS idx_tasks_user_id ON tasks(user_id)');
        $db->exec('CREATE INDEX IF NOT EXISTS idx_tasks_status ON tasks(status)');
        $db->exec('CREATE INDEX IF NOT EXISTS idx_subtasks_task_id ON subtasks(task_id)');
        
        $db->close();
    } catch (Exception $e) {
        error_log("Database initialization error: " . $e->getMessage());
        die("Database initialization failed");
    }
}

// XSS Prevention utility function
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function check_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
