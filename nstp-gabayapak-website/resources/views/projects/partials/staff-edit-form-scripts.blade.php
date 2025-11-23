<script>
console.log('🚀 Staff Edit Form Scripts Loading...');

// Global variables for form management
let formChanged = false;
let isSubmitting = false;
let membersAdded = new Set();
// Track added student IDs as a fallback (more reliable than emails)
let membersAddedIds = new Set();

// Normalize email for consistent set operations
function normalizeEmail(email) {
  return (email || '').toString().trim().toLowerCase();
}

// Ensure staff-specific tracking sets exist (some functions in other script blocks expect these)
if (typeof staff_addedMemberEmails === 'undefined') {
  var staff_addedMemberEmails = new Set();
}
if (typeof staff_addedMemberIds === 'undefined') {
  var staff_addedMemberIds = new Set();
}

// Provide a safe placeholder for staff_loadMemberList so other script blocks
// can call/delegate to it before the real implementation is parsed.
// The real `function staff_loadMemberList() { ... }` later in this file
// will overwrite this placeholder when loaded.
if (typeof window.staff_loadMemberList !== 'function') {
  window.staff_loadMemberList = function() {
    console.warn('staff_loadMemberList placeholder called — real implementation not loaded yet.');
    return;
  };
  // mark as placeholder so delegators can retry until real impl is available
  window.staff_loadMemberList._isPlaceholder = true;
}

// Immediate, robust delegated remove handler (canonical pattern)
// Attach as soon as this script is parsed so ordering issues won't break remove buttons.
(function attachStaffRemoveHandlerImmediate(){
  if (window.staff_removeRowHandlerAttached) return;
  document.addEventListener('click', function (e) {
    const btn = e.target && e.target.closest ? e.target.closest('.removeRow') : null;
    if (!btn) return;

    e.preventDefault();

    // If removing member, check if it's the project owner
    if (btn.closest && (btn.closest('#memberTable tbody tr') || btn.closest('.member-card'))) {
      const memberRow = btn.closest('tr, .member-card');
      const studentIdInput = memberRow && memberRow.querySelector ? memberRow.querySelector('input[name="member_student_id[]"]') : null;

      // If the row explicitly shows 'Project Owner' label, or role input indicates owner, block removal
      const ownerLabel = memberRow && memberRow.querySelector ? memberRow.querySelector('.text-blue-600, .project-owner-label') : null;
      const roleInput = memberRow && memberRow.querySelector ? memberRow.querySelector('input[name="member_role[]"]') : null;
      const roleValue = roleInput && roleInput.value ? String(roleInput.value).toLowerCase() : '';
      if (ownerLabel || roleValue.includes('owner') || roleValue.includes('project leader') || roleValue.includes('leader')) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'Project Owner is not allowed to be removed.' });
        else alert('Project Owner is not allowed to be removed.');
        return;
      }

      // Also block if student id matches known owner id (if available)
      if (studentIdInput && studentIdInput.value && typeof projectOwnerStudentId !== 'undefined' && projectOwnerStudentId !== null && String(studentIdInput.value) == String(projectOwnerStudentId)) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'Project Owner is not allowed to be removed.' });
        else alert('Project Owner is not allowed to be removed.');
        return;
      }

      const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
      const memberCardRows = document.querySelectorAll('.member-card').length;
      const totalMemberRows = memberTableRows + memberCardRows;
      if (totalMemberRows <= 1) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one team member is required.' });
        else alert('At least one team member is required.');
        return;
      }
    }

    // If removing activity, ensure at least one remains
    if (btn.closest && btn.closest('.activity-row')) {
      const activityRows = document.querySelectorAll('.activity-row').length;
      if (activityRows <= 1) {
        if (window.Swal) Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one activity is required.' });
        else alert('At least one activity is required.');
        return;
      }
    }

    // Confirm removal
    function performRemoval() {
      const row = btn.closest('tr, .grid, .activity-row, .budget-row, .member-card');
      if (!row) return;
      try {
        // cleanup tracking sets if member
        try {
          const emailInput = row.querySelector && row.querySelector('input[name="member_email[]"]');
          if (emailInput && emailInput.value && typeof staff_addedMemberEmails !== 'undefined') staff_addedMemberEmails.delete(emailInput.value);
          const idInput = row.querySelector && row.querySelector('input[name="member_student_id[]"]');
          if (idInput && idInput.value && typeof staff_addedMemberIds !== 'undefined') staff_addedMemberIds.delete(String(idInput.value));
        } catch (e) { console.warn('Error cleaning tracking sets', e); }

        // call centralized remover to ensure modal refresh and set cleanup
        try { staff_onMemberRemoved(row); } catch (e) { console.warn('staff_onMemberRemoved call failed', e); row.remove(); }
        try { if (typeof staff_markFormChanged === 'function') staff_markFormChanged('Item removed', 'removeRow'); else formChanged = true; } catch (e) {}
        // Inform user of successful removal
        if (window.Swal) {
          Swal.fire('Removed!', 'The item has been removed.', 'success');
        } else {
          alert('Removed!');
        }
      } catch (e) { console.error('performRemoval error', e); }
    }

    if (window.Swal) {
      Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, remove it!' })
        .then((result) => { if (result.isConfirmed) performRemoval(); });
    } else {
      if (confirm('Are you sure you want to remove this item?')) performRemoval();
    }
  });
  window.staff_removeRowHandlerAttached = true;
})();

// Function to add an activity row with data
function addActivityRow(stage = '', activity = '', timeframe = '', implementationDate = '', pointPerson = '', status = 'Planned', markDirty = true, activityId = '') {
  const desktopContainer = document.getElementById('activitiesContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'proposal-table-row activity-row flex items-center gap-4';
    newRow.innerHTML = `
      <div class="w-20 flex-none">
        <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" value="${stage}" >
        <input type="hidden" name="activity_id[]" value="${activityId || ''}">
      </div>
      <div class="flex-1 px-2">
        <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities...">${activity}</textarea>
      </div>
      <div class="w-36 px-2 flex-none">
        <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" value="${timeframe}">
      </div>
      <div class="w-44 px-2 flex-none">
        <input type="date" name="implementation_date[]" class="proposal-input w-full" value="${implementationDate}">
      </div>
      <div class="flex-1 px-2">
        <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s">${pointPerson}</textarea>
      </div>
      <div class="w-30 py-3 flex-none">
        <select name="status[]" class="proposal-select w-full">
          <option ${status === 'Planned' ? 'selected' : ''}>Planned</option>
          <option ${status === 'Ongoing' ? 'selected' : ''}>Ongoing</option>
        </select>
      </div>
      <div class="w-20 py-3 flex-none">
        <button type="button" class="proposal-remove-btn removeRow">Remove</button>
      </div>
    `;
    desktopContainer.appendChild(newRow);
  }



  const mobileContainer = document.getElementById('activitiesContainerMobile');
  if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'activity-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
        <input name="stage[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" placeholder="Stage" value="${stage}">
        <input type="hidden" name="activity_id[]" value="${activityId || ''}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
        <textarea name="activities[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2" placeholder="Specific Activities">${activity}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
        <input name="timeframe[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" placeholder="Time Frame" value="${timeframe}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Implementation Date <span class="text-red-500">*</span></label>
        <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" value="${implementationDate}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
        <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2" placeholder="Point Person/s">${pointPerson}</textarea>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <div class="space-y-1 flex-1">
          <label class="block text-xs font-medium text-gray-600">Status</label>
          <select name="status[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm">
            <option ${status === 'Planned' ? 'selected' : ''}>Planned</option>
            <option ${status === 'Ongoing' ? 'selected' : ''}>Ongoing</option>
          </select>
        </div>
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
  }
  if (markDirty) formChanged = true;
  // attachRemoveButtons(); // not needed: we use delegated listener
}

// Centralized handler when a member row is removed.
// Removes tracking entries (email/id), removes the DOM row, marks form changed,
// and refreshes the member modal list so the removed student becomes available again.
function staff_onMemberRemoved(row) {
  if (!row) return;
  try {
    // Extract inputs before removing DOM
    const emailInput = row.querySelector && row.querySelector('input[name="member_email[]"]');
    const idInput = row.querySelector && row.querySelector('input[name="member_student_id[]"]');
    const email = emailInput && emailInput.value ? String(emailInput.value).trim() : '';
    const sid = idInput && idInput.value ? String(idInput.value).trim() : '';

    // Remove from tracking sets used by both staff and client scripts (normalize emails)
    try { if (email && typeof membersAdded !== 'undefined') membersAdded.delete(normalizeEmail(email)); } catch (e) {}
    try { if (sid && typeof membersAddedIds !== 'undefined') membersAddedIds.delete(String(sid)); } catch (e) {}
    try { if (email && typeof staff_addedMemberEmails !== 'undefined') staff_addedMemberEmails.delete(normalizeEmail(email)); } catch (e) {}
    try { if (sid && typeof staff_addedMemberIds !== 'undefined') staff_addedMemberIds.delete(String(sid)); } catch (e) {}

    // Remove DOM
    try { row.remove(); } catch (e) { console.warn('Could not remove row element', e); }

    // Also remove any duplicate representations (desktop/mobile) that reference the same student id or email
    try {
      if (sid) {
        const sameIdInputs = Array.from(document.querySelectorAll('input[name="member_student_id[]"]'));
        sameIdInputs.forEach(inp => {
          try {
            if (String(inp.value).trim() === String(sid).trim()) {
              const ancestor = inp.closest('tr, .member-card, .member-row');
              if (ancestor && ancestor !== row) ancestor.remove();
            }
          } catch (e) {}
        });
      }
      if (email) {
        const sameEmailInputs = Array.from(document.querySelectorAll('input[name="member_email[]"]'));
        sameEmailInputs.forEach(inp => {
          try {
            if ((inp.value||'').toString().trim() === email) {
              const ancestor = inp.closest('tr, .member-card, .member-row');
              if (ancestor && ancestor !== row) ancestor.remove();
            }
          } catch (e) {}
        });
      }
    } catch (e) { console.warn('Error removing duplicate member representations', e); }

    // Mark form as changed
    try { if (typeof staff_markFormChanged === 'function') staff_markFormChanged('Member removed', 'members'); else formChanged = true; } catch (e) {}

    // If the member modal is open, refresh its list so the removed student reappears
    try {
      const modal = document.getElementById('memberModal');
      if (modal && !modal.classList.contains('hidden')) {
        // Use staff_loadMemberList if available, otherwise fallback to loadMemberList
        if (typeof window.staff_loadMemberList === 'function' && !window.staff_loadMemberList._isPlaceholder) {
          window.staff_loadMemberList();
        } else {
          loadMemberList();
        }
      }
    } catch (e) { console.warn('Error refreshing member modal after removal', e); }
  } catch (e) {
    console.error('staff_onMemberRemoved unexpected error', e);
  }
}

// Function to add a budget row with data
function addBudgetRow(activity = '', resources = '', partners = '', amount = '', markDirty = true, budgetId = '') {
  const desktopContainer = document.getElementById('budgetContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start budget-row';
    newRow.innerHTML = `
      <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity...">${activity || ''}</textarea>
      <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
      <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed...">${resources || ''}</textarea>
      <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations...">${partners || ''}</textarea>
      <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00" value="${amount || ''}">
      <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
    `;
    desktopContainer.appendChild(newRow);
  }

  const mobileContainer = document.getElementById('budgetContainerMobile');
  if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'budget-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Activity</label>
        <textarea name="budget_activity[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2">${activity || ''}</textarea>
        <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
        <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2">${resources || ''}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
        <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2">${partners || ''}</textarea>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <div class="space-y-1 flex-1">
          <label class="block text-xs font-medium text-gray-600">Amount</label>
          <input type="text" name="budget_amount[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" placeholder="₱ 0.00" value="${amount || ''}">
        </div>
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
  }
  if (markDirty) formChanged = true;
}

// Function to add a member row
function addMemberRow(studentId, name, email, contact, role = '', markDirty = true) {
  const desktopTable = document.querySelector('#memberTable tbody');
  if (desktopTable) {
    const newRow = document.createElement('tr');
    newRow.className = 'hover:bg-gray-50 transition-colors';
    newRow.innerHTML = `
      <td class="px-6 py-4">
        <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${name}" readonly>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </td>
      <td class="px-6 py-4">
        <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required value="${role}">
      </td>
      <td class="px-6 py-4">
        <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${email}" readonly>
      </td>
      <td class="px-6 py-4">
        <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus;border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${contact}" required readonly>
      </td>
      <td class="px-6 py-4 text-center">
        <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">Remove</button>
      </td>
    `;
    desktopTable.appendChild(newRow);
  }

  const mobileContainer = document.getElementById('memberContainer');
  if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
        <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${name}" readonly>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
        <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus;border-blue-500 transition-colors" placeholder="e.g., Member" required value="${role}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
        <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus;border-blue-500 transition-colors" value="${email}" readonly>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
        <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus;border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${contact}" required readonly>
      </div>
      <div class="flex justify-end">
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
  }
  // track added email and mark change
  if (email) membersAdded.add(normalizeEmail(email));
  // track added student id (string) to avoid duplicates when emails differ or missing
  if (studentId) membersAddedIds.add(String(studentId));
  // also keep staff-specific tracking sets in sync
  if (email) staff_addedMemberEmails.add(normalizeEmail(email));
  if (studentId) staff_addedMemberIds.add(String(studentId));
  if (markDirty) formChanged = true;
  console.log('Member row added:', name);
}

