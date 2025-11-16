# NSTP GabaYapak Website

This is a Laravel-based website for the NSTP GabaYapak project management system.

## Features Implemented

1. **User Authentication**
   - Login functionality with email and password
   - Registration for both students and staff
   - Password change functionality in the account section

2. **User Roles**
   - Student
   - Staff (NSTP Formator, NSTP Program Officer, SACSI Director, SACSI Admin Staff)

3. **Models**
   - User model with proper database schema mapping
   - Student model for student-specific information
   - Staff model for staff-specific information

4. **Views**
   - Login page
   - Registration page with role selection
   - Account page with password change functionality
   - Homepage with NSTP Formators section
   - Dashboard
   - Sidebar component

5. **Controllers**
   - LoginController for authentication
   - RegisterController for user registration
   - AccountController for account management

6. **Middleware**
   - CheckRole middleware for role-based access control

7. **Database**
   - Users table
   - Students table
   - Staff table
   - Proper foreign key relationships

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database settings
4. Run `php artisan key:generate`
5. Run `php artisan migrate:fresh --seed` to create tables and seed sample data
6. Run `php artisan serve` to start the development server

## Sample Users

After seeding the database, you can log in with the following credentials:

- **Admin User**: admin@example.com / password
- **Formator User**: formator@example.com / password
- **Student User**: student@example.com / password

## Features

- Staff with the "NSTP Program Officer" role can edit the NSTP Formators list on the homepage
- Responsive design with mobile-friendly sidebar
- Password change functionality in the account section
- Role-based access control