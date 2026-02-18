# StudyOrganizer – Laravel Study Planner & Assignment Tracker

<div style="width: auto; display: flex; justify-content: center">
<img alt="favicon.svg" src="public/favicon.svg" width="25%"/>
</div>


---

## Overview

StudyOrganizer is a full-stack Laravel application designed to manage courses, assignments, submissions, grading, and
personal notes within an academic environment.

The system supports multiple user roles (students, professors, administrators) and implements structured authorization
to ensure secure and controlled access to resources.

This project was developed as an advanced student project with a focus on clean architecture, security, and
production-style deployment practices.

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

The project includes an automation script designed to provision and deploy the application on an AWS EC2 instance.

The script handles:

* Nginx installation and configuration
* PHP-FPM setup
* Composer installation
* Node installation
* SQLite database setup (default)
* Environment configuration
* Permissions setup (www-data / app user)
* Database migrations and seeding
* HTTPS configuration via Certbot
* APP_URL configuration

The deployment workflow was tested on AWS EC2.
Compatibility with other providers (Google Cloud, Azure) has not been officially tested and may require adjustments.

---

## Technologies Used

* Laravel
* PHP
* Blade
* Tailwind CSS
* Filament (Admin panel)
* SQLite (default) – easily configurable
* Nginx
* PHP-FPM
* AWS EC2
* Cloudflare (DNS configuration)

---

## Possible Future Improvements

* Database switch to PostgreSQL or MySQL in production
* RDS integration
* Horizontal scaling support
* Messaging/notification system
* Activity logs
* CI/CD pipeline integration
* Containerized deployment (Docker/Kubernetes)
