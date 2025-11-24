<script>
console.log('🚀 Staff Edit Form Scripts Loading...');

// Ensure a safe placeholder exists for staff_syncHiddenIds so callers
// that run before the real implementation don't throw a ReferenceError.
// Provide a robust staff_syncHiddenIds implementation that prefers the
// desktop containers when present and uses computed style visibility
// to avoid mapping IDs to duplicate mobile/desktop rows incorrectly.
if (typeof window.staff_syncHiddenIds !== 'function' || window.staff_syncHiddenIds._isPlaceholder) {
  window._staff_syncHiddenIds_queue = window._staff_syncHiddenIds_queue || [];

  window.staff_syncHiddenIds = function(formEl) {
    try {
      const form = formEl || document.getElementById('projectForm');
      if (!form) return;

      function isVisible(el) {
        try { return el && getComputedStyle(el).display !== 'none'; } catch (e) { return !!(el && el.offsetParent !== null); }
      }

      function getPreferredRows(rowSelector, desktopId, mobileId) {
        // Prefer desktop container rows when they have visible items, else mobile.
        try {
          if (desktopId) {
            const desktop = document.getElementById(desktopId);
            if (desktop) {
              const drows = Array.from(desktop.querySelectorAll(rowSelector)).filter(r => r && isVisible(r));
              if (drows.length > 0) return drows;
            }
          }
          if (mobileId) {
            const mobile = document.getElementById(mobileId);
            if (mobile) {
              const mrows = Array.from(mobile.querySelectorAll(rowSelector)).filter(r => r && isVisible(r));
              if (mrows.length > 0) return mrows;
            }
          }
          // Fallback: any visible rows in document
          return Array.from(document.querySelectorAll(rowSelector)).filter(r => r && isVisible(r));
        } catch (e) {
          return Array.from(document.querySelectorAll(rowSelector)).filter(r => r && (r.offsetParent !== null));
        }
      }

      function syncIds(rowSelector, hiddenName, desktopId = null, mobileId = null) {
        const visibleRows = getPreferredRows(rowSelector, desktopId, mobileId);

        // Disable inputs in non-preferred duplicate rows so only preferred rows submit
        try {
          const allRows = Array.from(document.querySelectorAll(rowSelector));
          allRows.forEach(r => {
            try {
              const inputs = Array.from(r.querySelectorAll('input, textarea, select'));
              if (!visibleRows.includes(r)) {
                inputs.forEach(i => { try { i.disabled = true; i.setAttribute('data-disabled-by-sync','1'); } catch(e){} });
              } else {
                inputs.forEach(i => { try { if (i.hasAttribute('data-disabled-by-sync')) { i.removeAttribute('data-disabled-by-sync'); } i.disabled = false; } catch(e){} });
              }
            } catch (e) {}
          });
        } catch (e) {}

        // Capture existing hidden inputs (server-side rendered) that are NOT
        // inside any matching row so we can use them as fallbacks in order.
        const allHiddenInputs = Array.from(form.querySelectorAll(`input[name="${hiddenName}[]"]`));
        const fallbackHiddenInputs = allHiddenInputs.filter(i => !i.closest(rowSelector));
        const existingValues = fallbackHiddenInputs.map(i => i.value || '');

        const ids = [];
        let fallbackIndex = 0;
        visibleRows.forEach(r => {
          // Prefer an explicit hidden input inside the row
          const h = r.querySelector && r.querySelector(`input[name="${hiddenName}[]"]`);
          if (h && (h.value !== undefined)) {
            ids.push(h.value || '');
            return;
          }

          // If we're syncing row keys and the row element has a data-row-key attribute, use that
          if ((/row_key$/).test(hiddenName)) {
            try {
              const drk = (r.getAttribute && r.getAttribute('data-row-key')) || (r.dataset && r.dataset.rowKey);
              if (drk) {
                ids.push(String(drk));
                return;
              }
            } catch (e) {}
          }

          // Fallback to the next existing hidden input value (server-rendered fallback)
          const fallback = (fallbackIndex < existingValues.length) ? existingValues[fallbackIndex] : '';
          ids.push(fallback || '');
          fallbackIndex++;
        });

        // Optional debug: print arrays when enabled
        try {
          if (window.__DEBUG_SYNC) {
            console.debug('staff_syncHiddenIds debug:', { rowSelector, hiddenName, visibleRows: visibleRows.length, ids: ids.slice(), existingValues: existingValues.slice() });
          }
        } catch (e) {}

        // Deduplicate while preserving order (helps when both desktop and mobile rows are present)
        const deduped = [];
        const seen = new Set();
        ids.forEach(v => {
          // Preserve empty placeholders (don't collapse empty strings) — only dedupe non-empty values
          const k = (v || '').toString();
          if (k === '') {
            deduped.push(k);
          } else {
            if (!seen.has(k)) {
              seen.add(k);
              deduped.push(k);
            }
          }
        });

        // Remove existing inputs and recreate them in the same order as visible rows
        Array.from(form.querySelectorAll(`input[name="${hiddenName}[]"]`)).forEach(el => el.remove());
        deduped.forEach(val => {
          const inp = document.createElement('input');
          inp.type = 'hidden';
          inp.name = `${hiddenName}[]`;
          inp.value = val || '';
          form.appendChild(inp);
        });
      }

      // Sync activity and budget ids and their stable row keys using preferred containers
      syncIds('.activity-row', 'activity_id', 'activitiesContainer', 'activitiesContainerMobile');
      syncIds('.budget-row', 'budget_id', 'budgetContainer', 'budgetContainerMobile');
      // row keys allow deterministic matching on the server
      syncIds('.activity-row', 'activity_row_key', 'activitiesContainer', 'activitiesContainerMobile');
      syncIds('.budget-row', 'budget_row_key', 'budgetContainer', 'budgetContainerMobile');
    } catch (e) {
      console.warn('staff_syncHiddenIds error', e);
    }
  };
  window.staff_syncHiddenIds._isPlaceholder = false;

  // Drain any queued calls that occurred while placeholder was active
  try {
    if (window._staff_syncHiddenIds_queue && Array.isArray(window._staff_syncHiddenIds_queue) && window._staff_syncHiddenIds_queue.length > 0) {
      const queued = window._staff_syncHiddenIds_queue.slice();
      window._staff_syncHiddenIds_queue = [];
      queued.forEach((qForm) => {
        try { window.staff_syncHiddenIds(qForm); } catch (e) { console.warn('Draining queued staff_syncHiddenIds call failed', e); }
      });
    }
  } catch (e) { console.warn('Error while draining staff_syncHiddenIds queue', e); }
}

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
          try {
            // Record deleted ids and row keys so server can delete deterministically
            try {
              const formEl = document.getElementById('projectForm');
              if (formEl) {
                const actInput = row.querySelector && row.querySelector('input[name="activity_id[]"]');
                if (actInput && actInput.value) {
                  const del = document.createElement('input');
                  del.type = 'hidden';
                  del.name = 'deleted_activity_id[]';
                  del.value = actInput.value;
                  formEl.appendChild(del);
                }
                const actKey = row.querySelector && row.querySelector('input[name="activity_row_key[]"]');
                if (actKey && actKey.value) {
                  const delk = document.createElement('input');
                  delk.type = 'hidden';
                  delk.name = 'deleted_activity_row_key[]';
                  delk.value = actKey.value;
                  formEl.appendChild(delk);
                }
                const budInput = row.querySelector && row.querySelector('input[name="budget_id[]"]');
                if (budInput && budInput.value) {
                  const del2 = document.createElement('input');
                  del2.type = 'hidden';
                  del2.name = 'deleted_budget_id[]';
                  del2.value = budInput.value;
                  formEl.appendChild(del2);
                }
                const budKey = row.querySelector && row.querySelector('input[name="budget_row_key[]"]');
                if (budKey && budKey.value) {
                  const delk2 = document.createElement('input');
                  delk2.type = 'hidden';
                  delk2.name = 'deleted_budget_row_key[]';
                  delk2.value = budKey.value;
                  formEl.appendChild(delk2);
                }
              }
            } catch (e) { console.warn('Error recording deleted ids/keys', e); }
            staff_onMemberRemoved(row);
          } catch (e) { console.warn('staff_onMemberRemoved call failed', e); row.remove(); }
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
  // Normalize status for consistent comparisons (case-insensitive)
  const s = String(status || '').trim().toLowerCase();
  // Compute stable row key once so desktop and mobile representations share it
  const rowKey = (function() {
    try {
      if (activityId && activityId !== '') return 'act-' + String(activityId);
      return 'act-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2,9);
    } catch (e) { return 'act-' + Math.random().toString(36).slice(2,9); }
  })();

  const desktopContainer = document.getElementById('activitiesContainer');
  if (desktopContainer) {
    const newRow = document.createElement('div');
    newRow.className = 'proposal-table-row activity-row flex items-center gap-4';
    newRow.innerHTML = `
      <div class="w-20 flex-none">
        <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" value="${stage}" >
        <input type="hidden" name="activity_id[]" value="${activityId || ''}">
        <input type="hidden" name="activity_row_key[]" value="${rowKey}">
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
        <select name="status[]" ${s === 'completed' ? 'disabled title="Locked — completed by student"' : ''} class="proposal-select w-full ${s === 'completed' ? 'opacity-60 cursor-not-allowed' : ''}">
          <option value="Planned" ${s === 'planned' ? 'selected' : ''}>Planned</option>
          <option value="Ongoing" ${s === 'ongoing' ? 'selected' : ''}>Ongoing</option>
          <option value="Completed" ${s === 'completed' ? 'selected' : ''}>Completed</option>
        </select>
      </div>
      <div class="w-20 py-3 flex-none">
        <button type="button" class="proposal-remove-btn removeRow">Remove</button>
      </div>
    `;
    desktopContainer.appendChild(newRow);
    // tag the element with the row key for easier lookup
    try { newRow.setAttribute('data-row-key', rowKey); } catch (e) {}
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
        <input type="hidden" name="activity_row_key[]" value="${rowKey}">
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
          <select name="status[]" ${s === 'completed' ? 'disabled title="Locked — completed by student"' : ''} class="proposal-select w-full ${s === 'completed' ? 'opacity-60 cursor-not-allowed' : ''}">
            <option value="Planned" ${s === 'planned' ? 'selected' : ''}>Planned</option>
            <option value="Ongoing" ${s === 'ongoing' ? 'selected' : ''}>Ongoing</option>
            <option value="Completed" ${s === 'completed' ? 'selected' : ''}>Completed</option>
          </select>
        </div>
        <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
      </div>
    `;
    mobileContainer.appendChild(newCard);
    try { newCard.setAttribute('data-row-key', rowKey); } catch (e) {}
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

        // Also remove duplicate activity/budget representations by matching row keys or ids
        try {
          // Activities
          try {
            const actKeyInput = row.querySelector && row.querySelector('input[name="activity_row_key[]"]');
            const actIdInput = row.querySelector && row.querySelector('input[name="activity_id[]"]');
            const actKey = actKeyInput && actKeyInput.value ? String(actKeyInput.value).trim() : null;
            const actId = actIdInput && actIdInput.value ? String(actIdInput.value).trim() : null;
            if (actKey) {
              const others = Array.from(document.querySelectorAll('[data-row-key]'));
              others.forEach(o => {
                try {
                  if (o.getAttribute && o.getAttribute('data-row-key') === actKey) {
                    if (o !== row) o.remove();
                  }
                } catch (e) {}
              });
            }
            if (actId) {
              const inputs = Array.from(document.querySelectorAll('input[name="activity_id[]"]'));
              inputs.forEach(inp => {
                try {
                  if (String(inp.value).trim() === actId) {
                    const anc = inp.closest('tr, .activity-row, .grid');
                    if (anc && anc !== row) anc.remove();
                  }
                } catch (e) {}
              });
            }
          } catch (e) {}

          // Budgets
          try {
            const budKeyInput = row.querySelector && row.querySelector('input[name="budget_row_key[]"]');
            const budIdInput = row.querySelector && row.querySelector('input[name="budget_id[]"]');
            const budKey = budKeyInput && budKeyInput.value ? String(budKeyInput.value).trim() : null;
            const budId = budIdInput && budIdInput.value ? String(budIdInput.value).trim() : null;
            if (budKey) {
              const others = Array.from(document.querySelectorAll('[data-row-key]'));
              others.forEach(o => {
                try {
                  if (o.getAttribute && o.getAttribute('data-row-key') === budKey) {
                    if (o !== row) o.remove();
                  }
                } catch (e) {}
              });
            }
            if (budId) {
              const inputs = Array.from(document.querySelectorAll('input[name="budget_id[]"]'));
              inputs.forEach(inp => {
                try {
                  if (String(inp.value).trim() === budId) {
                    const anc = inp.closest('tr, .budget-row, .grid');
                    if (anc && anc !== row) anc.remove();
                  }
                } catch (e) {}
              });
            }
          } catch (e) {}
        } catch (e) { console.warn('Error removing duplicate activity/budget representations', e); }

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
  // Compute stable row key once so desktop and mobile representations share it
  const rowKey = (function() {
    try {
      if (budgetId && budgetId !== '') return 'bud-' + String(budgetId);
      return 'bud-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2,9);
    } catch (e) { return 'bud-' + Math.random().toString(36).slice(2,9); }
  })();

  const desktopContainer = document.getElementById('budgetContainer');
        if (desktopContainer) {
          const newRow = document.createElement('div');
          newRow.className = 'proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start budget-row';
          newRow.innerHTML = `
            <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity...">${activity || ''}</textarea>
            <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
            <input type="hidden" name="budget_row_key[]" value="${rowKey}">
            <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed...">${resources || ''}</textarea>
            <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations...">${partners || ''}</textarea>
            <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00" value="${amount || ''}">
            <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
          `;
          desktopContainer.appendChild(newRow);
          try { newRow.setAttribute('data-row-key', rowKey); } catch (e) {}
        }

  const mobileContainer = document.getElementById('budgetContainerMobile');
  if (mobileContainer) {
    const newCard = document.createElement('div');
    newCard.className = 'budget-row space-y-3 p-3 border-2 border-gray-400 rounded bg-white shadow-sm';
    // use the same rowKey for mobile representation
    const mobileRowKey = rowKey;
    newCard.innerHTML = `
      <div class="space-y-1">
        <label class="block text-xs font-medium text-gray-600">Activity</label>
        <textarea name="budget_activity[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm" rows="2">${activity || ''}</textarea>
        <input type="hidden" name="budget_id[]" value="${budgetId || ''}">
        <input type="hidden" name="budget_row_key[]" value="${mobileRowKey}">
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
    try { newCard.setAttribute('data-row-key', mobileRowKey); } catch (e) {}
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

      // Always sync hidden ids/row-keys immediately before submit
      try { if (typeof staff_syncHiddenIds === 'function') staff_syncHiddenIds(form); } catch (err) { console.warn('pre-submit staff_syncHiddenIds failed', err); }
      
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

      // Debug: log counts of visible rows vs hidden id/row_key inputs
      try {
        const ar = document.querySelectorAll('.activity-row').length;
        const ai = document.querySelectorAll('input[name="activity_id[]"]').length;
        const ark = document.querySelectorAll('input[name="activity_row_key[]"]').length;
        const br = document.querySelectorAll('.budget-row').length;
        const bi = document.querySelectorAll('input[name="budget_id[]"]').length;
        const brk = document.querySelectorAll('input[name="budget_row_key[]"]').length;
        console.debug('Submit counts: activities visible=', ar, 'activity_id[]=', ai, 'activity_row_key[]=', ark, 'budgets visible=', br, 'budget_id[]=', bi, 'budget_row_key[]=', brk);
      } catch (e) {}
      
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
          try {
            const fd = new FormData(form);
            console.debug('DEBUG before submit (review modal) - activity_id[]:', fd.getAll('activity_id[]'));
            console.debug('DEBUG before submit (review modal) - budget_id[]:', fd.getAll('budget_id[]'));
            // Also dump all keys for inspection
            for (const pair of fd.entries()) console.debug('FormData entry:', pair[0], pair[1]);
          } catch (e) { console.warn('Error dumping FormData before submit', e); }
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