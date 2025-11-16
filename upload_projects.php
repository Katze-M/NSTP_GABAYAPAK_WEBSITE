<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's a draft or final submission
    if (isset($_POST['save_draft'])) {
        // Handle save as draft
        // TODO: Add database logic to save draft
        
        // For now, show success and redirect
        echo "<script>
            window.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Draft Saved!',
                    text: 'Your project draft has been saved successfully.',
                    icon: 'success',
                    confirmButtonColor: '#2b50ff'
                }).then(() => {
                    window.location.href = '../dashboard/dashboard.php';
                });
            });
        </script>";
    } elseif (isset($_POST['submit_project'])) {
        // Handle project submission
        // TODO: Add database logic to save submitted project
        
        // For now, show success and redirect
        echo "<script>
            window.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Project Submitted!',
                    text: 'Your project has been submitted successfully for review.',
                    icon: 'success',
                    confirmButtonColor: '#2b50ff'
                }).then(() => {
                    window.location.href = '../dashboard/dashboard.php';
                });
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NSTP Project Management and Monitoring System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            sidebar: '#e86d63',
            sidebarAccent: '#f5dedd',
            pill: '#fbeaea',
            nstpBlue: '#2b50ff',
            nstpYellow: '#f2d35b',
            nstpMaroon: '#8e2b2b'
          },
          boxShadow: { subtle: '0 2px 6px rgba(0,0,0,0.1)' }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100">

<?php include '../components/sidebar.php'; ?>