// Function to load existing project data
function loadExistingData() {
  console.log('🔄 Loading existing project data...');
  
  const projectData = @json($project ?? null);

  // If blade didn't include members inside the serialized project, try the preloaded global
  try {
    if ((!projectData || !projectData.members || projectData.members.length === 0) && typeof window.preloadedProjectMembers !== 'undefined' && Array.isArray(window.preloadedProjectMembers) && window.preloadedProjectMembers.length > 0) {
      projectData.members = window.preloadedProjectMembers;
    }
  } catch (e) { console.warn('Error applying preloadedProjectMembers fallback', e); }
  
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
        activity.status || 'Planned',
        false,
        activity.Activity_ID || activity.id || ''
      );
    });
  } else {
    // Add one blank activity row if none exist
    addActivityRow('', '', '', '', '', 'Planned', false);
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
        false,
        budget.Budget_ID || budget.id || ''
      );
    });
  } else {
    // Add one blank budget row if none exist
    addBudgetRow('', '', '', '', false);
  }

  // Load members
  // Fallback: if student_ids is missing/empty, try to derive from projectData.members (blade-side members array)
  if ((!projectData.student_ids || projectData.student_ids.length === 0) && Array.isArray(projectData.members) && projectData.members.length > 0) {
    try {
      projectData.student_ids = projectData.members.map(m => (m.student_id || m.student_id || m.student_id === 0) ? (m.student_id || m.student_id) : null).filter(Boolean);
      // Build member_roles map if possible
      if (!projectData.member_roles || typeof projectData.member_roles !== 'object') projectData.member_roles = {};
      projectData.members.forEach(m => { if (m.student_id && m.role) projectData.member_roles[String(m.student_id)] = m.role; });
    } catch (e) { console.warn('Error deriving student_ids from project.members', e); }
  }

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
      // Build a lookup by student id for deterministic ordering and role assignment
      const studentsById = {};
      (students || []).forEach(s => {
        const id = s.id || s.student_id || '';
        if (id) studentsById[String(id)] = s;
      });

      // projectData.student_ids is the authoritative order; iterate it so roles map correctly
      (projectData.student_ids || []).forEach(sid => {
        const sidStr = String(sid);
        const student = studentsById[sidStr];
        if (!student) {
          console.warn('Student data for id not returned by details API:', sidStr);
          // Try to recover using the blade-provided projectData.members fallback
          try {
            const fallback = Array.isArray(projectData.members) ? projectData.members.find(m => String(m.student_id) === sidStr || String(m.id) === sidStr) : null;
            if (fallback) {
              console.info('Using fallback projectData.members for student id:', sidStr);
              const fallbackName = fallback.name || (fallback.first_name ? (fallback.first_name + ' ' + (fallback.last_name || '')) : '') || '';
              const fallbackEmail = fallback.email || fallback.school_email || '';
              const fallbackContact = fallback.contact_number || fallback.contact || '';
              // Resolve role from member_roles map if available
              let role = '';
              if (projectData.member_roles) {
                try {
                  if (Array.isArray(projectData.member_roles)) {
                    const idx = (projectData.student_ids || []).findIndex(id => String(id) === sidStr);
                    if (idx >= 0 && projectData.member_roles[idx]) role = projectData.member_roles[idx];
                  } else {
                    role = projectData.member_roles[sidStr] || projectData.member_roles[parseInt(sidStr)] || '';
                  }
                } catch (e) { console.warn('Error resolving role for fallback student', sidStr, e); }
              }
              addMemberRow(sidStr, fallbackName, fallbackEmail, fallbackContact, role || (fallback.role || ''), false);
              return;
            }
          } catch (e) { console.warn('Fallback lookup error for missing student id', sidStr, e); }
          // If we couldn't recover, skip this id silently (already warned)
          return;
        }

        // member_roles may be an object keyed by student id, or an array by index — support both
        let role = '';
        if (projectData.member_roles) {
          try {
            if (Array.isArray(projectData.member_roles)) {
              // find index of sid in student_ids and use same index
              const idx = (projectData.student_ids || []).findIndex(id => String(id) === sidStr);
              if (idx >= 0 && projectData.member_roles[idx]) role = projectData.member_roles[idx];
            } else {
              // member_roles might be an object keyed by id
              role = projectData.member_roles[sidStr] || projectData.member_roles[parseInt(sidStr)] || '';
            }
          } catch (e) {
            console.warn('Error resolving role for student', sidStr, e);
          }
        }

        addMemberRow(
          student.id,
          student.name || (student.first_name ? (student.first_name + ' ' + (student.last_name || '')) : '') || '',
          student.email || student.school_email || '',
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
  // Delegate to staff-specific loader which handles section/component and exclusions.
  // If only the placeholder exists because the real implementation is parsed later,
  // retry a few times with a short delay so user actions (clicks) don't immediately abort.
  const maxAttempts = 20;
  function _tryDelegate(attempt) {
    console.debug('loadMemberList: delegating to staff_loadMemberList, attempt', attempt);
    if (typeof window.staff_loadMemberList === 'function' && !window.staff_loadMemberList._isPlaceholder) {
      try {
        window.staff_loadMemberList();
      } catch (e) {
        console.error('Error delegating to staff_loadMemberList', e);
      }
      return;
    }
    if (attempt >= maxAttempts) {
      console.warn('staff_loadMemberList not available after multiple attempts. Falling back to internal loader.');
      // Fallback: run an internal loader that queries the students-for-staff endpoint without relying
      // on the deferred staff_loadMemberList implementation. This ensures the modal still shows
      // candidates when script ordering causes delegation to fail.
      try {
        const memberListEl = document.getElementById('memberList');
        if (!memberListEl) {
          console.error('Fallback loader: memberList element not found. Cannot load members.');
          return;
        }
        memberListEl.innerHTML = '<p class="text-center text-gray-500">Loading members (fallback)...</p>';

        const projectComponent = @json($project->Project_Component);
        const projectSection = @json($project->Project_Section);
        if (!projectSection || !projectComponent) {
          memberListEl.innerHTML = '<p class="text-center text-red-500">Project component and section not found.</p>';
          return;
        }

        const baseUrl = new URL('{{ route("projects.students.for-staff") }}', window.location.origin);
        baseUrl.searchParams.append('section', projectSection);
        baseUrl.searchParams.append('component', projectComponent);

        // Perform a diagnostic fetch without exclusions so that at least we can show available students
        fetch(baseUrl)
          .then(r => r.json())
          .then(allStudents => {
            console.debug('Fallback loader -> fetched students count=', allStudents ? allStudents.length : 0, allStudents);
            if (allStudents && allStudents.length > 0) {
              // Collect existing member emails/ids from the DOM so we can filter them out
              const existingMemberEmails = Array.from(document.querySelectorAll('input[name="member_email[]"]')).map(i => (i.value||'').trim()).filter(Boolean);
              const existingMemberIds = Array.from(document.querySelectorAll('input[name="member_student_id[]"]')).map(i => (i.value||'').trim()).filter(Boolean);

              let out = '<div class="space-y-2 p-2">';
              const candidates = allStudents.filter(s => {
                const sid = s.id ? String(s.id) : '';
                const sem = s.email ? String(s.email).trim() : '';
                if (sid && existingMemberIds.indexOf(sid) >= 0) return false;
                if (sem && existingMemberEmails.indexOf(sem) >= 0) return false;
                return true;
              });

              candidates.forEach(s => {
                // Render checkbox with data attributes so addSelectedMembersToForm can read them
                out += `<div class="p-2 border rounded bg-white"><label><input type=\"checkbox\" name=\"available_members[]\" value=\"${s.id}\" data-name=\"${(s.name||'').replace(/"/g,'&quot;')}\" data-email=\"${(s.email||'').replace(/"/g,'&quot;')}\" data-contact=\"${(s.contact_number||'').replace(/"/g,'&quot;')}\"> <strong>${s.name}</strong> — ${s.email || '(no email)'} — ${s.contact_number || '(no contact)'}</label></div>`;
              });
              if (candidates.length === 0) {
                out += '<p class="text-sm text-gray-600 mt-2">All students in this section/component are already added to the project.</p>';
              }
              out += '</div>';
              memberListEl.innerHTML = out;
            } else {
              memberListEl.innerHTML = `<p class=\"text-center text-gray-500\">No students found in ${projectComponent} - ${projectSection}.</p>`;
            }
          })
          .catch(err => {
            console.error('Fallback loader error', err);
            memberListEl.innerHTML = `<p class="text-center text-red-500">Error loading students (fallback). Check console/server logs.</p>`;
          });
      } catch (e) {
        console.error('Fallback loader unexpected error', e);
      }
      return;
    }
    // retry shortly
    setTimeout(function() { _tryDelegate(attempt + 1); }, 50);
  }
  _tryDelegate(0);
}

// Add selected members to the form (mirrors edit-form behavior)
function addSelectedMembersToForm() {
  const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
  if (!selectedMembers || selectedMembers.length === 0) {
    // show swal warning if available, else alert
    if (window.Swal) {
      Swal.fire({ icon: 'warning', title: 'No Members Selected', text: 'Please select at least one member to add to your team.', confirmButtonColor: '#3b82f6' });
    } else {
      alert('Please select at least one member to add to your team.');
    }
    return;
  }

  selectedMembers.forEach(checkbox => {
    const memberId = checkbox.value;
    const memberName = checkbox.dataset.name || '';
    const memberEmail = checkbox.dataset.email || '';
    const memberContact = checkbox.dataset.contact || '';

    // Track email and id in both staff and general sets (normalize email)
    if (memberEmail) {
      membersAdded.add(normalizeEmail(memberEmail));
      staff_addedMemberEmails.add(normalizeEmail(memberEmail));
    }
    if (memberId) {
      membersAddedIds.add(String(memberId));
      staff_addedMemberIds.add(String(memberId));
    }

    // Use existing helper to append rows/cards
    addMemberRow(memberId, memberName, memberEmail, memberContact);
  });

  // Close modal
  const modal = document.getElementById('memberModal');
  if (modal) modal.classList.add('hidden');

  // Success feedback
  if (window.Swal) {
    Swal.fire({ icon: 'success', title: 'Members Added Successfully!', text: `${selectedMembers.length} member(s) have been added to your team.`, timer: 2000, showConfirmButton: false });
  } else {
    alert(`${selectedMembers.length} member(s) added successfully!`);
  }
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
      const memberModalEl = document.getElementById('memberModal');
      if (memberModalEl) memberModalEl.classList.remove('hidden');
    });
  }
  
  if (openMemberModalMobileBtn) {
    openMemberModalMobileBtn.addEventListener('click', function() {
      loadMemberList();
      const memberModalEl = document.getElementById('memberModal');
      if (memberModalEl) memberModalEl.classList.remove('hidden');
    });
  }

  // Modal event handlers
  const closeMemberModalBtn = document.getElementById('closeMemberModal');
  if (closeMemberModalBtn) {
    closeMemberModalBtn.addEventListener('click', function() {
      const modalEl = document.getElementById('memberModal');
      if (modalEl) modalEl.classList.add('hidden');
    });
  }

  const cancelMemberSelectionBtn = document.getElementById('cancelMemberSelection');
  if (cancelMemberSelectionBtn) {
    cancelMemberSelectionBtn.addEventListener('click', function() {
      const modalEl = document.getElementById('memberModal');
      if (modalEl) modalEl.classList.add('hidden');
    });
  }

  const addSelectedMembersBtn = document.getElementById('addSelectedMembers');
  if (addSelectedMembersBtn) {
    addSelectedMembersBtn.addEventListener('click', function(event) {
      event.preventDefault();
      addSelectedMembersToForm();
    });
  }

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
      const row = e.target.closest('.member-row') || e.target.closest('.member-card');
      try { staff_onMemberRemoved(row); } catch (err) { console.warn('member remove handler failed', err); if (row) row.remove(); }
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
      
      isSubmitting = true;
      console.log('✅ Form submitted successfully');
    });
  }
  
  console.log('✅ Staff edit form initialized successfully');

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
          window.location.href = '{{ route("projects.show", $project) }}';
        }
      });
    });
  }

  // Client-side validation helper
  function validateFormRequirements() {
    // Adapted from edit-form-scripts: build errors array and return it (do not show modal here)
    const form = document.getElementById('projectForm');
    if (!form) return ['Project form not found'];
    const formData = new FormData(form);
    const errors = [];

    // Basic project fields
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

    // Team logo (if required) - keep original behavior minimal (skip if image present)
    const logoFile = formData.get('Project_Logo');
    const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
    if (!hasExistingLogo && (!logoFile || logoFile.size === 0)) {
      errors.push('A team logo is required for project submission.');
    }

    // Team members
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

    // Activities: iterate visible .activity-row elements directly to avoid desktop/mobile duplication
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

  // Review modal
  function showReviewModal(form) {
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
    const memberHtml = memberRows.length ? memberRows.map(r => {
      const name = r.querySelector('input[name="member_name[]"]')?.value || '—';
      const role = r.querySelector('input[name="member_role[]"]')?.value || '—';
      const email = r.querySelector('input[name="member_email[]"]')?.value || '—';
      const contact = r.querySelector('input[name="member_contact[]"]')?.value || '—';
      return `<li class="py-1"><strong>${name}</strong><div class="text-sm text-gray-600">${role} — ${email} — ${contact}</div></li>`;
    }).join('') : '<li class="text-gray-500">No members</li>';

    // Activities (visible only)
    const activityRows = Array.from(document.querySelectorAll('.activity-row')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    const activitiesHtml = activityRows.length ? activityRows.map((r, i) => {
      const stage = r.querySelector('input[name="stage[]"], textarea[name="stage[]"]')?.value || '—';
      const specific = r.querySelector('textarea[name="activities[]"], input[name="activities[]"]')?.value || '—';
      const timeframe = r.querySelector('input[name="timeframe[]"]')?.value || '—';
      const impl = r.querySelector('input[name="implementation_date[]"]')?.value || '—';
      const point = r.querySelector('input[name="point_person[]"], textarea[name="point_person[]"]')?.value || '—';
      return `
        <li class="py-2 border-b border-gray-100">
          <div class="flex justify-between items-start">
            <div><strong>Activity ${i+1}</strong> <span class="text-sm text-gray-600">(Stage ${stage})</span></div>
            <div class="text-sm text-gray-500">Timeframe: ${timeframe} — Date: ${impl}</div>
          </div>
          <div class="mt-1 text-sm">${specific}</div>
          <div class="mt-1 text-sm text-gray-600">Point: ${point}</div>
        </li>`;
    }).join('') : '<li class="text-gray-500">No activities</li>';

    // Budgets (visible only)
    const budgetRows = Array.from(document.querySelectorAll('.budget-row')).filter(r => r && r.offsetParent !== null && getComputedStyle(r).display !== 'none');
    const budgetsHtml = budgetRows.length ? budgetRows.map((r, i) => {
      const act = r.querySelector('textarea[name="budget_activity[]"]')?.value || '—';
      const res = r.querySelector('textarea[name="budget_resources[]"]')?.value || '—';
      const partners = r.querySelector('textarea[name="budget_partners[]"]')?.value || '—';
      const amount = r.querySelector('input[name="budget_amount[]"]')?.value || '—';
      return `<li class="py-1"><strong>Budget ${i+1}</strong><div class="text-sm text-gray-600">${act} — ${res} — ${partners} — <span class="font-medium">${amount}</span></div></li>`;
    }).join('') : '<li class="text-gray-500">No budgets</li>';

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

    Swal.fire({ title: '<div class="text-2xl font-bold">📋 Review Project Changes</div>', html: html, width: '800px', showCancelButton: true, confirmButtonText: 'Save Changes', cancelButtonText: 'Cancel' })
      .then(result => {
        if (result.isConfirmed) {
          isSubmitting = true;
          try { staff_syncHiddenIds(form); } catch (e) { console.warn('staff_syncHiddenIds failed', e); }
          form.submit();
        }
      });
  }

  // Attach save-style button handlers (show no-change modal if nothing edited)
  setTimeout(() => {
    try {
      const saveSelectors = ['#saveChanges', '.save-changes', 'button[data-action="save-changes"]', 'button[name="save_changes"]', 'button[data-role="save"]', 'button[type="submit"]'];
      const saveButtons = document.querySelectorAll(saveSelectors.join(','));
      saveButtons.forEach((btn) => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          if (!formChanged) {
            Swal.fire({ icon: 'info', title: 'No changes were made yet', text: 'No changes were made yet. Make some edits before saving.', confirmButtonColor: '#3085d6' });
            return;
          }
          const validationErrors = validateFormRequirements();
          if (validationErrors.length > 0) { Swal.fire({ icon: 'error', title: 'Validation Error!', html: `<div style="text-align:left">${validationErrors.join('<br>')}</div>`, confirmButtonColor: '#3085d6', width: '600px' }); return; }
          showReviewModal(form);
        });
      });
    } catch (e) { console.error('Error attaching save handlers', e); }
  }, 400);
});
</script>

  
<script>
/* ============================
Staff Project Edit Form JS (included inside a single DOMContentLoaded wrapper)
  ============================ */

/* Project data - passed from Laravel */
const projectOwnerStudentId = @json($project->student_id ?? null);

/* Single source of truth for member emails and data-population state */
let staff_addedMemberEmails = new Set();
let staff_addedMemberIds = new Set();
let staff_dataPopulated = false;
let staff_removeRowHandlerAttached = false;
let staff_formChanged = false;
let staff_initialFormData = {};
let staff_isSubmitting = false;
let staff_activitiesInitialized = false;
let staff_budgetsInitialized = false;
let staff_membersInitialized = false;
let staff_dataLoadingComplete = false; // Track when all data loading is complete
let staff_changeTrackingInitialized = false;

/* Helper: staff_safeAddListener */
function staff_safeAddListener(id, event, handler) {
  const el = document.getElementById(id);
  if (el) el.addEventListener(event, handler);
}

