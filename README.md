# AI-Powered ToDo List Application

A modern, feature-rich task management application enhanced with AI capabilities for smart task organization. Built with PHP, SQLite, and Bootstrap, featuring a responsive design with dark/light theme support.

## 🌟 Features

### User Management
- 🔐 Secure authentication system with password hashing
- 👤 User registration with email verification
- 🔄 Profile management (update username, email, password)
- 🚪 Session-based authentication with secure cookie handling

### Task Management
- ✨ AI-powered task optimization
  - Smart title refinement
  - Automatic description generation
  - Context-aware task suggestions
- ✅ Create, read, update, and delete tasks
- 🏷️ Priority levels (High, Medium, Low)
- 📋 Task status tracking (Active/Completed)
- 🔍 Advanced task filtering and search
- 📱 Responsive design for all devices

### UI/UX Features
- 🌓 Dark/Light theme toggle
- 💫 Modern, clean interface
- 🎨 Bootstrap 5 components
- 📱 Mobile-first responsive design
- ⚡ Real-time updates
- 🔔 User-friendly notifications

### Security Features
- 🔒 CSRF protection
- 🛡️ SQL injection prevention
- 🔐 XSS protection
- 🚫 Session hijacking prevention
- 📝 Input validation and sanitization

## 🚀 Technologies Used

- **Frontend**:
  - HTML5, CSS3, JavaScript
  - Bootstrap 5
  - Custom responsive design
  - Dark/Light theme support

- **Backend**:
  - PHP 7.4+
  - SQLite3 database
  - RESTful API architecture

- **AI Integration**:
  - Google Gemini API for task optimization
  - Natural language processing for task enhancement

## 📋 Requirements

- PHP 7.4 or higher
- SQLite3
- Modern web browser with JavaScript enabled
- Apache/Nginx web server

## 🛠️ Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/todo-list-ai.git
   ```

2. Configure your web server:
   - Point document root to the `todolist` directory
   - Ensure PHP has write permissions for the `database` directory

3. Set up the database:
   ```bash
   # The database will be automatically initialized on first run
   # Make sure the database directory is writable
   chmod 755 database
   ```

4. Configure the application:
   - Copy `config.example.php` to `config.php`
   - Update the configuration values as needed
   - Add your Gemini API key for AI features

5. Access the application:
   - Visit `http://localhost/todo-list-ai`
   - Register a new account
   - Start managing your tasks!

## 🔧 Configuration

Key configuration options in `config.php`:
```php
// Database Configuration
define('DB_PATH', 'database/todo.db');

// Security Settings
define('CSRF_TOKEN_SECRET', 'your-secret-key');
define('SESSION_LIFETIME', 3600);

// API Configuration
define('GEMINI_API_KEY', 'your-api-key');
```

## 📱 Usage

1. **User Registration/Login**:
   - Register with email and password
   - Login with credentials
   - Update profile information as needed

2. **Task Management**:
   - Create new tasks with title and description
   - Optionally use AI to optimize task content
   - Set priority levels
   - Mark tasks as complete
   - Filter and search tasks

3. **Theme Customization**:
   - Toggle between dark and light themes
   - Theme preference is saved automatically

## 🔒 Security

- Implements CSRF protection for all forms
- Uses prepared statements for database queries
- Sanitizes user input
- Secures session management
- Implements proper password hashing
- Protects against XSS attacks

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Bootstrap for the UI framework
- Google Gemini for AI capabilities
- SQLite for the database
- PHP community for inspiration and support

## 📞 Contact

Your Name - [@yourusername](https://twitter.com/yourusername)

Project Link: [https://github.com/yourusername/todo-list-ai](https://github.com/yourusername/todo-list-ai)

---
Made with ❤️ using PHP, SQLite, and AI 