<script>
console.log('🚀 Staff Edit Form Scripts Loading...');

// Global variables for form management
let formChanged = false;
let isSubmitting = false;
let membersAdded = new Set();

// Normalize email for consistent set operations
function normalizeEmail(email) {
    // Collect main project details (include long text fields)
    const projectName = document.querySelector('input[name="Project_Name"]')?.value || 'Not specified';
    const teamName = document.querySelector('input[name="Project_Team_Name"]')?.value || 'Not specified';
    const component = document.querySelector('select[name="Project_Component"]')?.value || 'Not specified';
    const section = document.querySelector('select[name="nstp_section"]')?.value || 'Not specified';
    const problems = document.querySelector('textarea[name="Project_Problems"]')?.value || '—';
    const goals = document.querySelector('textarea[name="Project_Goals"]')?.value || '—';
    const targetCommunity = document.querySelector('textarea[name="Project_Target_Community"]')?.value || '—';
    const solution = document.querySelector('textarea[name="Project_Solution"]')?.value || '—';
    const outcomes = document.querySelector('textarea[name="Project_Expected_Outcomes"]')?.value || '—';

    // Logo: prefer existing preview image if present
    const logoImg = document.querySelector('img[alt="Current Logo"]');
    const logoSrc = logoImg ? logoImg.src : '';

    // Members (visible only)
    const memberRows = Array.from(document.querySelectorAll('.member-row, .member-card, #memberTable tbody tr')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    // Build members table (columns: Name, Role, Email, Contact)
    const memberHtml = (() => {
      if (!memberRows.length) return `<div class="text-gray-500">No members</div>`;
      const rows = memberRows.map(r => {
        const name = r.querySelector('input[name="member_name[]"]')?.value || '—';
        const role = r.querySelector('input[name="member_role[]"]')?.value || '—';
        const email = r.querySelector('input[name="member_email[]"]')?.value || '—';
        const contact = r.querySelector('input[name="member_contact[]"]')?.value || '—';
        return `<tr><td class="px-2 py-1">${name}</td><td class="px-2 py-1 text-sm text-gray-600">${role}</td><td class="px-2 py-1 text-sm text-gray-600">${email}</td><td class="px-2 py-1 text-sm text-gray-600">${contact}</td></tr>`;
      }).join('');
      return `
        <div style="overflow:auto">
          <table class="w-full border-collapse">
            <thead>
              <tr class="text-left text-sm text-gray-700"><th class="px-2 py-1">Name</th><th class="px-2 py-1">Role</th><th class="px-2 py-1">Email</th><th class="px-2 py-1">Contact</th></tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>`;
    })();

    // Activities (visible only)
    const activityRows = Array.from(document.querySelectorAll('.activity-row')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    // Build activities table
    const activitiesHtml = (() => {
      if (!activityRows.length) return `<div class="text-gray-500">No activities</div>`;
      const rows = activityRows.map((r, i) => {
        const stage = r.querySelector('input[name="stage[]"], textarea[name="stage[]"]')?.value || '—';
        const specific = r.querySelector('textarea[name="activities[]"], input[name="activities[]"]')?.value || '—';
        const timeframe = r.querySelector('input[name="timeframe[]"]')?.value || '—';
        const impl = r.querySelector('input[name="implementation_date[]"]')?.value || '—';
        const point = r.querySelector('input[name="point_person[]"], textarea[name="point_person[]"]')?.value || '—';
        return `<tr><td class="px-2 py-1">${i+1}</td><td class="px-2 py-1">${stage}</td><td class="px-2 py-1">${specific}</td><td class="px-2 py-1">${timeframe}</td><td class="px-2 py-1">${impl}</td><td class="px-2 py-1">${point}</td></tr>`;
      }).join('');
      return `
        <div style="overflow:auto">
          <table class="w-full border-collapse text-sm">
            <thead>
              <tr class="text-left text-sm text-gray-700"><th class="px-2 py-1">#</th><th class="px-2 py-1">Stage</th><th class="px-2 py-1">Activity</th><th class="px-2 py-1">Timeframe</th><th class="px-2 py-1">Implementation</th><th class="px-2 py-1">Point Person</th></tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>`;
    })();

    // Budgets (visible only)
    const budgetRows = Array.from(document.querySelectorAll('.budget-row')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    // Build budgets table
    const budgetsHtml = (() => {
      if (!budgetRows.length) return `<div class="text-gray-500">No budgets</div>`;
      const rows = budgetRows.map((r, i) => {
        const act = r.querySelector('textarea[name="budget_activity[]"]')?.value || '—';
        const res = r.querySelector('textarea[name="budget_resources[]"]')?.value || '—';
        const partners = r.querySelector('textarea[name="budget_partners[]"]')?.value || '—';
        const amount = r.querySelector('input[name="budget_amount[]"]')?.value || '—';
        return `<tr><td class="px-2 py-1">${i+1}</td><td class="px-2 py-1">${act}</td><td class="px-2 py-1">${res}</td><td class="px-2 py-1">${partners}</td><td class="px-2 py-1 font-medium">${amount}</td></tr>`;
      }).join('');
      return `
        <div style="overflow:auto">
          <table class="w-full border-collapse text-sm">
            <thead>
              <tr class="text-left text-sm text-gray-700"><th class="px-2 py-1">#</th><th class="px-2 py-1">Activity</th><th class="px-2 py-1">Resources</th><th class="px-2 py-1">Partners</th><th class="px-2 py-1">Amount</th></tr>
            </thead>
            <tbody>${rows}</tbody>
          </table>
        </div>`;
    })();

    const html = `
      <div style="text-align:left; max-height:70vh; overflow:auto" class="p-4">
        <div class="flex gap-4">
          <div class="w-28 flex-shrink-0">
            ${logoSrc ? `<img src="${logoSrc}" alt="Logo" class="w-28 h-28 object-cover rounded border" />` : `<div class="w-28 h-28 bg-gray-100 rounded border flex items-center justify-center text-gray-400">No Logo</div>`}
          </div>
          <div class="flex-1">
            <h3 class="text-xl font-semibold">${projectName}</h3>
            <div class="text-sm text-gray-600">Team: <strong>${teamName}</strong> • ${component} / ${section}</div>
            <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-700">
              <div><strong>Problems</strong><div class="text-gray-600 mt-1">${problems}</div></div>
              <div><strong>Goals</strong><div class="text-gray-600 mt-1">${goals}</div></div>
              <div><strong>Target Community</strong><div class="text-gray-600 mt-1">${targetCommunity}</div></div>
              <div><strong>Solution</strong><div class="text-gray-600 mt-1">${solution}</div></div>
              <div><strong>Expected Outcomes</strong><div class="text-gray-600 mt-1">${outcomes}</div></div>
            </div>
          </div>
        </div>

        <hr class="my-3" />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
          <div class="col-span-1 lg:col-span-1">
            <h4 class="font-semibold">Members</h4>
            <ul class="mt-2 text-sm text-gray-700">${memberHtml}</ul>
          </div>
          <div class="col-span-1 lg:col-span-1">
            <h4 class="font-semibold">Activities</h4>
            <ul class="mt-2 text-sm text-gray-700">${activitiesHtml}</ul>
          </div>
          <div class="col-span-1 lg:col-span-1">
            <h4 class="font-semibold">Budgets</h4>
            <ul class="mt-2 text-sm text-gray-700">${budgetsHtml}</ul>
          </div>
        </div>
      </div>
    `;
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Specific Activity <span class="text-red-500">*</span></label>
        <textarea name="activities[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Specific Activity" required>${activity}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
        <input name="timeframe[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Time Frame" value="${timeframe}" required>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Implementation Date</label>
        <input name="implementation_date[]" type="date" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" value="${implementationDate}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person</label>
        <input name="point_person[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Point Person" value="${pointPerson}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Status</label>
        <select name="activity_status[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
          <option value="Planned" ${status === 'Planned' ? 'selected' : ''}>Planned</option>
          <option value="In Progress" ${status === 'In Progress' ? 'selected' : ''}>In Progress</option>
          <option value="Completed" ${status === 'Completed' ? 'selected' : ''}>Completed</option>
          <option value="On Hold" ${status === 'On Hold' ? 'selected' : ''}>On Hold</option>
        </select>
      </div>
      <div class="flex justify-end">
        <button type="button" class="remove-activity-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs transition-colors">Remove</button>
      </div>
    `;
    activitiesContainerMobile.appendChild(newCard);
  }
  
  if (markDirty) formChanged = true;
  console.log('Activity row added:', stage);
}

// Function to add a budget row with data
function addBudgetRow(activity = '', resources = '', partners = '', amount = '', markDirty = true) {
  const budgetContainer = document.getElementById('budgetContainer');
  const budgetContainerMobile = document.getElementById('budgetContainerMobile');

  if (budgetContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'budget-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start px-6 py-4 border-b border-gray-300';
    newRow.innerHTML = `
      <div>
        <textarea name="budget_activity[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Activity" required>${activity}</textarea>
      </div>
      <div>
        <textarea name="budget_resources[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Resources Needed" required>${resources}</textarea>
      </div>
      <div>
        <textarea name="budget_partners[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Partner Agencies">${partners}</textarea>
      </div>
      <div>
        <input name="budget_amount[]" type="number" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Amount" value="${amount}" required>
      </div>
      <div>
        <button type="button" class="remove-budget-btn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">Remove</button>
      </div>
    `;
    budgetContainer.appendChild(newRow);
  }

  // Mobile version
  if (budgetContainerMobile) {
    const newCard = document.createElement('div');
    newCard.className = 'budget-row bg-white p-3 rounded-lg border border-gray-300 shadow-sm space-y-3';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Activity <span class="text-red-500">*</span></label>
        <textarea name="budget_activity[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Activity" required>${activity}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Resources Needed <span class="text-red-500">*</span></label>
        <textarea name="budget_resources[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Resources Needed" required>${resources}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
        <textarea name="budget_partners[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Partner Agencies">${partners}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Amount <span class="text-red-500">*</span></label>
        <input name="budget_amount[]" type="number" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Amount" value="${amount}" required>
      </div>
      <div class="flex justify-end">
        <button type="button" class="remove-budget-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs transition-colors">Remove</button>
      </div>
    `;
    budgetContainerMobile.appendChild(newCard);
  }
  
  if (markDirty) formChanged = true;
  console.log('Budget row added:', activity);
}

// Function to add a member row
function addMemberRow(studentId, name, email, contact, role = '', markDirty = true) {
  const memberTableBody = document.getElementById('memberTableBody');
  const memberContainer = document.getElementById('memberContainer');

  if (memberTableBody) {
    const newRow = document.createElement('tr');
    newRow.className = 'member-row hover:bg-gray-50 transition-colors';
    newRow.innerHTML = `
      <td class="px-6 py-4">
        <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${name}" readonly>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </td>
      <td class="px-6 py-4">
        <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" value="${role}" required>
      </td>
      <td class="px-6 py-4">
        <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${email}" readonly>
      </td>
      <td class="px-6 py-4">
        <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${contact}" required>
      </td>
      <td class="px-6 py-4 text-center">
        <button type="button" class="remove-member-btn bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
          Remove
        </button>
      </td>
    `;
    memberTableBody.appendChild(newRow);
  }

  if (memberContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'member-row bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
        <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${name}" readonly>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
        <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" value="${role}" required>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
        <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${email}" readonly>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
        <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${contact}" required>
      </div>
      <div class="flex justify-end">
        <button type="button" class="remove-member-btn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
      </div>
    `;
    memberContainer.appendChild(newCard);
  }

  membersAdded.add(normalizeEmail(email));
  if (markDirty) formChanged = true;
  console.log('Member row added:', name);
}

// Function to load existing project data
function loadExistingData() {
  console.log('🔄 Loading existing project data...');
  
  const projectData = @json($project ?? null);
  
  if (!projectData) {
    console.warn('No project data available');
    return;
  }

  // Load activities
  if (projectData.activities && projectData.activities.length > 0) {
    console.log('Loading', projectData.activities.length, 'activities');
    projectData.activities.forEach(activity => {
      const implementationDate = activity.Implementation_Date ? activity.Implementation_Date.split('T')[0] : '';
      addActivityRow(
        activity.Stage || '',
        activity.Specific_Activity || '',
        activity.Time_Frame || '',
        implementationDate,
        activity.Point_Persons || '',
        activity.status || 'Planned'
      );
    });
  } else {
    // Add one blank activity row if none exist
    addActivityRow();
  }

  // Load budgets
  if (projectData.budgets && projectData.budgets.length > 0) {
    console.log('Loading', projectData.budgets.length, 'budget items');
    projectData.budgets.forEach(budget => {
      addBudgetRow(
        budget.Specific_Activity || '',
        budget.Resources_Needed || '',
        budget.Partner_Agencies || '',
        budget.Amount || '',
        false
      );
    });
  } else {
    // Add one blank budget row if none exist
    addBudgetRow('', '', '', '', false);
  }

  // Load members
  if (projectData.student_ids && projectData.student_ids.length > 0) {
    console.log('Loading', projectData.student_ids.length, 'members');
    
    // Fetch member details
    fetch('{{ route("api.students.details-staff") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ student_ids: projectData.student_ids })
    })
    .then(response => response.json())
    .then(students => {
      students.forEach((student, index) => {
        const role = projectData.member_roles && projectData.member_roles[index] ? projectData.member_roles[index] : '';
        addMemberRow(
          student.id,
          student.first_name + ' ' + student.last_name,
          student.school_email,
          student.contact_number || '',
          role,
          false
        );
      });
    })
    .catch(error => {
      console.error('Error loading member details:', error);
    });
  }

  console.log('✅ Data loading complete');
  // Reset change flag after initial load
  formChanged = false;
}

// Function to load available members
function loadMemberList() {
  const memberList = document.getElementById('memberList');
  memberList.innerHTML = '<p class="text-center text-gray-500">Loading members...</p>';
  
  const projectComponent = '@json($project->Project_Component)'.replace(/"/g, '');
  const projectSection = '@json($project->Project_Section)'.replace(/"/g, '');
  
  const url = new URL('{{ route("projects.students.for-staff") }}', window.location.origin);
  url.searchParams.append('section', projectSection);
  url.searchParams.append('component', projectComponent);
  
  // Exclude existing members
  Array.from(membersAdded).forEach(email => {
    url.searchParams.append('existing_members[]', email);
  });
  
  fetch(url)
    .then(response => response.json())
    .then(students => {
      memberList.innerHTML = '';
      if (students.length === 0) {
        memberList.innerHTML = '<p class="text-center text-gray-500">No available students found in this section/component.</p>';
        return;
      }
      
      students.forEach(student => {
        const memberDiv = document.createElement('div');
        memberDiv.className = 'flex items-center space-x-3 p-2 hover:bg-gray-50 rounded';
        memberDiv.innerHTML = `
          <input type="checkbox" name="available_members[]" value="${student.id}" 
                 data-name="${student.first_name} ${student.last_name}" 
                 data-email="${student.school_email}" 
                 data-contact="${student.contact_number || ''}" 
                 class="rounded">
          <label class="text-sm">${student.first_name} ${student.last_name} (${student.school_email})</label>
        `;
        memberList.appendChild(memberDiv);
      });
    })
    .catch(error => {
      console.error('Error loading members:', error);
      memberList.innerHTML = '<p class="text-center text-red-500">Error loading members. Please try again.</p>';
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  console.log('🚀 Initializing staff edit form...');
  
  // Load existing data first
  loadExistingData();
  
  // Set up button event handlers
  const addActivityBtn = document.getElementById('addActivityRow');
  const addBudgetBtn = document.getElementById('addBudgetRow');
  const openMemberModalBtn = document.getElementById('openMemberModal');
  const openMemberModalMobileBtn = document.getElementById('openMemberModalMobile');
  
  if (addActivityBtn) {
    addActivityBtn.addEventListener('click', function() {
      addActivityRow();
    });
  }
  
  if (addBudgetBtn) {
    addBudgetBtn.addEventListener('click', function() {
      addBudgetRow();
    });
  }
  
  if (openMemberModalBtn) {
    openMemberModalBtn.addEventListener('click', function() {
      loadMemberList();
      document.getElementById('memberModal').classList.remove('hidden');
    });
  }
  
  if (openMemberModalMobileBtn) {
    openMemberModalMobileBtn.addEventListener('click', function() {
      loadMemberList();
      document.getElementById('memberModal').classList.remove('hidden');
    });
  }

  // Modal event handlers
  document.getElementById('closeMemberModal').addEventListener('click', function() {
    document.getElementById('memberModal').classList.add('hidden');
  });

  document.getElementById('cancelMemberSelection').addEventListener('click', function() {
    document.getElementById('memberModal').classList.add('hidden');
  });

  document.getElementById('addSelectedMembers').addEventListener('click', function() {
    const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
    
    if (selectedMembers.length === 0) {
      alert('Please select at least one member.');
      return;
    }
    
    selectedMembers.forEach(checkbox => {
      const studentId = checkbox.value;
      const name = checkbox.dataset.name;
      const email = checkbox.dataset.email;
      const contact = checkbox.dataset.contact;
      
      addMemberRow(studentId, name, email, contact);
    });
    
    document.getElementById('memberModal').classList.add('hidden');
    
    // Show success message
    alert(`${selectedMembers.length} member(s) added successfully!`);
  });

  // Remove button handlers using event delegation
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-activity-btn')) {
      e.target.closest('.activity-row').remove();
      formChanged = true;
    }
    
    if (e.target.classList.contains('remove-budget-btn')) {
      e.target.closest('.budget-row').remove();
      formChanged = true;
    }
    
    if (e.target.classList.contains('remove-member-btn')) {
      const row = e.target.closest('.member-row');
      const email = row.querySelector('input[name="member_email[]"]').value;
      membersAdded.delete(normalizeEmail(email));
      row.remove();
      formChanged = true;
    }
  });

  // Auto-expand textareas
  document.addEventListener('input', function(e) {
    if (e.target.classList.contains('auto-expand')) {
      e.target.style.height = 'auto';
      e.target.style.height = e.target.scrollHeight + 'px';
    }
    
    // Mark form as changed
    formChanged = true;
  });
  
  // Form submission handler
  const form = document.getElementById('projectForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      if (isSubmitting) return;
      
      // Basic validation
      const activityRows = document.querySelectorAll('.activity-row');
      const budgetRows = document.querySelectorAll('.budget-row');
      const memberRows = document.querySelectorAll('.member-row');
      
      if (activityRows.length === 0) {
        e.preventDefault();
        alert('Please add at least one activity.');
        return;
      }
      
      if (budgetRows.length === 0) {
        e.preventDefault();
        alert('Please add at least one budget item.');
        return;
      }
      
      if (memberRows.length === 0) {
        e.preventDefault();
        alert('Please add at least one team member.');
        return;
      }
      // Disable hidden duplicate inputs before native submit
      try { disableHiddenFormInputs(); } catch (e) { console.warn('disableHiddenFormInputs error', e); }
      isSubmitting = true;
      console.log('✅ Form submitted successfully (hidden inputs disabled)');
    });
  }

  // Attach Cancel confirmation for main cancel button
  const cancelEditBtn = document.getElementById('cancelEditBtn');
  if (cancelEditBtn) {
    cancelEditBtn.addEventListener('click', function(e) {
      e.preventDefault();
      Swal.fire({
        title: 'Cancel Editing?',
        text: "Any unsaved changes will be lost. Are you sure you want to cancel?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, cancel editing',
      }).then((result) => {
        if (result.isConfirmed) {
          // Redirect back to project show
          window.location.href = '{{ route("projects.show", $project) }}';
        }
      });
    });
  }

  // Helper: validate current form values (client-side) and return array of error strings
  function validateFormRequirements() {
    // Use authoritative validation from edit-form-scripts, adapted to return errors array
    const form = document.getElementById('projectForm');
    if (!form) return ['Project form not found'];
    const formData = new FormData(form);
    const errors = [];

    const projectName = formData.get('Project_Name')?.trim();
    const teamName = formData.get('Project_Team_Name')?.trim();
    const component = formData.get('Project_Component')?.trim();
    const section = formData.get('nstp_section')?.trim();
    const problems = formData.get('Project_Problems')?.trim();
    const goals = formData.get('Project_Goals')?.trim();
    const targetCommunity = formData.get('Project_Target_Community')?.trim();
    const solution = formData.get('Project_Solution')?.trim();
    const outcomes = formData.get('Project_Expected_Outcomes')?.trim();

    if (!projectName) errors.push('The Project Name field is required.');
    if (!teamName) errors.push('The Team Name field is required.');
    if (!component) errors.push('The Component field is required.');
    if (!section) errors.push('The NSTP Section field is required.');
    if (!problems) errors.push('The Project Problems field is required.');
    if (!goals) errors.push('The Project Goals field is required.');
    if (!targetCommunity) errors.push('The Target Community field is required.');
    if (!solution) errors.push('The Project Solution field is required.');
    if (!outcomes) errors.push('The Expected Outcomes field is required.');

    const logoFile = formData.get('Project_Logo');
    const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
    if (!hasExistingLogo && (!logoFile || logoFile.size === 0)) {
      errors.push('A team logo is required for project submission.');
    }

    // Members
    const allMemberNames = formData.getAll('member_name[]');
    const allMemberRoles = formData.getAll('member_role[]');
    const allMemberEmails = formData.getAll('member_email[]');
    const allMemberContacts = formData.getAll('member_contact[]');

    const uniqueMembers = [];
    const processedEmails = new Set();
    for (let i = 0; i < allMemberNames.length; i++) {
      const name = allMemberNames[i]?.trim();
      const role = allMemberRoles[i]?.trim();
      const email = allMemberEmails[i]?.trim();
      const contact = allMemberContacts[i]?.trim();
      if ((name || role || email || contact) && (!email || !processedEmails.has(email))) {
        if (email) processedEmails.add(email);
        uniqueMembers.push({ name, role, email, contact, index: uniqueMembers.length + 1 });
      }
    }

    let validMembers = 0;
    uniqueMembers.forEach((member) => {
      const missingFields = [];
      if (!member.name) missingFields.push('Name');
      if (!member.role) missingFields.push('Role');
      if (!member.email) missingFields.push('Email');
      if (!member.contact) missingFields.push('Contact');
      if (missingFields.length > 0) {
        errors.push(`Team member ${member.index}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
      } else {
        validMembers++;
      }
    });
    if (validMembers === 0) errors.push('At least one complete team member info is required.');

    // Activities - iterate visible .activity-row elements to avoid desktop/mobile duplication
    const visibleActivityRows = Array.from(document.querySelectorAll('.activity-row')).filter(r => r.offsetParent !== null);
    let validActivities = 0;
    for (let i = 0; i < visibleActivityRows.length; i++) {
      const row = visibleActivityRows[i];
      const stage = row.querySelector('input[name="stage[]"], textarea[name="stage[]"]')?.value?.trim() || '';
      const activity = row.querySelector('textarea[name="activities[]"], input[name="activities[]"]')?.value?.trim() || '';
      const timeframe = row.querySelector('input[name="timeframe[]"]')?.value?.trim() || '';
      const implementationDate = row.querySelector('input[name="implementation_date[]"]')?.value?.trim() || '';
      const person = row.querySelector('input[name="point_person[]"], textarea[name="point_person[]"]')?.value?.trim() || '';

      if (stage || activity || timeframe || implementationDate || person) {
        const missingFields = [];
        if (!stage) missingFields.push('Stage');
        if (!activity) missingFields.push('Specific Activities');
        if (!timeframe) missingFields.push('Time Frame');
        if (!implementationDate) missingFields.push('Implementation Date');
        if (!person) missingFields.push('Point Persons');
        if (missingFields.length > 0) {
          errors.push(`Activity ${i+1}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
        } else {
          validActivities++;
        }
      }
    }
    if (validActivities === 0) errors.push('At least one complete activity is required.');

    // Budgets: partial rows must be complete
    const allBudgetActivities = formData.getAll('budget_activity[]');
    const allBudgetResources = formData.getAll('budget_resources[]');
    const allBudgetPartners = formData.getAll('budget_partners[]');
    const allBudgetAmounts = formData.getAll('budget_amount[]');

    for (let i = 0; i < allBudgetActivities.length; i++) {
      const act = allBudgetActivities[i]?.trim();
      const res = allBudgetResources[i]?.trim();
      const part = allBudgetPartners[i]?.trim();
      const amt = allBudgetAmounts[i]?.trim();
      if (act || res || part || amt) {
        const missingFields = [];
        if (!act) missingFields.push('Activity');
        if (!res) missingFields.push('Resources needed');
        if (!part) missingFields.push('Partner agencies');
        if (!amt) missingFields.push('Amount');
        if (missingFields.length > 0) {
          errors.push(`Budget row ${i+1}: ${missingFields.join(', ')} ${missingFields.length === 1 ? 'is' : 'are'} required.`);
        }
      }
    }

    return errors;
  }

  // Build and show a review modal summarizing the form, then submit on confirm
  function showReviewModal(form) {
    // Collect summary
    const projectName = document.querySelector('input[name="Project_Name"]')?.value || 'Not specified';
    const teamName = document.querySelector('input[name="Project_Team_Name"]')?.value || 'Not specified';
    const component = document.querySelector('select[name="Project_Component"]')?.value || 'Not specified';
    const section = document.querySelector('select[name="nstp_section"]')?.value || 'Not specified';

    // Members
    const memberRows = Array.from(document.querySelectorAll('.member-row, .member-card, #memberTable tbody tr')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    const memberHtml = memberRows.map(r => {
      const name = r.querySelector('input[name="member_name[]"]')?.value || '—';
      const role = r.querySelector('input[name="member_role[]"]')?.value || '—';
      const email = r.querySelector('input[name="member_email[]"]')?.value || '—';
      const contact = r.querySelector('input[name="member_contact[]"]')?.value || '—';
      return `<li><strong>${name}</strong> — ${role} — ${email} — ${contact}</li>`;
    }).join('');

    // Activities
    const activityRows = Array.from(document.querySelectorAll('.activity-row')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    const activitiesHtml = activityRows.map((r, i) => {
      const stage = r.querySelector('input[name="stage[]"], textarea[name="stage[]"]')?.value || '—';
      const specific = r.querySelector('textarea[name="activities[]"], input[name="activities[]"]')?.value || '—';
      const timeframe = r.querySelector('input[name="timeframe[]"]')?.value || '—';
      const impl = r.querySelector('input[name="implementation_date[]"]')?.value || '—';
      const point = r.querySelector('input[name="point_person[]"], textarea[name="point_person[]"]')?.value || '—';
      return `<li><strong>Activity ${i+1}</strong>: Stage ${stage} — ${specific} (Timeframe: ${timeframe}, Date: ${impl}, Point: ${point})</li>`;
    }).join('');

    // Budgets
    const budgetRows = Array.from(document.querySelectorAll('.budget-row')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    const budgetsHtml = budgetRows.map((r, i) => {
      const act = r.querySelector('textarea[name="budget_activity[]"]')?.value || '—';
      const res = r.querySelector('textarea[name="budget_resources[]"]')?.value || '—';
      const partners = r.querySelector('textarea[name="budget_partners[]"]')?.value || '—';
      const amount = r.querySelector('input[name="budget_amount[]"]')?.value || '—';
      return `<li><strong>Budget ${i+1}</strong>: ${act} — ${res} — ${partners} — ${amount}</li>`;
    }).join('');

    const html = `
      <div style="text-align:left; max-height:60vh; overflow:auto">
        <h3 class="text-lg font-semibold">Project</h3>
        <p><strong>Name:</strong> ${projectName}</p>
        <p><strong>Team:</strong> ${teamName}</p>
        <p><strong>Component / Section:</strong> ${component} / ${section}</p>
        <hr />
        <h3 class="text-lg font-semibold mt-2">Members</h3>
        <ul>${memberHtml || '<li>No members</li>'}</ul>
        <hr />
        <h3 class="text-lg font-semibold mt-2">Activities</h3>
        <ul>${activitiesHtml || '<li>No activities</li>'}</ul>
        <hr />
        <h3 class="text-lg font-semibold mt-2">Budgets</h3>
        <ul>${budgetsHtml || '<li>No budgets</li>'}</ul>
      </div>
    `;

    Swal.fire({
      title: '<div class="text-2xl font-bold">📋 Review Project Changes</div>',
      html: html,
      width: '800px',
      showCancelButton: true,
      confirmButtonText: 'Save Changes',
      cancelButtonText: 'Cancel',
      customClass: { popup: 'p-4' }
    }).then(result => {
      if (result.isConfirmed) {
        // Disable hidden duplicate inputs before programmatic submit
        try { disableHiddenFormInputs(); } catch (e) { console.warn('disableHiddenFormInputs error', e); }
        // Submit the form programmatically
        isSubmitting = true;
        form.submit();
      }
    });
  }

  // Disable hidden inputs to prevent duplicate desktop/mobile fields from being submitted
  function disableHiddenFormInputs() {
    try {
      const form = document.getElementById('projectForm');
      if (!form) return;
      const containers = Array.from(document.querySelectorAll('.activity-row, .budget-row, .member-row, .member-card, #memberTable tbody tr'));
      containers.forEach(el => {
        try {
          const style = getComputedStyle(el);
          if (el.offsetParent === null || style.display === 'none' || style.visibility === 'hidden') {
            el.querySelectorAll('input, textarea, select, button').forEach(control => {
              try { control.disabled = true; control.dataset._disabledByScript = '1'; } catch (e) {}
            });
          }
        } catch (e) {}
      });
      form.querySelectorAll('input,textarea,select').forEach(control => {
        try {
          const style = getComputedStyle(control);
          if (control.offsetParent === null || style.display === 'none' || style.visibility === 'hidden') {
            control.disabled = true; control.dataset._disabledByScript = '1';
          }
        } catch (e) {}
      });
      console.log('Disabled hidden form inputs (working)');
    } catch (e) { console.warn('disableHiddenFormInputs failed (working)', e); }
  }

  // Attach save-style button handlers (show no-change modal if nothing edited)
  setTimeout(() => {
    try {
      const saveSelectors = [
        '#saveChanges',
        '.save-changes',
        'button[data-action="save-changes"]',
        'button[name="save_changes"]',
        'button[data-role="save"]',
        'button[type="submit"]'
      ];
      const saveButtons = document.querySelectorAll(saveSelectors.join(','));
      saveButtons.forEach((btn) => {
        btn.addEventListener('click', function(e) {
          // Prevent default so we can control flow
          e.preventDefault();
          // If form hasn't changed, show info modal
          if (!formChanged) {
            Swal.fire({
              icon: 'info',
              title: 'No changes were made yet',
              text: 'No changes were made yet. Make some edits before saving.',
              confirmButtonColor: '#3085d6'
            });
            return;
          }

          // Validate fields
          const validationErrors = validateFormRequirements();
          if (validationErrors.length > 0) {
            const html = `<div style="text-align:left">${validationErrors.join('<br>')}</div>`;
            Swal.fire({ icon: 'error', title: 'Validation Error!', html: html, confirmButtonColor: '#3085d6', width: '600px' });
            return;
          }

          // Everything OK - show review modal and submit on confirm
          showReviewModal(form);
        });
      });
    } catch (e) {
      console.error('Error attaching save handlers', e);
    }
  }, 400);
  
  console.log('✅ Staff edit form initialized successfully');
});
</script>