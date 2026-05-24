# Employee Management System

A comprehensive employee management system built for XAMPP with Admin and Employee workflows.

## Features

### Admin Workflow
- **System Setup**: Department and designation configuration
- **Employee Onboarding**: Register new employees with auto-credential generation
- **Attendance & Leave Management**: Track attendance and process leave requests
- **Payroll Processing**: Calculate and process employee salaries
- **Profile Management**: Edit, review, and audit employee records
- **Task Allocation**: Assign tasks and monitor workflow

### Employee Workflow
- **Self-Service Profile**: View and update personal information
- **Clock In/Out**: Digital time tracking
- **Timesheet Reviews**: Monitor working hours and overtime
- **Leave Requests**: Submit and track leave applications
- **Expense Management**: Submit and track expenses
- **Task Management**: View assigned tasks and update progress
- **Announcements**: Read company-wide updates

## Installation

1. Extract the project to `htdocs` folder in XAMPP
2. Create a MySQL database named `employee_management`
3. Import `database/schema.sql`
4. Update database credentials in `config/db.php`
5. Access via `http://localhost/rudilyn`

## Directory Structure

```
├── admin/
├── employee/
├── config/
├── database/
├── assets/
├── includes/
└── index.php
```

## Default Credentials

- **Admin Username**: admin
- **Admin Password**: Admin@123

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache (XAMPP)
- Modern Web Browser
