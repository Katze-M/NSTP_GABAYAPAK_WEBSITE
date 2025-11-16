<?php
// Sample pending projects data (will replace with DB query results)
$rotc_projects = [
    [
        'id' => 10,
        'title' => 'Advanced Combat Training',
        'team' => 'Team Thunder',
        'submitted_date' => '2024-02-01',
        'status' => 'pending'
    ],
    [
        'id' => 11,
        'title' => 'Emergency Response Unit',
        'team' => 'Team Storm',
        'submitted_date' => '2024-02-02',
        'status' => 'pending'
    ]
];

$lts_projects = [
    [
        'id' => 12,
        'title' => 'Adult Basic Education',
        'team' => 'Team Phoenix',
        'submitted_date' => '2024-02-03',
        'status' => 'pending'
    ],
    [
        'id' => 13,
        'title' => 'Computer Literacy for Seniors',
        'team' => 'Team Silver',
        'submitted_date' => '2024-02-04',
        'status' => 'pending'
    ],
    [
        'id' => 14,
        'title' => 'Reading Recovery Program',
        'team' => 'Team Rainbow',
        'submitted_date' => '2024-02-05',
        'status' => 'pending'
    ]
];

$cwts_projects = [
    [
        'id' => 15,
        'title' => 'Youth Sports Development',
        'team' => 'Team Victory',
        'submitted_date' => '2024-02-06',
        'status' => 'pending'
    ],
    [
        'id' => 16,
        'title' => 'Senior Citizens Care Program',
        'team' => 'Team Golden',
        'submitted_date' => '2024-02-07',
        'status' => 'pending'
    ],
    [
        'id' => 17,
        'title' => 'Disaster Preparedness Initiative',
        'team' => 'Team Shield',
        'submitted_date' => '2024-02-08',
        'status' => 'pending'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pending Projects</title>
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
      <h1 class="text-2xl md:text-4xl font-bold text-black mb-2">Pending Projects</h1>
      <p class="text-base md:text-lg text-gray-700">Review and take action on pending submissions.</p>
    </div>

    <!-- Pending Projects Section -->
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
                  <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      View
                    </a>
                    <button onclick="approveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                      Approve
                    </button>
                    <button onclick="rejectProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      Reject
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No pending ROTC projects</p>
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
                  <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      View
                    </a>
                    <button onclick="approveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                      Approve
                    </button>
                    <button onclick="rejectProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      Reject
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No pending LTS projects</p>
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
                  <div class="flex flex-col sm:flex-row gap-2 sm:gap-2">
                    <a href="project_details.php?id=<?php echo $project['id']; ?>" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm text-center font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                      View
                    </a>
                    <button onclick="approveProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                      Approve
                    </button>
                    <button onclick="rejectProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title']); ?>')" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-xs md:text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                      Reject
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center text-gray-500 py-6 md:py-8 text-sm md:text-base">No pending CWTS projects</p>
            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

// Approve project function
function approveProject(projectId, projectTitle) {
  Swal.fire({
    title: 'Approve Project?',
    html: `Are you sure you want to approve<br><strong>"${projectTitle}"</strong>?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#16a34a',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Yes, approve it!',
    cancelButtonText: 'Cancel',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      // TODO: Add AJAX call to approve the project
      // For now, show success message
      Swal.fire({
        title: 'Approved!',
        text: `"${projectTitle}" has been approved successfully.`,
        icon: 'success',
        confirmButtonColor: '#3b82f6'
      }).then(() => {
        // Reload page or remove element
        location.reload();
      });
    }
  });
}

// Reject project function
function rejectProject(projectId, projectTitle) {
  // First modal: Ask for rejection reason
  Swal.fire({
    title: 'Reason for Rejection',
    html: `
      <div class="text-center">
        <p class="mb-2 text-gray-700">Please provide a reason for rejecting</p>
        <p class="font-bold mb-3">"${projectTitle}"</p>
        <textarea id="rejectionReason" class="swal2-textarea" placeholder="Enter rejection reason..." rows="4" style="width: 100%; min-height: 100px; resize: vertical; text-align: left;"></textarea>
      </div>
    `,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Continue',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    preConfirm: () => {
      const reason = document.getElementById('rejectionReason').value;
      if (!reason || reason.trim() === '') {
        Swal.showValidationMessage('Please provide a rejection reason');
        return false;
      }
      return reason;
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const rejectionReason = result.value;
      
      // Second modal: Confirmation
      Swal.fire({
        title: 'Confirm Rejection?',
        html: `
          <div class="text-center">
            <p class="mb-2 text-gray-700">You are about to reject:</p>
            <p class="font-bold mb-4">"${projectTitle}"</p>
            <div class="text-left">
              <p class="mb-2 text-gray-700 font-semibold">Reason:</p>
              <p class="bg-gray-100 p-3 rounded text-sm text-gray-800">${rejectionReason}</p>
            </div>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, reject it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
          // TODO: Add AJAX call to reject the project with the reason
          // For now, show success message
          Swal.fire({
            title: 'Rejected!',
            text: `"${projectTitle}" has been rejected.`,
            icon: 'success',
            confirmButtonColor: '#3b82f6'
          }).then(() => {
            // Reload page or remove element
            location.reload();
          });
        }
      });
    }
  });
}
</script>

</body>
</html>