<!-- Main Content -->
<main id="content" class="flex-1 md:ml-64 p-4 md:p-8 transition-all duration-300 bg-white min-h-screen">

  <!-- Project Proposal -->
  <section id="upload-project" class="space-y-6 md:space-y-8">

    <!-- Main Heading -->
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-4 md:mb-6 flex items-center gap-2">Project Proposal</h1>

    
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6 md:space-y-8">
      
      <!-- TEAM INFORMATION -->
      <div class="rounded-2xl bg-pill p-6 shadow-subtle space-y-4">
        <h2 class="text-2xl font-bold flex items-center gap-2">
          <span class="text-3xl">🖼️</span> Team Information
        </h2>

        <div class="space-y-3">
          <div>
            <label class="block text-lg font-medium">Project Name<span class="text-red-500">*</span></label>
            <input name="project_name" class="w-full px-3 py-2 rounded-lg border-gray-300" placeholder="Name of Project" required>
          </div>
          <div>
            <label class="block text-lg font-medium">Team Name<span class="text-red-500">*</span></label>
            <input name="team_name" class="w-full px-3 py-2 rounded-lg border-gray-300" placeholder="Name of Team" required>
          </div>
          <div>
            <label class="block text-lg font-medium">Team Logo<span class="text-red-500">*</span></label>
            <input type="file" name="team_logo" class="w-full px-3 py-2 rounded-lg border-gray-300 bg-white" required>
          </div>
          <!-- Component Dropdown -->
          <div class="relative">
            <label class="block text-lg font-medium">Component<span class="text-red-500">*</span></label>
            <select name="component" class="w-full px-3 py-2 rounded-lg border-gray-300 bg-white relative z-10 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
              <option value="">Select Component</option>
              <option value="LTS">Literacy Training Service (LTS)</option>
              <option value="CWTS">Civic Welfare Training Service (CWTS)</option>
              <option value="ROTC">Reserve Officers' Training Corps (ROTC)</option>
            </select>
          </div>
          <!-- Section Dropdown -->
          <div class="relative">
            <label class="block text-lg font-medium">Section<span class="text-red-500">*</span></label>
            <select name="nstp_section" required 
                    class="w-full px-3 py-2 rounded-lg border-gray-300 bg-white text-black relative z-10 focus:outline-none focus:ring-2 focus:ring-blue-400">
              <option value="" disabled selected>Section</option>
              <?php foreach (range('A', 'Z') as $letter): 
                $value = "Section $letter";
              ?>
                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- MEMBER PROFILE -->
      <div class="rounded-2xl bg-pill p-4 md:p-6 shadow-subtle">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2">
          <span class="text-2xl md:text-3xl">👥</span> Member Profile
        </h2>

        <!-- Desktop Table View -->
        <div class="hidden md:block mt-4">
          <div class="bg-white rounded-xl shadow-subtle overflow-hidden border border-gray-200">
            <table id="memberTable" class="w-full text-left">
              <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Name <span class="text-red-500">*</span>
                  </th>
                  <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Role/s <span class="text-red-500">*</span>
                  </th>
                  <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    School Email <span class="text-red-500">*</span>
                  </th>
                  <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                    Contact Number <span class="text-red-500">*</span>
                  </th>
                  <th class="px-6 py-4 text-sm font-semibold text-gray-700 uppercase tracking-wider text-center">
                    Action
                  </th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors">
                  <td class="px-6 py-4">
                    <input name="member_name[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Enter full name" required>
                  </td>
                  <td class="px-6 py-4">
                    <input name="member_role[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" required>
                  </td>
                  <td class="px-6 py-4">
                    <input type="email" name="member_email[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" required>
                  </td>
                  <td class="px-6 py-4">
                    <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                      Remove
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Mobile Card View -->
        <div id="memberContainer" class="md:hidden mt-4 space-y-3">
          <div class="member-card bg-white p-3 rounded-lg border shadow-sm space-y-3">
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
              <input name="member_name[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
            </div>
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
              <input name="member_role[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
            </div>
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
              <input type="email" name="member_email[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" placeholder="co230123@adzu.edu.ph" required>
            </div>
            <div class="space-y-1">
              <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
              <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
            </div>
            <div class="flex justify-end">
              <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
            </div>
          </div>
        </div>

        <button type="button" id="addMemberRow" class="mt-4 bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 text-xs md:text-sm">+ Add Member</button>
      </div>

      <!-- PROJECT DETAILS -->
      <div class="rounded-2xl bg-pill p-6 shadow-subtle space-y-4">
        <h2 class="text-2xl font-bold flex items-center gap-2">
          <span class="text-3xl">🎯</span> Project Details
        </h2>

        <div class="space-y-3">
          <div>
            <label class="block text-lg font-medium">Issues/Problem being addressed<span class="text-red-500">*</span></label>
            <textarea name="issues" rows="4" class="mt-1 w-full rounded-lg border-gray-300 auto-expand" required></textarea>
          </div>
          <div>
            <label class="block text-lg font-medium">Goal/Objectives<span class="text-red-500">*</span></label>
            <textarea name="objectives" rows="4" class="mt-1 w-full rounded-lg border-gray-300 auto-expand" required></textarea>
          </div>
          <div>
            <label class="block text-lg font-medium">Target Community<span class="text-red-500">*</span></label>
            <textarea name="target_community" rows="2" class="mt-1 w-full rounded-lg border-gray-300 auto-expand" required></textarea>
          </div>
          <div>
            <label class="block text-lg font-medium">Solutions/Activities to be implemented<span class="text-red-500">*</span></label>
            <textarea name="solutions" rows="4" class="mt-1 w-full rounded-lg border-gray-300 auto-expand" required></textarea>
          </div>
          <div>
            <label class="block text-lg font-medium">Expected Outcomes<span class="text-red-500">*</span></label>
            <textarea name="outcomes" rows="5" class="mt-1 w-full rounded-lg border-gray-300 auto-expand" required></textarea>
          </div>
        </div>
      </div>

      <!-- PROJECT ACTIVITIES -->
      <div class="rounded-2xl bg-pill p-4 md:p-6 shadow-subtle">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
          <span class="text-2xl md:text-3xl">📅</span> Project Activities
        </h2>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
          <div class="bg-white rounded-xl shadow-subtle overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200 px-6 py-4">
              <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                <div>Stage <span class="text-red-500">*</span></div>
                <div>Specific Activities <span class="text-red-500">*</span></div>
                <div>Time Frame <span class="text-red-500">*</span></div>
                <div>Point Person/s <span class="text-red-500">*</span></div>
                <div>Status</div>
                <div>Action</div>
              </div>
            </div>
            <div id="activitiesContainer" class="divide-y divide-gray-100">
              <div class="activity-row hover:bg-gray-50 transition-colors px-6 py-4">
                <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 items-start">
                  <input name="stage[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Planning" required>
                  <textarea name="activities[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
                  <input name="timeframe[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Week 1-2" required>
                  <textarea name="point_person[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
                  <select name="status[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
                    <option>Planned</option>
                    <option>Ongoing</option>
                  </select>
                  <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
          <div id="activitiesContainerMobile" class="space-y-3">
            <div class="activity-row space-y-3 p-3 border rounded bg-white shadow-sm">
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
                <input name="stage[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" placeholder="Stage" required>
              </div>
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
                <textarea name="activities[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Specific Activities" required></textarea>
              </div>
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
                <input name="timeframe[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" placeholder="Time Frame" required>
              </div>
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
                <textarea name="point_person[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Point Person/s" required></textarea>
              </div>
              <div class="flex flex-col sm:flex-row gap-2">
                <div class="space-y-1 flex-1">
                  <label class="block text-xs font-medium text-gray-600">Status</label>
                  <select name="status[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm">
                    <option>Planned</option>
                    <option>Ongoing</option>
                  </select>
                </div>
                <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <button type="button" id="addActivityRow" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">+ Add Activity</button>
      </div>

      <!-- BUDGET -->
      <div class="rounded-2xl bg-pill p-4 md:p-6 shadow-subtle">
        <h2 class="text-xl md:text-2xl font-bold flex items-center gap-2 mb-4">
          <span class="text-2xl md:text-3xl">💰</span> Budget
        </h2>

        <!-- Desktop Table View -->
        <div class="hidden md:block">
          <div class="bg-white rounded-xl shadow-subtle overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-b border-gray-200 px-6 py-4">
              <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 text-sm font-semibold text-gray-700 uppercase tracking-wider">
                <div>Activity</div>
                <div>Resources Needed</div>
                <div>Partner Agencies</div>
                <div>Amount</div>
                <div>Action</div>
              </div>
            </div>
            <div id="budgetContainer" class="divide-y divide-gray-100">
              <div class="budget-row hover:bg-gray-50 transition-colors px-6 py-4">
                <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
                  <textarea name="budget_activity[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none budget-optional" rows="2" placeholder="Describe the activity..."></textarea>
                  <textarea name="budget_resources[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none budget-optional" rows="2" placeholder="List resources needed..."></textarea>
                  <textarea name="budget_partners[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none budget-optional" rows="2" placeholder="Partner organizations..."></textarea>
                  <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm budget-optional" placeholder="₱ 0.00">
                  <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-3">
          <div id="budgetContainerMobile" class="space-y-3">
            <div class="budget-row space-y-3 p-3 border rounded bg-white shadow-sm">
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Activity</label>
                <textarea name="budget_activity[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm budget-optional" rows="2" placeholder="Activity"></textarea>
              </div>
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
                <textarea name="budget_resources[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm budget-optional" rows="2" placeholder="Resources Needed"></textarea>
              </div>
              <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
                <textarea name="budget_partners[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm budget-optional" rows="2" placeholder="Partner Agencies"></textarea>
              </div>
              <div class="flex flex-col sm:flex-row gap-2">
                <div class="space-y-1 flex-1">
                  <label class="block text-xs font-medium text-gray-600">Amount</label>
                  <input type="text" name="budget_amount[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm budget-optional" placeholder="₱ 0.00">
                </div>
                <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
              </div>
            </div>
          </div>
        </div>

        <button type="button" id="addBudgetRow" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">+ Add Budget Item</button>
      </div>

      <!-- SUBMIT and SAVE BUTTONS -->
      <div class="flex flex-col sm:flex-row gap-3 justify-end pt-6">
        <button type="submit" name="save_draft" class="rounded-lg bg-gray-200 px-4 py-2 text-sm md:text-base">Save as Draft</button>
        <button type="submit" name="submit_project" class="rounded-lg bg-nstpBlue text-white px-4 py-2 text-sm md:text-base">Submit Project</button>
      </div>
    </form>
    

  </section>
