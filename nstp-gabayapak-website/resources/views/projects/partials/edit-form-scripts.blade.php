<script>
/* ============================
   Consolidated Project Form JS
   ============================ */

/* Single source of truth for member emails and data-population state */
let addedMemberEmails = new Set();
let dataPopulated = false;
let _removeRowHandlerAttached = false;

/* Helper: safeAddListener */
function safeAddListener(id, event, handler) {
  const el = document.getElementById(id);
  if (el) el.addEventListener(event, handler);
}

/* --------------------
   Remove button handler
   - Uses event delegation so we don't reattach multiple times.
   -------------------- */
function attachRemoveButtons() {
  if (_removeRowHandlerAttached) return;
  document.addEventListener('click', function (e) {
    const btn = e.target.closest('.removeRow');
    if (!btn) return;

    e.preventDefault();

    // If removing member, ensure at least one remains
    if (btn.closest('#memberTable tbody tr') || btn.closest('.member-card')) {
      const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
      const memberCardRows = document.querySelectorAll('.member-card').length;
      const totalMemberRows = memberTableRows + memberCardRows;
      if (totalMemberRows <= 1) {
        Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one team member is required.', confirmButtonColor: '#3085d6' });
        return;
      }
    }

    // If removing activity, ensure at least one remains
    if (btn.closest('.activity-row')) {
      const activityRows = document.querySelectorAll('.activity-row').length;
      if (activityRows <= 1) {
        Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one activity is required.', confirmButtonColor: '#3085d6' });
        return;
      }
    }

    // Confirm removal
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
        const row = btn.closest('tr, .grid, .activity-row, .budget-row, .member-card');
        if (row) {
          // if member row, remove email from set
          if (row.querySelector && row.querySelector('input[name="member_email[]"]')) {
            const emailInput = row.querySelector('input[name="member_email[]"]');
            if (emailInput && emailInput.value) addedMemberEmails.delete(emailInput.value);
          }
          row.remove();
        }
        Swal.fire('Removed!', 'The item has been removed.', 'success');
      }
    });
  });
  _removeRowHandlerAttached = true;
}

/* --------------------
   Deduplication helpers - keep at most one empty activity/budget row
   (Safe to call multiple times)
   -------------------- */
function dedupeEmptyActivityRows() {
  try {
    const desktopContainer = document.getElementById('activitiesContainer');
    const mobileContainer = document.getElementById('activitiesContainerMobile');
    function isRowEmpty(row) {
      if (!row) return true;
      const inputs = row.querySelectorAll('input, textarea, select');
      for (let el of inputs) {
        const t = (el.type || '').toLowerCase();
        if (t === 'hidden') continue;
        if (el.name && (el.name === '_token' || el.name === '_method')) continue;
        if (el.value && el.value.toString().trim() !== '') return false;
      }
      return true;
    }

    if (desktopContainer) {
      const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .activity-row'));
      let emptyFound = false;
      rows.forEach(r => {
        if (isRowEmpty(r)) {
          if (!emptyFound) emptyFound = true; else r.remove();
        }
      });
    }

    if (mobileContainer) {
      const cards = Array.from(mobileContainer.querySelectorAll('.activity-row'));
      let emptyFound = false;
      cards.forEach(c => {
        if (isRowEmpty(c)) {
          if (!emptyFound) emptyFound = true; else c.remove();
        }
      });
    }
  } catch (e) {
    console.error('dedupeEmptyActivityRows error', e);
  }
}

function dedupeEmptyBudgetRows() {
  try {
    const desktopContainer = document.getElementById('budgetContainer');
    const mobileContainer = document.getElementById('budgetContainerMobile');

    function isBudgetRowEmpty(row) {
      if (!row) return true;
      const inputs = row.querySelectorAll('input, textarea, select');
      for (let el of inputs) {
        const t = (el.type || '').toLowerCase();
        if (t === 'hidden') continue;
        if (el.name && (el.name === '_token' || el.name === '_method')) continue;
        if (el.value && el.value.toString().trim() !== '') return false;
      }
      return true;
    }

    if (desktopContainer) {
      const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .budget-row'));
      const emptyRows = rows.filter(isBudgetRowEmpty);
      if (emptyRows.length > 1) emptyRows.slice(0, -1).forEach(r => r.remove());
    }

    if (mobileContainer) {
      const cards = Array.from(mobileContainer.querySelectorAll('.budget-row'));
      const emptyRows = cards.filter(isBudgetRowEmpty);
      if (emptyRows.length > 1) emptyRows.slice(0, -1).forEach(c => c.remove());
    }
  } catch (e) {
    console.error('dedupeEmptyBudgetRows error', e);
  }
}

