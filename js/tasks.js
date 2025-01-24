// Task management
let tasks = [];
let currentView = 'active';

async function loadTasks(view = 'active') {
    try {
        const response = await fetch(`php/tasks.php?action=list&status=${view}`);
        if (!response.ok) {
            throw new Error('Failed to load tasks');
        }
        const data = await response.json();
        tasks = data.tasks || [];
        renderTasks();
    } catch (error) {
        showError(error.message);
    }
}

async function createTask(title, description, priority) {
    try {
        const response = await fetch('php/tasks.php?action=create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ title, description, priority })
        });

        if (!response.ok) {
            throw new Error('Failed to create task');
        }

        const result = await response.json();
        if (result.success) {
            loadTasks(currentView);
            const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskModal'));
            modal.hide();
            document.getElementById('taskForm').reset();
        } else {
            throw new Error(result.message || 'Failed to create task');
        }
    } catch (error) {
        showError(error.message);
    }
}

async function updateTaskStatus(taskId, status) {
    try {
        const response = await fetch('php/tasks.php?action=update_status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ task_id: taskId, status })
        });

        if (!response.ok) {
            throw new Error('Failed to update task status');
        }

        const result = await response.json();
        if (result.success) {
            loadTasks(currentView);
        } else {
            throw new Error(result.message || 'Failed to update task status');
        }
    } catch (error) {
        showError(error.message);
    }
}

async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }

    try {
        const response = await fetch(`php/tasks.php?action=delete&task_id=${taskId}`, {
            method: 'POST'
        });

        if (!response.ok) {
            throw new Error('Failed to delete task');
        }

        const result = await response.json();
        if (result.success) {
            loadTasks(currentView);
        } else {
            throw new Error(result.message || 'Failed to delete task');
        }
    } catch (error) {
        showError(error.message);
    }
}

function createSubtask(taskId, title) {
    const formData = new FormData();
    formData.append('task_id', taskId);
    formData.append('title', title);

    fetch('php/tasks.php?action=create_subtask', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTasks();
        } else {
            showError(data.message);
        }
    })
    .catch(error => showError('Failed to create subtask'));
}

function updateSubtaskStatus(subtaskId, isCompleted) {
    const formData = new FormData();
    formData.append('subtask_id', subtaskId);
    formData.append('is_completed', isCompleted ? '1' : '0');

    fetch('php/tasks.php?action=update_subtask', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadTasks();
        } else {
            showError(data.message);
        }
    })
    .catch(error => showError('Failed to update subtask status'));
}

// UI Rendering
function renderTasks() {
    const taskList = document.getElementById('taskList');
    taskList.innerHTML = '';

    if (tasks.length === 0) {
        taskList.innerHTML = '<div class="alert alert-info">No tasks found.</div>';
        return;
    }

    tasks.forEach(task => {
        const taskElement = createTaskElement(task);
        taskList.appendChild(taskElement);
    });
}

function createTaskElement(task) {
    const card = document.createElement('div');
    card.className = 'card mb-3';
    card.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="card-title">${escapeHtml(task.title)}</h5>
                    <p class="card-text text-muted">${escapeHtml(task.description || '')}</p>
                    <span class="badge bg-${getPriorityColor(task.priority)}">${task.priority}</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-${task.status === 'completed' ? 'success' : 'outline-success'} me-2" 
                            onclick="updateTaskStatus(${task.id}, '${task.status === 'completed' ? 'active' : 'completed'}')">
                        <i class="bi bi-check-lg"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(${task.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    return card;
}

function renderSubtasks(task) {
    if (!task.subtasks || task.subtasks.length === 0) {
        return '';
    }

    return `
        <h6>Subtasks (${task.completed_subtasks}/${task.total_subtasks})</h6>
        <ul class="list-group">
            ${task.subtasks.map(subtask => `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <input type="checkbox" class="form-check-input me-2" 
                               ${subtask.is_completed ? 'checked' : ''} 
                               onchange="updateSubtaskStatus(${subtask.id}, this.checked)">
                        <span class="${subtask.is_completed ? 'text-decoration-line-through' : ''}">${escapeHtml(subtask.title)}</span>
                    </div>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSubtask(${subtask.id})">
                        <i class="bi bi-trash"></i>
                    </button>
                </li>
            `).join('')}
        </ul>
    `;
}

// Helper Functions
function calculateProgress(task) {
    if (!task.total_subtasks) return 0;
    return Math.round((task.completed_subtasks / task.total_subtasks) * 100);
}

function getPriorityColor(priority) {
    switch (priority.toLowerCase()) {
        case 'high': return 'danger';
        case 'medium': return 'warning';
        case 'low': return 'info';
        default: return 'secondary';
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showError(message) {
    const errorAlert = document.getElementById('errorAlert');
    errorAlert.textContent = message;
    errorAlert.classList.remove('d-none');
    setTimeout(() => {
        errorAlert.classList.add('d-none');
    }, 5000);
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    loadTasks();

    // Task form submission
    document.getElementById('taskForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const title = document.getElementById('taskTitle').value.trim();
        const description = document.getElementById('taskDescription').value.trim();
        const priority = document.getElementById('taskPriority').value;
        await createTask(title, description, priority);
    });

    // Navigation
    document.querySelectorAll('[data-view]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const view = e.target.dataset.view;
            currentView = view;
            document.querySelectorAll('[data-view]').forEach(l => l.classList.remove('active'));
            e.target.classList.add('active');
            loadTasks(view);
        });
    });

    // Search and filter
    const searchInput = document.getElementById('searchInput');
    const priorityFilter = document.getElementById('priorityFilter');
    const resetFilters = document.getElementById('resetFilters');

    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const priority = priorityFilter.value.toLowerCase();

        const filteredTasks = tasks.filter(task => {
            const matchesSearch = task.title.toLowerCase().includes(searchTerm) ||
                                (task.description && task.description.toLowerCase().includes(searchTerm));
            const matchesPriority = !priority || task.priority.toLowerCase() === priority;
            return matchesSearch && matchesPriority;
        });

        tasks = filteredTasks;
        renderTasks();
        tasks = originalTasks;
    }

    let originalTasks = [];
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

    const debouncedFilter = debounce(applyFilters, 300);

    searchInput.addEventListener('input', () => {
        originalTasks = [...tasks];
        debouncedFilter();
    });

    priorityFilter.addEventListener('change', () => {
        originalTasks = [...tasks];
        applyFilters();
    });

    resetFilters.addEventListener('click', () => {
        searchInput.value = '';
        priorityFilter.value = '';
        loadTasks(currentView);
    });
});
