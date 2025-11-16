<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Project Details - NSTP Project Management and Monitoring System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            sidebar: '#e86d63',
            sidebarAccent: '#f5dedd',
            pill: '#ffe9dc',
            nstpBlue: '#2b50ff',
            nstpBlue2: '#1e3a8a',
            nstpYellow: '#f2d35b',
            nstpMaroon: '#8e2b2b'
          },
          boxShadow: { subtle: '0 1px 2px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.1)' }
        }
      }
    }
  </script>
</head>
<body>

<?php include '../components/sidebar.php'; ?>

 <!-- Main Content -->
 <main id="content" class="flex-1 ml-64 p-6 transition-all duration-300 bg-white min-h-screen">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold">Project: Cuento Diatun</h1>
      <a href="./my_projects.php" class="rounded-lg bg-gray-100 hover:bg-gray-200 px-4 py-2">← Back to My Projects</a>
    </div>

    <!-- Team Information + Member Profile -->
    <section id="team-info" class="bg-white rounded-2xl shadow-subtle p-6">
      <header class="mb-4"><h2 class="text-2xl font-bold">Cuento Diatun</h2></header>
      <div class="rounded-2xl bg-sidebarAccent p-6">
        <h3 class="text-xl font-semibold mb-4 flex items-center gap-2">👥 Team Information</h3>
        <div class="grid grid-cols-3 gap-6 items-center">
          <div class="w-20 h-20 rounded-full border-2 border-gray-600"></div>
          <div>Team Aro</div>
          <div>Component</div>
        </div>
      </div>
      <div class="rounded-2xl bg-sidebarAccent p-6 mt-6">
        <h3 class="text-xl font-semibold mb-4 flex items-center gap-2">🧑‍🤝‍🧑 Member Profile</h3>
        <div class="overflow-auto">
          <table class="min-w-[700px] w-full border">
            <thead>
              <tr class="[&>th]:border [&>th]:px-3 [&>th]:py-2 text-left">
                <th>Name</th><th>Role/s</th><th>Email</th><th>Contact Number</th>
              </tr>
            </thead>
            <tbody>
              <tr class="[&>td]:border [&>td]:px-3 [&>td]:py-2"><td></td><td></td><td></td><td></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- PROJECT DETAILS (full) -->
    <section id="project-details" class="bg-white rounded-2xl shadow-subtle p-8 mt-6">
      <h2 class="text-3xl font-bold">Project Details</h2>
      <div class="mt-6 rounded-2xl bg-pill p-6 space-y-4">
        <div><label class="block text-sm font-medium">Issues/Problem being addressed *</label><textarea rows="3" class="mt-1 w-full rounded-lg border-gray-300"></textarea></div>
        <div><label class="block text-sm font-medium">Goal/Objectives *</label><textarea rows="3" class="mt-1 w-full rounded-lg border-gray-300"></textarea></div>
        <div><label class="block text-sm font-medium">Target Community *</label><input class="mt-1 w-full rounded-lg border-gray-300"></div>
        <div><label class="block text-sm font-medium">Solutions/Activities to be implemented *</label><textarea rows="3" class="mt-1 w-full rounded-lg border-gray-300"></textarea></div>
        <div><label class="block text-sm font-medium">Expected Outcomes *</label><textarea rows="3" class="mt-1 w-full rounded-lg border-gray-300"></textarea></div>
      </div>
    </section>

    <!-- Activities Form (inputs + action buttons) -->
    <section id="activities-form" class="bg-white rounded-2xl shadow-subtle p-6 mt-6">
      <h2 class="text-3xl font-bold mb-4">📅 Project Activities</h2>
      <div class="rounded-2xl bg-pill p-6 space-y-4">
        <div class="grid grid-cols-5 gap-4">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Stage">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Specific Activities">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Time Frame">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Point Person">
          <select class="rounded-md border-gray-300 bg-gray-100"><option>Ongoing</option><option>Completed</option><option>Planned</option></select>
        </div>
        <button class="rounded-md border px-3 py-1.5 text-sm bg-white hover:bg-gray-50">+ Add Row</button>
      </div>

      <div class="rounded-2xl bg-pill p-6 mt-6 space-y-4">
        <h3 class="text-xl font-semibold">₱ Budget</h3>
        <div class="grid grid-cols-4 gap-3">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Activity">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Resources Needed">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="Partner Agencies">
          <input class="rounded-md border-gray-300 bg-gray-100" placeholder="₱ 0">
        </div>
        <button class="rounded-md border px-3 py-1.5 text-sm bg-white hover:bg-gray-50">+ Add Row</button>
        <div class="flex gap-3 justify-end pt-6">
          <button class="rounded-lg bg-gray-200 px-4 py-2">Save as Draft</button>
          <button class="rounded-lg bg-nstpBlue text-white px-4 py-2">Submit Project</button>
        </div>
      </div>
    </section>

 </main>

</body>
</html>


