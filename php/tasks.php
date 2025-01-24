<?php
require_once 'config.php';
require_once 'db.php';

// Set JSON content type
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = get_db_connection();
    $action = $_GET['action'] ?? '';
    $user_id = $_SESSION['user_id'];

    switch ($action) {
        case 'create':
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data) {
                throw new Exception('Invalid request data');
            }

            // Validate required fields
            if (empty($data['title'])) {
                throw new Exception('Title is required');
            }

            // Sanitize inputs
            $title = htmlspecialchars($data['title']);
            $description = htmlspecialchars($data['description'] ?? '');
            $priority = strtolower($data['priority'] ?? 'medium');

            // Validate priority
            if (!in_array($priority, ['low', 'medium', 'high'])) {
                $priority = 'medium';
            }

            // Insert task
            $stmt = $db->prepare('INSERT INTO tasks (user_id, title, description, priority, status) VALUES (:user_id, :title, :description, :priority, :status)');
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->bindValue(':title', $title, SQLITE3_TEXT);
            $stmt->bindValue(':description', $description, SQLITE3_TEXT);
            $stmt->bindValue(':priority', $priority, SQLITE3_TEXT);
            $stmt->bindValue(':status', 'active', SQLITE3_TEXT);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to create task');
            }

            echo json_encode(['success' => true, 'message' => 'Task created successfully']);
            break;

        case 'list':
            $status = $_GET['status'] ?? 'active';
            if (!in_array($status, ['active', 'completed'])) {
                $status = 'active';
            }

            $stmt = $db->prepare('SELECT * FROM tasks WHERE user_id = :user_id AND status = :status ORDER BY created_at DESC');
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->bindValue(':status', $status, SQLITE3_TEXT);
            $result = $stmt->execute();
            
            $tasks = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $tasks[] = $row;
            }

            echo json_encode(['success' => true, 'tasks' => $tasks]);
            break;

        case 'update_status':
            // Get JSON input
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (!$data || !isset($data['task_id']) || !isset($data['status'])) {
                throw new Exception('Invalid request data');
            }

            $task_id = filter_var($data['task_id'], FILTER_VALIDATE_INT);
            $status = $data['status'];

            if (!$task_id) {
                throw new Exception('Invalid task ID');
            }

            if (!in_array($status, ['active', 'completed'])) {
                throw new Exception('Invalid status');
            }

            // Verify task belongs to user
            $stmt = $db->prepare('SELECT user_id FROM tasks WHERE id = :task_id');
            $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $task = $result->fetchArray(SQLITE3_ASSOC);

            if (!$task || $task['user_id'] != $user_id) {
                throw new Exception('Task not found');
            }

            // Update task status
            $stmt = $db->prepare('UPDATE tasks SET status = :status WHERE id = :task_id AND user_id = :user_id');
            $stmt->bindValue(':status', $status, SQLITE3_TEXT);
            $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to update task status');
            }

            echo json_encode(['success' => true, 'message' => 'Task status updated successfully']);
            break;

        case 'delete':
            $task_id = filter_var($_GET['task_id'], FILTER_VALIDATE_INT);
            
            if (!$task_id) {
                throw new Exception('Invalid task ID');
            }

            // Verify task belongs to user
            $stmt = $db->prepare('SELECT user_id FROM tasks WHERE id = :task_id');
            $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $task = $result->fetchArray(SQLITE3_ASSOC);

            if (!$task || $task['user_id'] != $user_id) {
                throw new Exception('Task not found');
            }

            // Delete task
            $stmt = $db->prepare('DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id');
            $stmt->bindValue(':task_id', $task_id, SQLITE3_INTEGER);
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete task');
            }

            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            break;

        default:
            throw new Exception('Invalid action');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($db)) {
        close_db_connection($db);
    }
}
