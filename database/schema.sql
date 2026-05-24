-- Employee Management System Database Schema

CREATE DATABASE IF NOT EXISTS employee_management;
USE employee_management;

-- Departments Table
CREATE TABLE departments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dept_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Designations Table
CREATE TABLE designations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    dept_id INT NOT NULL,
    designation_name VARCHAR(100) NOT NULL,
    description TEXT,
    salary_range_min DECIMAL(10,2),
    salary_range_max DECIMAL(10,2),
    hierarchy_level INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dept_id) REFERENCES departments(id) ON DELETE CASCADE,
    UNIQUE KEY unique_designation (dept_id, designation_name)
);

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') DEFAULT 'employee',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Employees Table
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    emp_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    postal_code VARCHAR(10),
    country VARCHAR(50),
    dept_id INT NOT NULL,
    designation_id INT NOT NULL,
    joining_date DATE NOT NULL,
    employment_type ENUM('Full-Time', 'Part-Time', 'Contract', 'Intern') DEFAULT 'Full-Time',
    salary DECIMAL(10,2),
    bank_account_number VARCHAR(50),
    bank_ifsc_code VARCHAR(20),
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    qualification TEXT,
    skills TEXT,
    status ENUM('Active', 'Inactive', 'On Leave', 'Terminated') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dept_id) REFERENCES departments(id),
    FOREIGN KEY (designation_id) REFERENCES designations(id)
);

-- Attendance Table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emp_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    check_in_time TIME,
    check_out_time TIME,
    status ENUM('Present', 'Absent', 'Late', 'Half-Day', 'WFH') DEFAULT 'Absent',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (emp_id, attendance_date)
);

-- Leave Types Table
CREATE TABLE leave_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    leave_name VARCHAR(50) NOT NULL UNIQUE,
    days_allowed INT NOT NULL,
    is_paid TINYINT(1) DEFAULT 1,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Leave Requests Table
CREATE TABLE leave_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emp_id INT NOT NULL,
    leave_type_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    num_days INT NOT NULL,
    reason TEXT,
    status ENUM('Pending', 'Approved', 'Rejected', 'Cancelled') DEFAULT 'Pending',
    approved_by INT,
    approval_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id),
    FOREIGN KEY (approved_by) REFERENCES employees(id)
);

-- Leave Balance Table
CREATE TABLE leave_balance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emp_id INT NOT NULL UNIQUE,
    leave_type_id INT NOT NULL,
    allocated_days INT NOT NULL,
    used_days INT DEFAULT 0,
    balance_days INT,
    year INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (leave_type_id) REFERENCES leave_types(id)
);

-- Expense Request Table
CREATE TABLE expense_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emp_id INT NOT NULL,
    expense_category VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    expense_date DATE NOT NULL,
    description TEXT,
    receipt_path VARCHAR(255),
    status ENUM('Pending', 'Approved', 'Rejected', 'Reimbursed') DEFAULT 'Pending',
    approved_by INT,
    approval_remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES employees(id)
);

-- Tasks Table
CREATE TABLE tasks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_name VARCHAR(150) NOT NULL,
    description TEXT,
    assigned_by INT NOT NULL,
    assigned_to INT NOT NULL,
    department_id INT,
    priority ENUM('Low', 'Medium', 'High', 'Urgent') DEFAULT 'Medium',
    status ENUM('Not Started', 'In Progress', 'Completed', 'On Hold', 'Cancelled') DEFAULT 'Not Started',
    due_date DATE,
    completion_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_by) REFERENCES employees(id),
    FOREIGN KEY (assigned_to) REFERENCES employees(id),
    FOREIGN KEY (department_id) REFERENCES departments(id)
);

-- Task Comments Table
CREATE TABLE task_comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    task_id INT NOT NULL,
    emp_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (emp_id) REFERENCES employees(id) ON DELETE CASCADE
);

-- Payroll Table
CREATE TABLE payroll (
    id INT PRIMARY KEY AUTO_INCREMENT,
    emp_id INT NOT NULL,
    payroll_month INT NOT NULL,
    payroll_year INT NOT NULL,
    basic_salary DECIMAL(10,2),
    overtime_hours DECIMAL(5,2),
    overtime_amount DECIMAL(10,2),
    deductions DECIMAL(10,2),
    penalties DECIMAL(10,2),
    bonuses DECIMAL(10,2),
    net_salary DECIMAL(10,2),
    status ENUM('Draft', 'Processed', 'Paid') DEFAULT 'Draft',
    payment_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (emp_id) REFERENCES employees(id) ON DELETE CASCADE,
    UNIQUE KEY unique_payroll (emp_id, payroll_month, payroll_year)
);

-- Announcements Table
CREATE TABLE announcements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_by INT NOT NULL,
    target_audience ENUM('All', 'Department', 'Specific') DEFAULT 'All',
    target_dept_id INT,
    is_published TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES employees(id),
    FOREIGN KEY (target_dept_id) REFERENCES departments(id)
);

-- Audit Log Table
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_value LONGTEXT,
    new_value LONGTEXT,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Create Indexes for Performance
CREATE INDEX idx_emp_dept ON employees(dept_id);
CREATE INDEX idx_emp_designation ON employees(designation_id);
CREATE INDEX idx_attendance_date ON attendance(attendance_date);
CREATE INDEX idx_leave_status ON leave_requests(status);
CREATE INDEX idx_task_status ON tasks(status);
CREATE INDEX idx_payroll_month_year ON payroll(payroll_month, payroll_year);

-- Insert Sample Data
INSERT INTO departments (dept_name, description) VALUES
('Human Resources', 'HR Department'),
('Information Technology', 'IT Department'),
('Sales', 'Sales Department'),
('Finance', 'Finance Department'),
('Operations', 'Operations Department');

INSERT INTO leave_types (leave_name, days_allowed, is_paid) VALUES
('Casual Leave', 12, 1),
('Sick Leave', 10, 1),
('Annual Leave', 20, 1),
('Unpaid Leave', 10, 0),
('Maternity Leave', 180, 1);

-- Insert Admin User
INSERT INTO users (username, email, password_hash, role, is_active) VALUES
('admin', 'admin@employeemgmt.com', '$2y$10$YourHashedPasswordHere', 'admin', 1);
