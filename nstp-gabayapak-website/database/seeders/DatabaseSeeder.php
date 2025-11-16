<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create sample users - check if they already exist first
        $adminUser = User::firstOrCreate(
            ['user_Email' => 'admin@example.com'],
            [
                'user_Name' => 'Admin User',
                'user_Password' => Hash::make('password'),
                'user_Type' => 'staff',
                'user_role' => 'SACSI Admin Staff',
            ]
        );

        $formatorUser = User::firstOrCreate(
            ['user_Email' => 'formator@example.com'],
            [
                'user_Name' => 'Formator User',
                'user_Password' => Hash::make('password'),
                'user_Type' => 'staff',
                'user_role' => 'NSTP Formator',
            ]
        );

        $studentUser = User::firstOrCreate(
            ['user_Email' => 'student@example.com'],
            [
                'user_Name' => 'Student User',
                'user_Password' => Hash::make('password'),
                'user_Type' => 'student',
                'user_role' => 'Student',
            ]
        );

        // Create student profile if it doesn't exist
        if (!$studentUser->student) {
            Student::create([
                'user_id' => $studentUser->user_id,
                'student_contact_number' => '09123456789',
                'student_course' => 'BSIT',
                'student_year' => 2,
                'student_section' => 'Section A',
                'student_component' => 'CWTS',
            ]);
        }

        // Create staff profiles if they don't exist
        if (!$adminUser->staff) {
            Staff::create([
                'user_id' => $adminUser->user_id,
                'staff_formal_picture' => '',
            ]);
        }

        if (!$formatorUser->staff) {
            Staff::create([
                'user_id' => $formatorUser->user_id,
                'staff_formal_picture' => '',
            ]);
        }
    }
}