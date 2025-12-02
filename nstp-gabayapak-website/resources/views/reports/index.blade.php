@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<section id="reports" class="bg-white rounded-2xl shadow-subtle p-4 md:p-8">
    <style>
      /* Hide the global sidebar and its overlay when printing/download PDF */
      @media print {
        #sidebar, #sidebar-overlay { display: none !important; }
        /* Avoid capturing interactive controls in print header */
        .no-print { display: none !important; }
        /* Hide mobile menu button when printing/downloading PDF */
        #mobileMenuBtn { display: none !important; }
      }
      
      /* Prevent action buttons (eg. "View Project") from being squished when the
         adjacent title/description is long. This targets common alert/notice/banner
         patterns and makes the text area break/wrap while keeping the button size. */
      .banner-flex,
      .alert,
      .notice,
      .project-banner,
      .info-banner,
      .attached-project {
        display: flex;
        gap: 1rem;
        align-items: center;
        justify-content: space-between;
        flex-wrap: nowrap;
      }

      .banner-flex .banner-text,
      .alert .banner-text,
      .notice .banner-text,
      .project-banner .banner-text,
      .info-banner .banner-text,
      .attached-project .banner-text {
        flex: 1 1 auto;
        min-width: 0; /* allow the text to shrink and wrap correctly inside flex */
        overflow-wrap: anywhere;
        word-break: break-word;
      }

      .banner-flex .btn,
      .alert .btn,
      .notice .btn,
      .project-banner .btn,
      .info-banner .btn,
      .attached-project .btn {
        flex: 0 0 auto; /* don't let the button shrink */
        white-space: nowrap; /* keep button text on one line */
      }

      /* Utility: make long sentences break at a chosen spot using <wbr> or by adding
         a span with class 'banner-text' around the message in your template. */
      
      /* Mobile optimizations for small screens (360x600 and similar) */
      @media (max-width: 400px) {
        /* Adjust header and buttons */
        #reports h2 {
          font-size: 1.5rem !important;
        }
        
        #reports > div:first-of-type p {
          font-size: 0.75rem !important;
        }
        
        /* Make buttons smaller on very small screens */
        #reports button {
          font-size: 0.75rem !important;
          padding: 0.5rem 0.75rem !important;
        }
        
        /* Filter adjustments */
        #filterWrapper {
          flex-direction: column !important;
          gap: 0.5rem !important;
        }
        
        #filterWrapper > div {
          width: 100% !important;
        }
        
        #filterWrapper select {
          width: 100% !important;
          font-size: 0.75rem !important;
          padding: 0.5rem !important;
        }
        
        #filterWrapper label {
          font-size: 0.75rem !important;
        }
        
        #clearFiltersBtn {
          width: 100% !important;
          margin-left: 0 !important;
          margin-top: 0.25rem !important;
        }
        
        /* Cards adjustments */
        .grid.grid-cols-2 {
          gap: 0.5rem !important;
        }
        
        .rounded-xl {
          padding: 0.5rem !important;
        }
        
        .rounded-xl p:first-child {
          font-size: 0.65rem !important;
        }
        
        .rounded-xl p:nth-child(2) {
          font-size: 1.5rem !important;
        }
        
        .rounded-xl p:last-child {
          font-size: 0.6rem !important;
        }
        
        /* Section headings */
        h3 {
          font-size: 1.125rem !important;
        }
      }
    </style>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 md:mb-6 gap-4">
      <div>
        <h2 class="text-2xl md:text-3xl font-bold">Reports</h2>
        <p class="text-gray-600 text-sm md:text-base">Summary overview of NSTP project submissions and progress</p>
      </div>
      <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-wrap z-10 items-center">
        <button onclick="downloadCSV()" class="w-full sm:w-auto px-3 py-2 md:px-4 md:py-2 bg-blue-500 text-white rounded-lg text-xs md:text-sm shadow-subtle hover:bg-blue-600 text-center">
          Export CSV
        </button>
        <button onclick="downloadPDF()" class="w-full sm:w-auto px-3 py-2 md:px-4 md:py-2 bg-red-500 text-white rounded-lg text-xs md:text-sm shadow-subtle  hover:bg-red-600 text-center">
          Download PDF
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col sm:flex-row gap-3 items-end mb-4">
      <div id="filterWrapper" class="flex gap-3 w-full sm:w-auto">
        <div>
          <label class="text-sm text-gray-600">Component</label>
          <select id="filterComponent" class="mt-1 block rounded-lg border-gray-200 px-3 py-2 text-sm">
            <option value="All">All Components</option>
          </select>
        </div>
        <div id="sectionFilterContainer">
          <label class="text-sm text-gray-600">Section</label>
          <select id="filterSection" class="mt-1 block rounded-lg border-gray-200 px-3 py-2 text-sm">
            <option value="All">All Sections</option>
          </select>
        </div>
        <div class="flex items-end">
          <button id="clearFiltersBtn" onclick="clearFilters()" class="mt-1 ml-2 px-3 py-2 rounded-lg border border-gray-200 text-sm text-gray-700 hover:bg-gray-100">Clear</button>
        </div>
      </div>
    </div>

    <!-- Project Proposals Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mb-6 md:mb-8">
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Approved Proposals</p>
        <p id="approvedProposals" class="text-xl md:text-3xl font-bold text-green-600">{{ $project_proposals['approved'] }}</p>
        <p id="approvedProposalsPct" class="text-xs text-gray-400">{{ $project_proposals['total'] ? round(($project_proposals['approved'] / $project_proposals['total']) * 100, 1) : 0 }}% of total</p>
      </div>
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Pending Proposals</p>
        <p id="pendingProposals" class="text-xl md:text-3xl font-bold text-orange-600">{{ $project_proposals['pending'] }}</p>
        <p id="pendingProposalsPct" class="text-xs text-gray-400">{{ $project_proposals['total'] ? round(($project_proposals['pending'] / $project_proposals['total']) * 100, 1) : 0 }}% of total</p>
      </div>
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Draft Proposals</p>
        <p id="draftProposals" class="text-xl md:text-3xl font-bold text-yellow-400">{{ $project_proposals['draft'] }}</p>
        <p id="draftProposalsPct" class="text-xs text-gray-400">{{ $project_proposals['total'] ? round(($project_proposals['draft'] / $project_proposals['total']) * 100, 1) : 0 }}% of total</p>
      </div>
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Total Proposals</p>
        <p id="totalProposals" class="text-3xl font-bold text-black">{{ $project_proposals['total'] }}</p>
      </div>
    </div>

    <!-- Project Status Summary -->
    <h3 class="text-xl md:text-2xl font-bold mt-6 md:mt-8 mb-3 md:mb-4">Project Implementation Status</h3>
    <div class="grid grid-cols-3 sm:grid-cols-3 gap-3 md:gap-4 mb-6 md:mb-8">
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Ongoing</p>
        <p id="statusOngoing" class="text-2xl md:text-3xl font-bold text-blue-600">{{ $project_status['ongoing'] }}</p>
      </div>
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Completed</p>
        <p id="statusCompleted" class="text-2xl md:text-3xl font-bold text-green-600">{{ $project_status['completed'] }}</p>
      </div>
      <div class="rounded-xl border bg-white p-3 md:p-4 shadow-subtle text-center">
        <p class="text-xs md:text-sm text-black">Archived</p>
        <p id="statusArchived" class="text-2xl md:text-3xl font-bold text-gray-500">{{ $project_status['archived'] }}</p>
      </div>
    </div>

    <!-- Component Breakdown -->
    <h3 class="text-xl md:text-2xl font-bold mt-6 md:mt-8 mb-3 md:mb-4">Total Projects per Component</h3>
      <div class="flex justify-center items-center mb-6 md:mb-8">
      <div class="w-full md:max-w-xs">
        <canvas id="componentChart" width="200" height="200" class="max-w-full"></canvas>
        <div id="chartMessage" class="text-center text-gray-600 mt-3" style="display:none;"></div>
      </div>
    </div>

    <!-- Project Progress -->
    <h3 class="text-xl md:text-2xl font-bold mt-6 md:mt-8 mb-3 md:mb-4">Project Progress</h3>
    <div class="space-y-4">
      @foreach ($project_progress as $proj)
        @php
          $color = '#2b50ff'; // default blue for ROTC
          if ($proj['component'] === 'LTS') $color = '#f2d35b';
          elseif ($proj['component'] === 'CWTS') $color = '#e63946';
        @endphp
        <div>
          <div class="flex justify-between text-sm font-medium text-gray-700">
            <span>{{ $proj['name'] }} ({{ $proj['component'] }})</span>
            <span>{{ $proj['progress'] }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-3 mt-1">
            <div class="h-3 rounded-full" style="background-color: {{ $color }}; width: {{ $proj['progress'] }}%"></div>
          </div>
          <div class="text-sm text-gray-600 mt-1">
            Total Budget: <span class="font-bold text-gray-900 text-base">₱{{ number_format($proj['budget'], 2) }}</span>
          </div>
        </div>
      @endforeach
    </div>

  </section>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script>
      // Embed projects data (used for filtering). Expect each project to have at least:
      // { name, component, progress, budget, section?, proposal_status?, status? }
      const projectsDataRaw = {!! json_encode($project_progress) !!};

      // Only include projects that are completed, archived, OR approved for chart/progress
      // (we treat approved proposals as 'ongoing' proposals that should appear in reports,
      // and archived projects should also appear in completed/progress views)
      function isCompletedAndApproved(p) {
        return p && (
          p.status === 'completed' ||
          p.status === 'archived' ||
          p.status === 'approved' ||
          p.proposal_status === 'approved'
        );
      }

      // For filters and summaries we derive two sets depending on need:
      // - projectsDataRaw: all projects embedded from server
      // - eligibleProjects: projects that are completed && approved (used for chart/progress)

      // Build initial component and section lists from the full raw projects data (so filters apply to full scope)
      const uniqueComponents = Array.from(new Set(projectsDataRaw.map(p => p.component))).filter(Boolean);
      const componentSelect = document.getElementById('filterComponent');
      uniqueComponents.sort().forEach(c => {
        const o = document.createElement('option'); o.value = c; o.text = c; componentSelect.appendChild(o);
      });

      const uniqueSections = Array.from(new Set(projectsDataRaw.map(p => p.section).filter(Boolean))).filter(Boolean);
      const sectionSelect = document.getElementById('filterSection');
      if (uniqueSections.length === 0) {
        // Hide section filter when no section data is present
        document.getElementById('sectionFilterContainer').style.display = 'none';
      } else {
        uniqueSections.sort().forEach(s => {
          const o = document.createElement('option'); o.value = s; o.text = s; sectionSelect.appendChild(o);
        });
      }

      // Helper to compute counts per component from a projects array
      function computeComponentCounts(arr) {
        const counts = {};
        arr.forEach(p => {
          const k = p.component || 'Unknown';
          counts[k] = (counts[k] || 0) + 1;
        });
        return counts;
      }

      // Color mapping used for chart
      const colorMap = { 'CWTS': '#e63946', 'LTS': '#f2d35b', 'ROTC': '#2b50ff', 'NSTP': '#2b50ff' };

      // Initialize chart with eligible (completed+approved) data
      const initialCounts = computeComponentCounts(projectsDataRaw.filter(isCompletedAndApproved));
      const chartLabels = Object.keys(initialCounts);
      const chartValues = Object.values(initialCounts);
      const ctx = document.getElementById('componentChart').getContext('2d');
      // Register datalabels plugin so labels appear on the chart
      if (typeof ChartDataLabels !== 'undefined') {
        Chart.register(ChartDataLabels);
      }
      const componentChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: chartLabels,
          datasets: [{
            data: chartValues,
            backgroundColor: chartLabels.map(l => colorMap[l] || '#2b50ff'),
            borderColor: '#fff',
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          plugins: {
            legend: { position: 'bottom' },
            datalabels: {
              color: '#ffffff',
              formatter: (value, ctx) => {
                // show raw count and percentage on separate lines
                const sum = ctx.chart.data.datasets[0].data.reduce((a,b) => a + b, 0) || 0;
                const pct = sum ? Math.round((value / sum) * 1000) / 10 : 0;
                return value + '\n' + pct + '%';
              },
              font: { weight: '600', size: 11 },
              anchor: 'center',
              align: 'center',
              clamp: true
            }
          }
        }
      });

      // Render project progress list from an array of projects
      function renderProjectProgress(arr) {
        const container = document.querySelector('#reports .space-y-4');
        container.innerHTML = '';
        arr.forEach(proj => {
          // Use red fill for CWTS progress bars, yellow for LTS
          const color = proj.component === 'LTS' ? '#f2d35b' : (proj.component === 'CWTS' ? '#e63946' : '#2b50ff');
          const div = document.createElement('div');
          div.innerHTML = `
            <div class="flex justify-between text-sm font-medium text-gray-700">
              <span>${proj.name} (${proj.component}${proj.section ? ' — ' + proj.section : ''})</span>
              <span>${proj.progress}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 mt-1">
              <div class="h-3 rounded-full" style="background-color: ${color}; width: ${proj.progress}%"></div>
            </div>
            <div class="text-sm text-gray-600 mt-1">
              Total Budget: <span class="font-bold text-gray-900 text-base">₱${Number(proj.budget).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2})}</span>
            </div>
          `;
          container.appendChild(div);
        });
        // If there are no projects to render, show an informative message
        if (!arr.length) {
          const comp = (document.getElementById('filterComponent') || {}).value || 'All';
          const secEl = document.getElementById('filterSection');
          const sec = secEl ? secEl.value : 'All';
          container.innerHTML = `<div class="text-center text-gray-600 py-6">No data for ${comp} - ${sec}</div>`;
          return;
        }
      }

      // Update summary cards if proposal/status fields exist in data
      // This function expects `baseArr` to be the filtered raw dataset (respecting component/section filters)
      // It will also handle empty arrays and set counts to zero accordingly.
      function updateSummaryCards(baseArr) {
        // Detect whether the raw embedded data contains proposal/status fields
        const hasProposalStatus = projectsDataRaw && projectsDataRaw.length && projectsDataRaw[0].hasOwnProperty('proposal_status');
        const hasStatus = projectsDataRaw && projectsDataRaw.length && projectsDataRaw[0].hasOwnProperty('status');

        // Use safe defaults when baseArr is empty
        const total = baseArr.length || 0;
        if (hasProposalStatus) {
          const approved = baseArr.filter(p => p.proposal_status === 'approved' || p.status === 'completed' || p.status === 'archived').length || 0;
          const pending = baseArr.filter(p => p.proposal_status === 'pending').length || 0;
          const draft = baseArr.filter(p => p.proposal_status === 'draft').length || 0;
          document.getElementById('approvedProposals').textContent = approved;
          document.getElementById('pendingProposals').textContent = pending;
          document.getElementById('draftProposals').textContent = draft;
          document.getElementById('totalProposals').textContent = total;
          document.getElementById('approvedProposalsPct').textContent = total ? (Math.round((approved/total)*1000)/10) + '% of total' : '0% of total';
          document.getElementById('pendingProposalsPct').textContent = total ? (Math.round((pending/total)*1000)/10) + '% of total' : '0% of total';
          document.getElementById('draftProposalsPct').textContent = total ? (Math.round((draft/total)*1000)/10) + '% of total' : '0% of total';
        }

        if (hasStatus) {
          // Ongoing should count both projects explicitly 'ongoing' and proposals that are 'approved'
          const ongoing = baseArr.filter(p => p.status === 'ongoing' || p.proposal_status === 'approved').length || 0;
          // Count archived as completed for the completed metric
          const completed = baseArr.filter(p => p.status === 'completed' || p.status === 'archived').length || 0;
          const archived = baseArr.filter(p => p.status === 'archived').length || 0;
          document.getElementById('statusOngoing').textContent = ongoing;
          document.getElementById('statusCompleted').textContent = completed;
          document.getElementById('statusArchived').textContent = archived;
        }
      }

      // Keep track of current filtered projects for CSV export
      // Initialize to eligible (completed+approved) projects to avoid undefined variable errors
      let currentFilteredProjects = projectsDataRaw.filter(isCompletedAndApproved);

      function applyFilters() {
        const comp = document.getElementById('filterComponent').value;
        const secEl = document.getElementById('filterSection');
        const sec = secEl ? secEl.value : 'All';

        // Apply component/section filtering on the raw projects data
        const baseFilteredRaw = projectsDataRaw.filter(p => {
          const compMatch = (comp === 'All') || (p.component === comp);
          const secMatch = (sec === 'All') || (!p.section) || (p.section === sec);
          return compMatch && secMatch;
        });

        // Chart and progress should still only show completed+approved projects within the filtered scope
        const filteredEligible = baseFilteredRaw.filter(isCompletedAndApproved);
        currentFilteredProjects = filteredEligible;

        // Update chart using eligible projects
          const counts = computeComponentCounts(filteredEligible);
          const chartMessageEl = document.getElementById('chartMessage');
          if (!Object.keys(counts).length) {
            // No data: hide canvas and show message
            componentChart.canvas.style.display = 'none';
            chartMessageEl.style.display = 'block';
            chartMessageEl.textContent = `No data for ${comp} - ${sec}`;
          } else {
            // Have data: update chart and ensure canvas visible
            componentChart.canvas.style.display = '';
            chartMessageEl.style.display = 'none';
            componentChart.data.labels = Object.keys(counts);
            componentChart.data.datasets[0].data = Object.values(counts);
            componentChart.data.datasets[0].backgroundColor = componentChart.data.labels.map(l => colorMap[l] || '#2b50ff');
            componentChart.update();
          }

        // Update project progress list
        renderProjectProgress(filteredEligible);

        // Update summary cards using the filtered raw dataset so counts reflect selected scope
        updateSummaryCards(baseFilteredRaw);
      }

      // Wire up filter events
      document.getElementById('filterComponent').addEventListener('change', applyFilters);
      if (document.getElementById('filterSection')) document.getElementById('filterSection').addEventListener('change', applyFilters);

      // Clear filters button handler
      function clearFilters() {
        const comp = document.getElementById('filterComponent');
        const sec = document.getElementById('filterSection');
        if (comp) comp.value = 'All';
        if (sec) sec.value = 'All';
        // Re-apply filters and ensure chart + progress are refreshed.
        applyFilters();
        try {
          if (typeof componentChart !== 'undefined' && componentChart) {
            componentChart.update();
          } else {
            // If chart isn't available for some reason, reload the page as a fallback
            // so the UI fully resets to server state.
            window.location.reload();
          }
        } catch (e) {
          // fallback to reload if update fails
          try { window.location.reload(); } catch(_) {}
        }
      }

      // Initial render using the eligible projects and then apply current filters
      renderProjectProgress(currentFilteredProjects);
      applyFilters();

      function downloadCSV() {
        // Generate an Excel-compatible HTML file (saved as .xls) so styling is preserved
        const compFilter = (document.getElementById('filterComponent') || {}).value || 'All';
        const secEl = document.getElementById('filterSection');
        const secFilter = secEl ? secEl.value : 'All';

        const approved = document.getElementById('approvedProposals')?.textContent.trim() || '0';
        const pending = document.getElementById('pendingProposals')?.textContent.trim() || '0';
        const draft = document.getElementById('draftProposals')?.textContent.trim() || '0';
        const total = document.getElementById('totalProposals')?.textContent.trim() || '0';

        const ongoing = document.getElementById('statusOngoing')?.textContent.trim() || '0';
        const completed = document.getElementById('statusCompleted')?.textContent.trim() || '0';
        const archived = document.getElementById('statusArchived')?.textContent.trim() || '0';

        // Component breakdown
        let compRows = [];
        if (window.componentChart && componentChart.data && Array.isArray(componentChart.data.labels)) {
          componentChart.data.labels.forEach((label, i) => {
            const val = (componentChart.data.datasets && componentChart.data.datasets[0] && componentChart.data.datasets[0].data && componentChart.data.datasets[0].data[i]) || 0;
            compRows.push({component: label, count: Number(val)});
          });
        } else {
          const compCounts = {};
          (Array.isArray(currentFilteredProjects) ? currentFilteredProjects : []).forEach(p => { const k = p.component || 'Unknown'; compCounts[k] = (compCounts[k] || 0) + 1; });
          ['ROTC','LTS','CWTS'].forEach(k => compRows.push({component:k, count: compCounts[k] || 0}));
        }

        // Progress rows
        const progressRows = [];
        const container = document.querySelector('#reports .space-y-4');
        if (container) {
          container.querySelectorAll(':scope > div').forEach(div => {
            try {
              const titleSpan = div.querySelector('div.flex.justify-between span:first-child');
              const progressSpan = div.querySelector('div.flex.justify-between span:last-child');
              const budgetSpan = div.querySelector('.text-sm .font-bold');
              const title = titleSpan ? titleSpan.textContent.trim() : '';
              const progressText = progressSpan ? progressSpan.textContent.trim() : '';
              const budgetText = budgetSpan ? budgetSpan.textContent.trim().replace('₱','').replace(/,/g,'') : '';

              let name = title;
              let component = '';
              let section = '';
              const m = title.match(/^(.*)\s*\(([^)]*)\)$/);
              if (m) {
                name = m[1].trim();
                const meta = m[2].split('—').map(s => s.trim());
                component = meta[0] || '';
                section = meta[1] || '';
              }

              progressRows.push({name, component, section, progress: progressText, budget: budgetText});
            } catch (e) { /* ignore parse errors for a row */ }
          });
        }

        // Helper to escape HTML
        function esc(s) { if (s === null || typeof s === 'undefined') return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;'); }

        // Color mapping
        const statusColors = { approved: '#16a34a', pending: '#f97316', draft: '#f59e0b', total: '#2563eb' };
        const statusColorsCaps = { 'Approved': '#16a34a', 'Pending': '#f97316', 'Draft': '#f59e0b', 'Total': '#2563eb' };
        const implColors = { Ongoing: '#2b50ff', Completed: '#16a34a', Archived: '#f59e0b' };
        const compColors = { 'ROTC': '#2b50ff', 'LTS': '#f2d35b', 'CWTS': '#e63946' };

        const now = new Date();
        const dateStr = now.toLocaleDateString();
        const timeStr = now.toLocaleTimeString();

        // Build HTML table
        let html = '<html><head><meta charset="utf-8"></head><body>';
        html += '<table border="0" cellpadding="4" cellspacing="0">';
        html += `<tr><td colspan="5" style="font-weight:700;font-size:18px;padding-bottom:8px;">${esc('NSTP Project Management System - Reports Export')}</td></tr>`;
        html += `<tr><td colspan="5" style="padding-bottom:6px;"><strong>Generated on:</strong> ${esc(dateStr)}, <em>${esc(timeStr)}</em></td></tr>`;
        html += `<tr><td colspan="5" style="padding-bottom:6px;"><strong>Active Filters:</strong> Component: ${esc(compFilter)} &nbsp;&nbsp; Section: ${esc(secFilter)}</td></tr>`;
        html += '<tr><td colspan="5"></td></tr>';

        // Project proposals summary
        html += '<tr><td colspan="5" style="font-weight:700;padding-top:8px;padding-bottom:4px;">PROJECT PROPOSALS SUMMARY</td></tr>';
        html += '<tr><th style="font-weight:700;border-bottom:1px solid #ddd;">Status</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Count</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Percentage</th><td></td><td></td></tr>';
        const totNum = Number(total) || 0;
        const approvedNum = Number(approved) || 0;
        const pendingNum = Number(pending) || 0;
        const draftNum = Number(draft) || 0;
        function pct(n) { return totNum ? ((Math.round((n/totNum)*1000)/10) + '%') : '0%'; }

        html += `<tr><td style="background:${statusColorsCaps['Approved']};color:#fff;font-weight:700;">Approved</td><td style="font-weight:700;">${esc(approvedNum)}</td><td>${esc(pct(approvedNum))}</td><td></td><td></td></tr>`;
        html += `<tr><td style="background:${statusColorsCaps['Pending']};color:#fff;font-weight:700;">Pending</td><td style="font-weight:700;">${esc(pendingNum)}</td><td>${esc(pct(pendingNum))}</td><td></td><td></td></tr>`;
        html += `<tr><td style="background:${statusColorsCaps['Draft']};color:#000;font-weight:700;">Draft</td><td style="font-weight:700;">${esc(draftNum)}</td><td>${esc(pct(draftNum))}</td><td></td><td></td></tr>`;
        html += `<tr><td style="background:${statusColorsCaps['Total']};color:#fff;font-weight:700;">Total</td><td style="font-weight:700;">${esc(totNum)}</td><td style="font-weight:700;">100%</td><td></td><td></td></tr>`;
        html += '<tr><td colspan="5"></td></tr>';

        // Project implementation status
        html += '<tr><td colspan="5" style="font-weight:700;padding-top:8px;padding-bottom:4px;">PROJECT IMPLEMENTATION STATUS</td></tr>';
        html += '<tr><th style="font-weight:700;border-bottom:1px solid #ddd;">Status</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Count</th><td></td><td></td><td></td></tr>';
        html += `<tr><td style="background:${implColors['Ongoing']};color:#fff;font-weight:700;">Ongoing</td><td style="font-weight:700;">${esc(ongoing)}</td><td></td><td></td><td></td></tr>`;
        html += `<tr><td style="background:${implColors['Completed']};color:#fff;font-weight:700;">Completed</td><td style="font-weight:700;">${esc(completed)}</td><td></td><td></td><td></td></tr>`;
        html += `<tr><td style="background:${implColors['Archived']};color:#000;font-weight:700;">Archived</td><td style="font-weight:700;">${esc(archived)}</td><td></td><td></td><td></td></tr>`;
        html += '<tr><td colspan="5"></td></tr>';

        // Projects per component
        html += '<tr><td colspan="5" style="font-weight:700;padding-top:8px;padding-bottom:4px;">PROJECTS PER COMPONENT</td></tr>';
        html += '<tr><th style="font-weight:700;border-bottom:1px solid #ddd;">Component</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Count</th><td></td><td></td><td></td></tr>';
        compRows.forEach(r => {
          const ccol = compColors[r.component] || '#2b50ff';
          html += `<tr><td style="background:${ccol};color:#fff;font-weight:700;">${esc(r.component)}</td><td style="font-weight:700;">${esc(r.count)}</td><td></td><td></td><td></td></tr>`;
        });
        html += '<tr><td colspan="5"></td></tr>';

        // Project progress details
        html += '<tr><td colspan="5" style="font-weight:700;padding-top:8px;padding-bottom:4px;">PROJECT PROGRESS DETAILS</td></tr>';
        html += '<tr><th style="font-weight:700;border-bottom:1px solid #ddd;">Project Name</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Component</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Section</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Progress</th><th style="font-weight:700;border-bottom:1px solid #ddd;">Total Budget</th></tr>';
        progressRows.forEach(r => {
          const compColor = compColors[r.component] || '#2b50ff';
          const progClean = String(r.progress).replace('%','').trim();
          html += '<tr>' +
                  `<td style="font-weight:700;">${esc(r.name)}</td>` +
                  `<td style="background:${compColor};color:#fff;font-weight:700;">${esc(r.component)}</td>` +
                  `<td>${esc(r.section)}</td>` +
                  `<td style="font-weight:700;">${esc(r.progress)}</td>` +
                  `<td style="font-weight:700;font-style:italic;">₱${Number(r.budget || 0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})}</td>` +
                  '</tr>';
        });

        html += '</table></body></html>';

        // Download as .xls so Excel renders the HTML with formatting
        const blob = new Blob([html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
        const link = document.createElement('a');
        const filename = 'nstp_comprehensive_reports_' + new Date().toISOString().split('T')[0] + '.xls';
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        setTimeout(() => { URL.revokeObjectURL(link.href); link.remove(); }, 1000);
      }

      function downloadPDF() {
        window.print();
      }
    </script>
@endsection