/* Debug function to manually trigger form changed state */
function staff_debugFormChanges() {
  console.log('=== STAFF FORM DEBUG INFO ===');
  console.log('staff_formChanged flag:', staff_formChanged);
  console.log('staff_hasFormChanged():', staff_hasFormChanged());
  console.log('staff_initialFormData keys:', Object.keys(staff_initialFormData));
  console.log('staff_currentFormData keys:', Object.keys(staff_getFormData()));
  console.log('staff_dataPopulated:', staff_dataPopulated);
  console.log('staff_isSubmitting:', staff_isSubmitting);
  
  // Check if form and submit button exist
  const form = document.getElementById('projectForm');
  const submitBtn = document.querySelector('button[type="submit"]');
  const submitBtns = document.querySelectorAll('button[type="submit"], input[type="submit"]');
  
  console.log('Form element found:', !!form);
  console.log('Submit button found:', !!submitBtn);
  console.log('All submit buttons found:', submitBtns.length);
  
  submitBtns.forEach((btn, index) => {
    console.log(`Submit button ${index}:`, btn.id || btn.className || 'no id/class', 'visible:', btn.offsetParent !== null);
  });
  
  // Manually set form as changed for testing
  staff_formChanged = true;
  console.log('Manually set staff_formChanged to true');
  
  // Try to trigger form submission
  if (form) {
    console.log('Form element found, attempting manual submit trigger');
    const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
    form.dispatchEvent(submitEvent);
  }
  
  // Try clicking the submit button if it exists
  if (submitBtn) {
    console.log('Attempting to click submit button');
    submitBtn.click();
  }
}

// Add emergency bypass function
function staff_emergencySubmit() {
  console.log('🚨 STAFF EMERGENCY SUBMIT TRIGGERED');
  staff_formChanged = true;
  const form = document.getElementById('projectForm');
  if (form) {
    // Bypass all checks and submit directly
    staff_isSubmitting = true;
    console.log('Bypassing all validation, submitting form directly');
    try { staff_syncHiddenIds(form); } catch (e) { console.warn('staff_syncHiddenIds failed', e); }
    form.submit();
  } else {
    console.error('Form not found!');
  }
}

// Make debug functions globally available
window.staff_debugFormChanges = staff_debugFormChanges;
window.staff_emergencySubmit = staff_emergencySubmit;

/* --------------------
   CORE DATA MANIPULATION FUNCTIONS
   -------------------- */

// Function to add an activity row with data
function staff_addActivityRow(stage, specificActivity, timeframe, implementationDate, pointPerson, status, addToMobile = true, activityId = '') {
  console.log('Adding activity row:', stage, specificActivity);
  
  // Desktop table view
  const desktopContainer = document.getElementById('activitiesContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'activity-row grid grid-cols-[1fr_2fr_1fr_1fr_1fr_1fr_auto] gap-4 items-center px-6 py-4 border-b border-gray-300';
    newRow.innerHTML = `
      <div>
        <input name="stage[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Stage" value="${stage || ''}" required>
        <input type="hidden" name="activity_id[]" value="${activityId || ''}">
      </div>
      <div>
        <textarea name="activities[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Specific Activity" required>${specificActivity || ''}</textarea>
      </div>
      <div>
        <input name="timeframe[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Time Frame" value="${timeframe || ''}" required>
      </div>
      <div>
        <input name="implementation_date[]" type="date" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" value="${implementationDate || ''}">
      </div>
      <div>
        <input name="point_person[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Point Person" value="${pointPerson || ''}">
      </div>
      <div>
        <select name="status[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
          <option value="Planned" ${status === 'Planned' ? 'selected' : ''}>Planned</option>
          <option value="In Progress" ${status === 'In Progress' ? 'selected' : ''}>In Progress</option>
          <option value="Completed" ${status === 'Completed' ? 'selected' : ''}>Completed</option>
          <option value="On Hold" ${status === 'On Hold' ? 'selected' : ''}>On Hold</option>
        </select>
      </div>
      <div>
        <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">Remove</button>
      </div>
    `;
    desktopContainer.appendChild(newRow);
  }

  // Mobile card view
  if (addToMobile) {
    const mobileContainer = document.getElementById('activitiesContainerMobile');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'activity-row bg-white p-3 rounded-lg border border-gray-300 shadow-sm space-y-3';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
          <input name="stage[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Stage" value="${stage || ''}" required>
          <input type="hidden" name="activity_id[]" value="${activityId || ''}">
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Specific Activity <span class="text-red-500">*</span></label>
          <textarea name="activities[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Specific Activity" required>${specificActivity || ''}</textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
          <input name="timeframe[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Time Frame" value="${timeframe || ''}" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Implementation Date</label>
          <input name="implementation_date[]" type="date" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" value="${implementationDate || ''}">
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Point Person</label>
          <input name="point_person[]" type="text" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Point Person" value="${pointPerson || ''}">
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Status</label>
          <select name="status[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-500">
            <option value="Planned" ${status === 'Planned' ? 'selected' : ''}>Planned</option>
            <option value="In Progress" ${status === 'In Progress' ? 'selected' : ''}>In Progress</option>
            <option value="Completed" ${status === 'Completed' ? 'selected' : ''}>Completed</option>
            <option value="On Hold" ${status === 'On Hold' ? 'selected' : ''}>On Hold</option>
          </select>
        </div>
        <div class="flex justify-end">
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs transition-colors">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }
  }
  
  staff_markFormChanged('Activity added', 'activities');
}

// Function to add a blank activity row
function staff_addBlankActivityRow() {
  staff_addActivityRow('', '', '', '', '', 'Planned');
}

