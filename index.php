<?php
require_once 'php/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">ToDo List</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-view="active">Active Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-view="completed">Completed Tasks</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <button id="themeToggle" class="btn btn-outline-light me-2">
                        <i id="themeIcon" class="bi bi-sun-fill"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                    <i class="bi bi-plus-lg"></i> Add Task
                </button>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search tasks...">
                    <select id="priorityFilter" class="form-select" style="max-width: 150px;">
                        <option value="">All Priorities</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                    <button id="resetFilters" class="btn btn-outline-secondary">Reset</button>
                </div>
            </div>
        </div>

        <div id="errorAlert" class="alert alert-danger d-none" role="alert"></div>
        <div id="taskList"></div>
    </div>

    <!-- Add Task Modal -->
    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="taskForm">
                        <div class="mb-3">
                            <label for="taskTitle" class="form-label">Title</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="taskTitle" required>
                                <button class="btn btn-outline-secondary" type="button" id="optimizeTitle">
                                    <i class="bi bi-magic"></i> Optimize
                                </button>
                            </div>
                            <div class="form-text">Click optimize to improve the task title using AI</div>
                        </div>
                        <div class="mb-3">
                            <label for="taskDescription" class="form-label">Description</label>
                            <div class="input-group">
                                <textarea class="form-control" id="taskDescription" rows="3"></textarea>
                                <button class="btn btn-outline-secondary" type="button" id="optimizeDescription">
                                    <i class="bi bi-magic"></i> Optimize
                                </button>
                            </div>
                            <div class="form-text">Click optimize to improve the task description using AI</div>
                        </div>
                        <div class="mb-3">
                            <label for="taskPriority" class="form-label">Priority</label>
                            <select class="form-select" id="taskPriority">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/tasks.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Handle title optimization
        document.getElementById('optimizeTitle').addEventListener('click', async () => {
            const titleInput = document.getElementById('taskTitle');
            const originalTitle = titleInput.value.trim();
            
            if (!originalTitle) {
                return;
            }
            
            try {
                const response = await fetch('php/get_task_suggestions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title: originalTitle })
                });
                
                const data = await response.json();
                if (data.success) {
                    titleInput.value = data.title;
                } else {
                    throw new Error(data.message || 'Failed to optimize title');
                }
            } catch (error) {
                console.error('Title optimization failed:', error);
            }
        });

        // Handle description optimization
        document.getElementById('optimizeDescription').addEventListener('click', async () => {
            const titleInput = document.getElementById('taskTitle');
            const descriptionInput = document.getElementById('taskDescription');
            const originalTitle = titleInput.value.trim();
            const originalDescription = descriptionInput.value.trim();
            
            if (!originalTitle || !originalDescription) {
                return;
            }
            
            try {
                const response = await fetch('php/get_task_suggestions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title: originalTitle,
                        description: originalDescription
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    descriptionInput.value = data.description;
                } else {
                    throw new Error(data.message || 'Failed to optimize description');
                }
            } catch (error) {
                console.error('Description optimization failed:', error);
            }
        });
    });
    </script>
</body>
</html>
