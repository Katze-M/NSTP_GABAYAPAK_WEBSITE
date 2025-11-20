<script>
// --- CONSOLIDATED DYNAMIC ROW LOGIC ---
let addedMemberEmails = new Set();
let dataPopulated = false;

// Attach remove button handler (event delegation)
function attachRemoveButtons() {
  if (!window._removeRowHandlerAttached) {
    document.addEventListener('click', function(e) {
      const btn = e.target.closest('.removeRow');
      if (!btn) return;
      e.preventDefault();
      // Prevent removing last member
      if (btn.closest('#memberTable tbody tr') || btn.closest('.member-card')) {
        const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
        const memberCardRows = document.querySelectorAll('.member-card').length;
        if (memberTableRows + memberCardRows <= 1) {
          Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one team member is required.', confirmButtonColor: '#3085d6' });
          return;
        }
        const memberRow = btn.closest('tr, .member-card');
        const emailInput = memberRow.querySelector('input[name="member_email[]"]');
        if (emailInput && emailInput.value) addedMemberEmails.delete(emailInput.value);
      }
      // Prevent removing last activity
      if (btn.closest('.activity-row')) {
        const activityRows = document.querySelectorAll('.activity-row').length;
        if (activityRows <= 1) {
          Swal.fire({ icon: 'error', title: 'Cannot Remove', text: 'At least one activity is required.', confirmButtonColor: '#3085d6' });
          return;
        }
      }
      Swal.fire({ title: 'Are you sure?', text: "You won't be able to revert this!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes, remove it!' }).then((result) => {
        if (result.isConfirmed) {
          btn.closest('tr, .grid, .activity-row, .budget-row, .member-card').remove();
          Swal.fire('Removed!', 'The item has been removed.', 'success');
        }
      });
    });
    window._removeRowHandlerAttached = true;
  }
}

// Add a blank activity row only if none exist
function addBlankActivityRow() {
  const desktopContainer = document.getElementById('activitiesContainer');
  const mobileContainer = document.getElementById('activitiesContainerMobile');
  const desktopRows = desktopContainer ? desktopContainer.querySelectorAll('.activity-row').length : 0;
  const mobileRows = mobileContainer ? mobileContainer.querySelectorAll('.activity-row').length : 0;
  if (desktopRows === 0 && mobileRows === 0) {
    addActivityRow('', '', '', '', 'Planned');
  }
}

// Add a blank budget row only if none exist
function addBlankBudgetRow() {
  const desktopContainer = document.getElementById('budgetContainer');
  const mobileContainer = document.getElementById('budgetContainerMobile');
  const desktopRows = desktopContainer ? desktopContainer.querySelectorAll('.budget-row').length : 0;
  const mobileRows = mobileContainer ? mobileContainer.querySelectorAll('.budget-row').length : 0;
  if (desktopRows === 0 && mobileRows === 0) {
    addBudgetRow('', '', '', '');
  }
}

// Dedupe helpers
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
          if (!emptyFound) { emptyFound = true; } else { r.remove(); }
        }
      });
    }
    if (mobileContainer) {
      const cards = Array.from(mobileContainer.querySelectorAll('.activity-row'));
      let emptyFound = false;
      cards.forEach(c => {
        if (isRowEmpty(c)) {
          if (!emptyFound) { emptyFound = true; } else { c.remove(); }
        }
      });
    }
  } catch (e) { console.error('dedupeEmptyActivityRows error', e); }
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
      let emptyFound = false;
      rows.forEach(r => {
        if (isBudgetRowEmpty(r)) {
          if (!emptyFound) { emptyFound = true; } else { r.remove(); }
        }
      });
    }
    if (mobileContainer) {
      const cards = Array.from(mobileContainer.querySelectorAll('.budget-row'));
      let emptyFound = false;
      cards.forEach(c => {
        if (isBudgetRowEmpty(c)) {
          if (!emptyFound) { emptyFound = true; } else { c.remove(); }
        }
      });
    }
  } catch (e) { console.error('dedupeEmptyBudgetRows error', e); }
}

// DOMContentLoaded: attach remove, dedupe after population
document.addEventListener('DOMContentLoaded', function() {
  if (!dataPopulated) {
    attachRemoveButtons();
    dataPopulated = true;
  }
  setTimeout(() => {
    dedupeEmptyActivityRows();
    dedupeEmptyBudgetRows();
  }, 0);
});

// Save as Draft: dedupe before submit

