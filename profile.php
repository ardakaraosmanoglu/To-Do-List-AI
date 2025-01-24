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
    <title>Profile - ToDo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
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
                            <li><a class="dropdown-item active" href="profile.php"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item text-danger" id="logoutBtn"><i class="bi bi-box-arrow-right me-2"></i>Logout</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="profile-avatar mb-3">
                                <i class="bi bi-person-circle display-1"></i>
                            </div>
                            <h2 class="card-title">Profile Settings</h2>
                        </div>

                        <form id="profileForm" class="needs-validation" novalidate>
                            <div class="mb-4">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="username" 
                                           value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
                                </div>
                                <div class="invalid-feedback">Please enter a valid username.</div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0" id="email" required>
                                </div>
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-4">Change Password</h5>
                            <div class="mb-4">
                                <label for="currentPassword" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0" id="currentPassword">
                                    <button class="btn btn-outline-secondary border-start-0 toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="newPassword" class="form-label">New Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0" id="newPassword">
                                    <button class="btn btn-outline-secondary border-start-0 toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Leave blank if you don't want to change the password.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="errorAlert" class="alert alert-danger mt-3 d-none" role="alert"></div>
        <div id="successAlert" class="alert alert-success mt-3 d-none" role="alert"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('profileForm');
        const errorAlert = document.getElementById('errorAlert');
        const successAlert = document.getElementById('successAlert');

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                button.querySelector('i').classList.toggle('bi-eye');
                button.querySelector('i').classList.toggle('bi-eye-slash');
            });
        });

        // Load user data
        fetch('php/auth.php?action=get_profile')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('username').value = data.user.username;
                    document.getElementById('email').value = data.user.email;
                } else {
                    throw new Error(data.message || 'Failed to load profile data');
                }
            })
            .catch(error => {
                errorAlert.textContent = error.message;
                errorAlert.classList.remove('d-none');
            });

        // Handle form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            form.classList.add('was-validated');

            if (!form.checkValidity()) {
                return;
            }

            const formData = {
                username: document.getElementById('username').value.trim(),
                email: document.getElementById('email').value.trim(),
                current_password: document.getElementById('currentPassword').value,
                new_password: document.getElementById('newPassword').value
            };

            try {
                const response = await fetch('php/auth.php?action=update_profile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();
                if (data.success) {
                    successAlert.textContent = 'Profile updated successfully';
                    successAlert.classList.remove('d-none');
                    errorAlert.classList.add('d-none');
                    
                    // Clear password fields
                    document.getElementById('currentPassword').value = '';
                    document.getElementById('newPassword').value = '';
                    
                    // Hide success message after 5 seconds
                    setTimeout(() => {
                        successAlert.classList.add('d-none');
                    }, 5000);
                } else {
                    throw new Error(data.message || 'Failed to update profile');
                }
            } catch (error) {
                errorAlert.textContent = error.message;
                errorAlert.classList.remove('d-none');
                successAlert.classList.add('d-none');
            }
        });

        // Handle logout
        document.getElementById('logoutBtn').addEventListener('click', async () => {
            try {
                const response = await fetch('php/auth.php?action=logout');
                const data = await response.json();
                if (data.success) {
                    window.location.href = 'login.php';
                }
            } catch (error) {
                console.error('Logout failed:', error);
            }
        });
    });
    </script>
</body>
</html> 