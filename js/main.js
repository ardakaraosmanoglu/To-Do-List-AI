// Check authentication status
async function checkAuth() {
    try {
        const response = await fetch('php/auth.php?action=check');
        if (!response.ok) {
            throw new Error('Authentication check failed');
        }
        const data = await response.json();
        if (!data.authenticated) {
            window.location.href = 'login.php';
        }
    } catch (error) {
        console.error('Auth check error:', error);
        window.location.href = 'login.php';
    }
}

// Theme management
function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
    updateThemeIcon(savedTheme);
}

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.className = theme === 'dark' ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
    }
}

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon(newTheme);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    checkAuth();
    initTheme();

    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }

    // Logout
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('php/auth.php?action=logout');
                if (!response.ok) {
                    throw new Error('Logout failed');
                }
                const data = await response.json();
                if (data.success) {
                    window.location.href = 'login.php';
                } else {
                    throw new Error(data.message || 'Logout failed');
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('Failed to logout. Please try again.');
            }
        });
    }

    // Navigation
    document.querySelectorAll('[data-view]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('[data-view]').forEach(el => el.classList.remove('active'));
            e.target.classList.add('active');
            loadTasks(e.target.dataset.view === 'completed' ? 'completed' : 'active');
        });
    });

    // Search and Filters
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    document.getElementById('searchInput').addEventListener('input', 
        debounce(() => applyFilters(), 300)
    );

    document.getElementById('priorityFilter').addEventListener('change', () => applyFilters());
    document.getElementById('statusFilter').addEventListener('change', () => applyFilters());
    
    document.getElementById('resetFilters').addEventListener('click', () => {
        document.getElementById('searchInput').value = '';
        document.getElementById('priorityFilter').value = '';
        document.getElementById('statusFilter').value = '';
        applyFilters();
    });
});

// Filter application
function applyFilters() {
    const searchQuery = document.getElementById('searchInput').value.toLowerCase();
    const priorityFilter = document.getElementById('priorityFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;

    document.querySelectorAll('.task-card').forEach(card => {
        const title = card.querySelector('.task-title').textContent.toLowerCase();
        const description = card.querySelector('.task-description').textContent.toLowerCase();
        const priority = card.querySelector('.task-priority').textContent.toLowerCase();
        const status = card.querySelector('.task-status').textContent.toLowerCase();

        const matchesSearch = searchQuery === '' || 
            title.includes(searchQuery) || 
            description.includes(searchQuery);
        
        const matchesPriority = priorityFilter === '' || priority === priorityFilter;
        const matchesStatus = statusFilter === '' || status === statusFilter;

        card.style.display = matchesSearch && matchesPriority && matchesStatus ? 'block' : 'none';
    });
}

// Error handling
function showError(message) {
    // You can implement a more sophisticated error display system here
    alert(message);
}
