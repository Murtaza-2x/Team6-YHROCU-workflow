# ROCU Tasks & Projects

A simple PHP web application that allows admins to create and manage tasks and projects, with user assignment using Auth0 for authentication and user management.

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Project Structure](#project-structure)
- [Configuration](#configuration)
- [Usage](#usage)

---

## Overview

This application enables users (admin or normal roles) to:

- Log in via Auth0.
- Create and manage tasks.
- Assign users from Auth0 to specific tasks.
- View project details and tasks.
- Track status and priority (e.g., New, In Progress, Complete, Urgent, etc.).
- Store and display the creator’s Auth0 user info (`created_by`).

---

## Requirements

- **PHP 7.4+** (or 8.x)
- **MySQL** (or compatible MariaDB)
- **Composer** (for dependency management)
- **Auth0** account (for authentication/user management)

---

## Installation

1. **Install dependencies**:

   composer install

2. **Set up your database**:

   Create a new MySQL database (e.g., rocu_tasks).

   Run any provided SQL migrations or create tables manually. Tables typically include:
    - tasks
    - projects
    - task_assigned_users

    Ensure the tasks table has a created_by column (VARCHAR(255)) to store the Auth0 user ID.

3. **Configure environment variables**:

   Copy .env.example to .env (if provided) or create a new .env file.

   Add your database credentials, Auth0 credentials, and any other sensitive info.

4. **Set up your web server**:

  Configure your Apache/nginx document root to point to the project folder (or a public subdirectory if you have one).

  Ensure any .htaccess or rewrite rules (if needed) are properly

**Project Structure**

  A simplified overview of the relevant files/folders:

your-project/
├── INCLUDES/
│   ├── env_loader.php
│   ├── role_helper.php
│   ├── inc_connect.php
│   ├── inc_disconnect.php
│   ├── inc_header.php
│   ├── inc_footer.php
│   ├── inc_dashboard.php
│   ├── inc_taskcreate.php
│   └── Auth0UserFetcher.php
├── create-task-page.php
├── list-task-page.php
├── view-task-page.php
├── create-project-page.php
├── view-project-page.php
├── index.php
├── .gitignore
├── composer.json
└── README.md

**Configuration**

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