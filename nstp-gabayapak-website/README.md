# NSTP GabaYapak Website

This is a Laravel-based website for the NSTP GabaYapak project management and monitoring system.

## Features Implemented

1. Account creation - SACSI staff and NSTP students needs to create their accounts to access the website. Registration approvals will be implemented, SACSi Director is auto-approved but only 1 SACSI Director account is allowed (thus, the account will be passed down if new staff assumes the position).
2. Dashboard - Display an overview about the projects, quick actions were added for increased accessibility, and the upcoming activities were listed in chronological order so that Staff will be guided with the next activities to be implemented per project (STAFF SIDE ONLY)
3. Current Projects Page - Display all approved projects (including projects with status "completed"). Organized per component and section. Contains All Approved Projects list
4. Pending Projects Page - Display pending projects that were submitted by the students. Only NSTP Formators are allowed to "endorse" projects and only the NSTP Coordinators are allowed to "approve" projects, but the SACSI Director and NSTP Program Officer can still view pending projects, organized by which stage they are in (to be endorsed/to be approved). Also included here is the list of rejected projects. Rejected projects by NSTP Formators will appear again in their Pending Projects Page when project leader resubmits. Projects rejected by NSTP Coordinators will appear on their end as well. Meaning, resubmitted projects will be displayed on the staff (NSTP Formator/NSTP Coordinator) until they endorse/approve those 9that is the time they can move on to the next stage of project approval. (STAFF SIDE ONLY)
5. Archived Projects Page - Only completed projects can be archived and will be displayed here. Staff can unarchive projects again and unarchived projects will reappear in the Current Projects Page in their respective component and section. (STAFF SIDE ONLY)
6. Upload Project Page - Each project leaders and members can only be tied to one project EXCEPT for students who are under the ROTC Component. This page is where the students will upload their project proposals and a project proposal can be saved as draft (requires Project Name and Team Name fields) or be submitted directly from the upload project page (STUDENT SIDE ONLY)
7. My Projects Page - Displays all relevant projects with all statuses belonging to that student (student who is logged in) or that a student is part/a member of (STUDENT SIDE ONLY)
8. Reports - Detailed summaries about the projects which includes counts, breakdown of total projects per component (only approved projects, regardless if the project has been completed/archived), and also includes a progress bar for each project. Progress bar relies on the division of activities and how many activities were outlines by the students. This is further divided into portions: Planned gets 1/3 of the entire activity's percentage, Ongoing gets 2/3, and Completed gets 3/3 or simply 1 (this means that for that specific activity item, it is 100% complete) (STAFF SIDE ONLY)
9. About Page - Displays relevant pictures in the form of sliders and info about the SACSI Office, the NSTP Program, and also includes a list of NSTP Formators (only the staff can manage the list)
10. Profile Page - Displays relevant information of the users, which depends on the information gathered from the account registration. All users can change their password, but only the SACSI Director, NSTP Program Officer, and the NSTP Coordinators can edit their information (name and formal picture only)
11. Logout - Logs user out of the website, redirected to login after logout.

## Installation

1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database settings
4. Run `php artisan key:generate`
5. Run `php artisan migrate:fresh` to create tables. (Seeding is optional.)

If you want to seed sample data, run:

```
php artisan migrate:fresh --seed
```
6. Run `php artisan serve` to start the development server

## Sample Users

After seeding the database, you can log in with the following credentials:

- **Admin User**: admin@example.com / password
- **Formator User**: formator@example.com / password
- **Student User**: student@example.com / password

## Features
- Responsive design with mobile-friendly sidebar
- Password change functionality in the account section
- Role-based access control
- Edit information feature in profile page for SACSI Director/NSTP Program Officer/NSTP Coordinators only

## Files
In the migrations, there are premade migrations (like jobs) and removed migration (like old users table which is premade). A new users migration was added and curated based on the needs of the website. other migrations are just removal of columns and addition of columns. 

For Auth Controllers, the AccountController is primarily involved in the Profile page. LoginController is for logging in. Staff logins will be redirected to Dashboard while Student logins will be redirected to the Upload Project page.

In Middleware, "CheckRole.php" is the old middleware used to check roles of users. New "CheckRoles.php" accepts and checks multiple roles

In app/Console\Commands, CleanupOrphanedMedia.php is a file that allows admins (who manage the system's technicalities) clean/delete photos that are no longer used in the system (like delete projects logos whose projects were also deleted previously, so that is will not hang around the storage folders)

Files in scripts folder were mostly used for debugging in the past and was not removed because it might be useful again sometime in the future