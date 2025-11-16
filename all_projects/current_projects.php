<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>All Projects</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Custom colors and background images */
    .light-pink-bg { background-color: #FFE4E1; }
    
    .rotc-bg {
      background-image: url('../assets/1000036078.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }
    
    .lts-bg {
      background-image: url('../assets/1000036079.jpg');
      background-size: cover;
      background-position: center 30%;
      background-repeat: no-repeat;
    }
    
    .cwts-bg {
      background-image: url('../assets/1000036076.jpg');
      background-size: cover;
      background-position: center 30%;
      background-repeat: no-repeat;
    }
    
    /* Overlay for better text readability */
    .project-overlay {
      background: rgba(0, 0, 0, 0.4);
      border-radius: 0.5rem;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
      .rotc-bg, .lts-bg, .cwts-bg {
        background-attachment: scroll;
        min-height: 120px;
      }
      
      .project-card {
        padding: 2rem 1rem !important;
        text-align: center !important;
      }
      
      .project-title {
        font-size: 1.5rem !important;
        text-align: center !important;
      }
    }
    
    @media (max-width: 480px) {
      .project-card {
        padding: 1.5rem 0.75rem !important;
        text-align: center !important;
      }
      
      .project-title {
        font-size: 1.25rem !important;
        text-align: center !important;
      }
    }
  </style>
</head>
<body class="bg-gray-100 flex">

  <!-- Include Sidebar -->
  <?php include '../components/sidebar.php'; ?>

  <!-- Main Content -->
  <main id="content" class="flex-1 md:ml-64 p-4 md:p-6 transition-all duration-300 bg-white min-h-screen">

    <!-- Page Header -->
    <div class="mb-6 md:mb-8">
      <h1 class="text-2xl md:text-4xl font-bold text-black mb-2">All Current Projects</h1>
      <p class="text-base md:text-lg text-gray-700">0 of 0 projects organized</p>
    </div>

    <!-- Project Categories Container -->
    <div class="light-pink-bg p-6 rounded-lg">
      <div class="space-y-6">
        <h2 class="text-2xl font-semibold text-black mb-6">Recent Projects...</h2>
        <!-- ROTC Projects -->
        <div class="w-full">
          <a href="rotc.php" class="block">
            <div class="rotc-bg text-white project-card p-6 sm:p-8 md:p-12 rounded-lg text-center hover:opacity-90 cursor-pointer transition-opacity relative overflow-hidden">
              <div class="project-overlay absolute inset-0"></div>
              <div class="relative z-10">
                <h3 class="project-title text-xl sm:text-2xl md:text-3xl font-bold">ROTC Projects</h3>
              </div>
            </div>
          </a>
        </div>

        <!-- LTS Projects -->
        <div class="w-full">
          <a href="lts.php" class="block">
            <div class="lts-bg text-white project-card p-6 sm:p-8 md:p-12 rounded-lg text-center hover:opacity-90 cursor-pointer transition-opacity relative overflow-hidden">
              <div class="project-overlay absolute inset-0"></div>
              <div class="relative z-10">
                <h3 class="project-title text-xl sm:text-2xl md:text-3xl font-bold">LTS Projects</h3>
              </div>
            </div>
          </a>
        </div>

        <!-- CWTS Projects -->
        <div class="w-full">
          <a href="cwts.php" class="block">
            <div class="cwts-bg text-white project-card p-6 sm:p-8 md:p-12 rounded-lg text-center hover:opacity-90 cursor-pointer transition-opacity relative overflow-hidden">
              <div class="project-overlay absolute inset-0"></div>
              <div class="relative z-10">
                <h3 class="project-title text-xl sm:text-2xl md:text-3xl font-bold">CWTS Projects</h3>
              </div>
            </div>
          </a>
        </div>
      </div>
    </div>
  </main>

  <!-- Additional JavaScript for mobile menu -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile menu toggle
      const menuBtn = document.getElementById('menuBtn');
      const sidebar = document.getElementById('sidebar');
      
      if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', () => {
          sidebar.classList.toggle('-translate-x-full');
        });
      }
    });
  </script>

</body>
</html>
