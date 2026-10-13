# StudyOrganizer – Laravel Study Planner & Assignment Tracker

<p align="center">
  <img src="public/favicon.svg" alt="StudyOrganizer Logo" width="25%">
</p>

---

## Overview

StudyOrganizer is a full-stack Laravel application designed to manage courses, assignments, submissions, grading, and
personal notes within an academic environment.

The system supports multiple user roles (students, professors, administrators) and implements structured authorization
to ensure secure and controlled access to resources.

This project was developed as an advanced student project with a focus on clean architecture, security, and
production-style deployment practices.

### Developer guides

- [Local setup, administrator accounts, and testing](docs/development.md)
- [Production Docker deployment and troubleshooting](docs/docker-production.md)
- [Docker image publishing and GitHub releases](docs/releasing.md)
- [Changelog](CHANGELOG.md)

Deployment domains, account details, and credentials are configured locally.
The examples in these guides use placeholder domains and addresses.

---

## Purpose

The goal of StudyOrganizer is to simulate a lightweight Learning Management System (LMS) that demonstrates:

* Role-based access control
* Secure assignment submission and grading workflows
* Course-scoped resource management
* File upload and storage abstraction
* Clean architectural separation
* Production-style deployment setup

The project emphasizes understanding how backend systems work beyond basic CRUD functionality.

---

## Core Features

### Authentication & Roles

* Laravel Breeze authentication
* Interactive administrator creation with `php artisan admin:create`
* Role enum system (STUDENT, PROFESSOR, ADMIN)
* Role-based access restrictions
* Gates and Policies for model-level authorization

### Courses

* Course creation and management (Admin)
* Assignment of students and professors to courses
* Scoped access to course-specific resources

### Assignments

* Course-scoped assignment management
* Configurable fields (title, description, due date, grading strategy, attempt limits, etc.)
* Publish/unpublish functionality
* Professors/Admins can create, edit, and delete assignments

### Submissions

* Students can submit assignments (with attempt limits)
* Markdown-supported submission content
* File attachments
* Controlled editing before grading
* Professors/Admins can grade and ungrade submissions

### Notes System

* Global notes (not tied to a course)
* Course-specific notes
* Markdown editor integration
* Owner-only access
* XSS-aware rendering

### File Management

* Course materials upload
* Submission attachments
* Environment-based storage disk configuration
* Download and inline file responses
* File type labeling utility
* Configurable upload size limits (Nginx + PHP)

### Admin Panel

* Built with Filament (Admin-only)
* User management
* Course management
* Pivot-based assignment of students/professors

---

## Architecture Highlights

* Clean Controller-based architecture (no heavy UI frameworks for main app)
* Explicit routing (no full `Route::resource` overuse)
* Dedicated Action classes for domain logic (e.g., grading)
* Scoped route bindings for nested resources
* Policy-based authorization
* Environment-driven storage abstraction
* Markdown rendering component with sanitation considerations

---

## Deployment & Infrastructure

For container deployment behind the shared Nginx proxy, follow
**[the production Docker guide](docs/docker-production.md)**. It covers native
Nginx and Docker Nginx, image publishing, migrations, persistent files, backups,
and HTTPS forwarding. The existing `docker-compose.yaml` remains a development
database setup; production uses `docker-compose.prod.yml` explicitly.

The project also includes a legacy automation script for provisioning a native
PHP-FPM installation on an AWS EC2 instance. Use the production Docker guide
for deployment behind an existing shared Nginx proxy.

### Automated Provisioning Script

The setup script handles:

* Nginx installation and configuration
* PHP-FPM setup
* Composer installation
* Node installation
* SQLite database setup (default)
* Environment configuration
* Permissions setup (www-data / app user)
* Database migrations and seeding
* HTTPS configuration via Certbot
* Automatic upload limit configuration (default: 20MB)
* APP_URL configuration

Upload limits are automatically applied to:

* Nginx (`client_max_body_size`)
* PHP (`upload_max_filesize`, `post_max_size`)

This ensures consistent file size handling across the stack.

---

## Database Support

### Default: SQLite

The project uses SQLite by default for simplicity and rapid deployment.

### PostgreSQL (Production-Ready)

The application is fully compatible with PostgreSQL and supports migration to:

* Local PostgreSQL
* AWS RDS PostgreSQL

A dedicated automation script is included to:

* Install the required `pdo_pgsql` PHP extension
* Update `.env` configuration to use `pgsql`
* Configure database credentials
* Clear Laravel caches
* Run migrations
* Optionally seed the database

### Moving existing data

Changing the database connection and running Laravel migrations does not copy
existing data. Transfer PostgreSQL data using a logical dump/restore; SQLite
requires a separately verified data conversion. Preserve the existing Laravel
key and transfer private uploads as well as public files.

The legacy `database_transition_script_to_postgres_rds.sh` uses
`migrate:fresh --force`, which drops existing tables. It provisions an empty
database and is not a data transfer script. Do not run it against a database
whose data you need to preserve. See the migration section of the production guide.

---

## AWS Deployment Compatibility

The deployment workflow has been tested on:

* AWS EC2 (Ubuntu)
* Nginx + PHP-FPM
* Optional AWS RDS PostgreSQL
* Cloudflare DNS integration

Elastic IP usage is recommended to prevent IP changes during instance stop/start operations.

Compatibility with other cloud providers (Google Cloud, Azure) has not been officially tested and may require
adjustments.

---

## Technologies Used

* Laravel
* PHP
* Blade
* Tailwind CSS
* Filament (Admin panel)
* SQLite (default)
* PostgreSQL (optional production configuration)
* Nginx
* PHP-FPM
* AWS EC2
* AWS RDS (optional)
* Cloudflare (DNS configuration)

---

## Possible Future Improvements

* Full RDS production configuration templates
* Horizontal scaling support
* Messaging/notification system
* Activity logs
* CI/CD pipeline integration
* Kubernetes deployment
* Direct-to-S3 file uploads for large file optimization
