# Student Attendance Management System

A comprehensive web-based attendance management system with facial recognition capabilities, designed for educational institutions to track student attendance efficiently.

## System Overview

The Student Attendance Management System (SAMS) is a PHP-based web application that provides:
- Multi-role access (Administrator, Lecturer, Student)
- QR code-based attendance tracking
- Facial recognition for attendance verification
- Comprehensive reporting and analytics
- Real-time dashboard with statistics

## User Roles and Permissions

### 1. Administrator
- **Dashboard**: View system statistics (students, classes, attendance records)
- **User Management**: Create, edit, and delete admin users
- **Institution Management**: Manage departments, faculties, colleges
- **Course Management**: Create and manage courses
- **Class Management**: Manage classes and class arms
- **Semester/Term Management**: Set active semesters
- **Reports**: Generate attendance reports and analytics

### 2. Lecturer/Class Teacher
- **Dashboard**: View personal teaching statistics
- **Attendance Management**: Take attendance via QR codes
- **Student Management**: View student lists
- **Reports**: View attendance trends and records
- **QR Code Generation**: Generate attendance QR codes for classes

### 3. Student
- **Attendance**: Mark attendance via QR code scanning
- **Facial Recognition**: Verify identity through facial recognition
- **View Records**: Access personal attendance records

## Database Schema

The system uses MySQL database with the following main tables:

### Core Tables
- `tbladmin` - Administrator users
- `tblmoderator` - Lecturer/Teacher users
- `tblstudents` - Student information with facial encodings
- `tblattendance` - Attendance records
- `tblcourses` - Course information
- `tblclass` - Class information
- `tblclassarms` - Class arm/division information
- `tblsemester` - Academic semesters/terms
- `tbldepartment` - Academic departments
- `tblfaculty` - Faculty information
- `tblcollege` - College information
- `qr_tokens` - QR code validation tokens

### Level-specific Tables
- `level_100`, `level_200`, `level_300`, `level_400` - Student data organized by academic level

## Key Features

### 1. QR Code Attendance System
- Lecturers generate QR codes for specific classes
- Students scan QR codes to mark attendance
- Automatic validation with token-based security
- Real-time attendance status updates

### 2. Facial Recognition Integration
- Students register facial encodings during enrollment
- Real-time face detection and matching
- Confidence-based attendance verification (85%+ threshold)
- Webcam integration for live capture

### 3. Comprehensive Reporting
- Weekly, monthly, and yearly attendance trends
- Graphical representation of attendance data
- Export functionality for records
- Real-time dashboard statistics

### 4. Multi-level Access Control
- Role-based permissions system
- Session management and security
- Password hashing with modern encryption

## Installation and Setup

### Prerequisites
- XAMPP/WAMP/LAMP stack
- PHP 7.4+
- MySQL 5.7+
- Python 3.13+ (for facial recognition)
- Face recognition libraries

### Installation Steps

1. **Clone or extract the project files** to your web server directory
   ```
   c:/xampp/htdocs/Student-Attendance-Management-System-main/
   ```

2. **Create the database**
   ```sql
   CREATE DATABASE tbl;
   ```

3. **Import the database schema**
   ```sql
   USE tbl;
   SOURCE DATABASE FILE/attendancesystem.sql;
   ```

4. **Configure database connection**
   Edit `Includes/dbcon.php` with your database credentials:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "root"; // Change to your MySQL password
   $db = "tbl";
   ```

5. **Set up Python environment for facial recognition**
   ```bash
   pip install face-recognition
   pip install numpy
   pip install opencv-python
   ```

6. **Configure Python path**
   Update the Python path in recognition files to match your installation:
   ```php
   $python = escapeshellarg("C:\\Python313\\python.exe");
   ```

7. **Set file permissions**
   Ensure uploads directory has write permissions:
   ```
   chmod 755 recognition/uploads/
   ```

8. **Start the web server**
   ```
   Start Apache and MySQL services
   ```

## Usage Guide

### For Administrators

1. **Login**: Access the system at `http://localhost/Student-Attendance-Management-System-main/`
2. **Select Role**: Choose "Administrator" and enter credentials
3. **Dashboard**: View system overview and statistics
4. **Manage Users**: Use admin panel to add/edit admin users
5. **Configure System**: Set up departments, faculties, courses, and semesters

### For Lecturers

1. **Login**: Access with lecturer credentials
2. **Dashboard**: View teaching statistics and upcoming classes
3. **Generate QR Codes**: Create attendance QR codes for classes
4. **View Reports**: Access attendance trends and student records
5. **Manage Attendance**: Review and update attendance records

### For Students

1. **Scan QR Code**: Use mobile device to scan class QR code
2. **Facial Verification**: Capture face for identity verification
3. **Mark Attendance**: System automatically records attendance upon successful verification

## Technical Details

### Frontend Technologies
- HTML5, CSS3, JavaScript
- Bootstrap 4.6+ for responsive design
- Chart.js for data visualization
- jQuery for DOM manipulation

### Backend Technologies
- PHP 7.4+ for server-side processing
- MySQL for data storage
- Python 3.13+ for facial recognition algorithms

### Security Features
- Password hashing with `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session management with proper validation
- QR code token validation with expiration

### Facial Recognition System
- Uses `face_recognition` Python library
- Stores facial encodings as JSON in database
- Real-time webcam capture and processing
- Confidence threshold of 85% for verification

## File Structure

```
Student-Attendance-Management-System-main/
├── Admin/                 # Administrator module
│   ├── Includes/         # Admin includes (sidebar, topbar)
│   ├── ajax/             # AJAX handlers
│   ├── css/              # Admin stylesheets
│   ├── js/               # Admin JavaScript
│   └── *.php            # Admin functionality files
├── ClassTeacher/          # Lecturer module
│   ├── Includes/         # Lecturer includes
│   ├── css/              # Lecturer styles
│   ├── js/               # Lecturer scripts
│   └── *.php            # Lecturer functionality
├── recognition/          # Facial recognition module
│   ├── uploads/          # Temporary image storage
│   ├── known_faces/      # Pre-registered faces
│   └── *.py             # Python recognition scripts
├── Includes/             # Shared includes
│   ├── dbcon.php        # Database connection
│   ├── session.php      # Session management
│   └── *.php            # Other shared functionality
├── DATABASE FILE/        # Database schema files
├── vendor/              # Third-party libraries
├── uploads/             # File uploads directory
└── *.php               # Main application files
```

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `Includes/dbcon.php`
   - Ensure MySQL service is running
   - Verify database 'tbl' exists

2. **Facial Recognition Not Working**
   - Verify Python installation path
   - Check face_recognition library installation
   - Ensure webcam permissions are granted

3. **QR Code Generation Issues**
   - Check PHP QR code library installation
   - Verify token table exists in database

4. **Session Problems**
   - Check PHP session configuration
   - Verify file permissions on session directory

5. **File Upload Issues**
   - Check uploads directory permissions
   - Verify PHP file_upload settings

### Performance Optimization

- Enable PHP opcache for better performance
- Use MySQL indexing on frequently queried columns
- Implement caching for static content
- Optimize image processing for facial recognition

## Support

For technical support or questions about the system, please contact your system administrator or refer to the documentation files in the project directory.

## License

This project is licensed under the terms included in the LICENSE file.

## Future Enhancements

- Advanced analytics and machine learning predictions
- Integration with learning management systems
- Biometric device integration
- Offline capability for remote areas

---





*Last Updated: [11/10/2025]*
*System Version: 1.0*
