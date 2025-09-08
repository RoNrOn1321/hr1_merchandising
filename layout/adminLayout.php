<?php
function adminLayout($children) {
    $currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar with Boxicons & Dropdowns</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="flex">

    <div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gray-800 text-white transform -translate-x-full transition-transform duration-300 ease-in-out z-50">
        <div class="flex items-center justify-between p-4 border-b border-gray-700">
            <span class="text-xl font-bold">Hr1-Merchandising</span>
            <button id="closeSidebar" class="text-white text-2xl hover:text-red-500"><i class='bx bx-x'></i></button>
        </div>
       <ul class="mt-4">
    <!-- Dashboard -->
    <li class="px-4 py-3 hover:bg-gray-700 cursor-pointer flex items-center space-x-3 
        <?php echo ($currentPage == 'dashboard.php') ? 'bg-gray-700' : ''; ?>">
        <a href="dashboard.php" class="flex items-center space-x-3 w-full">
            <i class='bx bx-grid-alt'></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Applicant Management -->
    <li id="applicantBtn" class="py-2 cursor-pointer">
        <div class="px-4 py-3 hover:bg-gray-700 flex items-center justify-between space-x-3">
            <div class="flex items-center space-x-3">
                <i class='bx bx-folder'></i>
                <span>Applicant Management</span>
            </div>
            <i id="applicantArrow" class='bx bx-chevron-down transform transition-transform duration-300'></i>
        </div>
        <ul id="applicantDropdown" class="pl-8 mt-2 hidden">
            <li class="<?php echo ($currentPage == 'job_posting.php') ? 'bg-gray-700' : ''; ?>">
                <a href="job_posting.php" class="block px-4 py-2 hover:bg-gray-700">Job posting & applicant sourcing</a>
            </li>
            <li class="<?php echo ($currentPage == 'resume_tracking.php') ? 'bg-gray-700' : ''; ?>">
                <a href="resume_tracking.php" class="block px-4 py-2 hover:bg-gray-700">Resume collection & tracking</a>
            </li>
            <li class="<?php echo ($currentPage == 'applicant_status.php') ? 'bg-gray-700' : ''; ?>">
                <a href="applicant_status.php" class="block px-4 py-2 hover:bg-gray-700">Applicant communication & status updates</a>
            </li>
        </ul>
    </li>

    <!-- Recruitment Management -->
    <li id="recruitmentBtn" class="py-2 cursor-pointer">
        <div class="px-4 py-3 hover:bg-gray-700 flex items-center justify-between space-x-3">
            <div class="flex items-center space-x-3">
                <i class='bx bx-briefcase-alt-2'></i>
                <span>Recruitment Management</span>
            </div>
            <i id="recruitmentArrow" class='bx bx-chevron-down transform transition-transform duration-300'></i>
        </div>
        <ul id="recruitmentDropdown" class="pl-8 mt-2 hidden">
            <li class="<?php echo ($currentPage == 'shortlisting.php') ? 'bg-gray-700' : ''; ?>">
                <a href="shortlisting.php" class="block px-4 py-2 hover:bg-gray-700">Candidate shortlisting & screening</a>
            </li>
            <li class="<?php echo ($currentPage == 'interview.php') ? 'bg-gray-700' : ''; ?>">
                <a href="interview.php" class="block px-4 py-2 hover:bg-gray-700">Interview scheduling & conducting</a>
            </li>
            <li class="<?php echo ($currentPage == 'job_offer.php') ? 'bg-gray-700' : ''; ?>">
                <a href="job_offer.php" class="block px-4 py-2 hover:bg-gray-700">Job offer & acceptance management</a>
            </li>
        </ul>
    </li>
     <!-- Training & Development -->
        <li id="trainingBtn" class="py-2 cursor-pointer">
            <div class="px-4 py-3 hover:bg-gray-700 flex items-center justify-between space-x-3 
                <?php echo ($currentPage == 'training.php') ? 'bg-gray-700' : ''; ?>">
                <div class="flex items-center space-x-3">
                    <i class='bx bx-book-content'></i>
                    <span>Training & Development</span>
                </div>
                <i id="trainingArrow" class='bx bx-chevron-down transform transition-transform duration-300'></i>
            </div>
            <ul id="trainingDropdown" class="pl-8 mt-2 hidden">
                <li><a href="learning.php" class="block px-4 py-2 hover:bg-gray-700 cursor-pointer">Learning Modules</a></li>
                <li><a href="performance.php" class="block px-4 py-2 hover:bg-gray-700 cursor-pointer">Performance Tracking</a></li>
                <li><a href="evaluation.php" class="block px-4 py-2 hover:bg-gray-700 cursor-pointer">Evaluation & Feedback</a></li>
            </ul>
        </li>

        <!-- Reports & Analytics -->
        <li class="px-4 py-3 hover:bg-gray-700 cursor-pointer flex items-center space-x-3 
            <?php echo ($currentPage == 'reports.php') ? 'bg-gray-700' : ''; ?>">
            <a href="reports.php" class="flex items-center space-x-3 w-full">
                <i class='bx bx-bar-chart'></i>
                <span>Reports & Analytics</span>
            </a>
        </li>

   
</ul>
    </div>

    <div class="flex-1 min-h-screen bg-gray-100 px-4 py-2">
        
       <header class="flex items-center justify-between bg-white p-4 shadow rounded-md">
  <!-- Left: Menu Toggle -->
  <div class="flex items-center">
    <button id="toggleBtn" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      <i class='bx bx-menu'></i>
    </button>
  </div>

  <!-- Center: Search -->
  <div class="flex-1 mx-4 max-w-full">
    <div class="relative w-full">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <i class='bx bx-search text-gray-400 text-lg'></i>
      </div>
      <input type="text" placeholder="Search..." 
             class="w-full md:w-[50%] pl-10 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
  </div>

  <!-- Right: Notifications & Profile -->
  <div class="flex items-center space-x-4">
    <!-- Notifications -->
    <div class="relative">
      <button id="notificationBtn" class="relative">
        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        <i class='bx bx-bell text-2xl text-gray-700'></i>
      </button>
      <div id="notificationDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
        <div class="p-4 border-b border-gray-200 font-bold">Notifications</div>
        <ul>
          <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Notification 1</li>
          <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Notification 2</li>
          <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Notification 3</li>
        </ul>
      </div>
    </div>

    <!-- Profile -->
    <div class="relative">
      <button id="profileHeaderBtn" class="flex items-center cursor-pointer">
        <img src="https://i.pravatar.cc/40" alt="Profile" class="h-8 w-8 rounded-full border-2 border-gray-300">
      </button>
      <div id="profileDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
        <ul>
          <li class="px-4 py-2 font-medium border-b border-gray-200 cursor-default">John Doe</li>
          <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">View Profile</li>
          <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Settings</li>
          <li class="px-4 py-2 hover:bg-gray-100 cursor-pointer">Logout</li>
        </ul>
      </div>
    </div>
  </div>
</header>


        <main class="p-6">
            <?php echo $children; ?>
        </main>
        
    </div>

    <script>
        // Sidebar toggle
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn');
        const closeSidebar = document.getElementById('closeSidebar');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
        closeSidebar.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
        });

        // Profile dropdown
        const profileHeaderBtn = document.getElementById('profileHeaderBtn');
        const profileDropdown = document.getElementById('profileDropdown');

        profileHeaderBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent document click from closing it immediately
            profileDropdown.classList.toggle('hidden');
        });

        // Notification dropdown
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent document click from closing it immediately
            notificationDropdown.classList.toggle('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            if (!profileHeaderBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.add('hidden');
            }
            if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.add('hidden');
            }
            // Close sidebar dropdowns
            const dropdowns = document.querySelectorAll('.sidebar-dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.previousElementSibling.contains(e.target)) {
                    dropdown.classList.add('hidden');
                    const arrow = dropdown.previousElementSibling.querySelector('.bx-chevron-down');
                    if (arrow) {
                        arrow.classList.remove('rotate-180');
                    }
                }
            });
        });

        // Sidebar dropdown logic
        function setupDropdown(buttonId, dropdownId, arrowId) {
            const button = document.getElementById(buttonId);
            const dropdown = document.getElementById(dropdownId);
            const arrow = document.getElementById(arrowId);
            if (button && dropdown && arrow) {
                button.addEventListener('click', (e) => {
                    e.stopPropagation(); // Stop event bubbling to prevent closing other dropdowns
                    const isHidden = dropdown.classList.contains('hidden');
                    // Close all other dropdowns
                    const allDropdowns = document.querySelectorAll('.sidebar-dropdown');
                    allDropdowns.forEach(d => {
                        if (d.id !== dropdownId) {
                            d.classList.add('hidden');
                            const a = d.previousElementSibling.querySelector('.bx-chevron-down');
                            if (a) {
                                a.classList.remove('rotate-180');
                            }
                        }
                    });
                    
                    // Toggle the clicked dropdown
                    dropdown.classList.toggle('hidden');
                    arrow.classList.toggle('rotate-180');
                });
            }
        }
        
        setupDropdown('applicantBtn', 'applicantDropdown', 'applicantArrow');
        setupDropdown('recruitmentBtn', 'recruitmentDropdown', 'recruitmentArrow');
        setupDropdown('onboardingBtn', 'onboardingDropdown', 'onboardingArrow');
        setupDropdown('performanceBtn', 'performanceDropdown', 'performanceArrow');
        setupDropdown('socialBtn', 'socialDropdown', 'socialArrow');
        setupDropdown('trainingBtn','trainingDropdown', 'trainingArrow');
    </script>
</body>
</html>

<?php
}
?>