// Function to add a budget row with data
function staff_addBudgetRow(activity, resources, partners, amount, addToMobile = true, budgetId = '') {
  console.log('Adding budget row:', activity, resources);
  
  // Desktop table view
  const desktopContainer = document.getElementById('budgetContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'budget-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start px-6 py-4 border-b border-gray-300';
    newRow.innerHTML = `
    function staff_addBudgetRow(activity, resources, partners, amount, addToMobile = true, budgetId = '') {
      <div>
      <textarea name="budget_activity[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Activity" required>${activity || ''}</textarea>
      <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
      </div>
      <div>
        <textarea name="budget_resources[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Resources Needed" required>${resources || ''}</textarea>
      </div>
      <div>
        <textarea name="budget_partners[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Partner Agencies">${partners || ''}</textarea>
      </div>
      <div>
        <input name="budget_amount[]" type="number" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Amount" value="${amount || ''}" required>
      </div>
      <div>
        <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">Remove</button>
      </div>
    `;
    desktopContainer.appendChild(newRow);
  }

  // Mobile card view
  if (addToMobile) {
    const mobileContainer = document.getElementById('budgetContainerMobile');
    if (mobileContainer) {
      if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'budget-row bg-white p-3 rounded-lg border border-gray-300 shadow-sm space-y-3';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Activity <span class="text-red-500">*</span></label>
        <textarea name="budget_activity[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Activity" required>${activity || ''}</textarea>
        <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Resources Needed <span class="text-red-500">*</span></label>
          <textarea name="budget_resources[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Resources Needed" required>${resources || ''}</textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
          <textarea name="budget_partners[]" class="w-full px-2 py-1 border border-gray-300 rounded text-sm auto-expand focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Partner Agencies">${partners || ''}</textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Amount <span class="text-red-500">*</span></label>
          <input name="budget_amount[]" type="number" step="0.01" class="w-full px-2 py-1 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500" placeholder="Amount" value="${amount || ''}" required>
        </div>
        <div class="flex justify-end">
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs transition-colors">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }
  }
  
  staff_markFormChanged('Budget added', 'budgets');
}

// Function to add a blank budget row
function staff_addBlankBudgetRow() {
  staff_addBudgetRow('', '', '', '');
}

// Function to add a member row placeholder
function staff_addMemberRowPlaceholder(studentId, isOwner = false) {
  console.log('Adding member placeholder for student ID:', studentId, 'isOwner:', isOwner);
  
  // Add to desktop table
  const desktopTable = document.querySelector('#memberTableBody');
  if (desktopTable) {
    const newRow = document.createElement('tr');
    newRow.className = 'member-row hover:bg-gray-50 transition-colors';
    newRow.innerHTML = `
      <td class="px-6 py-4">
        <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="Loading..." readonly>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </td>
      <td class="px-6 py-4">
        <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required value="${isOwner ? 'Project Leader' : ''}">
      </td>
      <td class="px-6 py-4">
        <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="Loading..." readonly>
      </td>
      <td class="px-6 py-4">
        <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required value="">
      </td>
      <td class="px-6 py-4 text-center">
        ${isOwner ? 
          '<span class="text-sm text-blue-600 font-medium">Project Owner</span>' : 
          '<button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">Remove</button>'
        }
      </td>
    `;
    desktopTable.appendChild(newRow);
  }
  
  // Add to mobile view
  const mobileContainer = document.getElementById('memberContainer');
  if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'member-row bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
        <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="Loading..." readonly>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
        <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required value="${isOwner ? 'Project Leader' : ''}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
        <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="Loading..." readonly>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
        <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" required value="">
      </div>
      ${!isOwner ? 
        '<div class="flex justify-end"><button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button></div>' : 
        '<div class="text-xs text-blue-600 font-medium">Project Owner</div>'
      }
    `;
    mobileContainer.appendChild(newCard);
  }
  
  staff_markFormChanged('Member added', 'members');
}

/* --------------------
   EVENT HANDLERS INITIALIZATION
   -------------------- */

// Function to initialize event handlers for buttons
function staff_initializeEventHandlers() {
  console.log('🚀 Starting staff_initializeEventHandlers...');
  
  const addActivityBtn = document.getElementById('addActivityRow');
  const addBudgetBtn = document.getElementById('addBudgetRow');
  const openMemberModalBtn = document.getElementById('openMemberModal');
  const openMemberModalMobileBtn = document.getElementById('openMemberModalMobile');
  
  console.log('🔍 Button Elements Check:');
  console.log('- addActivityRow:', !!addActivityBtn);
  console.log('- addBudgetRow:', !!addBudgetBtn);
  console.log('- openMemberModal:', !!openMemberModalBtn);
  console.log('- openMemberModalMobile:', !!openMemberModalMobileBtn);
  
  if (addActivityBtn) {
    addActivityBtn.addEventListener('click', function() {
      console.log('Add activity button clicked');
      staff_addBlankActivityRow();
    });
    console.log('✅ Add activity button handler set');
  } else {
    console.error('❌ Add activity button not found!');
  }

  if (addBudgetBtn) {
    addBudgetBtn.addEventListener('click', function() {
      console.log('Add budget button clicked');
      staff_addBlankBudgetRow();
    });
    console.log('✅ Add budget button handler set');
  } else {
    console.error('❌ Add budget button not found!');
  }

  if (openMemberModalBtn) {
    openMemberModalBtn.addEventListener('click', function() {
      console.log('Open member modal button clicked');
      staff_loadMemberList();
      const memberModalEl = document.getElementById('memberModal');
      if (memberModalEl) memberModalEl.classList.remove('hidden');
    });
    console.log('✅ Open member modal button handler set');
  } else {
    console.error('❌ Open member modal button not found!');
  }

  if (openMemberModalMobileBtn) {
    openMemberModalMobileBtn.addEventListener('click', function() {
      console.log('Open member modal mobile button clicked');
      staff_loadMemberList();
      const memberModalEl = document.getElementById('memberModal');
      if (memberModalEl) memberModalEl.classList.remove('hidden');
    });
    console.log('✅ Open member modal mobile button handler set');
  } else {
    console.error('❌ Open member modal mobile button not found!');
  }

  console.log('✅ Event handlers initialization complete');
}

// Make debug functions globally available
window.staff_debugFormChanges = staff_debugFormChanges;
window.staff_emergencySubmit = staff_emergencySubmit;

// Function to debug change detection (accessible from browser console)
function staff_debugChangeDetection() {
  console.log('\n=== STAFF CHANGE DETECTION DEBUG REPORT ===');
  console.log('staff_formChanged flag:', staff_formChanged);
  console.log('staff_changeTrackingInitialized:', staff_changeTrackingInitialized);
  console.log('staff_dataLoadingComplete:', staff_dataLoadingComplete);
  
  const currentData = staff_getFormData();
  const currentKeys = Object.keys(currentData);
  const initialKeys = Object.keys(staff_initialFormData);
  
  console.log('Current form data keys:', currentKeys.length);
  console.log('Initial form data keys:', initialKeys.length);
  
  console.log('staff_hasFormChanged() result:', staff_hasFormChanged());
  
  if (currentKeys.length !== initialKeys.length) {
    console.log('⚠️ Key count mismatch!');
    const currentSet = new Set(currentKeys);
    const initialSet = new Set(initialKeys);
    const newKeys = currentKeys.filter(k => !initialSet.has(k));
    const missingKeys = initialKeys.filter(k => !currentSet.has(k));
    if (newKeys.length) console.log('New keys:', newKeys);
    if (missingKeys.length) console.log('Missing keys:', missingKeys);
  }
  
  // Check specific field differences
  const differences = [];
  currentKeys.forEach(key => {
    const currentValue = JSON.stringify(currentData[key]);
    const initialValue = JSON.stringify(staff_initialFormData[key]);
    if (currentValue !== initialValue) {
      differences.push({
        field: key,
        current: currentValue.substring(0, 100) + (currentValue.length > 100 ? '...' : ''),
        initial: initialValue ? initialValue.substring(0, 100) + (initialValue.length > 100 ? '...' : '') : 'undefined'
      });
    }
  });
  
  if (differences.length > 0) {
    console.log('Field differences found:');
    differences.forEach(diff => {
      console.log(`- ${diff.field}:`);
      console.log(`  Current: ${diff.current}`);
      console.log(`  Initial: ${diff.initial}`);
    });
  } else {
    console.log('No field differences found');
  }
  
  console.log('=== END DEBUG REPORT ===\n');
  
  return {
    staff_formChanged,
    changeTrackingInitialized,
    hasChanges: staff_hasFormChanged(),
    currentDataKeys: currentKeys.length,
    initialDataKeys: initialKeys.length,
    differences: differences.length
  };
}

// Function to manually reset change tracking (accessible from console)
function staff_manualResetTracking() {
  console.log('🔧 Manual change tracking reset requested');
  resetChangeTracking('manual reset');
  console.log('✅ Change tracking manually reset. Try your action again.');
}

// Function to debug form structure and validation (accessible from console)
function staff_debugFormStructure() {
  console.log('\n=== FORM STRUCTURE DEBUG REPORT ===');
  
  const form = document.getElementById('projectForm');
  console.log('Form exists:', !!form);
  
  if (form) {
    const allInputs = form.querySelectorAll('input, textarea, select');
    const requiredInputs = form.querySelectorAll('[required]');
    const visibleRequired = Array.from(requiredInputs).filter(field => field.offsetParent !== null);
    
    console.log('Total form inputs:', allInputs.length);
    console.log('Required inputs:', requiredInputs.length);
    console.log('Visible required inputs:', visibleRequired.length);
    
    // Check dynamic sections
    const memberRows = document.querySelectorAll('.member-row, .member-card');
    const activityRows = document.querySelectorAll('.activity-row');
    const budgetRows = document.querySelectorAll('.budget-row');
    
    console.log('Member rows:', memberRows.length);
    console.log('Activity rows:', activityRows.length);
    console.log('Budget rows:', budgetRows.length);
    
    // Check budget row content
    if (budgetRows.length > 0) {
      console.log('Budget rows analysis:');
      budgetRows.forEach((row, index) => {
        const activityInput = row.querySelector('textarea[name="budget_activity[]"]');
        const resourceInput = row.querySelector('textarea[name="budget_resources[]"]');
        const partnerInput = row.querySelector('textarea[name="budget_partners[]"]');
        const amountInput = row.querySelector('input[name="budget_amount[]"]');
        
        const activityValue = activityInput ? activityInput.value.trim() : '';
        const resourceValue = resourceInput ? resourceInput.value.trim() : '';
        const partnerValue = partnerInput ? partnerInput.value.trim() : '';
        const amountValue = amountInput ? amountInput.value.trim() : '';
        
        const hasContent = activityValue || resourceValue || partnerValue || amountValue;
        const isComplete = activityValue && resourceValue && amountValue; // Updated to require Amount
        console.log(`  Row ${index + 1}: Activity="${activityValue}", Resources="${resourceValue}", Partners="${partnerValue}", Amount="${amountValue}", HasContent: ${hasContent}, Complete: ${isComplete}`);
      });
    }
    
    // Check for any hidden required fields that might cause "not focusable" errors
    const hiddenRequired = Array.from(requiredInputs).filter(field => {
      return field.offsetParent === null || field.style.display === 'none';
    });
    
    if (hiddenRequired.length > 0) {
      console.warn('Hidden required fields found (these can cause validation errors):');
      hiddenRequired.forEach((field, index) => {
        console.log(`${index + 1}. ${field.name || field.placeholder || 'unnamed'} - ${field.tagName}`);
      });
    }
    
    // Test validation
    console.log('Running validation test...');
    const validationResult = validateFormRequirements();
    console.log('Validation result:', validationResult);
  }
  
  console.log('=== END FORM STRUCTURE REPORT ===\n');
}

// Make all debug functions globally available
window.debugChangeDetection = staff_debugChangeDetection;
window.manualResetTracking = staff_manualResetTracking;
window.debugFormStructure = staff_debugFormStructure;
window.emergencySubmit = staff_emergencySubmit;
window.markFormChanged = staff_markFormChanged;

/* --------------------
   Remove button handler
   - Uses event delegation so we don't reattach multiple times.
   -------------------- */
function staff_attachRemoveButtons() {
  if (staff_removeRowHandlerAttached) return;
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.removeRow');
    if (!btn) return;

    e.preventDefault();

    // If removing member, check if it's the project owner
    if (btn.closest('#memberTable tbody tr') || btn.closest('.member-card')) {
      const memberRow = btn.closest('tr, .member-card');
      const studentIdInput = memberRow.querySelector('input[name="member_student_id[]"]');

      // Check if this member is the project owner
      if (studentIdInput && studentIdInput.value && studentIdInput.value == projectOwnerStudentId) {
        if (window.Swal) {
          Swal.fire({ 
            icon: 'error', 
            title: 'Cannot Remove', 
            text: 'Project Owner is not allowed to be removed.', 
            confirmButtonColor: '#3085d6' 
          });
        } else {
          alert('Project Owner is not allowed to be removed.');
        }
        return;
      }

      const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
      const memberCardRows = document.querySelectorAll('.member-card').length;
      const totalMemberRows = memberTableRows + memberCardRows;
      if (totalMemberRows <= 1) {
        if (window.Swal) {
          Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one team member is required.', confirmButtonColor: '#3085d6' });
        } else {
          alert('At least one team member is required.');
        }
        return;
      }
    }

    // If removing activity, ensure at least one remains
    if (btn.closest('.activity-row')) {
      const activityRows = document.querySelectorAll('.activity-row').length;
      if (activityRows <= 1) {
        if (window.Swal) {
          Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one activity is required.', confirmButtonColor: '#3085d6' });
        } else {
          alert('At least one activity is required.');
        }
        return;
      }
    }

    // Confirm removal (use Swal when available)
    function performRemoval() {
      const row = btn.closest('tr, .grid, .activity-row, .budget-row, .member-card');
      if (row) {
        // if member row, remove email from staff_addedMemberEmails set and id from staff_addedMemberIds
        try {
          if (row.querySelector && row.querySelector('input[name="member_email[]"]')) {
            const emailInput = row.querySelector('input[name="member_email[]"]');
            if (emailInput && emailInput.value) {
              staff_addedMemberEmails.delete(emailInput.value);
              console.debug('Deleted from staff_addedMemberEmails:', emailInput.value);
            }
          }
          if (row.querySelector && row.querySelector('input[name="member_student_id[]"]')) {
            const idInput = row.querySelector('input[name="member_student_id[]"]');
            if (idInput && idInput.value) {
              staff_addedMemberIds.delete(String(idInput.value));
              console.debug('Deleted from staff_addedMemberIds:', idInput.value);
            }
          }
        } catch (e) {
          console.warn('Error while cleaning up tracking sets during removal', e);
        }

        row.remove();
        staff_markFormChanged('Item removed', 'removeRow');
        console.log('Form marked as changed - item removed via removeRow button');
      }
    }

    if (window.Swal) {
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, remove it!'
      }).then((result) => {
        if (result.isConfirmed) {
          performRemoval();
          Swal.fire('Removed!', 'The item has been removed.', 'success');
        }
      });
    } else {
      const ok = confirm("Are you sure you want to remove this item? This action cannot be undone.");
      if (ok) {
        performRemoval();
        alert('Removed!');
      }
    }
  });
  staff_removeRowHandlerAttached = true;
}



/* --------------------
   Initialize event handlers
   -------------------- */
function staff_initializeEventHandlers() {
  console.log('🚀 Starting staff_initializeEventHandlers...');
  
  const addActivityBtn = document.getElementById('addActivityRow');
  const addBudgetBtn = document.getElementById('addBudgetRow');
  const openMemberModalBtn = document.getElementById('openMemberModal');
  const openMemberModalMobileBtn = document.getElementById('openMemberModalMobile');
  
  console.log('🔍 Button Elements Check:');
  console.log('- addActivityRow:', !!addActivityBtn);
  console.log('- addBudgetRow:', !!addBudgetBtn);
  console.log('- openMemberModal:', !!openMemberModalBtn);
  console.log('- openMemberModalMobile:', !!openMemberModalMobileBtn);
  
  if (addActivityBtn) {
    console.log('✅ Setting up add activity button event handler');
    addActivityBtn.addEventListener('click', () => {
      console.log('🎯 Add activity button clicked - staff version');
      // Desktop table view
      const desktopContainer = document.getElementById('activitiesContainer');
      if (desktopContainer) {
        const newRow = document.createElement('div');
        newRow.className = 'activity-row hover:bg-gray-50 transition-colors px-4 py-2';
        newRow.innerHTML = `
          <div class="flex items-center gap-4">
            <div class="w-12 flex-none">
              <input name="stage[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg text-sm" placeholder="e.g., Planning" required>
            </div>
            <div class="flex-1 px-2">
              <textarea name="activities[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm resize-none w-full" rows="2" placeholder="Describe specific activities..." required></textarea>
            </div>
            <div class="w-36 px-2 flex-none">
              <input name="timeframe[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg text-sm" placeholder="e.g., Week 1-2" required>
            </div>
            <div class="w-36 px-2 flex-none">
              <input type="date" name="implementation_date[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm w-full" required>
            </div>
            <div class="flex-1 px-2">
              <textarea name="point_person[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm resize-none w-full" rows="2" placeholder="Responsible person/s" required></textarea>
            </div>
            <div class="w-[120px] px-2">
              <select name="status[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm">
                <option>Planned</option>
                <option>Ongoing</option>
              </select>
            </div>
            <div class="w-[90px] px-2">
              <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium">Remove</button>
            </div>
          </div>
        `;
        desktopContainer.appendChild(newRow);
      }

      // Mobile card view
      const mobileContainer = document.getElementById('activitiesContainerMobile');
      if (mobileContainer) {
        const newCard = document.createElement('div');
        newCard.className = 'activity-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
        newCard.innerHTML = `
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
            <input name="stage[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Stage" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
            <textarea name="activities[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Specific Activities" required></textarea>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
            <input name="timeframe[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Time Frame" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Implementation Date <span class="text-red-500">*</span></label>
            <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
            <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Point Person/s" required></textarea>
          </div>
          <div class="flex flex-col sm:flex-row gap-2">
            <div class="space-y-1 flex-1">
              <label class="block text-xs font-medium text-gray-600">Status</label>
              <select name="status[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors">
                <option>Planned</option>
                <option>Ongoing</option>
              </select>
            </div>
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
          </div>
        `;
        mobileContainer.appendChild(newCard);
      }
      
      // Mark form as changed
      staff_markFormChanged('Activity row added', 'activities');
      console.log('Form marked as changed - activity added');
    });
  } else {
    console.error('Add activity button not found during initialization');
  }

  // Set up add budget button
  if (addBudgetBtn) {
    console.log('✅ Setting up add budget button event handler');
    addBudgetBtn.addEventListener('click', () => {
      console.log('🎯 Add budget button clicked - staff version');
      // Desktop table view
      const desktopContainer = document.getElementById('budgetContainer');
      if (desktopContainer) {
        const newRow = document.createElement('div');
        newRow.className = 'budget-row hover:bg-gray-50 transition-colors px-6 py-4';
        newRow.innerHTML = `
          <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
            <textarea name="budget_activity[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity..."></textarea>
            <textarea name="budget_resources[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed..."></textarea>
            <textarea name="budget_partners[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations..."></textarea>
            <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="₱ 0.00">
            <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
          </div>
        `;
        desktopContainer.appendChild(newRow);
      }

      // Mobile card view
      const mobileContainer = document.getElementById('budgetContainerMobile');
      if (mobileContainer) {
        const newCard = document.createElement('div');
        newCard.className = 'budget-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
        newCard.innerHTML = `
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Activity</label>
            <textarea name="budget_activity[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Activity"></textarea>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
            <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Resources"></textarea>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
            <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Partners"></textarea>
          </div>
          <div class="flex flex-col sm:flex-row gap-2">
            <div class="space-y-1 flex-1">
              <label class="block text-xs font-medium text-gray-600">Amount</label>
              <input type="text" name="budget_amount[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="₱ 0.00">
            </div>
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
          </div>
        `;
        mobileContainer.appendChild(newCard);
      }
      
      // Mark form as changed
      staff_markFormChanged('Budget row added', 'budgets');
      console.log('Form marked as changed - budget added');
    });
  } else {
    console.error('Add budget button not found during initialization');
  }

  // Set up member modal buttons
  if (openMemberModalBtn) {
    console.log('✅ Setting up open member modal button event handler');
    openMemberModalBtn.addEventListener('click', function() {
      console.log('🎯 Open member modal button clicked - staff version');
      staff_loadMemberList();
      const memberModalEl = document.getElementById('memberModal');
      if (memberModalEl) memberModalEl.classList.remove('hidden');
    });
  } else {
    console.error('❌ Open member modal button not found during staff initialization');
  }

  if (openMemberModalMobileBtn) {
    console.log('Setting up open member modal mobile button');
    openMemberModalMobileBtn.addEventListener('click', function() {
      console.log('Open member modal mobile button clicked');
      staff_loadMemberList();
      const memberModalEl = document.getElementById('memberModal');
      if (memberModalEl) memberModalEl.classList.remove('hidden');
    });
  } else {
    console.error('Open member modal mobile button not found during initialization');
  }

  // Attach remove buttons using event delegation
  staff_attachRemoveButtons();
}

/* --------------------
   Load member list from project's original section and component
   -------------------- */
function staff_loadMemberList() {
  const memberList = document.getElementById('memberList');
  if (!memberList) {
    console.error('Member list container `#memberList` not found in DOM. Aborting load.');
    return;
  }
  memberList.innerHTML = '<p class="text-center text-gray-500">Loading members...</p>';
  
  // Use the project's original component and section, not the form values
  // This ensures consistency and prevents staff from accidentally changing the filtering
  const projectComponent = @json($project->Project_Component);
  const projectSection = @json($project->Project_Section);
  
  if (!projectSection || !projectComponent) {
    memberList.innerHTML = '<p class="text-center text-red-500">Project component and section not found.</p>';
    return;
  }
 
  // Collect existing member IDs/emails directly from the current form DOM to ensure accuracy
  const existingMemberEmails = Array.from(document.querySelectorAll('input[name="member_email[]"]'))
    .map(i => (i.value || '').trim()).filter(v => v !== '');
  const existingMemberIds = Array.from(document.querySelectorAll('input[name="member_student_id[]"]'))
    .map(i => (i.value || '').trim()).filter(v => v !== '');

  // Debug: show what we collected from the DOM (more reliable than in-memory sets)
  console.debug('staff_loadMemberList -> DOM-collected existingMemberEmails=', existingMemberEmails);
  console.debug('staff_loadMemberList -> DOM-collected existingMemberIds=', existingMemberIds);
 
  // Fetch students from the same section and component, excluding existing members
  const url = new URL('{{ route("projects.students.for-staff") }}', window.location.origin);
  url.searchParams.append('section', projectSection);
  url.searchParams.append('component', projectComponent);
  existingMemberEmails.forEach(email => {
    url.searchParams.append('existing_members[]', email);
  });
  existingMemberIds.forEach(id => {
    url.searchParams.append('existing_member_ids[]', id);
  });

  // Debug: show constructed URL and exclusion lists
  try {
    console.debug('staff_loadMemberList -> URL:', url.toString());
    console.debug('staff_loadMemberList -> exclude emails:', existingMemberEmails);
    console.debug('staff_loadMemberList -> exclude ids:', existingMemberIds);
  } catch (e) {
    console.debug('staff_loadMemberList debug error', e);
  }
 
  fetch(url)
    .then(response => response.json())
    .then(students => {
      console.debug('staff_loadMemberList -> fetched students count=', students ? students.length : 0, students);
      if (!students || students.length === 0) {
        // Diagnostic fallback: query the same endpoint without exclusions to see if server-side filtering
        // (by email or id) is removing results. This helps debug why one remaining student doesn't appear.
        const baseUrl = new URL('{{ route("projects.students.for-staff") }}', window.location.origin);
        baseUrl.searchParams.append('section', projectSection);
        baseUrl.searchParams.append('component', projectComponent);
        console.debug('staff_loadMemberList -> primary fetch returned 0. Trying debug fetch without exclusions:', baseUrl.toString());

        fetch(baseUrl)
          .then(r => r.json())
          .then(allStudents => {
            console.debug('staff_loadMemberList -> debug fetch (no exclusions) count=', allStudents ? allStudents.length : 0, allStudents);
            if (allStudents && allStudents.length > 0) {
              // Build HTML showing that exclusions removed students and list them for inspection
              let dbgHtml = '<div class="p-3 bg-yellow-50 border border-yellow-200 rounded">';
              dbgHtml += `<p class="text-sm text-yellow-800">No available students after applying exclusions. ${allStudents.length} student(s) exist in this section/component but were excluded by your current member list or IDs.</p>`;
              dbgHtml += '<div class="space-y-2 mt-2">';
                // Filter out students that are already present in the form (by email or id)
                const filtered = allStudents.filter(s => {
                  const sid = s.id ? String(s.id) : '';
                  const sem = s.email ? String(s.email).trim() : '';
                  if (existingMemberIds.indexOf(sid) >= 0) return false;
                  if (sem && existingMemberEmails.indexOf(sem) >= 0) return false;
                  return true;
                });

                filtered.forEach(s => {
                  dbgHtml += `<div class="p-2 border rounded bg-white"><strong>${s.name}</strong> — ${s.email || '(no email)'} — ${s.contact_number || '(no contact)'}</div>`;
                });
                if (filtered.length === 0) {
                  dbgHtml += '<p class="text-sm text-gray-600 mt-2">All students in this section/component are already added to the project.</p>';
                }
              dbgHtml += '</div></div>';
              // Also show what was excluded
              dbgHtml += '<pre class="mt-2 text-xs text-gray-600 bg-gray-50 p-2 rounded">Excluded emails: ' + JSON.stringify(existingMemberEmails) + '\nExcluded ids: ' + JSON.stringify(existingMemberIds) + '</pre>';
              memberList.innerHTML = dbgHtml;
              return;
            }

            // If even the debug fetch returned no students, show friendly message
            memberList.innerHTML = `<p class="text-center text-gray-500">No students found in ${projectComponent} - ${projectSection}.</p>`;
          })
          .catch(err => {
            console.error('staff_loadMemberList -> debug fetch error', err);
            memberList.innerHTML = `<p class="text-center text-red-500">Error loading students (debug). Please check server logs.</p>`;
          });
        return;
      }
     
      let html = '';
      students.forEach(student => {
        html += `
          <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
            <div class="flex items-center">
              <input type="checkbox" id="member${student.id}" name="available_members[]" value="${student.id}" class="mr-2" data-name="${student.name}" data-email="${student.email}" data-contact="${student.contact_number}">
              <label for="member${student.id}" class="text-sm">
                <span class="font-medium">${student.name}</span> -
                <span class="text-gray-600">${student.email}</span>
                <span class="text-gray-500 text-xs block">${student.contact_number}</span>
              </label>
            </div>
            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Same Section</span>
          </div>
        `;
      });
     
      memberList.innerHTML = html;
    })
    .catch(error => {
      console.error('Error fetching students:', error);
      memberList.innerHTML = '<p class="text-center text-red-500">Error loading students. Please try again.</p>';
    });
}

  // Ensure the global reference points to the real implementation and clear any placeholder flag
  try {
    if (typeof window !== 'undefined') {
      window.staff_loadMemberList = staff_loadMemberList;
      if (window.staff_loadMemberList && window.staff_loadMemberList._isPlaceholder) delete window.staff_loadMemberList._isPlaceholder;
    }
  } catch (e) { console.warn('Could not export staff_loadMemberList to window scope', e); }

  // Member selection modal event handlers
