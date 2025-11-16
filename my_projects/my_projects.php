<?php
// placeholder project data
$project = [
  'id' => 1,
  'name' => 'Cuento Diatun',
  'team' => 'Team Aro',
  'component' => 'CWTS'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>NSTP Project Management and Monitoring System</title>
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


 <!-- My Projects (card over background) -->
 <section id="my-projects" class="relative rounded-2xl overflow-hidden shadow-subtle">
  <div class="absolute inset-0 bg-center bg-cover opacity-30" style="background-image:url('');"></div>
  <div class="relative p-8">
    <h2 class="text-3xl font-bold mb-6">My Projects</h2>

    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-subtle p-6 w-64">
      <div class="flex flex-col items-center text-center">
        <h3 class="font-semibold text-lg"><?php echo htmlspecialchars($project['name']); ?></h3>
        <div class="w-20 h-20 rounded-full border-2 border-gray-600 my-4 flex items-center justify-center bg-gray-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4zM12 12a4 4 0 100-8 4 4 0 000 8z" />
          </svg>
        </div>
        <p class="text-sm text-gray-600"><?php echo htmlspecialchars($project['team']); ?></p>
        <a href="../all_projects/project_details.php?id=<?php echo $project['id']; ?>&component=CWTS" 
        class="mt-4 inline-block w-full text-center rounded-lg bg-nstpBlue text-white px-4 py-2 hover:bg-nstpBlue2 transition">
          View Project
        </a>
      </div>
    </div>

  </div>
</section>

</main>


</body>
</html>