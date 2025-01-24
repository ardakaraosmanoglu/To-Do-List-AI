<?php
require_once 'php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About - ToDo List App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">ToDo List</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <button class="btn btn-light me-2" id="themeToggle">
                        <i class="bi bi-moon-fill"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">About ToDo List App</h3>
                    </div>
                    <div class="card-body">
                        <section class="mb-4">
                            <h4>Features</h4>
                            <ul>
                                <li>User Management
                                    <ul>
                                        <li>Secure registration and login system</li>
                                        <li>Password hashing for security</li>
                                        <li>Input validation and sanitization</li>
                                    </ul>
                                </li>
                                <li>Task Management
                                    <ul>
                                        <li>Create, update, and delete tasks</li>
                                        <li>Add task descriptions and set priorities</li>
                                        <li>Track task status (Pending/In Progress/Completed)</li>
                                        <li>Create and manage subtasks</li>
                                        <li>Visual progress tracking</li>
                                    </ul>
                                </li>
                                <li>Organization
                                    <ul>
                                        <li>Search functionality for tasks</li>
                                        <li>Filter by priority and status</li>
                                        <li>Separate views for active and completed tasks</li>
                                        <li>Progress tracking with visual indicators</li>
                                    </ul>
                                </li>
                                <li>User Interface
                                    <ul>
                                        <li>Responsive design for all devices</li>
                                        <li>Dark/Light mode toggle</li>
                                        <li>Real-time updates</li>
                                        <li>Intuitive and user-friendly interface</li>
                                    </ul>
                                </li>
                            </ul>
                        </section>

                        <section class="mb-4">
                            <h4>Usage Instructions</h4>
                            <h5 class="mt-3">Getting Started</h5>
                            <ol>
                                <li>Register an account or login if you already have one</li>
                                <li>Once logged in, you'll be taken to the main dashboard</li>
                                <li>Use the navigation menu to switch between active and completed tasks</li>
                            </ol>

                            <h5 class="mt-3">Creating Tasks</h5>
                            <ol>
                                <li>Use the task creation form at the top of the dashboard</li>
                                <li>Enter a title (required) and optional description</li>
                                <li>Select a priority level (Low/Medium/High)</li>
                                <li>Click "Add Task" to create the task</li>
                            </ol>

                            <h5 class="mt-3">Managing Tasks</h5>
                            <ol>
                                <li>Update task status using the dropdown menu on each task</li>
                                <li>Add subtasks using the input field below each task</li>
                                <li>Check/uncheck subtasks to mark them as completed</li>
                                <li>Use the delete button to remove tasks</li>
                            </ol>

                            <h5 class="mt-3">Organization</h5>
                            <ol>
                                <li>Use the search bar to find specific tasks</li>
                                <li>Filter tasks by priority or status</li>
                                <li>Click the reset button to clear all filters</li>
                                <li>Switch between active and completed tasks views</li>
                            </ol>
                        </section>

                        <section class="mb-4">
                            <h4>Technical Specifications</h4>
                            <ul>
                                <li>Frontend:
                                    <ul>
                                        <li>HTML5</li>
                                        <li>CSS3 with custom properties for theming</li>
                                        <li>JavaScript (ES6+)</li>
                                        <li>Bootstrap 5 for responsive design</li>
                                    </ul>
                                </li>
                                <li>Backend:
                                    <ul>
                                        <li>PHP 7.4+</li>
                                        <li>SQLite 3 database</li>
                                        <li>RESTful API architecture</li>
                                    </ul>
                                </li>
                                <li>Security:
                                    <ul>
                                        <li>Password hashing using bcrypt</li>
                                        <li>CSRF protection</li>
                                        <li>Input sanitization</li>
                                        <li>Prepared statements for database queries</li>
                                    </ul>
                                </li>
                            </ul>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
