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
  safeAddListener('addActivityRow', 'click', function() {
    // Desktop table view
    const desktopContainer = document.getElementById('activitiesContainer');
    if (desktopContainer) {
      const newRow = document.createElement('div');
      newRow.className = 'activity-row hover:bg-gray-50 transition-colors px-6 py-4';
      newRow.innerHTML = `
        <div class="grid grid-cols-[1fr_2fr_2fr_2fr_1fr_auto] gap-4 items-start">
          <input name="stage[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Planning" required>
          <textarea name="activities[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Describe specific activities..." required></textarea>
          <input name="timeframe[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm" placeholder="e.g., Week 1-2" required>
          <textarea name="point_person[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm resize-none" rows="2" placeholder="Responsible person/s" required></textarea>
          <select name="status[]" class="w-full px-3 py-2 border-2 border-gray-400 rounded-lg focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition-colors text-sm">
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
    // Add a blank budget row using the existing function that prevents duplicates
    addBlankBudgetRow();
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

  // Save Project handler for editing submitted/current projects (staff)
  const saveProjectBtn = document.getElementById('saveProjectBtn');
  if (saveProjectBtn) {
    saveProjectBtn.addEventListener('click', function() {
      const form = document.getElementById('projectForm');
      const currentStatus = this.dataset.currentStatus || '';
      if (currentStatus === 'submitted') {
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