const closeMemberModalBtn2 = document.getElementById('closeMemberModal');
if (closeMemberModalBtn2) {
  closeMemberModalBtn2.addEventListener('click', function(event) {
    event.preventDefault();
    const modalEl = document.getElementById('memberModal');
    if (modalEl) modalEl.classList.add('hidden');
  });
}

const cancelMemberSelectionBtn2 = document.getElementById('cancelMemberSelection');
if (cancelMemberSelectionBtn2) {
  cancelMemberSelectionBtn2.addEventListener('click', function(event) {
    event.preventDefault();
    const modalEl = document.getElementById('memberModal');
    if (modalEl) modalEl.classList.add('hidden');
    if (window.Swal) {
      Swal.fire({
        icon: 'info',
        title: 'Cancelled',
        text: 'Member selection has been cancelled.',
        timer: 1500,
        showConfirmButton: false
      });
    }
  });
}

// Add selected members to the form
const addSelectedMembersBtn2 = document.getElementById('addSelectedMembers');
if (addSelectedMembersBtn2) {
  addSelectedMembersBtn2.addEventListener('click', function(event) {
  event.preventDefault();
  const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
 
  if (selectedMembers.length === 0) {
    Swal.fire({
      icon: 'warning',
      title: 'No Members Selected',
      text: 'Please select at least one member to add to your team.',
      confirmButtonText: 'OK',
      confirmButtonColor: '#3b82f6'
    });
    return;
  }
 
  selectedMembers.forEach(checkbox => {
    const memberId = checkbox.value;
    const memberName = checkbox.dataset.name;
    const memberEmail = checkbox.dataset.email;
    const memberContact = checkbox.dataset.contact;
   
    // Add email and id to staff_addedMemberEmails/Ids sets to prevent duplicates
    if (memberEmail) staff_addedMemberEmails.add(memberEmail);
    if (memberId) staff_addedMemberIds.add(String(memberId));
   
    // Add to desktop table
    const desktopTable = document.querySelector('#memberTableBody');
    if (desktopTable) {
      const newRow = document.createElement('tr');
      newRow.className = 'hover:bg-gray-50 transition-colors member-row';
      newRow.innerHTML = `
        <td class="px-6 py-4">
          <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberName}" readonly>
          <input type="hidden" name="member_student_id[]" value="${memberId}">
        </td>
        <td class="px-6 py-4">
          <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required>
        </td>
        <td class="px-6 py-4">
          <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberEmail}" readonly>
        </td>
        <td class="px-6 py-4">
          <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${memberContact}" required>
        </td>
        <td class="px-6 py-4 text-center">
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
            Remove
          </button>
        </td>
      `;
      desktopTable.appendChild(newRow);
    }
   
    // Add to mobile view
    const mobileContainer = document.getElementById('memberContainer');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3 member-row';
      newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
          <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberName}" readonly>
          <input type="hidden" name="member_student_id[]" value="${memberId}">
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
          <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Member" required>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
          <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberEmail}" readonly>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
          <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" value="${memberContact}" required>
        </div>
        <div class="flex justify-end">
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
        </div>
      `;
      mobileContainer.appendChild(newCard);
    }
  });
 
  // Mark form as changed when members are added - MOVED TO IMMEDIATE EXECUTION
  staff_markFormChanged('Members added', 'members');
  console.log('Form marked as changed - members added');
 
  // Close modal
  const memberModalEl2 = document.getElementById('memberModal');
  if (memberModalEl2) memberModalEl2.classList.add('hidden');
  
  // Show success message
  Swal.fire({
    icon: 'success',
    title: 'Members Added Successfully!',
    text: `${selectedMembers.length} member(s) have been added to your team.`,
    timer: 2000,
    showConfirmButton: false
  });
});

// Auto-expand textarea
document.addEventListener("input", function (e) {
  if (e.target.classList.contains("auto-expand")) {
    e.target.style.height = "auto";
    e.target.style.height = e.target.scrollHeight + "px";
  }
});

// Function to mark form as changed (global scope)
function staff_markFormChanged(reason, fieldName) {
  if (!staff_formChanged) {
    staff_formChanged = true;
    console.log('🔥 FORM MARKED AS CHANGED:', reason, 'Field:', fieldName || 'unknown');
  }
}

/* --------------------
   MAIN INITIALIZATION - Single DOMContentLoaded handler
   -------------------- */
  console.log('Staff scripts included; initialization will run from parent DOMContentLoaded listener.');
  
  // First, let's find the form to make sure it exists
  console.log('🔍 Looking for project form...');
  const projectForm = document.getElementById('projectForm');
  if (!projectForm) {
    console.error('❌ projectForm not found! Checking all forms on page...');
    const allForms = document.querySelectorAll('form');
    allForms.forEach((form, index) => {
      console.log(`Form ${index}:`, {
        id: form.id,
        action: form.action,
        method: form.method,
        classes: form.className
      });
    });
  } else {
    console.log('✅ Project form found:', projectForm.id);
  }
  
  // Prevent multiple initializations and re-initialization during submission
  if (staff_dataPopulated || staff_isSubmitting) {
    console.log('Staff data already populated or submitting, skipping...');
    return;
  }
  staff_dataPopulated = true;
  
  // Debug: Check if required containers exist
  const memberTableBody = document.getElementById('memberTableBody');
  const activitiesContainer = document.getElementById('activitiesContainer');
  const budgetContainer = document.getElementById('budgetContainer');
  
  console.log('🔍 Container check:');
  console.log('- memberTableBody:', !!memberTableBody);
  console.log('- activitiesContainer:', !!activitiesContainer);
  console.log('- budgetContainer:', !!budgetContainer);
  
  // Initialize change tracking
  staff_initializeChangeTracking();
  
  // Initialize event handlers for buttons
  console.log('🔧 Setting up staff event handlers...');
  staff_initializeEventHandlers();
  
  // Set up form submission handler IMMEDIATELY after change tracking
  console.log('🔧 Setting up form submission handler...');
  staff_setupFormSubmissionHandler();
  
  // Immediately configure form to prevent validation issues
  // Reuse existing projectForm variable
  if (projectForm) {
    // Pre-emptively disable HTML5 validation to prevent "not focusable" errors
    projectForm.setAttribute('novalidate', 'novalidate');
    console.log('Pre-emptively disabled HTML5 validation on form');
    
    // Also add event listener to prevent any default form validation
    projectForm.addEventListener('invalid', function(e) {
      console.log('Prevented invalid event on:', e.target.name);
      e.preventDefault();
    }, true);
    
    projectForm.addEventListener('submit', function(e) {
      // Ensure no HTML5 validation interferes with our custom validation
      if (!projectForm.hasAttribute('novalidate')) {
        projectForm.setAttribute('novalidate', 'novalidate');
      }
    }, false);
  }
  
  // Test change detection immediately
  setTimeout(() => {
    console.log('=== TESTING CHANGE DETECTION ===');
    console.log('Simulating input change for testing...');
    
    // Find a text input to test with
    const testInput = document.querySelector('input[name="Project_Name"], textarea[name="Project_Problems"]');
    if (testInput) {
      console.log('Found test input:', testInput.name);
      
      // Simulate user input
      const originalValue = testInput.value;
      testInput.value = originalValue + ' test';
      
      // Trigger events
      testInput.dispatchEvent(new Event('input', { bubbles: true }));
      testInput.dispatchEvent(new Event('change', { bubbles: true }));
      
      // Check if change was detected
      setTimeout(() => {
        console.log('After test input - staff_formChanged:', staff_formChanged);
        
        // Restore original value
        testInput.value = originalValue;
        testInput.dispatchEvent(new Event('input', { bubbles: true }));
        testInput.dispatchEvent(new Event('change', { bubbles: true }));
        
        console.log('Change detection test complete. If staff_formChanged is still false, there may be an issue with event listeners.');
      }, 100);
    } else {
      console.log('No test input found for change detection test');
    }
  }, 1000);
  
  // Add immediate test to verify everything is set up correctly
  setTimeout(() => {
    console.log('🧪 RUNNING SETUP VERIFICATION TEST');
    const form = document.getElementById('projectForm');
    const submitBtns = document.querySelectorAll('button[type="submit"], input[type="submit"]');
    
    console.log('Form exists:', !!form);
    console.log('Submit buttons found:', submitBtns.length);
    console.log('staff_formChanged flag:', staff_formChanged);
    console.log('Initial data captured:', Object.keys(staff_initialFormData).length > 0);
    
    if (!form) {
      console.error('🔴 CRITICAL: Form not found! Check if form ID is correct.');
    }
    
    if (submitBtns.length === 0) {
      console.error('🔴 CRITICAL: No submit buttons found!');
    }
    
    console.log('Setup verification complete. Ready for testing.');
    console.log('💡 To test: Make a change to any field, then click submit button.');
    console.log('💡 Emergency: Run staff_emergencySubmit() in console to force submit.');
    console.log('💡 Debug: Run debugChangeDetection() to see change detection state.');
    console.log('💡 Reset: Run staff_manualResetTracking() to manually reset change detection.');
    console.log('💡 Structure: Run staff_debugFormStructure() to see form structure and validation state.');
    
    // Mark data loading as complete
    staff_dataLoadingComplete = true;
    console.log('📝 Staff data loading marked as complete');
  }, 2000);

  // Clear default activity rows ONLY if they are empty (not pre-populated by server)
  const desktopActivityContainer = document.getElementById('activitiesContainer');
  const mobileActivityContainer = document.getElementById('activitiesContainerMobile');
  
  console.log('🔍 Staff Activity Container Check:');
  console.log('- Desktop container found:', !!desktopActivityContainer);
  console.log('- Mobile container found:', !!mobileActivityContainer);
  
  // Check if activities are already populated by server-side rendering
  const existingDesktopActivities = desktopActivityContainer ? desktopActivityContainer.children.length : 0;
  const existingMobileActivities = mobileActivityContainer ? mobileActivityContainer.children.length : 0;
  
  console.log('📊 Staff Activity Population Check:');
  console.log('- Existing desktop activities:', existingDesktopActivities);
  console.log('- Existing mobile activities:', existingMobileActivities);
  
  // Only clear if containers are empty (meaning this is a fresh form, not edit form)
  if (desktopActivityContainer && existingDesktopActivities === 0) {
      console.log('Clearing empty desktop activity container');
      desktopActivityContainer.innerHTML = '';
  }
  if (mobileActivityContainer && existingMobileActivities === 0) {
      console.log('Clearing empty mobile activity container');
      mobileActivityContainer.innerHTML = '';
  }
  
  // Clear default budget rows ONLY if they are empty (not pre-populated by server)
  const desktopBudgetContainer = document.getElementById('budgetContainer');
  const mobileBudgetContainer = document.getElementById('budgetContainerMobile');
  
  const existingDesktopBudgets = desktopBudgetContainer ? desktopBudgetContainer.children.length : 0;
  const existingMobileBudgets = mobileBudgetContainer ? mobileBudgetContainer.children.length : 0;
  
  console.log('Existing budgets - Desktop:', existingDesktopBudgets, 'Mobile:', existingMobileBudgets);
  
  if (desktopBudgetContainer && existingDesktopBudgets === 0) {
      console.log('Clearing empty desktop budget container');
      desktopBudgetContainer.innerHTML = '';
  }
  if (mobileBudgetContainer && existingMobileBudgets === 0) {
      console.log('Clearing empty mobile budget container');
      mobileBudgetContainer.innerHTML = '';
  }
  
  // Add existing activities (only once and only if not already present)
  if (!staff_activitiesInitialized && desktopActivityContainer) {
    console.log('🏁 Starting staff activities initialization...');
    const currentActivityCount = desktopActivityContainer.children.length;
    console.log('Current activity count before initialization:', currentActivityCount);
    
    if (currentActivityCount === 0) {
      console.log('📋 No existing activities found, initializing from project data...');
      staff_activitiesInitialized = true;
      @if(isset($project->activities) && $project->activities->count() > 0)
        console.log('📊 Found {{ $project->activities->count() }} activities from project data');
        @foreach($project->activities as $activity)
          console.log('Adding activity: {{ addslashes($activity->Stage) }}');
          staff_addActivityRow('{{ addslashes($activity->Stage) }}', '{{ addslashes($activity->Specific_Activity) }}', '{{ addslashes($activity->Time_Frame) }}', '{{ $activity->Implementation_Date ? $activity->Implementation_Date->format('Y-m-d') : '' }}', '{{ addslashes($activity->Point_Persons) }}', '{{ addslashes($activity->status) }}', false, '{{ addslashes($activity->Activity_ID ?? $activity->id ?? '') }}');
        @endforeach
      @else
        // Add one blank activity row
        console.log('Adding blank activity row...');
        staff_addBlankActivityRow();
      @endif
      
      // Verify activities were added
      setTimeout(() => {
        const activityCount = document.querySelectorAll('.activity-row').length;
        console.log('Activity rows after initialization:', activityCount);
        if (activityCount === 0) {
          console.warn('No activity rows found after initialization, adding emergency blank row');
          staff_addBlankActivityRow();
          
          // Double-check after adding
          setTimeout(() => {
            const finalActivityCount = document.querySelectorAll('.activity-row').length;
            console.log('Final activity count after emergency add:', finalActivityCount);
            if (finalActivityCount === 0) {
              console.error('CRITICAL: Still no activity rows after emergency add!');
            }
          }, 100);
        }
      }, 200);
    } else {
      console.log('Activities already present (', currentActivityCount, '), skipping JavaScript initialization');
      staff_activitiesInitialized = true;
      
      // Just make sure existing activities have the correct CSS classes for our scripts
      const existingRows = desktopActivityContainer.children;
      for (let i = 0; i < existingRows.length; i++) {
        if (!existingRows[i].classList.contains('activity-row')) {
          existingRows[i].classList.add('activity-row');
          console.log('Added activity-row class to existing row', i);
        }
      }
    }
  }
  
  // Add existing budget items (only once and only if not already present)
  if (!staff_budgetsInitialized && desktopBudgetContainer) {
    const currentBudgetCount = desktopBudgetContainer.children.length;
    console.log('Current budget count before initialization:', currentBudgetCount);
    
    if (currentBudgetCount === 0) {
      console.log('No existing budgets found, initializing...');
      staff_budgetsInitialized = true;
      @if(isset($project->budgets) && $project->budgets->count() > 0)
        @foreach($project->budgets as $budget)
          staff_addBudgetRow('{{ addslashes($budget->Specific_Activity ?? '') }}', '{{ addslashes($budget->Resources_Needed ?? '') }}', '{{ addslashes($budget->Partner_Agencies ?? '') }}', '{{ addslashes($budget->Amount ?? '') }}', false, '{{ addslashes($budget->Budget_ID ?? $budget->id ?? '') }}');
        @endforeach
      @else
        // Add one blank budget row if no budget items exist
        console.log('Adding blank budget row...');
        staff_addBlankBudgetRow();
      @endif
      
      // Verify budgets were added
      setTimeout(() => {
        const budgetCount = document.querySelectorAll('.budget-row').length;
        console.log('Budget rows after initialization:', budgetCount);
        if (budgetCount === 0) {
          console.warn('No budget rows found after initialization, adding emergency blank row');
          staff_addBlankBudgetRow();
          
          // Double-check after adding
          setTimeout(() => {
            const finalBudgetCount = document.querySelectorAll('.budget-row').length;
            console.log('Final budget count after emergency add:', finalBudgetCount);
            if (finalBudgetCount === 0) {
              console.error('CRITICAL: Still no budget rows after emergency add!');
            }
          }, 100);
        }
      }, 200);
    } else {
      console.log('Budgets already present (', currentBudgetCount, '), skipping JavaScript initialization');
      staff_budgetsInitialized = true;
      
      // Just make sure existing budgets have the correct CSS classes for our scripts
      const existingRows = desktopBudgetContainer.children;
      for (let i = 0; i < existingRows.length; i++) {
        if (!existingRows[i].classList.contains('budget-row')) {
          existingRows[i].classList.add('budget-row');
          console.log('Added budget-row class to existing row', i);
        }
      }
    }
  }
  
  // Populate existing team members (only once)
  if (!staff_membersInitialized) {
    console.log('Initializing members...');
    staff_membersInitialized = true;
    
    // Clear existing member rows first to prevent duplicates
    const memberTableBody = document.getElementById('memberTableBody');
    const memberContainer = document.getElementById('memberContainer');
    if (memberTableBody) {
      memberTableBody.innerHTML = '';
      console.log('Cleared desktop member table');
    }
    if (memberContainer) {
      memberContainer.innerHTML = '';
      console.log('Cleared mobile member container');
    }
    
    // Reset the added member emails set
    staff_addedMemberEmails.clear();
    console.log('Reset member emails tracking set');
    
    // Populate existing team members
    @if(isset($project->student_ids) && $project->student_ids)
      const studentIds = {!! json_encode($project->student_ids) !!};
      if (Array.isArray(studentIds) && studentIds.length > 0) {
        // Add all members
        studentIds.forEach((studentId, index) => {
          const isOwner = studentId == {{ $project->student_id }}; // Check if this is the project owner
          staff_addMemberRowPlaceholder(studentId, isOwner);
        });
      }
    @else
      // If no student IDs, add the current project owner
      staff_addMemberRowPlaceholder({{ $project->student_id }}, true);
    @endif
    
    // Populate member details after a small delay
    setTimeout(staff_populateMemberDetails, 100);
  }

  // Final coordination - set up all event handlers and ensure change tracking is properly initialized
  setTimeout(() => {
    console.log('🎯 Final coordination phase...');
    staff_initializeChangeTracking();
    staff_setupFormSubmissionHandler();
    staff_attachRemoveButtons();
    
    // Enhanced validation check to ensure all sections loaded correctly
    const verifyDataLoading = () => {
      console.log('🔍 Verifying data loading...');
      const memberCount = document.querySelectorAll('.member-row, .member-card').length;
      const activityCount = document.querySelectorAll('.activity-row').length;
      const budgetCount = document.querySelectorAll('.budget-row').length;
      
      console.log('Final counts - Members:', memberCount, 'Activities:', activityCount, 'Budgets:', budgetCount);
      
      // Verify members have data
      const memberNames = Array.from(document.querySelectorAll('input[name="member_name[]"]')).map(input => input.value.trim()).filter(Boolean);
      const memberEmails = Array.from(document.querySelectorAll('input[name="member_email[]"]')).map(input => input.value.trim()).filter(Boolean);
      console.log('Member names populated:', memberNames.length);
      console.log('Member emails populated:', memberEmails.length);
      
      // Verify activities have data 
      const activityStages = Array.from(document.querySelectorAll('input[name="stage[]"]')).map(input => input.value.trim()).filter(Boolean);
      const activityDescs = Array.from(document.querySelectorAll('textarea[name="activities[]"]')).map(input => input.value.trim()).filter(Boolean);
      console.log('Activity stages populated:', activityStages.length);
      console.log('Activity descriptions populated:', activityDescs.length);
      
      // Verify budgets have data
      const budgetActivities = Array.from(document.querySelectorAll('textarea[name="budget_activity[]"]')).map(input => input.value.trim()).filter(Boolean);
      const budgetResources = Array.from(document.querySelectorAll('textarea[name="budget_resources[]"]')).map(input => input.value.trim()).filter(Boolean);
      console.log('Budget activities populated:', budgetActivities.length);
      console.log('Budget resources populated:', budgetResources.length);
      
      // Log any issues
      if (memberCount === 0) console.error('❌ No member rows found!');
      if (activityCount === 0) console.error('❌ No activity rows found!');
      if (budgetCount === 0) console.warn('⚠️ No budget rows found (this may be intentional)');
      
      if (memberNames.length === 0 && memberCount > 0) console.warn('⚠️ Member rows exist but no names populated');
      if (activityStages.length === 0 && activityCount > 0) console.warn('⚠️ Activity rows exist but no stages populated');
      
      console.log('✅ Data loading verification complete');
    };
    
    // Run verification
    verifyDataLoading();
    
    // Final reset of change tracking after everything is loaded
    setTimeout(() => {
      if (staff_dataLoadingComplete) {
        resetChangeTracking('final coordination after all data loaded');
        console.log('✅ All initialization complete with final change tracking reset!');
        
        // Run one final verification after reset
        setTimeout(verifyDataLoading, 200);
      }
    }, 1500);
  }, 500);

// Function to add an activity row with existing data
function staff_addActivityRow(stage, specificActivity, timeframe, implementationDate, pointPerson, status, addToMobile = true, activityId = '') {
  // Desktop table view
  const desktopContainer = document.getElementById('activitiesContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'activity-row hover:bg-gray-50 transition-colors px-4 py-2';
    newRow.innerHTML = `
      <div class="flex items-center gap-4">
        <div class="w-12 flex-none">
          <input name="stage[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg text-sm" placeholder="e.g., Planning" required value="${stage || ''}">
          <input type="hidden" name="activity_id[]" value="${activityId || ''}">
        </div>
        <div class="flex-1 px-2">
          <textarea name="activities[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm resize-none w-full" rows="2" placeholder="Describe specific activities..." required>${specificActivity || ''}</textarea>
        </div>
        <div class="w-36 px-2 flex-none">
          <input name="timeframe[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg text-sm" placeholder="e.g., Week 1-2" required value="${timeframe || ''}">
        </div>
        <div class="w-36 px-2 flex-none">
          <input type="date" name="implementation_date[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm w-full" required value="${implementationDate || ''}">
        </div>
        <div class="flex-1 px-2">
          <textarea name="point_person[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm resize-none w-full" rows="2" placeholder="Responsible person/s" required>${pointPerson || ''}</textarea>
        </div>
        <div class="w-[120px] px-2">
          <select name="status[]" class="px-3 py-2 border-2 border-gray-400 rounded-lg text-sm">
            <option ${status === 'Planned' ? 'selected' : ''}>Planned</option>
            <option ${status === 'Ongoing' ? 'selected' : ''}>Ongoing</option>
          </select>
        </div>
        <div class="w-[90px] px-2">
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm font-medium">Remove</button>
        </div>
      </div>
    `;
    desktopContainer.appendChild(newRow);
  }

  // Mobile card view - only add if explicitly requested (for manual additions)
  if (addToMobile) {
    const mobileContainer = document.getElementById('activitiesContainerMobile');
    if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'activity-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
    newCard.innerHTML = `
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Stage <span class="text-red-500">*</span></label>
          <input name="stage[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Stage" value="${stage || ''}" required>
          <input type="hidden" name="activity_id[]" value="${activityId || ''}">
        </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Specific Activities <span class="text-red-500">*</span></label>
        <textarea name="activities[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Specific Activities" required>${specificActivity || ''}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Time Frame <span class="text-red-500">*</span></label>
        <input name="timeframe[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Time Frame" value="${timeframe || ''}" required>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Implementation Date <span class="text-red-500">*</span></label>
        <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${implementationDate || ''}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
        <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Point Person/s" required>${pointPerson || ''}</textarea>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <div class="space-y-1 flex-1">
          <label class="block text-xs font-medium text-gray-600">Status</label>
          <select name="status[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors">
            <option ${status === 'Planned' ? 'selected' : ''}>Planned</option>
            <option ${status === 'Ongoing' ? 'selected' : ''}>Ongoing</option>
          </select>
        </div>
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
  }
}

// Function to add a blank activity row
function staff_addBlankActivityRow() {
  staff_addActivityRow('', '', '', '', '', 'Planned');
}

// Function to add a budget row with existing data
function staff_addBudgetRow(activity, resources, partners, amount, addToMobile = true) {
  // Format amount for display
  let displayAmount = '';
  if (amount !== null && amount !== undefined && amount !== '') {
    // If amount is already a number, format it properly
    if (!isNaN(amount)) {
      displayAmount = parseFloat(amount).toFixed(2);
    } else {
      displayAmount = amount;
    }
  }
  
  // Desktop table view
  const desktopContainer = document.getElementById('budgetContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'budget-row hover:bg-gray-50 transition-colors px-6 py-4';
    newRow.innerHTML = `
      <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
        <textarea name="budget_activity[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity...">${activity || ''}</textarea>
        <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
        <textarea name="budget_resources[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed...">${resources || ''}</textarea>
        <textarea name="budget_partners[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations...">${partners || ''}</textarea>
        <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="0.00" value="${displayAmount}">
        <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
      </div>
    `;
    desktopContainer.appendChild(newRow);
  }

  // Mobile card view - only add if explicitly requested (for manual additions)
  if (addToMobile) {
    const mobileContainer = document.getElementById('budgetContainerMobile');
    if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'budget-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Activity</label>
        <textarea name="budget_activity[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Activity">${activity || ''}</textarea>
        <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Resources Needed</label>
        <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Resources Needed">${resources || ''}</textarea>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
        <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Partner Agencies">${partners || ''}</textarea>
      </div>
      <div class="flex flex-col sm:flex-row gap-2">
        <div class="space-y-1 flex-1">
          <label class="block text-xs font-medium text-gray-600">Amount</label>
          <input type="text" name="budget_amount[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="0.00" value="${displayAmount}">
        </div>
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
    }
  }
}

