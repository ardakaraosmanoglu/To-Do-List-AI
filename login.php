<?php
require_once 'php/config.php';
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ToDo List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="auth-container">
        <div class="text-center mb-4">
            <h1 class="display-5 fw-bold text-primary mb-2">Welcome Back</h1>
            <p class="text-muted">Sign in to continue to your tasks</p>
        </div>
        <div class="card">
            <div class="card-body">
                <form id="loginForm" novalidate>
                    <div class="mb-4">
                        <label for="username" class="form-label">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="username" required>
                        </div>
                        <div class="invalid-feedback">Please enter your username or email.</div>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password" required>
                            <button class="btn btn-outline-secondary border-start-0" type="button" id="togglePassword">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                    </div>
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? <a href="register.php" class="text-decoration-none">Create one</a></p>
                    </div>
                </form>
            </div>
        </div>
        <div id="errorAlert" class="alert alert-danger mt-3 d-none" role="alert"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('loginForm');
            const errorAlert = document.getElementById('errorAlert');
            const togglePasswordBtn = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                form.classList.add('was-validated');

                if (!form.checkValidity()) {
                    return;
                }

                const formData = {
                    username: document.getElementById('username').value.trim(),
                    password: passwordInput.value
                };

                try {
                    const response = await fetch('php/auth.php?action=login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    const data = await response.json();
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        throw new Error(data.message || 'Login failed');
                    }
                } catch (error) {
                    errorAlert.textContent = error.message;
                    errorAlert.classList.remove('d-none');
                }
            });

            // Toggle password visibility
            togglePasswordBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                togglePasswordBtn.querySelector('i').classList.toggle('bi-eye');
                togglePasswordBtn.querySelector('i').classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>
