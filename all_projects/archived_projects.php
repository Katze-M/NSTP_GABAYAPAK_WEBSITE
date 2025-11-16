<?php
// Sample archived projects data (will replace with DB query results)
$rotc_projects = [
    [
        'id' => 20,
        'title' => 'Basic Military Training',
        'team' => 'Team Eagle',
        'submitted_date' => '2023-12-15',
        'status' => 'archived'
    ],
    [
        'id' => 21,
        'title' => 'Cadet Leadership Program',
        'team' => 'Team Falcon',
        'submitted_date' => '2023-12-20',
        'status' => 'archived'
    ]
];

$lts_projects = [
    [
        'id' => 22,
        'title' => 'Early Childhood Literacy',
        'team' => 'Team Sunshine',
        'submitted_date' => '2023-12-10',
        'status' => 'archived'
    ],
    [
        'id' => 23,
        'title' => 'English Language Learning',
        'team' => 'Team Stars',
        'submitted_date' => '2023-12-12',
        'status' => 'archived'
    ],
    [
        'id' => 24,
        'title' => 'Mathematics Tutoring Program',
        'team' => 'Team Numbers',
        'submitted_date' => '2023-12-18',
        'status' => 'archived'
    ]
];

$cwts_projects = [
    [
        'id' => 25,
        'title' => 'Community Clean-up Drive',
        'team' => 'Team Green',
        'submitted_date' => '2023-12-05',
        'status' => 'archived'
    ],
    [
        'id' => 26,
        'title' => 'Feeding Program for Children',
        'team' => 'Team Heart',
        'submitted_date' => '2023-12-08',
        'status' => 'archived'
    ],
    [
        'id' => 27,
        'title' => 'Elderly Care Initiative',
        'team' => 'Team Wisdom',
        'submitted_date' => '2023-12-14',
        'status' => 'archived'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Archived Projects</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .light-pink-bg { background-color: #FFE4E1; }
  </style>
</head>
<body class="bg-gray-100 flex">

  <!-- Sidebar -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content -->
  <main id="content" class="flex-1 md:ml-64 p-4 md:p-6 transition-all duration-300 bg-white min-h-screen">

    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
      <h1 class="text-2xl md:text-4xl font-bold text-black mb-2">Archived Projects</h1>
      <p class="text-base md:text-lg text-gray-700">View archived project submissions.</p>
    </div>

    <!-- Archived Projects Section -->
    <div class="light-pink-bg p-4 md:p-6 rounded-lg">
      <div class="space-y-6 md:space-y-8">
        
        <!-- ROTC Projects -->
        <div>
          <h3 class="text-xl md:text-2xl font-bold text-black mb-3 md:mb-4">ROTC</h3>
          <div class="bg-white rounded-lg p-4 md:p-6 min-h-[120px]">
            <?php if (!empty($rotc_projects)): ?>
              <?php foreach ($rotc_projects as $project): ?>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 md:p-4 border-b border-gray-200 last:border-b-0 gap-3">
                  <div class="flex-1 min-w-0">
                    <h4 class="text-base md:text-lg font-bold text-black"><?php echo htmlspecialchars($project['title']); ?></h4>
                    <p class="text-sm md:text-base text-gray-600 italic"><?php echo htmlspecialchars($project['team']); ?></p>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      View
                    </a>
                    <button onclick="unarchiveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                      Unarchive
                    </button>
                    <button onclick="deleteProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      Delete
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No archived ROTC projects</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- LTS Projects -->
        <div>
          <h3 class="text-xl md:text-2xl font-bold text-black mb-3 md:mb-4">LTS</h3>
          <div class="bg-white rounded-lg p-4 md:p-6 min-h-[120px]">
            <?php if (!empty($lts_projects)): ?>
              <?php foreach ($lts_projects as $project): ?>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 md:p-4 border-b border-gray-200 last:border-b-0 gap-3">
                  <div class="flex-1 min-w-0">
                    <h4 class="text-base md:text-lg font-bold text-black"><?php echo htmlspecialchars($project['title']); ?></h4>
                    <p class="text-sm md:text-base text-gray-600 italic"><?php echo htmlspecialchars($project['team']); ?></p>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      View
                    </a>
                    <button onclick="unarchiveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                      Unarchive
                    </button>
                    <button onclick="deleteProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      Delete
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No archived LTS projects</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- CWTS Projects -->
        <div>
          <h3 class="text-xl md:text-2xl font-bold text-black mb-3 md:mb-4">CWTS</h3>
          <div class="bg-white rounded-lg p-4 md:p-6 min-h-[120px]">
            <?php if (!empty($cwts_projects)): ?>
              <?php foreach ($cwts_projects as $project): ?>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 md:p-4 border-b border-gray-200 last:border-b-0 gap-3">
                  <div class="flex-1 min-w-0">
                    <h4 class="text-base md:text-lg font-bold text-black"><?php echo htmlspecialchars($project['title']); ?></h4>
                    <p class="text-sm md:text-base text-gray-600 italic"><?php echo htmlspecialchars($project['team']); ?></p>
                  </div>
                  <div class="flex flex-wrap gap-2">
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      View
                    </a>
                    <button onclick="unarchiveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                      Unarchive
                    </button>
                    <button onclick="deleteProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center flex-1 sm:flex-initial font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      Delete
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No archived CWTS projects</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- JS -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const menuBtn = document.getElementById('menuBtn');
      const sidebar = document.getElementById('sidebar');

      if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
          sidebar.classList.toggle('-translate-x-full');
        });
      }
    });

    // Unarchive project function
    function unarchiveProject(projectId, projectTitle) {
      Swal.fire({
        title: 'Unarchive Project?',
        html: `Are you sure you want to restore<br><strong>"${projectTitle}"</strong><br>to current projects?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, unarchive it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // TODO: Add AJAX call to unarchive the project
          // For now, show success message
          Swal.fire({
            title: 'Unarchived!',
            text: 'Project has been restored to current projects.',
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