// Single robust Save as Draft handler
document.addEventListener('DOMContentLoaded', function() {
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    let projectForm = document.getElementById('projectForm');
    
    // Find the form if it wasn't found by ID, traversing up from the button
    if (!projectForm && saveDraftBtn) {
        projectForm = saveDraftBtn.closest('form');
    }
    
    // Flag to prevent double submission
    let isSubmitting = false; 

    if (saveDraftBtn && projectForm) {
        saveDraftBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Stop default form submission
            
            if (isSubmitting) return;

            // 1. Show Confirmation Modal (using Swal.fire)
            Swal.fire({
                title: 'Save as Draft?',
                text: "Your project will be saved as a draft and can be edited later.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, save as draft!'
            }).then((result) => {
                // This code only runs AFTER the user interacts with the modal
                if (result.isConfirmed) {
                    isSubmitting = true; // Set flag to prevent double submission

                    // 2. Perform Clean-up (Remove Blank Rows)
                    
                    // --- Activity Rows Cleanup ---
                    document.querySelectorAll('.activity-row').forEach(row => {
                        // Select all inputs/textareas, excluding hidden fields and specific status fields
                        const inputs = Array.from(row.querySelectorAll('input:not([type="hidden"]), textarea, select')).filter(
                            input => !input.name || !input.name.startsWith('status[')
                        );
                        // Check if ALL filtered inputs are empty
                        const isEmpty = inputs.every(input => input.value.trim() === '');
                        if (isEmpty) row.remove();
                    });

                    // --- Budget Rows Cleanup ---
                    document.querySelectorAll('.budget-row, .proposal-table-row').forEach(row => {
                        // Select budget-related fields (adjust selector as needed)
                        const budgetInputs = Array.from(row.querySelectorAll('textarea[name^="budget_"], input[name^="budget_"]'));
                        
                        // Exclude status fields and ensure at least one budget field is present for check
                        const filtered = budgetInputs.filter(input => !input.name || !input.name.startsWith('status['));
                        
                        if (filtered.length > 0) {
                            const isEmpty = filtered.every(input => input.value.trim() === '');
                            if (isEmpty) row.remove();
                        }
                    });
                    
                    // 3. Call External/Helper Functions (assuming they are defined globally)
                    // The 'relaxRequiredForDraft' and 'prepareFormForSubmit' functions are run here.
                    if (typeof relaxRequiredForDraft === 'function') relaxRequiredForDraft(projectForm);
                    if (typeof dedupeEmptyActivityRows === 'function') dedupeEmptyActivityRows();
                    if (typeof dedupeEmptyBudgetRows === 'function') dedupeEmptyBudgetRows();
                    // Assuming removeAllEmptyBudgetRows is a global function
                    if (typeof removeAllEmptyBudgetRows === 'function') removeAllEmptyBudgetRows(); 
                    if (typeof prepareFormForSubmit === 'function') prepareFormForSubmit(projectForm);

                    // 4. Set Draft/Submit Flags
                    // Ensure you have these hidden inputs in your form:
                    const saveDraftInput = document.getElementById('saveDraftInput');
                    const submitProjectInput = document.getElementById('submitProjectInput');
                    
                    if (saveDraftInput) saveDraftInput.value = '1';
                    if (submitProjectInput) submitProjectInput.value = '0';
                    
                    // 5. Submit the Form
                    projectForm.submit();
                }
            });
            // The code that was previously here (cleanup and projectForm.submit())
            // has been REMOVED as it was causing the immediate, non-confirmed submission.
        });
    }
});