// Function to add a blank budget row
function staff_addBlankBudgetRow() {
  staff_addBudgetRow('', '', '', '');
}

// Function to add a member row placeholder
function staff_addMemberRowPlaceholder(studentId, isOwner = false) {
  // Add to desktop table
  const desktopTable = document.querySelector('#memberTableBody');
  if (desktopTable) {
    const newRow = document.createElement('tr');
    newRow.className = 'hover:bg-gray-50 transition-colors member-row';
    newRow.innerHTML = `
      <td class="px-6 py-4">
        <input name="member_name[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Enter full name" ${isOwner ? 'readonly' : 'required'}>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </td>
      <td class="px-6 py-4">
        <input name="member_role[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" ${isOwner ? 'required' : ''}>
      </td>
      <td class="px-6 py-4">
        <input type="email" name="member_email[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" ${isOwner ? 'readonly' : 'required'}>
      </td>
      <td class="px-6 py-4">
        <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" ${isOwner ? 'readonly' : 'required'}>
      </td>
      <td class="px-6 py-4 text-center">
        <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
          Remove
        </button>
      </td>
    `;
    desktopTable.appendChild(newRow);
  }
  
  // Add to mobile view
  const mobileContainer = document.getElementById('memberContainer');
  if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3 member-row';
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
        <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="Enter full name" ${isOwner ? 'readonly' : 'required'}>
        <input type="hidden" name="member_student_id[]" value="${studentId}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
        <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="e.g., Project Leader" ${isOwner ? 'required' : ''}>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
        <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="co230123@adzu.edu.ph" ${isOwner ? 'readonly' : 'required'}>
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
        <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="09XX XXX XXXX" ${isOwner ? 'readonly' : 'required'}>
      </div>
      <div class="flex justify-end">
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
  }
}



// Function to populate member details
function staff_populateMemberDetails() {
  console.log('Populating member details...');
  
  const memberStudentIds = [];
  document.querySelectorAll('input[name="member_student_id[]"]').forEach(input => {
    if (input.value) {
      memberStudentIds.push(parseInt(input.value));
    }
  });
  
  if (memberStudentIds.length === 0) {
    console.log('No member student IDs found, returning');
    return;
  }
  
  // First try to populate with existing project member data
  @if(isset($project->student_ids) && $project->student_ids)
    const projectStudentIds = {!! json_encode($project->student_ids) !!};
    const projectMemberRoles = {!! json_encode($project->member_roles ?? []) !!};
    
    console.log('Project student IDs:', projectStudentIds);
    console.log('Project member roles:', projectMemberRoles);
    
    // Update member details from existing project data
    document.querySelectorAll('input[name="member_student_id[]"]').forEach((input, index) => {
      const studentId = input.value;
      const memberRow = input.closest('.member-row, tr, .member-card');
      
      if (memberRow && projectStudentIds.includes(parseInt(studentId))) {
        // Update role from stored data
        const roleInput = memberRow.querySelector('input[name="member_role[]"]');
        if (roleInput && projectMemberRoles[studentId]) {
          roleInput.value = projectMemberRoles[studentId];
          console.log('Set role for student', studentId, ':', projectMemberRoles[studentId]);
        }
      }
    });
  @endif
  
  // Fetch member details from API to get names, emails, contacts
  fetch('{{ route("api.students.details-staff") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({ student_ids: memberStudentIds })
  })
  .then(response => {
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(students => {
    console.log('Fetched student details:', students);
    
    document.querySelectorAll('input[name="member_student_id[]"]').forEach(input => {
      const studentId = parseInt(input.value);
      const student = students.find(s => s.id === studentId);
      const memberRow = input.closest('.member-row, tr, .member-card');
      
      if (student && memberRow) {
        console.log('Updating member row for student:', student.name);
        
        // Add email to our tracking set
        if (student.email) {
          staff_addedMemberEmails.add(student.email);
        }
        // Also track student id to prevent accidental duplicates when email differs
        if (student.id) {
          staff_addedMemberIds.add(String(student.id));
        }
        
        // Update form inputs
        const nameInput = memberRow.querySelector('input[name="member_name[]"]');
        if (nameInput) nameInput.value = student.name || '';
        
        const emailInput = memberRow.querySelector('input[name="member_email[]"]');
        if (emailInput) emailInput.value = student.email || '';
        
        const contactInput = memberRow.querySelector('input[name="member_contact[]"]');
        if (contactInput) contactInput.value = student.contact_number || '';
      }
    });
    
    // Reinitialize change tracking after populating member details
    setTimeout(() => {
      resetChangeTracking('after member population');
    }, 100);
  })
  .catch(error => {
    console.error('Error fetching student details:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error Loading Member Details',
      text: 'Could not load member information. Please refresh the page and try again.',
      confirmButtonColor: '#3085d6'
    });
  });
}

