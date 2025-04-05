# ROCU Tasks & Projects

A simple PHP web application that allows admins to create and manage tasks and projects, with user assignment using Auth0 for authentication and user management.

   **For now for demonstration purposes ALL Configuration is done. You do not need to do anything with 0Auth.**

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

---

## Overview

This application enables users (admin or normal roles) to:

- Log in via Auth0.
- Create and manage tasks.
- Assign users from Auth0 to specific tasks.
- View project details and tasks.
- Track status and priority (e.g., New, In Progress, Complete, Urgent, etc.).

  **EMAIL NOTIFICATION Mailtrap.io ACCOUNT DETAILS (SIGN IN WITH GOOGLE):**
  Yhrocunotifications@gmail.com 
  YHROCUPass1

---

## Requirements

- **PHP 7.4+** (or 8.x)
- **MySQL** (or compatible MariaDB)
- **Composer** (for dependency management)
- **Auth0** account (for authentication/user management)

---

## Installation

1. **Clone Main Repository**:

   Create YHROCU-CLONE for 0Auth to work.
   Clone the repository into your htdocs/YHROCU-CLONE folder.

3. **Install dependencies**:

   Install/Use composer and change directory into the SOFTWARE_AUTH folder and run this command:
   
   composer install

5. **Set up your database**:

   Create a new MySQL database (e.g., rocu).

   Run rocu.sql file which can be found inside DATABASE folder.

6. **Set up your web server if not using XAMPP**:

  Configure your Apache/nginx document root to point to the project folder (or a public subdirectory if you have one).

  Ensure any .htaccess or rewrite rules (if needed) are properly.

  If using XAMPP run Apache & MySQL.

**Configuration (Not required currntly)**

   Auth0
   You’ll need valid Auth0 credentials:
    - Domain: your-tenant.auth0.com
    - Client ID
    - Client Secret

   Set these in .env or your environment variables so the app can authenticate and fetch user details.

**Usage**

   - Login
    Navigate to /index.php in your browser.
    Log in using Auth0 (admin role required to create tasks/projects).

   - Create a Task
    Visit create-task-page.php.
    Fill out the form (subject, project, status, priority, description) and assign at least one user.
    Submit to see a success message and redirect to the new task’s page.

   - View Tasks
    Go to list-task-page.php or your dashboard.
    See all tasks (or only those assigned to you, depending on your role), along with status and priority labels.

   - View a Single Task
    Click on a task link to open view-task-page.php?id=....
    See detailed info, including assigned users and the user who created the task (created_by).

   - Projects
    Create a new project via create-project-page.php.
    View project details at view-project-page.php?id=....

   **Testing**

   To run php tests:
    -	Open a terminal inside C:\xampp\htdocs\YHROCU-CLONE\Team6-YHROCU-workflow\SOFTWARE_AUTH.
    -	Make sure composer is installed on your computer and run “composer install”
    -	Run “vendor\bin\phpunit” for all tests or append with UNITTESTING/[test file] to test a specific file.
    -	
  To run node.js tests:
    -	Install Node.js & NPM with commands: “node -v” and “npm -v”
    -	Install jest with commands: “npm init -y” and “npm install --save-dev jest”
    -	Run “npm test” or “npx jest path/to/[test file].js”