</main>

<script>
  // helper: remove row when button is clicked
  function attachRemoveButtons() {
    document.querySelectorAll('.removeRow').forEach(btn => {
      btn.onclick = () => btn.closest('tr, .grid, .activity-row, .budget-row, .member-card').remove();
    });
  }

  // Add Row for Member Profile
  document.getElementById('addMemberRow').addEventListener('click', () => {
    // Desktop table view
    const table = document.getElementById('memberTable').querySelector('tbody');
    if (table) {
      const newRow = document.createElement('tr');
      newRow.className = 'border-t';
      newRow.innerHTML = `
        <td class="px-6 py-4"><input name="member_name[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Enter full name" required></td>
        <td class="px-6 py-4"><input name="member_role[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" required></td>
        <td class="px-6 py-4"><input type="email" name="member_email[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="name@adzu.edu.ph" required></td>
        <td class="px-6 py-4"><input type="tel" name="member_contact[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required></td>
        <td class="px-6 py-4 text-center"><button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">Remove</button></td>
      `;
      table.appendChild(newRow);
    }

    // Mobile card view
    const mobileContainer = document.getElementById('memberContainer');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'member-card bg-white p-3 rounded-lg border shadow-sm space-y-3';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
          <input name="member_name[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
          <input name="member_role[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
          <input type="email" name="member_email[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
          <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm" required>
        </div>
        <div class="flex justify-end">
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }
    
    attachRemoveButtons();
  });

  // Add Row for Activities
  document.getElementById('addActivityRow').addEventListener('click', () => {
    // Desktop table view
    const desktopContainer = document.getElementById('activitiesContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'activity-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <input name="stage[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Planning" required>
          <textarea name="activities[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
          <input name="timeframe[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Week 1-2" required>
          <textarea name="point_person[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
          <select name="status[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }

    // Mobile card view
    const mobileContainer = document.getElementById('activitiesContainerMobile');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'activity-row space-y-3 p-3 border rounded bg-white shadow-sm';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
          <input name="stage[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" placeholder="Stage" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
          <textarea name="activities[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Specific Activities" required></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
          <input name="timeframe[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" placeholder="Time Frame" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
          <textarea name="point_person[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Point Person/s" required></textarea>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <div class="space-y-1 flex-1">
            <label class="block text-xs font-medium text-gray-600">Status</label>
            <select name="status[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm">
              <option>Planned</option>
              <option>Ongoing</option>
            </select>
          </div>
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }
    
    attachRemoveButtons();
  });

  // Add Row for Budget
  document.getElementById('addBudgetRow').addEventListener('click', () => {
    // Desktop table view
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'budget-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <textarea name="budget_activity[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity..."></textarea>
          <textarea name="budget_resources[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed..."></textarea>
          <textarea name="budget_partners[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations..."></textarea>
          <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm" placeholder="₱ 0.00">
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }

    // Mobile card view
    const mobileContainer = document.getElementById('budgetContainerMobile');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'budget-row space-y-3 p-3 border rounded bg-white shadow-sm';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Activity</label>
          <textarea name="budget_activity[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Activity"></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
          <textarea name="budget_resources[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Resources Needed"></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
          <textarea name="budget_partners[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" rows="2" placeholder="Partner Agencies"></textarea>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <div class="space-y-1 flex-1">
            <label class="block text-xs font-medium text-gray-600">Amount</label>
            <input type="text" name="budget_amount[]" class="w-full rounded-md border-gray-300 px-2 py-1 text-sm" placeholder="₱ 0.00">
          </div>
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }
    
    attachRemoveButtons();
  });

  // Auto-expand textarea
  document.addEventListener("input", function (e) {
    if (e.target.classList.contains("auto-expand")) {
      e.target.style.height = "auto";
      e.target.style.height = e.target.scrollHeight + "px";
    }
  });

  // initial remove buttons
  attachRemoveButtons();