// Function to serialize form data for comparison
function staff_getFormData() {
  const form = document.getElementById('projectForm');
  if (!form) return {};
  
  const formData = new FormData(form);
  const data = {};
  
  for (let [key, value] of formData.entries()) {
    if (data[key]) {
      // Handle multiple values for the same key (like arrays)
      if (Array.isArray(data[key])) {
        data[key].push(value);
      } else {
        data[key] = [data[key], value];
      }
    } else {
      data[key] = value;
    }
  }
  
  return data;
}

// Ensure activity_id[] and budget_id[] arrays exactly match visible rows
function staff_syncHiddenIds(formEl) {
  try {
    const form = formEl || document.getElementById('projectForm');
    if (!form) return;

    // Helper to sync a named id array based on visible rows
    function syncIds(rowSelector, hiddenName) {
      const visibleRows = Array.from(document.querySelectorAll(rowSelector)).filter(r => r && r.offsetParent !== null);
      const ids = visibleRows.map(r => {
        const h = r.querySelector(`input[name="${hiddenName}[]"]`);
        return h ? (h.value || '') : '';
      });

      // Remove any existing hidden inputs with this name on the form
      Array.from(form.querySelectorAll(`input[name="${hiddenName}[]"]`)).forEach(el => el.remove());

      // Recreate hidden inputs in the same order as visible rows
      ids.forEach(val => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = `${hiddenName}[]`;
        inp.value = val || '';
        form.appendChild(inp);
      });
    }

    // Sync activity and budget ids
    syncIds('.activity-row', 'activity_id');
    syncIds('.budget-row', 'budget_id');
    console.debug('staff_syncHiddenIds: synced activity_id[] and budget_id[] to visible rows');
  } catch (e) {
    console.warn('staff_syncHiddenIds error', e);
  }
}

// Function to check if form has changed
function staff_hasFormChanged() {
  const currentData = staff_getFormData();
  return JSON.stringify(currentData) !== JSON.stringify(staff_initialFormData);
}

// Function to properly reset change tracking after all data is loaded
function resetChangeTracking(reason = 'manual') {
  console.log('🔄 RESETTING CHANGE TRACKING:', reason);
  
  const currentData = staff_getFormData();
  const dataKeys = Object.keys(currentData);
  
  console.log('Capturing new initial data with', dataKeys.length, 'keys');
  console.log('Sample data keys:', dataKeys.slice(0, 10));
  
  // Log some sample form values for debugging
  const projectName = document.querySelector('input[name="project_name"]')?.value || 'N/A';
  const memberCount = document.querySelectorAll('input[name="member_name[]"]').length;
  const activityCount = document.querySelectorAll('input[name="stage[]"]').length;
  
  console.log('Form state - Project:', projectName, 'Members:', memberCount, 'Activities:', activityCount);
  
  staff_initialFormData = currentData;
  formChanged = false;
  changeTrackingInitialized = true;
  
  console.log('✅ Change tracking reset complete');
}

// Function to setup form submission handler
function staff_setupFormSubmissionHandler() {
  console.log('🔧 Setting up staff form submission handler...');
  
  const form = document.getElementById('projectForm');
  if (form) {
    console.log('✅ Project form found, adding submit listener');
    
    form.addEventListener('submit', function(e) {
      console.log('🚀 FORM SUBMIT EVENT TRIGGERED!');
      // If submission is already in progress (triggered by the review modal confirm),
      // return early WITHOUT calling preventDefault so the native submission can proceed.
      if (staff_isSubmitting) {
        console.log('⏳ Already submitting, allowing native submit to continue...');
        return; // Allow native submission to proceed
      }
      // Otherwise prevent default to run the review flow
      e.preventDefault();

      // Check if any changes were made (use formChanged flag or hasFormChanged function)
      console.log('=== FORM SUBMISSION ATTEMPT ===');
      console.log('Form submission check - staff_formChanged:', staff_formChanged, 'staff_hasFormChanged():', staff_hasFormChanged());
      console.log('staff_dataPopulated:', staff_dataPopulated, 'staff_isSubmitting:', staff_isSubmitting);
      
      // Get current form data for comparison
      const currentFormData = staff_getFormData();
      const currentFormString = JSON.stringify(currentFormData);
      const initialFormString = JSON.stringify(staff_initialFormData);
      const dataActuallyChanged = currentFormString !== initialFormString;
      
      console.log('Data comparison - initial vs current different:', dataActuallyChanged);
      console.log('Current form data keys:', Object.keys(currentFormData));
      console.log('Initial form data keys:', Object.keys(staff_initialFormData));
      console.log('Initial form data length:', Object.keys(staff_initialFormData).length);
      
      // If initial form data is empty, we can't compare properly - allow submission
      const initialDataExists = Object.keys(staff_initialFormData).length > 0;
      console.log('Initial data exists for comparison:', initialDataExists);
      
      // Detailed check for each condition
      console.log('=== SUBMISSION VALIDATION DEBUG ===');
      console.log('1. formChanged flag:', formChanged);
      console.log('2. staff_hasFormChanged() result:', staff_hasFormChanged());
      console.log('3. dataActuallyChanged:', dataActuallyChanged);
      console.log('4. initialDataExists:', initialDataExists);
      console.log('5. changeTrackingInitialized:', changeTrackingInitialized);
      
      // If change tracking wasn't properly initialized, show warning
      if (!changeTrackingInitialized) {
        console.log('⚠️ WARNING: Change tracking not properly initialized!');
      }
      
      // More lenient check - allow submission if:
      // 1. formChanged flag is true, OR
      // 2. hasFormChanged() returns true, OR 
      // 3. data actually changed, OR
      // 4. we don't have initial data to compare against
      const shouldAllowSubmission = staff_formChanged || staff_hasFormChanged() || dataActuallyChanged || !initialDataExists;
      console.log('Final decision - Should allow submission:', shouldAllowSubmission);
      
      if (shouldAllowSubmission) {
        const reasons = [];
        if (staff_formChanged) reasons.push('staff_formChanged flag');
        if (staff_hasFormChanged()) reasons.push('staff_hasFormChanged() detected changes');
        if (dataActuallyChanged) reasons.push('data comparison shows changes');
        if (!initialDataExists) reasons.push('no initial data to compare');
        console.log('Allowing submission because of:', reasons.join(', '));
      }
      
      if (!shouldAllowSubmission) {
        console.log('BLOCKING submission - no changes detected');
        Swal.fire({
          icon: 'info',
          title: 'No changes were made yet',
          text: 'No changes were made yet. Make some edits before saving.',
          confirmButtonColor: '#3085d6'
        });
        return;
      }
      
      console.log('ALLOWING submission - changes detected');
      
      // First run our custom validation
      if (!validateFormRequirements()) {
        return;
      }
      
      // Disable HTML5 validation temporarily to prevent "not focusable" errors
      const form = document.getElementById('projectForm');
      if (form) {
        // Completely disable HTML5 validation
        form.setAttribute('novalidate', 'novalidate');
        console.log('Disabled HTML5 validation to prevent focusability errors');
        
        // More aggressive approach: temporarily remove ALL required attributes
        const allRequiredFields = form.querySelectorAll('[required]');
        const hiddenOrMissingRequired = [];
        
        console.log('Found', allRequiredFields.length, 'required fields, temporarily removing all required attributes');
        
        allRequiredFields.forEach((field, index) => {
          // Store original state for restoration
          hiddenOrMissingRequired.push({
            element: field,
            name: field.name || field.placeholder || `field-${index}`,
            wasRequired: true
          });
          // Remove required attribute from ALL fields to prevent validation conflicts
          field.removeAttribute('required');
        });
        
        console.log('Temporarily removed required attributes from', hiddenOrMissingRequired.length, 'fields');
        
        // Store them to restore later
        window.hiddenRequiredFields = hiddenOrMissingRequired;
      }
      
      // Show review modal before saving
      console.log('About to show review modal. staff_formChanged:', staff_formChanged, 'staff_hasFormChanged():', staff_hasFormChanged());
      
      // Restore form validation state after modal handling (optional restoration)
      if (window.hiddenRequiredFields && window.hiddenRequiredFields.length > 0) {
        console.log('Note: Could restore', window.hiddenRequiredFields.length, 'required fields, but keeping them disabled for submission');
        // Optional: Restore required attributes if needed for future validation
        // window.hiddenRequiredFields.forEach(fieldInfo => {
        //   if (fieldInfo.wasRequired && fieldInfo.element && fieldInfo.element.parentNode) {
        //     fieldInfo.element.setAttribute('required', '');
        //   }
        // });
        window.hiddenRequiredFields = null;
      }
      
      // Keep form validation disabled for submission
      // Use existing form variable instead of redeclaring
      if (form && form.hasAttribute('novalidate')) {
        // Keep HTML5 validation disabled for submission to prevent errors
        console.log('Keeping HTML5 validation disabled for clean submission');
      }
      
      // Debug: Log current member data before showing review
      const currentMemberNames = Array.from(document.querySelectorAll('input[name="member_name[]"]'))
        .filter(input => input.value && input.value.trim() && input.offsetParent !== null)
        .map(input => input.value.trim());
      console.log('Current members before review modal:', currentMemberNames);
      
      showReviewModal(form);
    });
  } else {
    console.error('❌ PROJECT FORM NOT FOUND! Cannot set up submission handler.');
    
    // Try to find any form on the page
    const allForms = document.querySelectorAll('form');
    console.log('All forms on page:', allForms.length);
    allForms.forEach((f, index) => {
      console.log(`Form ${index}:`, f.id || f.className || 'no id/class');
    });
  }
  
  // Also add a click listener to any submit buttons as backup
  setTimeout(() => {
    const submitButtons = document.querySelectorAll('button[type="submit"], input[type="submit"]');
    console.log('🔍 Found submit buttons:', submitButtons.length);
    
    submitButtons.forEach((btn, index) => {
      console.log(`Setting up click listener on submit button ${index}:`, btn.id || btn.className);
      btn.addEventListener('click', function(e) {
        console.log('🖱️ SUBMIT BUTTON CLICKED!', btn.id || btn.className);
        
        // Only proceed if form has changed
        if (!formChanged) {
          console.log('Form not changed, showing alert');
          e.preventDefault();
          Swal.fire({
            icon: 'info',
            title: 'No changes were made yet',
            text: 'No changes were made yet. Make some edits before saving.',
            confirmButtonColor: '#3085d6'
          });
          return;
        }
        
        console.log('Form changed detected, allowing submission');
      });
    });
  }, 1000);

  // Additionally, attach handlers to any non-submit buttons that function as a "Save Changes" trigger
  setTimeout(() => {
    try {
      const form = document.getElementById('projectForm');
      if (!form) return;

      const saveSelectors = [
        '#saveChanges',
        '.save-changes',
        'button[data-action="save-changes"]',
        'button[name="save_changes"]',
        'button[data-role="save"]'
      ];
      const saveButtons = document.querySelectorAll(saveSelectors.join(','));
      console.log('🔍 Found save-style buttons:', saveButtons.length);
      saveButtons.forEach((btn, idx) => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          console.log('Save-style button clicked, dispatching form submit (backup) -', btn.id || btn.className);
          // Dispatch a cancellable submit event so our submit listener runs
          const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
          form.dispatchEvent(submitEvent);
        });
      });
    } catch (e) {
      console.error('Error attaching save-style button handlers', e);
    }
  }, 1200);
}

// Function to initialize change tracking
function staff_initializeChangeTracking() {
  console.log('=== INITIALIZING STAFF CHANGE TRACKING ===');
  
  // Capture initial form state AFTER data is populated
  setTimeout(() => {
    if (!staff_changeTrackingInitialized) {
      resetChangeTracking('initial setup');
    } else {
      console.log('⚠️ Change tracking already initialized, skipping initial capture');
    }
  }, 1200); // Increased timeout to ensure all data is loaded
  
  // Add change listeners to all form inputs using document-level event delegation
  // This ensures we catch events from dynamically added content
  
  
  // Listen for input events on the entire document with aggressive options
  document.addEventListener('input', function(e) {
    if (staff_isSubmitting) return; // Don't track changes during submission
    
    console.log('Input event detected on:', e.target.name || e.target.id || e.target.tagName);
    
    // More comprehensive check - look for form-related inputs
    if (e.target.name && (
        e.target.name.includes('Project_') || 
        e.target.name.includes('member_') || 
        e.target.name.includes('stage') || 
        e.target.name.includes('activities') || 
        e.target.name.includes('timeframe') || 
        e.target.name.includes('implementation_date') || 
        e.target.name.includes('point_person') || 
        e.target.name.includes('status') || 
        e.target.name.includes('budget_') || 
        e.target.name.includes('nstp_section')
    )) {
      staff_markFormChanged('Field name match', e.target.name);
      return;
    }
    
    // Check by form membership
    const form = document.getElementById('projectForm');
    if (form && form.contains(e.target)) {
      staff_markFormChanged('Form contains target', e.target.name || e.target.id);
      return;
    }
    
    // Fallback: Check if the event target is within the project form containers
    const containers = [
      document.getElementById('memberContainer'),
      document.getElementById('memberTableBody'),
      document.getElementById('activitiesContainer'),
      document.getElementById('activitiesContainerMobile'),
      document.getElementById('budgetContainer'),
      document.getElementById('budgetContainerMobile')
    ];
    
    for (let container of containers) {
      if (container && container.contains(e.target)) {
        staff_markFormChanged('Container match', e.target.name || e.target.id);
        return;
      }
    }
  }, { passive: false, capture: true });
  
  // Listen for change events on the entire document
  document.addEventListener('change', function(e) {
    if (staff_isSubmitting) return; // Don't track changes during submission
    
    console.log('Change event detected on:', e.target.name || e.target.id || e.target.tagName);
    
    // Same logic as input event
    if (e.target.name && (
        e.target.name.includes('Project_') || 
        e.target.name.includes('member_') || 
        e.target.name.includes('stage') || 
        e.target.name.includes('activities') || 
        e.target.name.includes('timeframe') || 
        e.target.name.includes('implementation_date') || 
        e.target.name.includes('point_person') || 
        e.target.name.includes('status') || 
        e.target.name.includes('budget_') || 
        e.target.name.includes('nstp_section')
    )) {
      staff_markFormChanged('Change field name match', e.target.name);
      return;
    }
    
    // Check by form membership
    const form = document.getElementById('projectForm');
    if (form && form.contains(e.target)) {
      staff_markFormChanged('Change form contains target', e.target.name || e.target.id);
      return;
    }
    
    // Container check
    const containers = [
      document.getElementById('memberContainer'),
      document.getElementById('memberTableBody'),
      document.getElementById('activitiesContainer'),
      document.getElementById('activitiesContainerMobile'),
      document.getElementById('budgetContainer'),
      document.getElementById('budgetContainerMobile')
    ];
    
    for (let container of containers) {
      if (container && container.contains(e.target)) {
        staff_markFormChanged('Change container match', e.target.name || e.target.id);
        return;
      }
    }
  }, { passive: false, capture: true });
  
  // Also listen for when new rows are added
  document.addEventListener('click', function(e) {
    if (staff_isSubmitting) return;
    if (e.target.id === 'addActivityRow' || e.target.id === 'addBudgetRow' || 
        e.target.id === 'addSelectedMembers') {
      setTimeout(() => {
        staff_markFormChanged('Add row button clicked', e.target.id);
      }, 100);
    }
  });
  
  console.log('Change tracking initialized with improved event delegation and field name detection');
}

// Form validation and submission is now handled in main DOMContentLoaded initialization

