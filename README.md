# Attendance Reporting System

## Overview
The Attendance Reporting System is a PHP-based web application that allows users to manage and report attendance records using a MySQL database. This system is designed to simplify the process of tracking attendance for various events or classes.

## Features
- User authentication (login and registration)
- Attendance tracking for users
- Reporting features to generate attendance summaries
- Admin panel for managing users and attendance records

## Technologies Used
- PHP 7.x or higher
- MySQL 5.x or higher
- HTML/CSS for frontend
- JavaScript for interactivity

## Installation

### Prerequisites
- A web server (e.g., Apache or Nginx)
- PHP installed on the server
- MySQL database server

### Steps
1. **Clone the repository:**
   ```bash
   git clone https://github.com/ayalaHer/Attendance-Reporting-System.git
   ```

2. **Navigate to the project directory:**
   ```bash
   cd Attendance-Reporting-System
   ```

3. **Create a MySQL database:**
   ```sql
   CREATE DATABASE attendance_reporting_system_db;
   ```

4. **Import the database schema:**
   - Locate the `schema.sql` file in the project directory and import it into the `attendance_db` database.

5. **Configure the database connection:**
   - Open `config.php` and update the database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'attendance_reporting_system_db');
     ```

6. **Start the web server and access the application:**
   - Navigate to `http://localhost/AttendanceReportingSystemProject/index.html` in your web browser.

## Usage
- Register a new account or log in with existing credentials.
- Navigate to the attendance section to mark attendance or view reports.

## Contributing
Feel free to fork the repository and submit pull requests for any improvements or features.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments
- PHP and MySQL documentation for reference.
- Open-source libraries used in the project.