// SweetAlert2 Validation + Confirmation Logic
const form = document.querySelector('form');
const saveDraftBtn = document.querySelector('button[name="save_draft"]');
const submitBtn = document.querySelector('button[name="submit_project"]');

// helper: check native required validity
function validateForm() {
  // Check all required fields manually to ensure they're filled
  const requiredInputs = form.querySelectorAll('[required]');
  let allValid = true;
  let firstInvalid = null;
  let missingFields = [];

  requiredInputs.forEach(input => {
    // Skip budget fields - they are optional
    if (input.classList.contains('budget-optional')) {
      input.classList.remove('border-red-500');
      return;
    }

    // Skip hidden fields (mobile/desktop responsive duplicates)
    if (input.offsetParent === null) {
      return;
    }

    // For file inputs, check if a file is selected
    if (input.type === 'file') {
      if (!input.files || input.files.length === 0) {
        allValid = false;
        input.classList.add('border-red-500');
        if (!firstInvalid) firstInvalid = input;
        missingFields.push(input.name || 'Team Logo');
      } else {
        input.classList.remove('border-red-500');
      }
    }
    // For select inputs, check if a value is selected
    else if (input.tagName === 'SELECT') {
      if (!input.value || input.value === '') {
        allValid = false;
        input.classList.add('border-red-500');
        if (!firstInvalid) firstInvalid = input;
        missingFields.push(input.name || input.id || 'Dropdown field');
      } else {
        input.classList.remove('border-red-500');
      }
    }
    // For text inputs, textareas, and email inputs
    else {
      if (!input.value || input.value.trim() === '') {
        allValid = false;
        input.classList.add('border-red-500');
        if (!firstInvalid) firstInvalid = input;
        missingFields.push(input.name || input.placeholder || 'Text field');
      } else {
        input.classList.remove('border-red-500');
      }
    }
  });

  // Show which fields are missing
  if (!allValid) {
    console.log('Missing fields:', missingFields);
  }

  // Scroll to first invalid field
  if (!allValid && firstInvalid) {
    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
    firstInvalid.focus();
  }

  return allValid;
}