// Submit Project: dedupe before submit
safeAddListener('submitProjectBtn', 'click', function() {
  if (!validateFormRequirements()) return;
  dedupeEmptyActivityRows();
  dedupeEmptyBudgetRows();
  setTimeout(() => {
    dedupeEmptyActivityRows();
    dedupeEmptyBudgetRows();
  }, 0);
  showConfirmationModal();
});

  // Add Row for Activities
  document.getElementById('addActivityRow').addEventListener('click', () => {
    console.debug('[edit-form] addActivityRow button clicked');
    // Desktop table view - create markup identical to static row
    const desktopContainer = document.getElementById('activitiesContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'proposal-table-row activity-row flex items-center gap-4';
      newRow.innerHTML = `
        <div class="w-20 flex-none">
          <input name="stage[]" class="proposal-input w-full" placeholder="e.g., 1" required>
        </div>
        <div class="flex-1 px-2">
          <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
        </div>
        <div class="w-36 px-2 flex-none">
          <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" required>
        </div>
        <div class="w-44 px-2 flex-none">
          <input type="date" name="implementation_date[]" class="proposal-input w-full" required>
        </div>
        <div class="flex-1 px-2">
          <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
        </div>
        <div class="w-30 py-3 flex-none">
          <select name="status[]" class="proposal-select w-full">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
        </div>
        <div class="w-20 py-3 flex-none">
          <button type="button" class="proposal-remove-btn removeRow">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
      attachRemoveButtons();
    }


    // Mobile card view - keep existing card layout but ensure remove button has removeRow
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
      attachRemoveButtons();
    }
   
    attachRemoveButtons();
  });

    // Remove duplicate/extra empty activity rows, leaving at most one blank row
    function dedupeEmptyActivityRows() {
      try {
        const desktopContainer = document.getElementById('activitiesContainer');
        const mobileContainer = document.getElementById('activitiesContainerMobile');

        // Helper to determine if a row/card is empty (all inputs/textareas/selects blank)
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

        // Deduplicate desktop rows
        if (desktopContainer) {
          const rows = Array.from(desktopContainer.querySelectorAll('.proposal-table-row, .activity-row'));
          // Keep at most one empty row
          let emptyFound = false;
          rows.forEach(r => {
            if (isRowEmpty(r)) {
              if (!emptyFound) {
                emptyFound = true; // keep the first empty
              } else {
                r.remove();
              }
            }
          });
        }

        // Deduplicate mobile cards
        if (mobileContainer) {
          const cards = Array.from(mobileContainer.querySelectorAll('.activity-row'));
          let emptyFound = false;
          cards.forEach(c => {
            if (isRowEmpty(c)) {
              if (!emptyFound) {
                emptyFound = true;
              } else {
                c.remove();
              }
            }
          });
        }
      } catch (e) {
        // swallow errors to not block submission
        console.error('dedupeEmptyActivityRows error', e);
      }
    }

    // Remove duplicate/extra empty budget rows, leaving at most one blank row
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
          // Remove all empty rows except the last one
          const emptyRows = rows.filter(isBudgetRowEmpty);
          if (emptyRows.length > 1) {
            emptyRows.slice(0, -1).forEach(r => r.remove());
          }
        }

        if (mobileContainer) {
          const cards = Array.from(mobileContainer.querySelectorAll('.budget-row'));
          const emptyRows = cards.filter(isBudgetRowEmpty);
          if (emptyRows.length > 1) {
            emptyRows.slice(0, -1).forEach(c => c.remove());
          }
        }
      } catch (e) {
        console.error('dedupeEmptyBudgetRows error', e);
      }
    }



  // Add Row for Budget
  safeAddListener('addBudgetRow', 'click', function() {
    // Desktop table view - always append after the last budget row
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start budget-row';
      newRow.innerHTML = `
        <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity..."></textarea>
        <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed..."></textarea>
        <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations..."></textarea>
        <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00">
        <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
      `;
      // Always append at the end
      desktopContainer.appendChild(newRow);
    }

    // Mobile card view - always append after the last budget row
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
          <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Resources Needed"></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
          <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Partner Agencies"></textarea>
        </div>
        <div class="flex flex-col sm:flex-row gap-2">
          <div class="space-y-1 flex-1">
            <label class="block text-xs font-medium text-gray-600">Amount</label>
            <input type="text" name="budget_amount[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" placeholder="₱ 0.00">
          </div>
          <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs whitespace-nowrap">Remove</button>
        </div>
      `;
      // Always append at the end
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

  // Handle Save as Draft
  safeAddListener('saveDraftBtn', 'click', function() {
    Swal.fire({
      title: 'Save as Draft?',
      text: "Your project will be saved as a draft and can be edited later.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, save as draft!'
    }).then((result) => {
        if (result.isConfirmed) {
        const form = document.getElementById('projectForm');
        relaxRequiredForDraft(form);
        // Remove duplicate/extra empty activity and budget rows before submit
        dedupeEmptyActivityRows();
        dedupeEmptyBudgetRows();
        // Remove all empty budget rows before submit
        removeAllEmptyBudgetRows();
        prepareFormForSubmit(form);
        document.getElementById('saveDraftInput').value = '1';
        document.getElementById('submitProjectInput').value = '0';
        form.submit();
      }
    })
  });

  // Handle Cancel Edit with confirmation
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
        // Get the project ID from the current URL or form
        const projectId = '{{ $project->Project_ID }}';
        window.location.href = "{{ route('projects.show', $project) }}";
      }
    })
  });

  // Handle Submit Project with confirmation
  safeAddListener('submitProjectBtn', 'click', function() {
    if (!validateFormRequirements()) return;
    showConfirmationModal();
  });

  // Show confirmation modal with detailed project information
  function showConfirmationModal() {
    const form = document.getElementById('projectForm');
    const formData = new FormData(form);
    
    const projectName = formData.get('Project_Name') || 'N/A';
    const teamName = formData.get('Project_Team_Name') || 'N/A';
    const component = formData.get('Project_Component') || 'N/A';
    const section = formData.get('nstp_section') || 'N/A';
    
    const teamLogoFile = formData.get('Project_Logo');
    // Detect existing logo image (rendered on edit view)
    const hasExistingLogo = !!document.querySelector('img[alt="Current Logo"]');
    let teamLogoHTML = '<div class="text-sm text-gray-600">No file uploaded</div>';
    if (teamLogoFile && teamLogoFile.size > 0) {
      teamLogoHTML = `<div class="text-sm text-gray-600">${teamLogoFile.name} (${(teamLogoFile.size / 1024).toFixed(2)} KB)</div>`;
    }

    // Client-side check: if submitting and no uploaded or existing logo, show friendly message
    const submitting = true; // this modal is only shown when user intends to submit
    if (!hasExistingLogo && (!teamLogoFile || teamLogoFile.size === 0)) {
      Swal.fire({
        icon: 'warning',
        title: 'Logo Required',
        text: 'Submitting a project requires a team logo. Please upload a logo or save as draft.',
        confirmButtonColor: '#3085d6'
      });
      return;
    }
    
    const problems = formData.get('Project_Problems') || 'N/A';
    const goals = formData.get('Project_Goals') || 'N/A';
    const targetCommunity = formData.get('Project_Target_Community') || 'N/A';
    const solution = formData.get('Project_Solution') || 'N/A';
    const outcomes = formData.get('Project_Expected_Outcomes') || 'N/A';
    
    Swal.fire({
      title: '<div class="text-2xl font-bold text-gray-800">📋 Review Project Proposal</div>',
      html: `
        <div class="text-left space-y-4 max-h-[500px] overflow-y-auto px-3 py-2">
          <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
            <h3 class="font-bold text-blue-700 mb-3 text-lg flex items-center gap-2">
              <span>🖼️</span> Team Information
            </h3>
            <div class="space-y-2">
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Project Name</span>
                <div class="text-sm font-bold text-gray-800">${projectName}</div>
              </div>
            </div>
          </div>
        </div>
      `,
      width: '700px',
      showCancelButton: true,
      confirmButtonColor: '#2b50ff',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '✓ Proceed to Submit',
      cancelButtonText: '✕ Cancel',
      reverseButtons: true
    }).then((reviewResult) => {
      if (reviewResult.isConfirmed) {
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
            document.getElementById('saveDraftInput').value = '0';
            document.getElementById('submitProjectInput').value = '1';
            // Remove duplicate/extra empty activity and budget rows before final submit
            dedupeEmptyActivityRows();
            dedupeEmptyBudgetRows();
            removeAllEmptyBudgetRows();
            prepareFormForSubmit(form);
            form.submit();
          // Remove all empty budget rows (for submit)
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
          }
        });
      }
    });
  }

  // Save Project handler for editing pending/current projects (staff)
  const saveProjectBtn = document.getElementById('saveProjectBtn');
  if (saveProjectBtn) {
    saveProjectBtn.addEventListener('click', function() {
      const form = document.getElementById('projectForm');
      const currentStatus = this.dataset.currentStatus || '';
      if (currentStatus === 'pending') {
        document.getElementById('submitProjectInput').value = '1';
      } else {
        document.getElementById('submitProjectInput').value = '0';
      }
      document.getElementById('saveDraftInput').value = '0';
      // Remove duplicate/extra empty activity and budget rows before saving
      dedupeEmptyActivityRows();
      dedupeEmptyBudgetRows();
      prepareFormForSubmit(form);
      form.submit();
    });
  }

  // Disable inputs inside elements that are not displayed
  function prepareFormForSubmit(form) {
    form.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
    form.querySelectorAll('input, textarea, select').forEach(el => {
      try {
        const t = (el.type || '').toLowerCase();
        if (t === 'hidden') return;
        if (el.name && (el.name === '_token' || el.name === '_method')) return;
        if (el.offsetParent === null) {
          el.disabled = true;
        }
      } catch (e) {}
    });
  }

  // Remove `required` attributes from fields for draft saving
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
        if (el.hasAttribute('required')) el.removeAttribute('required');
      });
    });
  }

  // Helper to safely add event listeners
  function safeAddListener(id, event, handler) {
    const el = document.getElementById(id);
    if (el) el.addEventListener(event, handler);
  }

  // Member modal handlers
  safeAddListener('openMemberModal', 'click', function() {
    loadMemberList();
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.remove('hidden');
  });

  safeAddListener('openMemberModalMobile', 'click', function() {
    loadMemberList();
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.remove('hidden');
  });

  safeAddListener('closeMemberModal', 'click', function() {
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.add('hidden');
  });

  safeAddListener('cancelMemberSelection', 'click', function() {
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.add('hidden');
  });

  // Load member list from same section and component
  function loadMemberList() {
    const memberList = document.getElementById('memberList');
    if (!memberList) return;
    memberList.innerHTML = '<p class="text-center text-gray-500">Loading members...</p>';
   
    const existingMemberEmails = Array.from(addedMemberEmails);
    const url = new URL('{{ route("projects.students.same-section") }}', window.location.origin);
    existingMemberEmails.forEach(email => {
      url.searchParams.append('existing_members[]', email);
    });
   
    fetch(url)
      .then(response => response.json())
      .then(students => {
        if (students.length === 0) {
          memberList.innerHTML = '<p class="text-center text-gray-500">No students found in your section and component.</p>';
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

  // Add selected members to the form
  safeAddListener('addSelectedMembers', 'click', function() {
    const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
   
    selectedMembers.forEach(checkbox => {
      const memberId = checkbox.value;
      const memberName = checkbox.dataset.name;
      const memberEmail = checkbox.dataset.email;
      const memberContact = checkbox.dataset.contact;
     
      addedMemberEmails.add(memberEmail);
     
      // Add to desktop table view
      const desktopTable = document.querySelector('#memberTable tbody');
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
            <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberContact}" required>
          </td>
          <td class="px-6 py-4 text-center">
            <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
              Remove
            </button>
          </td>
        `;
        desktopTable.appendChild(newRow);
      }
      
      // Add to mobile card view
      const mobileContainer = document.getElementById('memberContainer');
      if (mobileContainer) {
        const newCard = document.createElement('div');
        newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
        newCard.innerHTML = `
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
            <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${memberName}" readonly>
            <input type="hidden" name="member_student_id[]" value="${memberId}">
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
            <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
            <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${memberEmail}" readonly>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
            <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${memberContact}" readonly>
          </div>
          <div class="flex justify-end">
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
          </div>
        `;
        mobileContainer.appendChild(newCard);
      }
    });
   
    attachRemoveButtons();
    document.getElementById('memberModal').classList.add('hidden');
  });
  // Utility functions
  function addBlankBudgetRow() {
    addBudgetRow('', '', '', '');
  }

  function addBudgetRow(activity, resources, partners, amount) {
    let displayAmount = '';
    if (amount !== null && amount !== undefined && amount !== '') {
      if (!isNaN(amount)) {
        displayAmount = parseFloat(amount).toFixed(2);
      } else {
        displayAmount = amount;
      }
    }
    
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'budget-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <textarea name="budget_activity[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity...">${activity || ''}</textarea>
          <textarea name="budget_resources[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed...">${resources || ''}</textarea>
          <textarea name="budget_partners[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations...">${partners || ''}</textarea>
          <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="0.00" value="${displayAmount}">
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }
  }

  // Validate minimum requirements for form submission
  function validateFormRequirements() {
    const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
    const memberCardRows = document.querySelectorAll('.member-card').length;
    const totalMemberRows = memberTableRows + memberCardRows;
    
    if (totalMemberRows < 1) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'At least one team member is required.',
        confirmButtonColor: '#3085d6'
      });
      return false;
    }

    const activityRows = document.querySelectorAll('.activity-row').length;
    if (activityRows < 1) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'At least one activity is required.',
        confirmButtonColor: '#3085d6'
      });
      return false;
    }

    return true;
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    if (!dataPopulated) {
      attachRemoveButtons();
      dataPopulated = true;
    }
  });
</script>
<script>
  // Keep track of added member emails to prevent duplicates
  let addedMemberEmails = new Set();
  // Keep track of whether data has already been populated to prevent duplicates
  let dataPopulated = false;

  // Initialize addedMemberEmails with existing member emails
  document.addEventListener('DOMContentLoaded', function() {
    // Only collect member emails if we haven't already done so
    if (addedMemberEmails.size === 0) {
      // Collect existing member emails
      document.querySelectorAll('input[name="member_email[]"]').forEach(input => {
        if (input.value) {
          addedMemberEmails.add(input.value);
        }
      });
    }
    attachRemoveButtons();
  });

  // helper: remove row when button is clicked with SweetAlert2 confirmation
  function attachRemoveButtons() {
    document.querySelectorAll('.removeRow').forEach(btn => {
      btn.onclick = function() {
        // Check if this is an attempt to remove the last member
        if (btn.closest('#memberTable tbody tr') || btn.closest('.member-card')) {
          const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
          const memberCardRows = document.querySelectorAll('.member-card').length;
          const totalMemberRows = memberTableRows + memberCardRows;
          
          if (totalMemberRows <= 1) {
            Swal.fire({
              icon: 'error',
              title: 'Cannot Remove',
              text: 'At least one team member is required.',
              confirmButtonColor: '#3085d6'
            });
            return;
          }
          
          // Remove member email from addedMemberEmails set when removing a member
          const memberRow = btn.closest('tr, .member-card');
          const emailInput = memberRow.querySelector('input[name="member_email[]"]');
          if (emailInput && emailInput.value) {
            addedMemberEmails.delete(emailInput.value);
          }
        }
        
        // Check if this is an attempt to remove the last activity
        if (btn.closest('.activity-row')) {
          const activityRows = document.querySelectorAll('.activity-row').length;
          if (activityRows <= 1) {
            Swal.fire({
              icon: 'error',
              title: 'Cannot Remove',
              text: 'At least one activity is required.',
              confirmButtonColor: '#3085d6'
            });
            return;
          }
        }
        
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
            btn.closest('tr, .grid, .activity-row, .budget-row, .member-card').remove();
            Swal.fire(
              'Removed!',
              'The item has been removed.',
              'success'
            )
          }
        })
      };
    });
  }

  // Add Row for Activities
  document.getElementById('addActivityRow').addEventListener('click', () => {
    // Desktop table view - create markup identical to static row
    const desktopContainer = document.getElementById('activitiesContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'proposal-table-row flex items-center gap-4';
      newRow.innerHTML = `
        <div class="w-12 flex-none">
          <input name="stage[]" class="proposal-input w-full" placeholder="e.g., Planning" required>
        </div>
        <div class="flex-1 px-2">
          <textarea name="activities[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
        </div>
        <div class="w-36 px-2 flex-none">
          <input name="timeframe[]" class="proposal-input w-full" placeholder="e.g., Week 1-2" required>
        </div>
        <div class="w-36 px-2 flex-none">
          <input type="date" name="implementation_date[]" class="proposal-input w-full" required>
        </div>
        <div class="flex-1 px-2">
          <textarea name="point_person[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
        </div>
        <div class="w-32 px-2 flex-none">
          <select name="status[]" class="proposal-select w-full">
            <option>Planned</option>
            <option>Ongoing</option>
          </select>
        </div>
        <div class="w-24 px-2 flex-none">
          <button type="button" class="proposal-remove-btn removeRow">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }


    // Mobile card view - keep existing card layout but ensure remove button has removeRow
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
   
    attachRemoveButtons();
  });



  // Add Row for Budget
  safeAddListener('addBudgetRow', 'click', function() {
    // Desktop table view - create markup identical to static budget row
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'proposal-table-row grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start';
      newRow.innerHTML = `
        <textarea name="budget_activity[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Describe the activity..."></textarea>
        <textarea name="budget_resources[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="List resources needed..."></textarea>
        <textarea name="budget_partners[]" class="proposal-textarea w-full resize-none" rows="2" placeholder="Partner organizations..."></textarea>
        <input type="text" name="budget_amount[]" class="proposal-input w-full" placeholder="₱ 0.00">
        <button type="button" class="proposal-remove-btn removeRow whitespace-nowrap">Remove</button>
      `;
      desktopContainer.appendChild(newRow);
    }

    // Mobile card view - mirror existing mobile layout and ensure remove button has removeRow
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
          <textarea name="budget_resources[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Resources Needed"></textarea>
        </div>
        <div class="space-y-1">
          <label class="block text-xs font-medium text-gray-600">Partner Agencies</label>
          <textarea name="budget_partners[]" class="w-full rounded-md border-2 border-gray-400 px-2 py-1 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" rows="2" placeholder="Partner Agencies"></textarea>
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

    attachRemoveButtons();
  });

  // Auto-expand textarea
  document.addEventListener("input", function (e) {
    if (e.target.classList.contains("auto-expand")) {
      e.target.style.height = "auto";
      e.target.style.height = e.target.scrollHeight + "px";
    }
  });

  // Handle Save as Draft
  safeAddListener('saveDraftBtn', 'click', function() {
    Swal.fire({
      title: 'Save as Draft?',
      text: "Your project will be saved as a draft and can be edited later.",
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, save as draft!'
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.getElementById('projectForm');
        relaxRequiredForDraft(form);
        prepareFormForSubmit(form);
        document.getElementById('saveDraftInput').value = '1';
        document.getElementById('submitProjectInput').value = '0';
        form.submit();
      }
    })
  });

  // Handle Cancel Edit with confirmation
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
        // Get the project ID from the current URL or form
        const projectId = '{{ $project->Project_ID }}';
        window.location.href = "{{ route('projects.show', $project) }}";
      }
    })
  });

  // Handle Submit Project with confirmation
  safeAddListener('submitProjectBtn', 'click', function() {
    if (!validateFormRequirements()) return;
    showConfirmationModal();
  });

  // Show confirmation modal with detailed project information
  function showConfirmationModal() {
    const form = document.getElementById('projectForm');
    const formData = new FormData(form);
    
    const projectName = formData.get('Project_Name') || 'N/A';
    const teamName = formData.get('Project_Team_Name') || 'N/A';
    const component = formData.get('Project_Component') || 'N/A';
    const section = formData.get('nstp_section') || 'N/A';
    
    const teamLogoFile = formData.get('Project_Logo');
    let teamLogoHTML = '<div class="text-sm text-gray-600">No file uploaded</div>';
    if (teamLogoFile && teamLogoFile.size > 0) {
      teamLogoHTML = `<div class="text-sm text-gray-600">${teamLogoFile.name} (${(teamLogoFile.size / 1024).toFixed(2)} KB)</div>`;
    }
    
    const problems = formData.get('Project_Problems') || 'N/A';
    const goals = formData.get('Project_Goals') || 'N/A';
    const targetCommunity = formData.get('Project_Target_Community') || 'N/A';
    const solution = formData.get('Project_Solution') || 'N/A';
    const outcomes = formData.get('Project_Expected_Outcomes') || 'N/A';
    
    Swal.fire({
      title: '<div class="text-2xl font-bold text-gray-800">📋 Review Project Proposal</div>',
      html: `
        <div class="text-left space-y-4 max-h-[500px] overflow-y-auto px-3 py-2">
          <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
            <h3 class="font-bold text-blue-700 mb-3 text-lg flex items-center gap-2">
              <span>🖼️</span> Team Information
            </h3>
            <div class="space-y-2">
              <div class="bg-white p-2 rounded">
                <span class="text-xs text-gray-500 uppercase font-semibold">Project Name</span>
                <div class="text-sm font-bold text-gray-800">${projectName}</div>
              </div>
            </div>
          </div>
        </div>
      `,
      width: '700px',
      showCancelButton: true,
      confirmButtonColor: '#2b50ff',
      cancelButtonColor: '#6b7280',
      confirmButtonText: '✓ Proceed to Submit',
      cancelButtonText: '✕ Cancel',
      reverseButtons: true
    }).then((reviewResult) => {
      if (reviewResult.isConfirmed) {
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
            document.getElementById('saveDraftInput').value = '0';
            document.getElementById('submitProjectInput').value = '1';
            prepareFormForSubmit(form);
            form.submit();
          }
        });
      }
    });
  }

  // Save Project handler for editing pending/current projects (staff)
  const saveProjectBtn = document.getElementById('saveProjectBtn');
  if (saveProjectBtn) {
    saveProjectBtn.addEventListener('click', function() {
      const form = document.getElementById('projectForm');
      const currentStatus = this.dataset.currentStatus || '';
      if (currentStatus === 'pending') {
        document.getElementById('submitProjectInput').value = '1';
      } else {
        document.getElementById('submitProjectInput').value = '0';
      }
      document.getElementById('saveDraftInput').value = '0';
      prepareFormForSubmit(form);
      form.submit();
    });
  }

  // Disable inputs inside elements that are not displayed
  function prepareFormForSubmit(form) {
    form.querySelectorAll('input, textarea, select').forEach(el => el.disabled = false);
    form.querySelectorAll('input, textarea, select').forEach(el => {
      try {
        const t = (el.type || '').toLowerCase();
        if (t === 'hidden') return;
        if (el.name && (el.name === '_token' || el.name === '_method')) return;
        if (el.offsetParent === null) {
          el.disabled = true;
        }
      } catch (e) {}
    });
  }

  // Remove `required` attributes from fields for draft saving
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
        if (el.hasAttribute('required')) el.removeAttribute('required');
      });
    });
  }

  // Helper to safely add event listeners
  function safeAddListener(id, event, handler) {
    const el = document.getElementById(id);
    if (el) el.addEventListener(event, handler);
  }

  // Member modal handlers
  safeAddListener('openMemberModal', 'click', function() {
    loadMemberList();
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.remove('hidden');
  });

  safeAddListener('openMemberModalMobile', 'click', function() {
    loadMemberList();
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.remove('hidden');
  });

  safeAddListener('closeMemberModal', 'click', function() {
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.add('hidden');
  });

  safeAddListener('cancelMemberSelection', 'click', function() {
    const mm = document.getElementById('memberModal');
    if (mm) mm.classList.add('hidden');
  });

  // Load member list from same section and component
  function loadMemberList() {
    const memberList = document.getElementById('memberList');
    if (!memberList) return;
    memberList.innerHTML = '<p class="text-center text-gray-500">Loading members...</p>';
   
    const existingMemberEmails = Array.from(addedMemberEmails);
    const url = new URL('{{ route("projects.students.same-section") }}', window.location.origin);
    existingMemberEmails.forEach(email => {
      url.searchParams.append('existing_members[]', email);
    });
   
    fetch(url)
      .then(response => response.json())
      .then(students => {
        if (students.length === 0) {
          memberList.innerHTML = '<p class="text-center text-gray-500">No students found in your section and component.</p>';
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

  // Add selected members to the form
  safeAddListener('addSelectedMembers', 'click', function() {
    const selectedMembers = document.querySelectorAll('input[name="available_members[]"]:checked');
   
    selectedMembers.forEach(checkbox => {
      const memberId = checkbox.value;
      const memberName = checkbox.dataset.name;
      const memberEmail = checkbox.dataset.email;
      const memberContact = checkbox.dataset.contact;
     
      addedMemberEmails.add(memberEmail);
     
      // Add to desktop table view
      const desktopTable = document.querySelector('#memberTable tbody');
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
            <input type="tel" name="member_contact[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" value="${memberContact}" required>
          </td>
          <td class="px-6 py-4 text-center">
            <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
              Remove
            </button>
          </td>
        `;
        desktopTable.appendChild(newRow);
      }
      
      // Add to mobile card view
      const mobileContainer = document.getElementById('memberContainer');
      if (mobileContainer) {
        const newCard = document.createElement('div');
        newCard.className = 'member-card bg-white p-3 rounded-lg border-2 border-gray-400 shadow-sm space-y-3';
        newCard.innerHTML = `
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Name <span class="text-red-500">*</span></label>
            <input name="member_name[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${memberName}" readonly>
            <input type="hidden" name="member_student_id[]" value="${memberId}">
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Role/s <span class="text-red-500">*</span></label>
            <input name="member_role[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">School Email <span class="text-red-500">*</span></label>
            <input type="email" name="member_email[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${memberEmail}" readonly>
          </div>
          <div class="space-y-1">
            <label class="block text-xs font-medium text-gray-600">Contact Number <span class="text-red-500">*</span></label>
            <input type="tel" name="member_contact[]" class="w-full px-2 py-1 border-2 border-gray-400 rounded text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors" required value="${memberContact}" readonly>
          </div>
          <div class="flex justify-end">
            <button type="button" class="removeRow bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-xs">Remove</button>
          </div>
        `;
        mobileContainer.appendChild(newCard);
      }
    });
   
    attachRemoveButtons();
    document.getElementById('memberModal').classList.add('hidden');
  });
  // Utility functions
  function addBlankBudgetRow() {
    addBudgetRow('', '', '', '');
  }

  function addBudgetRow(activity, resources, partners, amount) {
    let displayAmount = '';
    if (amount !== null && amount !== undefined && amount !== '') {
      if (!isNaN(amount)) {
        displayAmount = parseFloat(amount).toFixed(2);
      } else {
        displayAmount = amount;
      }
    }
    
    const desktopContainer = document.getElementById('budgetContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'budget-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <textarea name="budget_activity[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe the activity...">${activity || ''}</textarea>
          <textarea name="budget_resources[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="List resources needed...">${resources || ''}</textarea>
          <textarea name="budget_partners[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Partner organizations...">${partners || ''}</textarea>
          <input type="text" name="budget_amount[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="0.00" value="${displayAmount}">
          <button type="button" class="removeRow bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm whitespace-nowrap">Remove</button>
        </div>
      `;
      desktopContainer.appendChild(newRow);
    }
  }

  // Validate minimum requirements for form submission
  function validateFormRequirements() {
    const memberTableRows = document.querySelectorAll('#memberTable tbody tr').length;
    const memberCardRows = document.querySelectorAll('.member-card').length;
    const totalMemberRows = memberTableRows + memberCardRows;
    
    if (totalMemberRows < 1) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'At least one team member is required.',
        confirmButtonColor: '#3085d6'
      });
      return false;
    }

    const activityRows = document.querySelectorAll('.activity-row').length;
    if (activityRows < 1) {
      Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: 'At least one activity is required.',
        confirmButtonColor: '#3085d6'
      });
      return false;
    }

    return true;
  }

  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    if (!dataPopulated) {
      attachRemoveButtons();
      dataPopulated = true;
    }
  });
</script>
