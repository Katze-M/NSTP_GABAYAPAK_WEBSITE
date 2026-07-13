<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class BulkStudentImportController extends Controller
{
    /**
     * Show the bulk student import form
     */
    public function showImportForm()
    {
        return view('bulk-import.form');
    }

    /**
     * Process the bulk student import from Excel file
     */
    public function import(Request $request)
    {
        // Validate file - use custom validation to accept common MIME types for CSV files
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:5120', // 5MB max
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();
                    
                    // Allow common file extensions
                    $allowedExt = ['csv', 'xlsx', 'xls'];
                    
                    // Allow common MIME types for these files
                    $allowedMimes = [
                        'text/csv',
                        'text/plain',
                        'application/csv',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ];
                    
                    if (!in_array($ext, $allowedExt) || !in_array($mimeType, $allowedMimes)) {
                        $fail('The file field must be a CSV or Excel file (csv, xlsx, xls).');
                    }
                },
            ],
        ]);

        try {
            $file = $request->file('file');
            
            // Use the temporary path where PHP stored the uploaded file
            // This avoids complex path handling issues on Windows
            $tempPath = $file->getPathname();
            $ext = strtolower($file->getClientOriginalExtension());
            
            if (!file_exists($tempPath)) {
                throw new \Exception("Uploaded file not found at temporary location");
            }
            
            // Parse the file directly from the temporary upload location
            $data = $this->parseFile($tempPath, $ext);

            if (empty($data)) {
                return back()->with('error', 'The file is empty or has invalid format.');
            }

            // Validate rows
            $validation = $this->validateRows($data);
            if (!$validation['valid']) {
                return back()->withErrors(['file' => implode(', ', $validation['errors'])]);
            }

            // Import students
            $result = $this->createStudentsFromData($data);

            return view('bulk-import.results', [
                'successful' => $result['successful'],
                'failed' => $result['failed'],
                'total' => count($data),
                'defaultPassword' => 'Student@NSTP123', // Inform users of the default password
            ]);
        } catch (\Exception $e) {
            \Log::error('Bulk import error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'An error occurred while processing the file: ' . $e->getMessage());
        }
    }

    /**
     * Parse Excel or CSV file
     */
    private function parseFile($filePath, $ext = null)
    {
        // If extension not provided, extract from path
        if (!$ext) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        }
        $ext = strtolower($ext);
        
        if (in_array($ext, ['xlsx', 'xls'])) {
            // For Excel files, try to use PhpSpreadsheet if available
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                return $this->parseExcel($filePath);
            } else {
                // Fallback: Load Excel using PHP's built-in zip capabilities for modern .xlsx files
                return $this->parseExcelWithZip($filePath);
            }
        } else {
            return $this->parseCSV($filePath);
        }
    }

    /**
     * Parse CSV file
     */
    private function parseCSV($filePath)
    {
        $data = [];
        
        // Verify file exists
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: " . $filePath);
        }
        
        // Verify file is readable
        if (!is_readable($filePath)) {
            throw new \Exception("File is not readable: " . $filePath);
        }
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            
            if (!$headers) {
                fclose($handle);
                return [];
            }
            
            // Trim header names
            $headers = array_map('trim', $headers);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (empty(array_filter($row))) continue; // Skip empty rows
                
                // Create associative array
                $rowAssoc = [];
                foreach ($headers as $i => $header) {
                    $rowAssoc[$header] = isset($row[$i]) ? trim($row[$i]) : '';
                }
                $data[] = $rowAssoc;
            }
            fclose($handle);
        } else {
            throw new \Exception("Failed to open CSV file for reading");
        }
        return $data;
    }

    /**
     * Parse Excel file using PhpSpreadsheet (if available)
     */
    private function parseExcel($filePath)
    {
        $data = [];
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            $headers = [];
            $firstRow = true;
            
            foreach ($sheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                
                if (empty(array_filter($rowData))) continue; // Skip empty rows
                
                if ($firstRow) {
                    $headers = array_map('trim', $rowData);
                    $firstRow = false;
                } else {
                    $rowAssoc = [];
                    foreach ($headers as $i => $header) {
                        $rowAssoc[$header] = isset($rowData[$i]) ? trim($rowData[$i]) : '';
                    }
                    $data[] = $rowAssoc;
                }
            }
        } catch (\Exception $e) {
            return [];
        }
        
        return $data;
    }

    /**
     * Parse Excel file using built-in ZIP (for .xlsx files)
     * This is a fallback when PhpSpreadsheet is not installed
     */
    private function parseExcelWithZip($filePath)
    {
        // For simplicity, only handle CSV files without external dependencies
        // Excel files should be uploaded as CSV
        return [];
    }

    /**
     * Validate rows have required fields
     */
    private function validateRows($data)
    {
        $required = ['Email', 'Full Name', 'Contact Number', 'Course', 'Year', 'Section', 'Component'];
        $errors = [];
        
        foreach ($data as $index => $row) {
            foreach ($required as $field) {
                if (!isset($row[$field]) || empty(trim($row[$field]))) {
                    $errors[] = "Row " . ($index + 2) . ": Missing or empty '$field'";
                }
            }
            
            // Validate email format (ADZU student ID format: co230123@adzu.edu.ph)
            if (isset($row['Email'])) {
                $email = trim($row['Email']);
                // Must match pattern: 2 letters + 6 digits + @adzu.edu.ph
                if (!preg_match('/^[a-zA-Z]{2}\d{6}@adzu\.edu\.ph$/', $email)) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid email format '$email'. Must be ADZU student ID format (e.g., co230123@adzu.edu.ph)";
                }
                
                // Check if email already exists
                if (User::where('user_Email', $email)->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Email '$email' already registered";
                }
            }
            
            // Validate year is numeric
            if (isset($row['Year'])) {
                if (!is_numeric(trim($row['Year']))) {
                    $errors[] = "Row " . ($index + 2) . ": Year must be numeric";
                }
            }
        }
        
        return [
            'valid' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    /**
     * Create students from validated data
     */
    private function createStudentsFromData($data)
    {
        $successful = 0;
        $failed = [];
        $defaultPassword = 'Student@NSTP123'; // Default password for all imported students
        
        foreach ($data as $index => $row) {
            try {
                $email = trim($row['Email']);
                $name = trim($row['Full Name']);
                
                // Create User with default password (hashed)
                $user = User::create([
                    'user_Name' => $name,
                    'user_Email' => $email,
                    'user_Password' => Hash::make($defaultPassword),
                    'user_Type' => 'student',
                    'user_role' => 'Student',
                    'approved' => true, // Auto-approve on import
                ]);
                
                // Create Student Profile
                Student::create([
                    'user_id' => $user->id,
                    'student_contact_number' => trim($row['Contact Number']),
                    'student_course' => trim($row['Course']),
                    'student_year' => (int)trim($row['Year']),
                    'student_section' => trim($row['Section']),
                    'student_component' => trim($row['Component']),
                ]);
                
                // Create auto-approved Approval record
                Approval::create([
                    'user_id' => $user->id,
                    'type' => 'student',
                    'status' => 'approved',
                ]);
                
                $successful++;
            } catch (\Exception $e) {
                $failed[] = [
                    'row' => $index + 2,
                    'email' => trim($row['Email'] ?? 'N/A'),
                    'reason' => $e->getMessage(),
                ];
            }
        }
        
        return [
            'successful' => $successful,
            'failed' => $failed,
        ];
    }

    /**
     * Download CSV template
     */
    public function downloadTemplate()
    {
        try {
            $filename = 'student_import_template_' . date('Y-m-d') . '.csv';
            
            // Create CSV content
            $headers = ['Email', 'Full Name', 'Contact Number', 'Course', 'Year', 'Section', 'Component'];
            $example = [
                'co230123@adzu.edu.ph',
                'Juan Dela Cruz',
                '09123456789',
                'BS Computer Science',
                '2',
                'Section A',
                'ROTC',
            ];
            
            // Set headers for file download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            
            // Output CSV
            $output = fopen('php://output', 'w');
            fputcsv($output, $headers);
            fputcsv($output, $example);
            fclose($output);
            exit();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to download template: ' . $e->getMessage());
        }
    }
}

