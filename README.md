# To Do List with AI

A modern, feature-rich task management web application built with PHP, JavaScript, and MySQL.

## Features

- **User Authentication**
  - Secure registration and login system
  - User profile management
  - Session handling and security

- **Task Management**
  - Create, update, and delete tasks
  - Add subtasks to break down complex tasks
  - Track task progress
  - Set task priorities
  - Filter and view tasks by different criteria

- **User Interface**
  - Clean and intuitive interface
  - Dark/Light theme toggle
  - Responsive design for all devices
  - Real-time updates
  - Error handling and user feedback

- **Task Features**
  - Priority levels with color coding
  - Progress tracking for tasks with subtasks
  - Date formatting and management
  - Task suggestions
  - HTML escape functionality for security

## Project Structure

```
├── css/
│   ├── dark-mode.css       # Dark theme styling
│   └── style.css          # Main application styles
├── js/
│   ├── auth.js            # Authentication functions
│   ├── main.js            # Core functionality
│   └── tasks.js           # Task management functions
├── php/
│   ├── auth.php           # Authentication backend
│   ├── config.php         # Application configuration
│   ├── db.php            # Database connection
│   ├── get_task_suggestions.php
│   ├── login.php         # Login handling
│   ├── register.php      # Registration handling
│   └── tasks.php         # Task management backend
├── about.php             # About page
├── index.php            # Main application page
├── login.php           # Login page
├── profile.php         # User profile page
└── register.php        # Registration page
```

## Technical Details

### Frontend
- JavaScript-based task management
- Real-time UI updates
- Theme switching functionality
- Filter system for tasks
- Error handling and display

### Backend
- PHP-based REST API
- LiteSQL database integration
- User authentication system
- Secure data handling

## Getting Started

1. Configure your web server with PHP and MySQL support
2. Set up the database using the provided schema
3. Update `php/config.php` with your database credentials
4. Deploy the application files to your web server
5. Access the application through your web browser

## Security Features

- Password hashing
- Session management
- HTML escaping for user input
- Secure database queries
- Input validation

## Requirements

- PHP 7.4+
- LiteSQL
- Modern web browser with JavaScript enabled
- Web server (Apache/Nginx recommended)

## Contributing

Feel free to submit issues and enhancement requests.

## License

This project is licensed under the MIT License - see the LICENSE file for details.