// SAVE AS DRAFT — skip required validation
saveDraftBtn.addEventListener('click', (e) => {
  e.preventDefault();

  Swal.fire({
    title: 'Save as Draft?',
    text: 'Your project will be saved as a draft even if some fields are incomplete.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2b50ff',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, save it',
  }).then((result) => {
    if (result.isConfirmed) {
      // temporarily disable required attributes
      const requiredElements = form.querySelectorAll('[required]');
      requiredElements.forEach(el => el.removeAttribute('required'));

      // Create a hidden input to mark this as draft submission
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'save_draft';
      input.value = '1';
      form.appendChild(input);

      form.submit(); // proceed with submission
    }
  });
});

// SUBMIT PROJECT — requires validation
submitBtn.addEventListener('click', (e) => {
  e.preventDefault();
  e.stopPropagation();

  if (!validateForm()) {
    Swal.fire({
      title: 'Incomplete Form',
      text: 'Please fill in all required fields before submitting.',
      icon: 'error',
      confirmButtonColor: '#2b50ff'
    });
    return;
  }

  // Gather all form data for review
  const formData = new FormData(form);
  
  // Team Information
  const projectName = formData.get('project_name') || 'N/A';
  const teamName = formData.get('team_name') || 'N/A';
  const component = formData.get('component') || 'N/A';
  const section = formData.get('nstp_section') || 'N/A';
  const teamLogoFile = formData.get('team_logo');
  
  // Create image preview for team logo
  let teamLogoHTML = '<div class="text-sm text-gray-600">No file uploaded</div>';
  if (teamLogoFile && teamLogoFile.size > 0) {
    const imageUrl = URL.createObjectURL(teamLogoFile);
    teamLogoHTML = `
      <div class="mt-2">
        <img src="${imageUrl}" alt="Team Logo" class="max-w-[200px] max-h-[200px] rounded-lg border-2 border-gray-300 shadow-sm object-contain">
        <div class="text-xs text-gray-500 mt-1">${teamLogoFile.name} (${(teamLogoFile.size / 1024).toFixed(2)} KB)</div>
      </div>
    `;
  }
  
  // Project Details
  const issues = formData.get('issues') || 'N/A';
  const objectives = formData.get('objectives') || 'N/A';
  const targetCommunity = formData.get('target_community') || 'N/A';
  const solutions = formData.get('solutions') || 'N/A';
  const outcomes = formData.get('outcomes') || 'N/A';
  
  // Members - Filter out empty entries from hidden mobile/desktop duplicates
  const allMemberNames = formData.getAll('member_name[]');
  const allMemberRoles = formData.getAll('member_role[]');
  const allMemberEmails = formData.getAll('member_email[]');
  const allMemberContacts = formData.getAll('member_contact[]');
  
  // Only get non-empty members
  const memberNames = [];
  const memberRoles = [];
  const memberEmails = [];
  const memberContacts = [];
  
  allMemberNames.forEach((name, idx) => {
    if (name && name.trim() !== '') {
      memberNames.push(name);
      memberRoles.push(allMemberRoles[idx] || '');
      memberEmails.push(allMemberEmails[idx] || '');
      memberContacts.push(allMemberContacts[idx] || '');
    }
  });
  
  let membersHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
  memberNames.forEach((name, idx) => {
    membersHTML += `
      <div class="mb-3 pb-3 ${idx < memberNames.length - 1 ? 'border-b border-gray-300' : ''}">
        <div class="flex items-center gap-2 mb-1">
          <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">${idx + 1}</span>
          <strong class="text-gray-800">${name}</strong>
          <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded">${memberRoles[idx] || 'N/A'}</span>
        </div>
        <div class="ml-8 text-xs text-gray-600">
          <div class="flex items-center gap-2 mt-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
            ${memberEmails[idx] || 'N/A'}
          </div>
          <div class="flex items-center gap-2 mt-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path></svg>
            ${memberContacts[idx] || 'N/A'}
          </div>
        </div>
      </div>`;
  });
  membersHTML += '</div>';
  
  // Activities - Filter out empty entries from hidden mobile/desktop duplicates
  const allStages = formData.getAll('stage[]');
  const allActivities = formData.getAll('activities[]');
  const allTimeframes = formData.getAll('timeframe[]');
  const allPointPersons = formData.getAll('point_person[]');
  const allStatuses = formData.getAll('status[]');
  
  // Only get non-empty activities
  const stages = [];
  const activities = [];
  const timeframes = [];
  const pointPersons = [];
  const statuses = [];
  
  allStages.forEach((stage, idx) => {
    if (stage && stage.trim() !== '') {
      stages.push(stage);
      activities.push(allActivities[idx] || '');
      timeframes.push(allTimeframes[idx] || '');
      pointPersons.push(allPointPersons[idx] || '');
      statuses.push(allStatuses[idx] || 'Planned');
    }
  });
  
  let activitiesHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
  stages.forEach((stage, idx) => {
    const statusColors = {
      'Planned': 'bg-yellow-100 text-yellow-800',
      'Ongoing': 'bg-blue-100 text-blue-800',
      'Completed': 'bg-green-100 text-green-800'
    };
    const statusColor = statusColors[statuses[idx]] || 'bg-gray-100 text-gray-800';
    activitiesHTML += `
      <div class="mb-3 pb-3 ${idx < stages.length - 1 ? 'border-b border-gray-300' : ''}">
        <div class="flex items-start justify-between mb-2">
          <div class="flex-1">
            <div class="font-bold text-gray-800 mb-1">${stage}</div>
            <div class="text-sm text-gray-700 whitespace-pre-wrap">${activities[idx] || 'N/A'}</div>
          </div>
          <span class="text-xs ${statusColor} px-2 py-1 rounded font-medium ml-2">${statuses[idx]}</span>
        </div>
        <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
          <div class="bg-white p-2 rounded">
            <span class="text-gray-500">⏱️ Timeframe:</span>
            <span class="font-medium text-gray-800 whitespace-pre-wrap">${timeframes[idx] || 'N/A'}</span>
          </div>
          <div class="bg-white p-2 rounded">
            <span class="text-gray-500">👤 Person:</span>
            <span class="font-medium text-gray-800 whitespace-pre-wrap">${pointPersons[idx] || 'N/A'}</span>
          </div>
        </div>
      </div>`;
  });
  activitiesHTML += '</div>';
  
  // Budget - Filter out empty entries from hidden mobile/desktop duplicates
  const allBudgetActivities = formData.getAll('budget_activity[]');
  const allBudgetResources = formData.getAll('budget_resources[]');
  const allBudgetPartners = formData.getAll('budget_partners[]');
  const allBudgetAmounts = formData.getAll('budget_amount[]');
  
  // Only get non-empty budget items
  const budgetActivities = [];
  const budgetResources = [];
  const budgetPartners = [];
  const budgetAmounts = [];
  
  allBudgetActivities.forEach((activity, idx) => {
    // Include if at least one field has data
    if ((activity && activity.trim() !== '') || 
        (allBudgetResources[idx] && allBudgetResources[idx].trim() !== '') || 
        (allBudgetPartners[idx] && allBudgetPartners[idx].trim() !== '') || 
        (allBudgetAmounts[idx] && allBudgetAmounts[idx].trim() !== '')) {
      budgetActivities.push(activity || '');
      budgetResources.push(allBudgetResources[idx] || '');
      budgetPartners.push(allBudgetPartners[idx] || '');
      budgetAmounts.push(allBudgetAmounts[idx] || '');
    }
  });
  
  let budgetHTML = '<div class="text-left max-h-40 overflow-y-auto border rounded-lg p-3 bg-gray-50">';
  let totalBudget = 0;
  
  budgetActivities.forEach((activity, idx) => {
    if (activity || budgetResources[idx] || budgetPartners[idx] || budgetAmounts[idx]) {
      // Extract numeric value from amount (remove peso sign and commas)
      let amountValue = budgetAmounts[idx] || '0';
      amountValue = amountValue.replace(/[₱,]/g, '').trim();
      const numericAmount = parseFloat(amountValue) || 0;
      totalBudget += numericAmount;
      
      // Format display amount with peso sign
      const displayAmount = numericAmount > 0 ? `₱ ${numericAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : '₱ 0.00';
      
      budgetHTML += `
        <div class="mb-3 pb-3 ${idx < budgetActivities.length - 1 ? 'border-b border-gray-300' : ''}">
          <div class="flex items-start justify-between mb-2">
            <div class="font-bold text-gray-800">${activity || 'Activity ' + (idx + 1)}</div>
            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-lg font-bold text-sm">${displayAmount}</div>
          </div>
          <div class="space-y-1 text-xs">
            <div class="flex items-start gap-2">
              <span class="text-gray-500 font-medium min-w-[80px]">📦 Resources:</span>
              <span class="text-gray-700 whitespace-pre-wrap">${budgetResources[idx] || 'N/A'}</span>
            </div>
            <div class="flex items-start gap-2">
              <span class="text-gray-500 font-medium min-w-[80px]">🤝 Partners:</span>
              <span class="text-gray-700 whitespace-pre-wrap">${budgetPartners[idx] || 'N/A'}</span>
            </div>
          </div>
        </div>`;
    }
  });
  
  // Add total budget at the bottom if there are budget items
  if (budgetActivities.length > 0 && totalBudget > 0) {
    const formattedTotal = `₱ ${totalBudget.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    budgetHTML += `
      <div class="mt-3 pt-3 border-t-2 border-yellow-300">
        <div class="flex items-center justify-between bg-yellow-100 px-4 py-3 rounded-lg">
          <span class="text-base font-bold text-gray-800">Total Budget:</span>
          <span class="text-lg font-bold text-green-700">${formattedTotal}</span>
        </div>
      </div>`;
  }
  
  budgetHTML += '</div>';
  
  // First Modal: Review all details
  Swal.fire({
    title: '<div class="text-2xl font-bold text-gray-800">📋 Review Project Proposal</div>',
    html: `
      <div class="text-left space-y-4 max-h-[500px] overflow-y-auto px-3 py-2">
        <!-- Team Information -->
        <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
          <h3 class="font-bold text-blue-700 mb-3 text-lg flex items-center gap-2">
            <span>🖼️</span> Team Information
          </h3>
          <div class="space-y-2">
            <div class="bg-white p-2 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold">Project Name</span>
              <div class="text-sm font-bold text-gray-800">${projectName}</div>
            </div>
            <div class="grid grid-cols-3 gap-2">
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Team</span>
                <div class="text-sm font-medium text-gray-800">${teamName}</div>
              </div>
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Component</span>
                <div class="text-sm font-medium text-gray-800">${component}</div>
              </div>
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Section</span>
                <div class="text-sm font-medium text-gray-800">${section}</div>
              </div>
            </div>
            <div class="bg-white p-2 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold">Team Logo</span>
              ${teamLogoHTML}
            </div>
          </div>
        </div>
        
        <!-- Members -->
        <div class="bg-purple-50 rounded-lg p-4 border-l-4 border-purple-500">
          <h3 class="font-bold text-purple-700 mb-3 text-lg flex items-center gap-2">
            <span>👥</span> Team Members
            <span class="text-xs bg-purple-200 text-purple-800 px-2 py-1 rounded-full">${memberNames.length} members</span>
          </h3>
          ${membersHTML}
        </div>
        
        <!-- Project Details -->
        <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-500">
          <h3 class="font-bold text-green-700 mb-3 text-lg flex items-center gap-2">
            <span>🎯</span> Project Details
          </h3>
          <div class="space-y-3">
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Issues/Problem</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${issues.substring(0, 500)}${issues.length > 500 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Goal/Objectives</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${objectives.substring(0, 500)}${objectives.length > 500 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Target Community</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${targetCommunity.substring(0, 300)}${targetCommunity.length > 300 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Solutions/Activities to be implemented</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${solutions.substring(0, 500)}${solutions.length > 500 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Expected Outcomes</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${outcomes.substring(0, 500)}${outcomes.length > 500 ? '...' : ''}</div>
            </div>
          </div>
        </div>
        
        <!-- Activities -->
        <div class="bg-orange-50 rounded-lg p-4 border-l-4 border-orange-500">
          <h3 class="font-bold text-orange-700 mb-3 text-lg flex items-center gap-2">
            <span>📅</span> Project Activities
            <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">${stages.length} activities</span>
          </h3>
          ${activitiesHTML}
        </div>
        
        <!-- Budget -->
        <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
          <h3 class="font-bold text-yellow-700 mb-3 text-lg flex items-center gap-2">
            <span>💰</span> Budget Items
          </h3>
          ${budgetHTML}
        </div>
      </div>
    `,
    width: '700px',
    showCancelButton: true,
    confirmButtonColor: '#2b50ff',
    cancelButtonColor: '#6b7280',
    confirmButtonText: '✓ Proceed to Submit',
    cancelButtonText: '✕ Cancel',
    reverseButtons: true,
    customClass: {
      container: 'review-modal',
      popup: 'rounded-2xl',
      confirmButton: 'font-bold px-6 py-3 rounded-lg',
      cancelButton: 'font-bold px-6 py-3 rounded-lg'
    }
  }).then((reviewResult) => {
    if (reviewResult.isConfirmed) {
      // Second Modal: Final confirmation
      Swal.fire({
        title: 'Submit Project?',
        text: 'Once submitted, your project will be sent for review.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2b50ff',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, submit it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
      }).then((confirmResult) => {
        if (confirmResult.isConfirmed) {
          // Create a hidden input to mark this as project submission
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'submit_project';
          input.value = '1';
          form.appendChild(input);

          // Use setTimeout to ensure DOM is ready
          setTimeout(() => {
            form.submit();
          }, 100);
        }
      });
    }
  });
});
</script>


</body>
</html>
