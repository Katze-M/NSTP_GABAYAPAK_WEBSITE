<?php
// Sample LTS projects data (will replace with DB query results)
$projects = [
    'A' => [
        [
            'id' => 1,
            'title' => 'Literacy Program for Children',
            'team' => 'Team Gamma',
            'submitted_date' => '2024-01-19',
            'approved_date' => '2024-01-24',
            'status' => 'ongoing',
            'component' => 'LTS',
            'section' => 'Section A'
        ],
        [
            'id' => 2,
            'title' => 'Digital Skills Training',
            'team' => 'Team Delta',
            'submitted_date' => '2024-01-20',
            'approved_date' => '2024-01-25',
            'status' => 'ongoing',
            'component' => 'LTS',
            'section' => 'Section A'
        ]
    ],
    'B' => [
        [
            'id' => 3,
            'title' => 'Environmental Awareness Campaign',
            'team' => 'Team Epsilon',
            'submitted_date' => '2024-01-21',
            'approved_date' => '2024-01-26',
            'status' => 'ongoing',
            'component' => 'LTS',
            'section' => 'Section B'
        ]
    ],
    'C' => [
        [
            'id' => 4,
            'title' => 'Health Education Program',
            'team' => 'Team Zeta',
            'submitted_date' => '2024-01-22',
            'approved_date' => '2024-01-27',
            'status' => 'ongoing',
            'component' => 'LTS',
            'section' => 'Section C'
        ]
    ],
    'D' => [],
    'E' => [],
    'F' => [],
    'G' => [],
    'H' => [],
    'I' => [],
    'J' => [],
    'K' => [],
    'L' => [],
    'M' => [],
    'N' => [],
    'O' => [],
    'P' => [],
    'Q' => [],
    'R' => [],
    'S' => [],
    'T' => [],
    'U' => [],
    'V' => [],
    'W' => [],
    'X' => [],
    'Y' => [],
    'Z' => []
];

// Get section from URL, default "A"
$section = isset($_GET['section']) ? strtoupper($_GET['section']) : 'A';

// Validate section (A-Z only)
if (!preg_match('/^[A-Z]$/', $section)) {
    $section = 'A';
}

// Get projects for current section
$currentProjects = $projects[$section] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LTS Projects - Section <?php echo $section; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .light-pink-bg { background-color: #FDF0E6; }
    .salmon-sidebar { background-color: #F8DCDC; }
    .section-nav-active { background-color: #FFEEDD; color: #000 !important; font-weight: 600; }
  </style>
</head>
<body class="bg-gray-100 flex">

  <!-- Include Sidebar -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content -->
  <main id="content" class="flex-1 md:ml-64 p-4 md:p-6 transition-all duration-300 light-pink-bg min-h-screen">

  <!-- Page Header -->
  <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div class="flex items-center space-x-2 md:space-x-4">
      <!-- Back Button -->
        <?php $backLink = "current_projects.php"; ?>
        <?php include '../components/back_button.php'; ?>

      <!-- Page Title -->
        <h1 class="text-xl md:text-4xl font-bold text-black">LTS Projects - Section <?php echo $section; ?></h1>
    </div>
  </div>

    <!-- Section Navigation -->
    <div class="mb-4 md:mb-6">
      <div class="bg-white p-3 md:p-4 rounded-xl shadow-sm border border-gray-200">
        <h3 class="text-sm md:text-base font-semibold text-gray-700 mb-3">Select Section:</h3>
        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-13 gap-2">
          <?php foreach (range('A', 'Z') as $letter): ?>
            <a href="?section=<?php echo $letter; ?>" 
               class="flex items-center justify-center w-8 h-8 md:w-10 md:h-10 rounded-lg text-xs md:text-sm font-semibold transition-all duration-200 transform hover:scale-105
                      <?php echo $section === $letter ? 'bg-blue-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-blue-100 hover:text-blue-700'; ?>">
              <?php echo $letter; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Projects Section -->
    <div class="light-pink-bg p-4 md:p-6 rounded-lg">
      <div class="space-y-3 md:space-y-4">
        <?php if (!empty($currentProjects)): ?>
          <?php foreach ($currentProjects as $project): ?>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 bg-white rounded-lg shadow-sm gap-3">
              <div class="flex-1 min-w-0">
                <h4 class="text-base md:text-lg font-bold text-black"><?php echo htmlspecialchars($project['title']); ?></h4>
                <p class="text-sm md:text-base text-gray-600 italic"><?php echo htmlspecialchars($project['team']); ?></p>
                <p class="text-xs md:text-sm text-gray-500">Submitted: <?php echo htmlspecialchars($project['submitted_date']); ?></p>
                <p class="text-xs md:text-sm text-gray-500">Approved: <?php echo htmlspecialchars($project['approved_date']); ?></p>
              </div>
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3">
                <span class="px-3 py-1 rounded-full text-xs md:text-sm font-medium 
                  <?php echo $project['status'] === 'ongoing' ? 'bg-blue-100 text-blue-800' : 
                            ($project['status'] === 'approved' ? 'bg-green-100 text-green-800' : 
                            ($project['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')); ?>">
                  <?php echo ucfirst($project['status']); ?>
                </span>
                <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                  <a href="project_details.php?id=<?php echo $project['id']; ?>&component=LTS" 
                     class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    View
                  </a>
                  <button onclick="archiveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" 
                     class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                    Archive
                  </button>
                  <button onclick="deleteProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" 
                     class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Delete
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No projects found in LTS Section <?php echo $section; ?></p>
        <?php endif; ?>
      </div>
    </div>

  </main>

  <!-- Additional JavaScript for mobile menu -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile menu toggle
      const menuBtn = document.getElementById('menuBtn');
      const sidebar = document.getElementById('sidebar');
      
      if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
          sidebar.classList.toggle('-translate-x-full');
        });
      }
    });

    // Archive project function
    function archiveProject(projectId, projectTitle) {
      Swal.fire({
        title: 'Archive Project?',
        html: `Are you sure you want to archive<br><strong>"${projectTitle}"</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, archive it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // TODO: Add AJAX call to archive the project
          // For now, show success message
          Swal.fire({
            title: 'Archived!',
            text: 'Project has been archived successfully.',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
          }).then(() => {
            // Reload page or remove element
            location.reload();
          });
        }
      });
    }

    // Delete project function
    function deleteProject(projectId, projectTitle) {
      Swal.fire({
        title: 'Delete Project?',
        html: `Are you sure you want to permanently delete<br><strong>"${projectTitle}"</strong>?<br><br><span class="text-red-600 font-semibold">This action cannot be undone!</span>`,
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // TODO: Add AJAX call to delete the project
          // For now, show success message
          Swal.fire({
            title: 'Deleted!',
            text: 'Project has been deleted permanently.',
            icon: 'success',
            confirmButtonColor: '#3b82f6'
          }).then(() => {
            // Reload page or remove element
            location.reload();
          });
        }
      });
    }
  </script>

</body>
</html>