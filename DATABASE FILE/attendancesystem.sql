-- Programs Table
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_name VARCHAR(100) NOT NULL
);

-- Students Table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    index_number VARCHAR(20) UNIQUE,
    name VARCHAR(100),
    email VARCHAR(100),
    gender ENUM('male', 'female'),
    level ENUM('100', '200', '300', '400'),
    program_id INT,
    FOREIGN KEY (program_id) REFERENCES programs(id)
);

-- Lecturers Table
CREATE TABLE lecturers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    department VARCHAR(100)
);

-- Courses Table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_code VARCHAR(20) UNIQUE,
    course_name VARCHAR(100),
    level ENUM('100', '200', '300', '400'),
    program_id INT,
    lecturer_id INT,
    FOREIGN KEY (program_id) REFERENCES programs(id),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id)
);

-- Course Enrollments Table (Many-to-Many)
CREATE TABLE course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- Attendance Table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    lecturer_id INT,
    date DATE,
    time TIME,
    location VARCHAR(255),
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (lecturer_id) REFERENCES lecturers(id)
);

-- Attendance Reports Table
CREATE TABLE attendance_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attendance_id INT,
    student_id INT,
    status ENUM('Present', 'Absent'),
    remarks TEXT,
    FOREIGN KEY (attendance_id) REFERENCES attendance(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Admins Table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Leave Requests Table
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT,
    is_staff BOOLEAN,
    leave_date DATE,
    leave_type ENUM('sick', 'personal', 'vacation') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Student Notifications Table
CREATE TABLE student_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    message TEXT,
    type ENUM('attendance', 'leave', 'general') DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Staff Notifications Table
CREATE TABLE staff_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    staff_id INT,
    message TEXT,
    type ENUM('attendance', 'leave', 'general') DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (staff_id) REFERENCES lecturers(id)
);

-- Feedback Table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    is_staff BOOLEAN,
    message TEXT NOT NULL,
    response_status ENUM('pending', 'responded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