/* removeAllEmptyBudgetRows: for final submit cleanup */
function removeAllEmptyBudgetRows() {
  const desktopContainer = document.getElementById('budgetContainer');
  const mobileContainer = document.getElementById('budgetContainerMobile');
  function isBudgetRowEmpty(row) {
    if (!row) return true;
    const inputs = row.querySelectorAll('input, textarea, select');
    for (let el of inputs) {
      const t = (el.type || '').toLowerCase();
      if (t === 'hidden') continue;
      if (el.name && (el.name === '_token' || el.name === '_method')) continue;
      if (el.value && el.value.toString().trim() !== '') return false;
    }
    return true;
  }
  if (desktopContainer) {
    const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .budget-row'));
    rows.forEach(r => { if (isBudgetRowEmpty(r)) r.remove(); });
  }
  if (mobileContainer) {
    const cards = Array.from(mobileContainer.querySelectorAll('.budget-row'));
    cards.forEach(c => { if (isBudgetRowEmpty(c)) c.remove(); });
  }
}

/* --------------------
   Helpers for adding rows (activities & budgets)
   Keep the UI markup similar to your original code.
   -------------------- */
function addActivityRow(stage='', activity='', timeframe='', implementation_date='', point_person='', status='Planned') {
  const desktopContainer = document.getElementById('activitiesContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'proposal-table-row activity-row flex items-center gap-4';
    newRow.innerHTML = `
      <div class="w-20 flex-none">
        <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" value="${stage}" >
      </div>
      <div class="flex-1 px-2">
        <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities...">${activity}</textarea>
      </div>
      <div class="w-36 px-2 flex-none">
        <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" value="${timeframe}">
      </div>
      <div class="w-44 px-2 flex-none">
        <input type="date" name="implementation_date[]" class="proposal-input w-full" value="${implementation_date}">
      </div>
      <div class="flex-1 px-2">
        <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s">${point_person}</textarea>
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
        <input type="date" name="implementation_date[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" value="${implementation_date}">
      </div>
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Point Person/s <span class="text-red-500">*</span></label>
        <textarea name="point_person[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2" placeholder="Point Person/s">${point_person}</textarea>
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
  // attachRemoveButtons(); // not needed: we use delegated listener
}


  // Handle Cancel Edit with confirmation - redirect to appropriate page
  safeAddListener('cancelEditBtn', 'click', function() {
    Swal.fire({
      title: 'Cancel Editing?',
      text: "Any unsaved changes will be lost. Are you sure you want to cancel?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc2626',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, cancel editing'
    }).then((result) => {
      if (result.isConfirmed) {
        // Show success message first, then redirect
        Swal.fire({
          icon: 'success',
          title: 'Editing Cancelled',
          text: 'You have successfully cancelled editing.',
          timer: 1500,
          showConfirmButton: false
        }).then(() => {
          // Always redirect to project show page
          window.location.href = "{{ route('projects.show', $project) }}";
        });
      }
    })
  });

function addBudgetRow(activity = '', resources = '', partners = '', amount = '') {
  const desktopContainer = document.getElementById('budgetContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start budget-row';
    newRow.innerHTML = `
      <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity...">${activity || ''}</textarea>
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
}

/* --------------------
   prepareFormForSubmit: disable inputs that are not visible (so only visible ones get sent)
   -------------------- */
function prepareFormForSubmit(form) {
  // Enable everything first
  form.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
  // Then disable hidden elements except hidden inputs / csrf / method / member inputs
  form.querySelectorAll('input, textarea, select').forEach(el => {
    try {
      const t = (el.type || '').toLowerCase();
      if (t === 'hidden') return;
      if (el.name && (el.name === '_token' || el.name === '_method')) return;
      // Don't disable member inputs - we need both desktop and mobile views to submit
      if (el.name && el.name.startsWith('member_')) return;
      // offsetParent === null indicates hidden by CSS (not in DOM flow)
      if (el.offsetParent === null) el.disabled = true;
    } catch (e) { /* ignore */ }
  });
}

/* --------------------
   relaxRequiredForDraft: remove required attributes for draft saving
   -------------------- */
function relaxRequiredForDraft(form) {
  const selectors = [
    'input[name^="member_"]',
    'input[name^="stage"]',
    'textarea[name^="activities"]',
    'input[name^="timeframe"]',
    'textarea[name^="point_person"]',
    'select[name^="status"]',
    'textarea[name^="budget_"]',
    'input[name^="budget_amount[]"]',
    'input[name="Project_Logo"]'
  ];
  selectors.forEach(sel => {
    form.querySelectorAll(sel).forEach(el => {
      if (el.hasAttribute && el.hasAttribute('required')) el.removeAttribute('required');
    });
  });
}

/* --------------------
   Validation for submit (minimum requirements)
   -------------------- */
function validateFormRequirements() {
  const form = document.getElementById('projectForm');
  if (!form) {
    console.error('Project form not found');
    return false;
  }
  
  const formData = new FormData(form);
  const errors = [];
  
  // Validate basic project fields
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
  
  // Validate team logo
  const logoFile = formData.get('Project_Logo');
  const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
  if (!hasExistingLogo && (!logoFile || logoFile.size === 0)) {
    errors.push('A team logo is required for project submission.');
  }

  // Validate team members
  const allMemberNames = formData.getAll('member_name[]');
  const allMemberRoles = formData.getAll('member_role[]');
  const allMemberEmails = formData.getAll('member_email[]');
  const allMemberContacts = formData.getAll('member_contact[]');

  // Filter out duplicates and empty entries (from desktop/mobile views)
  const uniqueMembers = [];
  const processedEmails = new Set();
  
  for (let i = 0; i < allMemberNames.length; i++) {
    const name = allMemberNames[i]?.trim();
    const role = allMemberRoles[i]?.trim();
    const email = allMemberEmails[i]?.trim();
    const contact = allMemberContacts[i]?.trim();
    
    // Only process if there's actual data and email hasn't been processed
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

  // Validate activities
  const allStages = formData.getAll('stage[]');
  const allActivities = formData.getAll('activities[]');
  const allTimeframes = formData.getAll('timeframe[]');
  const allImplementationDates = formData.getAll('implementation_date[]');
  const allPointPersons = formData.getAll('point_person[]');
  const allStatuses = formData.getAll('status[]');

  // Filter out duplicates from desktop/mobile views - only get visible inputs
  const visibleStages = [];
  const visibleActivities = [];
  const visibleTimeframes = [];
  const visibleImplementationDates = [];
  const visiblePointPersons = [];
  const visibleStatuses = [];
  
  // Get only inputs from visible containers
  const visibleStageInputs = document.querySelectorAll('input[name="stage[]"], textarea[name="stage[]"]');
  visibleStageInputs.forEach((input, index) => {
    if (input.offsetParent !== null) { // Only visible elements
      visibleStages.push(allStages[index] || '');
      visibleActivities.push(allActivities[index] || '');
      visibleTimeframes.push(allTimeframes[index] || '');
      visibleImplementationDates.push(allImplementationDates[index] || '');
      visiblePointPersons.push(allPointPersons[index] || '');
      visibleStatuses.push(allStatuses[index] || 'Planned');
    }
  });

  let validActivities = 0;
  for (let i = 0; i < visibleStages.length; i++) {
    const stage = visibleStages[i]?.trim();
    const activity = visibleActivities[i]?.trim();
    const timeframe = visibleTimeframes[i]?.trim();
    const implementationDate = visibleImplementationDates[i]?.trim();
    const person = visiblePointPersons[i]?.trim();
    const status = visibleStatuses[i]?.trim() || 'Planned';
    
    // Check if any activity field has content (indicating this row is being used)
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

  // Validate budget rows (optional, but if partially filled must be complete)
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

  // Show errors if any
  if (errors.length > 0) {
    const errorList = errors.join('<br>');
    Swal.fire({
      icon: 'error',
      title: 'Validation Error!',
      html: `<div class="text-center">${errorList}</div>`,
      confirmButtonColor: '#3085d6',
      width: '600px'
    });
    return false;
  }
  
  return true;
}

/* --------------------
   Initialize addedMemberEmails from existing inputs, and attach handlers
   -------------------- */
document.addEventListener('DOMContentLoaded', function () {
  // init addedMemberEmails only once
  if (addedMemberEmails.size === 0) {
    document.querySelectorAll('input[name="member_email[]"]').forEach(input => {
      if (input.value && input.value.trim() !== '') addedMemberEmails.add(input.value.trim());
    });
  }
  attachRemoveButtons();
  dataPopulated = true;

  // Add listeners for add buttons (if they exist)
  const addActivityBtn = document.getElementById('addActivityRow');
  if (addActivityBtn) addActivityBtn.addEventListener('click', function () {
    addActivityRow('', '', '', '', '', 'Planned');
    dedupeEmptyActivityRows(); // keep it tidy
  });

  const addBudgetBtn = document.getElementById('addBudgetRow');
  if (addBudgetBtn) addBudgetBtn.addEventListener('click', function () {
    addBudgetRow();
    dedupeEmptyBudgetRows();
  });

  // Member modal event listeners
  const openMemberModalBtn = document.getElementById('openMemberModal');
  if (openMemberModalBtn) {
    openMemberModalBtn.addEventListener('click', function() {
      loadMemberList();
      document.getElementById('memberModal').classList.remove('hidden');
    });
  }

  const openMemberModalMobileBtn = document.getElementById('openMemberModalMobile');
  if (openMemberModalMobileBtn) {
    openMemberModalMobileBtn.addEventListener('click', function() {
      loadMemberList();
      document.getElementById('memberModal').classList.remove('hidden');
    });
  }

  const closeMemberModalBtn = document.getElementById('closeMemberModal');
  if (closeMemberModalBtn) {
    closeMemberModalBtn.addEventListener('click', function() {
      document.getElementById('memberModal').classList.add('hidden');
    });
  }

  const cancelMemberSelectionBtn = document.getElementById('cancelMemberSelection');
  if (cancelMemberSelectionBtn) {
    cancelMemberSelectionBtn.addEventListener('click', function() {
      document.getElementById('memberModal').classList.add('hidden');
    });
  }

  const addSelectedMembersBtn = document.getElementById('addSelectedMembers');
  if (addSelectedMembersBtn) {
    addSelectedMembersBtn.addEventListener('click', function() {
      addSelectedMembersToForm();
    });
  }
});

/* --------------------
   Member modal functions
   -------------------- */
function loadMemberList() {
  const memberList = document.getElementById('memberList');
  memberList.innerHTML = '<p class="text-center text-gray-500">Loading members...</p>';
 
  // Fetch students from the same section and component
  fetch('{{ route("projects.students.same-section") }}')
    .then(response => response.json())
    .then(students => {
      if (students.length === 0) {
        memberList.innerHTML = '<p class="text-center text-gray-500">No students found in your section and component.</p>';
        return;
      }
     
      let html = '';
      students.forEach(student => {
        // Skip if this member is already added
        if (addedMemberEmails.has(student.email)) {
          return;
        }
        
        html += `
          <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
            <div class="flex items-center">
              <input type="checkbox" id="member${student.id}" name="available_members[]" value="${student.id}" class="mr-2" data-name="${student.name}" data-email="${student.email}" data-contact="${student.contact_number || ''}">
              <label for="member${student.id}" class="text-sm">
                <span class="font-medium">${student.name}</span> -
                <span class="text-gray-600">${student.email}</span>
                <span class="text-gray-500 text-xs block">${student.contact_number || 'No contact number'}</span>
              </label>
            </div>
            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Same Section</span>
          </div>
        `;
      });
     
      if (html === '') {
        memberList.innerHTML = '<p class="text-center text-gray-500">All students from your section are already added to the team.</p>';
      } else {
        memberList.innerHTML = html;
      }
    })
    .catch(error => {
      console.error('Error fetching students:', error);
      memberList.innerHTML = '<p class="text-center text-red-500">Error loading students. Please try again.</p>';
    });
}

function addSelectedMembersToForm() {
  const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
 
  selectedMembers.forEach(checkbox => {
    const memberId = checkbox.value;
    const memberName = checkbox.dataset.name;
    const memberEmail = checkbox.dataset.email;
    const memberContact = checkbox.dataset.contact || '';
    
    // Add to tracking set
    if (memberEmail) {
      addedMemberEmails.add(memberEmail);
    }
   
    // Add to desktop table
    const desktopTable = document.querySelector('#memberTable tbody');
    if (desktopTable) {
      const newRow = document.createElement('tr');
      newRow.className = 'hover:bg-gray-50 transition-colors';
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
      // Add new member at the end of the table
      desktopTable.appendChild(newRow);
    }
   
    // Add to mobile view
    const mobileContainer = document.getElementById('memberContainer');
    if (mobileContainer) {
      const newCard = document.createElement('div');
      newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
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
      // Add new member card at the end of the container
      mobileContainer.appendChild(newCard);
    }
  });
 
  // Close modal
  document.getElementById('memberModal').classList.add('hidden');
}

/* ============================
   Save Project (staff) handler
   Leaves submitProjectInput = 1 if data-current-status === 'pending'
   ============================ */
const saveProjectBtn = document.getElementById('saveProjectBtn');
if (saveProjectBtn) {
  saveProjectBtn.addEventListener('click', function () {
    const form = document.getElementById('projectForm');
    const currentStatus = this.dataset.currentStatus || '';
    if (currentStatus === 'pending') {
      const submitProjectInput = document.getElementById('submitProjectInput');
      if (submitProjectInput) submitProjectInput.value = '1';
    } else {
      const submitProjectInput = document.getElementById('submitProjectInput');
      if (submitProjectInput) submitProjectInput.value = '0';
    }
    const saveDraftInput = document.getElementById('saveDraftInput');
    if (saveDraftInput) saveDraftInput.value = '0';

    // Remove duplicate/extra empty activity and budget rows before saving
    dedupeEmptyActivityRows();
    dedupeEmptyBudgetRows();

    prepareFormForSubmit(form);
    form.submit();
  });
}

/* ============================
   Save as Draft
   - NO role requirement
   - NO auto-add rows
   - Remove required attributes
   ============================ */
(function() {
  let isSavingDraft = false;
  safeAddListener('saveDraftBtn', 'click', function (e) {
    e.preventDefault();
    if (isSavingDraft) return;
    const form = document.getElementById('projectForm');
    if (!form) return;

    Swal.fire({
      title: 'Save as Draft?',
      text: "Your project will be saved as a draft and can be edited later.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, save as draft!'
    }).then((result) => {
      if (!result.isConfirmed) return;
      isSavingDraft = true;

      // Relax required attributes (so draft can be saved without required fields)
      relaxRequiredForDraft(form);

      // Only remove completely empty rows, but preserve partial budget data for drafts
      dedupeEmptyActivityRows();
      dedupeEmptyBudgetRows(); // This only removes completely empty rows

      // DO NOT remove budget rows with partial data in draft mode
      // removeAllEmptyBudgetRows(); // Commented out for draft saving

      
      //prepare visible fields only
      prepareFormForSubmit(form);

      const saveDraftInput = document.getElementById('saveDraftInput');
      const submitProjectInput = document.getElementById('submitProjectInput');
      if (saveDraftInput) saveDraftInput.value = '1';
      if (submitProjectInput) submitProjectInput.value = '0';

      // final submit
      form.submit();
      // reset flag after short delay to prevent double-click issues (form navigation will usually occur)
      setTimeout(() => { isSavingDraft = false; }, 2000);
    });
  });
})();

/* ============================
   Submit with Review Details Modal
   - Shows full review modal (user provided implementation)
   - On confirm, runs final submit flow
   ============================ */
safeAddListener('submitProjectBtn', 'click', function (e) {
  e.preventDefault();
  // Validate minimum requirements
  if (!validateFormRequirements()) return;
  // Show review / confirmation modal
  showConfirmationModal();
});

/* --------------------
   showConfirmationModal implementation (as provided by you)
   Slight change: uses prepareFormForSubmit and removeAllEmptyBudgetRows before final submit
   -------------------- */
function showConfirmationModal() {
  const form = document.getElementById('projectForm');
  const formData = new FormData(form);

  // Team Information
  const projectName = formData.get('Project_Name') || 'N/A';
  const teamName = formData.get('Project_Team_Name') || 'N/A';
  const component = formData.get('Project_Component') || 'N/A';
  const section = formData.get('nstp_section') || 'N/A';

  // Get team logo file and detect existing logo (if any)
  const teamLogoFile = formData.get('Project_Logo');
  const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
  let teamLogoHTML = '<div class="text-sm text-gray-600">No file uploaded</div>';
  
  // Check for new file upload
  if (teamLogoFile && teamLogoFile.size > 0) {
    teamLogoHTML = `<div class="text-sm text-green-600">${teamLogoFile.name} (${(teamLogoFile.size / 1024).toFixed(2)} KB)</div>`;
  }
  // Check for existing logo
  else if (hasExistingLogo) {
    teamLogoHTML = `<div class="text-sm text-green-600">✓ Logo image is uploaded</div>`;
  }

  // If submitting and no uploaded or existing logo, show friendly message and abort
  if (!hasExistingLogo && (!teamLogoFile || teamLogoFile.size === 0)) {
    Swal.fire({
      icon: 'warning',
      title: 'Logo Required',
      text: 'Submitting a project requires a team logo. Please upload a logo or save as draft.',
      confirmButtonColor: '#3085d6'
    });
    return;
  }

  // Project Details
  const problems = formData.get('Project_Problems') || 'N/A';
  const goals = formData.get('Project_Goals') || 'N/A';
  const targetCommunity = formData.get('Project_Target_Community') || 'N/A';
  const solution = formData.get('Project_Solution') || 'N/A';
  const outcomes = formData.get('Project_Expected_Outcomes') || 'N/A';

  // Members - take unique (avoid duplicates from mobile/desktop)
  const allMemberNames = formData.getAll('member_name[]');
  const allMemberRoles = formData.getAll('member_role[]');
  const allMemberEmails = formData.getAll('member_email[]');
  const allMemberContacts = formData.getAll('member_contact[]');
  const uniqueMemberCount = Math.ceil(allMemberNames.length / 2);

  const memberNames = [];
  const memberRoles = [];
  const memberEmails = [];
  const memberContacts = [];

  for (let idx = 0; idx < uniqueMemberCount; idx++) {
    const name = allMemberNames[idx];
    if (name && name.trim() !== '') {
      memberNames.push(name);
      memberRoles.push(allMemberRoles[idx] || '');
      memberEmails.push(allMemberEmails[idx] || '');
      memberContacts.push(allMemberContacts[idx] || '');
    }
  }

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

  // Activities - collect non-empty entries
  const allStages = formData.getAll('stage[]');
  const allActivities = formData.getAll('activities[]');
  const allTimeframes = formData.getAll('timeframe[]');
  const allPointPersons = formData.getAll('point_person[]');
  const allStatuses = formData.getAll('status[]');

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

  // Budget
  const allBudgetActivities = formData.getAll('budget_activity[]');
  const allBudgetResources = formData.getAll('budget_resources[]');
  const allBudgetPartners = formData.getAll('budget_partners[]');
  const allBudgetAmounts = formData.getAll('budget_amount[]');

  const budgetActivities = [];
  const budgetResources = [];
  const budgetPartners = [];
  const budgetAmounts = [];

  allBudgetActivities.forEach((activity, idx) => {
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
      let amountValue = budgetAmounts[idx] || '0';
      amountValue = amountValue.replace(/[₱,]/g, '').trim();
      const numericAmount = parseFloat(amountValue) || 0;
      totalBudget += numericAmount;
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
          // Final: set flags, clean up and submit
          const saveDraftInput = document.getElementById('saveDraftInput');
          const submitProjectInput = document.getElementById('submitProjectInput');
          if (saveDraftInput) saveDraftInput.value = '0';
          if (submitProjectInput) submitProjectInput.value = '1';

          // cleanup duplicate/empty rows
          dedupeEmptyActivityRows();
          dedupeEmptyBudgetRows();
          removeAllEmptyBudgetRows();

          // disable hidden inputs so only visible values are submitted
          prepareFormForSubmit(form);

          // submit
          form.submit();
        }
      });
    }
  });
}
</script>
