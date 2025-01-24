<?php
require_once 'config.php';
require_once 'db.php';

function register_user($username, $password, $email) {
    if (empty($username) || empty($password) || empty($email)) {
        throw new Exception("All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email format");
    }

    if (strlen($password) < 8) {
        throw new Exception("Password must be at least 8 characters long");
    }

    try {
        $db = get_db_connection();
        
        // Check if username or email already exists
        $stmt = $db->prepare('SELECT id FROM users WHERE username = :username OR email = :email');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if ($result->fetchArray()) {
            throw new Exception("Username or email already exists");
        }

        // Hash password and insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashed_password, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            close_db_connection($db);
            return true;
        }
        throw new Exception("Registration failed");
    } catch (Exception $e) {
        if (isset($db)) {
            close_db_connection($db);
        }
        error_log("Registration error: " . $e->getMessage());
        throw new Exception("Registration failed: " . $e->getMessage());
    }
}

function login_user($username, $password) {
    if (empty($username) || empty($password)) {
        throw new Exception("Username and password are required");
    }

    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT id, username, password FROM users WHERE username = :username OR email = :email');
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':email', $username, SQLITE3_TEXT); // Allow login with email too
        $result = $stmt->execute();
        
        if ($user = $result->fetchArray(SQLITE3_ASSOC)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                // Regenerate session ID for security
                session_regenerate_id(true);
                close_db_connection($db);
                return true;
            }
        }
        close_db_connection($db);
        throw new Exception("Invalid username or password");
    } catch (Exception $e) {
        if (isset($db)) {
            close_db_connection($db);
        }
        error_log("Login error: " . $e->getMessage());
        throw new Exception("Login failed");
    }
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function logout_user() {
    $_SESSION = array();
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
    return true;
}

function get_user_info($user_id) {
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT id, username, email, created_at FROM users WHERE id = :id');
        $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching user info: " . $e->getMessage());
        throw $e;
    }
}

// Add these functions to handle profile operations
function get_profile($user_id) {
    try {
        $db = get_db_connection();
        $stmt = $db->prepare('SELECT username, email FROM users WHERE id = :user_id');
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);
        
        if (!$user) {
            throw new Exception('User not found');
        }
        
        close_db_connection($db);
        return $user;
    } catch (Exception $e) {
        if (isset($db)) {
            close_db_connection($db);
        }
        throw $e;
    }
}

function update_profile($user_id, $data) {
    // Validate username
    if (empty($data['username']) || strlen($data['username']) < 3 || strlen($data['username']) > 20) {
        throw new Exception('Username must be between 3 and 20 characters');
    }

    // Validate email
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    try {
        $db = get_db_connection();
        
        // Check if username or email is already taken
        $stmt = $db->prepare('SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :user_id');
        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        if ($result->fetchArray()) {
            throw new Exception('Username or email is already taken');
        }

        // Start transaction
        $db->exec('BEGIN TRANSACTION');

        // Update user info
        $stmt = $db->prepare('UPDATE users SET username = :username, email = :email WHERE id = :user_id');
        $stmt->bindValue(':username', $data['username'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->execute();

        // Update password if provided
        if (!empty($data['current_password']) && !empty($data['new_password'])) {
            // Verify current password
            $stmt = $db->prepare('SELECT password FROM users WHERE id = :user_id');
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);

            if (!password_verify($data['current_password'], $user['password'])) {
                throw new Exception('Current password is incorrect');
            }

            // Validate new password
            if (strlen($data['new_password']) < 8) {
                throw new Exception('New password must be at least 8 characters long');
            }

            // Update password
            $hash = password_hash($data['new_password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare('UPDATE users SET password = :password WHERE id = :user_id');
            $stmt->bindValue(':password', $hash, SQLITE3_TEXT);
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->execute();
        }

        $db->exec('COMMIT');
        $_SESSION['username'] = $data['username']; // Update session username
        close_db_connection($db);
        return true;
    } catch (Exception $e) {
        if (isset($db)) {
            $db->exec('ROLLBACK');
            close_db_connection($db);
        }
        throw $e;
    }
}

// Handle auth check and logout requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'register':
            try {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                
                if (!$data) {
                    throw new Exception('Invalid request data');
                }
                
                if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
                    throw new Exception('All fields are required');
                }
                
                if (register_user($data['username'], $data['password'], $data['email'])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registration successful',
                        'redirect' => '../login.php'
                    ]);
                } else {
                    throw new Exception('Registration failed');
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'login':
            try {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                
                if (!$data) {
                    throw new Exception('Invalid request data');
                }
                
                if (empty($data['username']) || empty($data['password'])) {
                    throw new Exception('Username and password are required');
                }
                
                if (login_user($data['username'], $data['password'])) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
                        'redirect' => '../index.php'
                    ]);
                } else {
                    throw new Exception('Invalid username or password');
                }
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        case 'check':
            echo json_encode([
                'authenticated' => isset($_SESSION['user_id']),
                'username' => isset($_SESSION['username']) ? $_SESSION['username'] : null
            ]);
            break;
            
        case 'logout':
            logout_user();
            echo json_encode([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            break;
            
        case 'get_profile':
            try {
                if (!isset($_SESSION['user_id'])) {
                    throw new Exception('Not authenticated');
                }
                $user = get_profile($_SESSION['user_id']);
                echo json_encode(['success' => true, 'user' => $user]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'update_profile':
            try {
                if (!isset($_SESSION['user_id'])) {
                    throw new Exception('Not authenticated');
                }
                
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);
                
                if (!$data) {
                    throw new Exception('Invalid request data');
                }
                
                update_profile($_SESSION['user_id'], $data);
                echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
    exit;
}
