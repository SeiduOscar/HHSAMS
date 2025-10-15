🎓 Student Attendance Management System (SAMS)

Investor-Focused Summary
The Student Attendance Management System (SAMS) revolutionizes how educational institutions manage attendance by combining automation, security, and AI-powered facial recognition. It eliminates manual record-keeping, reduces absenteeism, and provides real-time data analytics for better decision-making.
Designed for scalability, SAMS can be adopted across universities and schools, with potential for commercial licensing and institutional integration.

📖 Overview

The Student Attendance Management System (SAMS) is a next-generation web platform that digitizes attendance processes for schools and universities.
It features multi-role access, QR code attendance, facial verification, and advanced reporting, ensuring accuracy, transparency, and efficiency in student record management.

🎯 Key Objectives

Automate attendance and eliminate paper-based tracking

Enhance accountability through real-time verification

Provide insightful analytics for administrators

Improve security using AI-based facial recognition

👥 User Roles and Permissions
🧑‍💼 Administrator

Manage faculties, departments, and courses

Create, edit, and delete user accounts

Set academic semesters/terms

Generate detailed attendance reports

View real-time system metrics

👨‍🏫 Lecturer / Class Teacher

Generate unique QR codes for each class

Record attendance and monitor trends

Access class-wise and student-specific reports

Validate attendance using face recognition

🧑‍🎓 Student

Scan QR codes to mark attendance

Verify identity via facial recognition

Access personal attendance statistics and logs

🧩 Core Features
✅ 1. QR Code Attendance

Unique, secure QR codes per class session

Token-based validation for authenticity

Real-time update to attendance database

🧠 2. Facial Recognition

AI verification powered by Python’s face_recognition

Facial encoding at registration

85%+ accuracy threshold for validation

📊 3. Real-Time Reporting

Visual dashboards using Chart.js

Generate and export attendance reports

Weekly, monthly, and semester-based statistics

🔐 4. Security and Access Control

Encrypted passwords (password_hash())

SQL injection and XSS protection

Role-based access and session validation

🗄️ Database Overview
Core Tables
Table Name	Description
tbladmin	Admin accounts
tblmoderator	Lecturer accounts
tblstudents	Student details + facial data
tblattendance	Attendance logs
tblcourses	Course details
tblsemester	Academic terms
qr_tokens	Time-limited QR validation tokens