// Review modal function for staff edit
function showReviewModal(form) {
  // Collect form data for review
  const projectName = document.querySelector('input[name="Project_Name"]').value || 'Not specified';
  const teamName = document.querySelector('input[name="Project_Team_Name"]').value || 'Not specified';
  const component = document.querySelector('select[name="Project_Component"]').value || 'Not specified';
  const section = document.querySelector('select[name="nstp_section"]').value || 'Not specified';
  const problems = document.querySelector('textarea[name="Project_Problems"]').value || 'Not specified';
  const goals = document.querySelector('textarea[name="Project_Goals"]').value || 'Not specified';
  const targetCommunity = document.querySelector('textarea[name="Project_Target_Community"]').value || 'Not specified';
  const solution = document.querySelector('textarea[name="Project_Solution"]').value || 'Not specified';
  const outcomes = document.querySelector('textarea[name="Project_Expected_Outcomes"]').value || 'Not specified';
  
  // Collect member data - get all currently existing member rows (removed ones won't exist in DOM)
  const allMemberInputs = document.querySelectorAll('input[name="member_name[]"]');
  console.log('Found total member name inputs:', allMemberInputs.length);
  
  const memberNames = [];
  const memberRoles = [];
  
  allMemberInputs.forEach((nameInput, index) => {
    // Only include if the input has a value and is actually visible/accessible
    if (nameInput && nameInput.value && nameInput.value.trim() && 
        nameInput.offsetParent !== null) { // offsetParent is null if element is hidden
      
      // Find the corresponding role input in the same row/card
      const memberRow = nameInput.closest('tr, .member-card');
      const roleInput = memberRow ? memberRow.querySelector('input[name="member_role[]"]') : null;
      
      memberNames.push(nameInput.value.trim());
      memberRoles.push(roleInput && roleInput.value ? roleInput.value.trim() : 'Member');
      console.log('Added member to review:', nameInput.value.trim(), 'with role:', roleInput && roleInput.value ? roleInput.value.trim() : 'Member');
    }
  });
  
  let membersHTML = '';
  if (memberNames.length === 0) {
    membersHTML = '<div class="text-sm text-gray-500">No members found</div>';
  } else {
    memberNames.forEach((name, index) => {
      const role = memberRoles[index] || 'Member';
      membersHTML += `
        <div class="flex justify-between items-center bg-white p-2 rounded mb-1">
          <span class="font-medium">${name}</span>
          <span class="text-sm text-gray-600">${role}</span>
        </div>
      `;
    });
  }
  
  // Collect activities - only from existing, visible activity inputs
  const allStageInputs = document.querySelectorAll('input[name="stage[]"]');
  const allActivityInputs = document.querySelectorAll('textarea[name="activities[]"]');
  const allTimeframeInputs = document.querySelectorAll('input[name="timeframe[]"]');
  console.log('Found stage inputs:', allStageInputs.length, 'activity inputs:', allActivityInputs.length);
  
  let activitiesHTML = '';
  if (allStageInputs.length === 0) {
    activitiesHTML = '<div class="text-sm text-gray-500">No activities found</div>';
  } else {
    allStageInputs.forEach((stageInput, index) => {
      // Only include if the input has a value and is actually visible
      if (stageInput && stageInput.value && stageInput.value.trim() && stageInput.offsetParent !== null) {
        const stage = stageInput.value.trim();
        const activityInput = allActivityInputs[index];
        const timeframeInput = allTimeframeInputs[index];
        const activity = activityInput && activityInput.value ? activityInput.value.trim() : 'No description';
        const timeframe = timeframeInput && timeframeInput.value ? timeframeInput.value.trim() : 'No timeframe';
        
        activitiesHTML += `
          <div class="bg-white p-3 rounded mb-2">
            <div class="font-medium text-orange-700">${stage}</div>
            <div class="text-sm text-gray-600 mt-1">${activity.substring(0, 150)}${activity.length > 150 ? '...' : ''}</div>
            <div class="text-xs text-orange-600 mt-1">⏰ ${timeframe}</div>
          </div>
        `;
      }
    });
  }
  
  // Collect budget data - only from existing, visible budget inputs
  const allBudgetActivityInputs = document.querySelectorAll('textarea[name="budget_activity[]"]');
  const allBudgetAmountInputs = document.querySelectorAll('input[name="budget_amount[]"]');
  const allBudgetResourceInputs = document.querySelectorAll('textarea[name="budget_resources[]"]');
  console.log('Found budget activity inputs:', allBudgetActivityInputs.length, 'budget amount inputs:', allBudgetAmountInputs.length);
  
  let budgetHTML = '';
  if (allBudgetActivityInputs.length === 0) {
    budgetHTML = '<div class="text-sm text-gray-500">No budget items found</div>';
  } else {
    allBudgetActivityInputs.forEach((activityInput, index) => {
      // Only include if the input has a value and is actually visible
      if (activityInput && activityInput.value && activityInput.value.trim() && activityInput.offsetParent !== null) {
        const activity = activityInput.value.trim();
        const amountInput = allBudgetAmountInputs[index];
        const resourceInput = allBudgetResourceInputs[index];
        const amount = amountInput && amountInput.value ? amountInput.value.trim() : '0.00';
        const resources = resourceInput && resourceInput.value ? resourceInput.value.trim() : 'No resources specified';
        
        budgetHTML += `
          <div class="bg-white p-3 rounded mb-2">
            <div class="font-medium text-yellow-700">${activity}</div>
            <div class="text-sm text-gray-600 mt-1">💰 Amount: ₱${amount}</div>
            <div class="text-xs text-yellow-600 mt-1">📋 Resources: ${resources.substring(0, 100)}${resources.length > 100 ? '...' : ''}</div>
          </div>
        `;
      }
    });
  }
  
  // Show review modal
  Swal.fire({
    title: '<div class="text-2xl font-bold text-gray-800">📋 Review Project Changes</div>',
    html: `
      <div class="text-left space-y-4 max-h-[500px] overflow-y-auto px-3 py-2">
        <!-- Staff Notice -->
        <div class="bg-purple-50 rounded-lg p-3 border-l-4 border-purple-500">
          <div class="text-sm text-purple-700">
            <strong>Staff Edit Mode:</strong> You are saving changes as staff for this project.
          </div>
        </div>
        
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
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${problems.substring(0, 500)}${problems.length > 500 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Goal/Objectives</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${goals.substring(0, 500)}${goals.length > 500 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Target Community</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${targetCommunity.substring(0, 300)}${targetCommunity.length > 300 ? '...' : ''}</div>
            </div>
            <div class="bg-white p-3 rounded">
              <span class="text-xs text-gray-500 uppercase font-semibold block mb-1">Solutions/Activities to be implemented</span>
              <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${solution.substring(0, 500)}${solution.length > 500 ? '...' : ''}</div>
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
            <span class="text-xs bg-orange-200 text-orange-800 px-2 py-1 rounded-full">${Array.from(allStageInputs).filter(input => input.value && input.value.trim() && input.offsetParent !== null).length} activities</span>
          </h3>
          ${activitiesHTML}
        </div>
        
        <!-- Budget -->
        <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
          <h3 class="font-bold text-yellow-700 mb-3 text-lg flex items-center gap-2">
            <span>💰</span> Budget Items
            <span class="text-xs bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full">${Array.from(allBudgetActivityInputs).filter(input => input.value && input.value.trim() && input.offsetParent !== null).length} items</span>
          </h3>
          ${budgetHTML}
        </div>
      </div>
    `,
    width: '700px',
    showCancelButton: true,
    confirmButtonColor: '#7c3aed',
    cancelButtonColor: '#6b7280',
    confirmButtonText: '✓ Save Changes',
    cancelButtonText: '✕ Cancel',
    reverseButtons: true,
    customClass: {
      container: 'review-modal',
      popup: 'rounded-2xl',
      confirmButton: 'font-bold px-6 py-3 rounded-lg',
      cancelButton: 'font-bold px-6 py-3 rounded-lg'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // User confirmed, proceed with submission
      staff_isSubmitting = true;
      
      // Show loading state
      Swal.fire({
        title: 'Saving Changes...',
        text: 'Please wait while we save your project changes.',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
          Swal.showLoading();
        }
      });
      
      // Submit the form
      try { staff_syncHiddenIds(form); } catch (e) { console.warn('staff_syncHiddenIds failed', e); }
      form.submit();
    } else {
      staff_isSubmitting = false;
    }
  });
}

// Validate minimum requirements for form submission
function validateFormRequirements() {
  console.log('🔍 Starting form validation...');
  const form = document.getElementById('projectForm');
  if (!form) {
    console.error('Form not found!');
    return false;
  }
  
  // First, ensure we have the minimum required rows
  const activityRows = document.querySelectorAll('.activity-row');
  const budgetRows = document.querySelectorAll('.budget-row');
  const memberRows = document.querySelectorAll('.member-row, .member-card');
  
  console.log('Pre-validation count - Activities:', activityRows.length, 'Budgets:', budgetRows.length, 'Members:', memberRows.length);
  
  // Ensure at least one activity row exists
  if (activityRows.length === 0) {
    console.warn('No activity rows found, creating one...');
    staff_addBlankActivityRow();
    
    // Wait a moment and check again
    setTimeout(() => {
      const newActivityRows = document.querySelectorAll('.activity-row');
      if (newActivityRows.length === 0) {
        console.error('Failed to create activity row!');
        Swal.fire({
          icon: 'error',
          title: 'System Error',
          text: 'Unable to create activity section. Please refresh the page and try again.',
          confirmButtonColor: '#3085d6'
        });
        return false;
      }
    }, 50);
  }
  
  // Ensure at least one budget row exists (even if empty)
  if (budgetRows.length === 0) {
    console.warn('No budget rows found, creating one...');
    staff_addBlankBudgetRow();
  }
  
  // Check all required fields are filled, but only those that are visible and accessible
  const requiredFields = Array.from(form.querySelectorAll('[required]')).filter(field => {
    // Only validate fields that are actually visible and accessible
    return field.offsetParent !== null && field.style.display !== 'none';
  });
  
  console.log('Found', requiredFields.length, 'visible required fields to validate');
  
  let emptyFields = [];
  
  requiredFields.forEach(field => {
    const fieldValue = field.value ? field.value.trim() : '';
    if (!fieldValue) {
      const fieldName = field.placeholder || field.name || field.getAttribute('aria-label') || 'Unknown field';
      emptyFields.push(fieldName);
      console.log('Empty required field:', fieldName, 'Element:', field);
    }
  });
  
  if (emptyFields.length > 0) {
    console.log('Validation failed - empty required fields:', emptyFields);
    Swal.fire({
      icon: 'error',
      title: 'Required Fields Missing',
      html: `Please fill in the following required fields:<br><ul style="text-align: left; margin-top: 10px;"><li>• ${emptyFields.join('</li><li>• ')}</li></ul>`,
      confirmButtonColor: '#3085d6'
    });
    return false;
  }
  
  // Check minimum one member - count existing member inputs with values
  const existingMemberInputs = document.querySelectorAll('input[name="member_name[]"]');
  let validMemberCount = 0;
  
  existingMemberInputs.forEach(input => {
    if (input && input.value && input.value.trim() && input.offsetParent !== null) {
      validMemberCount++;
    }
  });
  
  console.log('Valid member count for validation:', validMemberCount);
  
  if (validMemberCount < 1) {
    console.log('Validation failed - no members');
    Swal.fire({
      icon: 'error',
      title: 'Validation Error',
      text: 'At least one member is required.',
      confirmButtonColor: '#3085d6'
    });
    return false;
  }
  
  // Check minimum one activity - count visible activity rows with content
  const visibleActivityRows = Array.from(document.querySelectorAll('.activity-row')).filter(row => {
    return row.offsetParent !== null; // Only count visible rows
  });
  
  let validActivityCount = 0;
  visibleActivityRows.forEach(row => {
    const stageInput = row.querySelector('input[name="stage[]"]');
    const activityInput = row.querySelector('textarea[name="activities[]"]');
    if (stageInput && activityInput && stageInput.value.trim() && activityInput.value.trim()) {
      validActivityCount++;
    }
  });
  
  console.log('Valid activity count for validation:', validActivityCount, '(total rows:', visibleActivityRows.length, ')');
  
  if (validActivityCount < 1) {
    console.log('Validation failed - no valid activities');
    Swal.fire({
      icon: 'error',
      title: 'Validation Error',
      text: 'At least one complete activity is required.',
      confirmButtonColor: '#3085d6'
    });
    return false;
  }
  
  // Check minimum one budget item - count visible budget rows with content
  const visibleBudgetRows = Array.from(document.querySelectorAll('.budget-row')).filter(row => {
    return row.offsetParent !== null; // Only count visible rows
  });
  
  let validBudgetCount = 0;
  let incompleteBudgetCount = 0;
  let budgetRowsStarted = 0;
  
  visibleBudgetRows.forEach((row, index) => {
    const activityInput = row.querySelector('textarea[name="budget_activity[]"]');
    const resourceInput = row.querySelector('textarea[name="budget_resources[]"]');
    const partnerInput = row.querySelector('textarea[name="budget_partners[]"]');
    const amountInput = row.querySelector('input[name="budget_amount[]"]');
    
    const activityValue = activityInput ? activityInput.value.trim() : '';
    const resourceValue = resourceInput ? resourceInput.value.trim() : '';
    const partnerValue = partnerInput ? partnerInput.value.trim() : '';
    const amountValue = amountInput ? amountInput.value.trim() : '';
    
    // Check if any field in this budget row has content (user has started filling it out)
    const hasAnyContent = activityValue || resourceValue || partnerValue || amountValue;
    
    if (hasAnyContent) {
      budgetRowsStarted++;
      
      // If user started filling out this row, check if it's complete
      // Now require Activity, Resources, AND Amount fields
      if (activityValue && resourceValue && amountValue) {
        validBudgetCount++;
      } else {
        incompleteBudgetCount++;
        // Track which fields are missing for better error messaging
        const missingFields = [];
        if (!activityValue) missingFields.push('Activity');
        if (!resourceValue) missingFields.push('Resources Needed');
        if (!amountValue) missingFields.push('Amount');
        
        console.log(`Budget row ${index + 1} missing:`, missingFields.join(', '));
      }
    }
  });
  
  console.log('Budget validation - Rows:', visibleBudgetRows.length, 'Started:', budgetRowsStarted, 'Complete:', validBudgetCount, 'Incomplete:', incompleteBudgetCount);
  
  // Only validate if user has started filling out budget rows
  if (budgetRowsStarted > 0 && incompleteBudgetCount > 0) {
    console.log('Validation failed - incomplete budget items found');
    
    // Identify which specific rows need completion
    const incompleteRowNumbers = [];
    visibleBudgetRows.forEach((row, index) => {
      const activityInput = row.querySelector('textarea[name="budget_activity[]"]');
      const resourceInput = row.querySelector('textarea[name="budget_resources[]"]');
      const amountInput = row.querySelector('input[name="budget_amount[]"]');
      
      const activityValue = activityInput ? activityInput.value.trim() : '';
      const resourceValue = resourceInput ? resourceInput.value.trim() : '';
      const amountValue = amountInput ? amountInput.value.trim() : '';
      const hasAnyContent = activityValue || resourceValue || amountValue;
      
      if (hasAnyContent && !(activityValue && resourceValue && amountValue)) {
        incompleteRowNumbers.push(index + 1);
      }
    });
    
    const rowText = incompleteRowNumbers.length === 1 ? 'row' : 'rows';
    const rowNumbers = incompleteRowNumbers.join(', ');
    
    Swal.fire({
      icon: 'error',
      title: 'Incomplete Budget Items', 
      text: `Budget ${rowText} ${rowNumbers}: Activity, Resources Needed, and Amount fields are required for each budget item you started.`,
      confirmButtonColor: '#3085d6'
    });
    return false;
  }
  
  // Note: We don't require budget items to exist at all - they're optional
  // But if the user starts filling them out, they need to be complete
  
  console.log('✅ Form validation passed!');
  return true;
}

</script>