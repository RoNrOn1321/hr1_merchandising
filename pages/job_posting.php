<?php
include '../layout/adminLayout.php';

$children = '
<div class="">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Job Posting & Applicant Sourcing</h1>
        <button onclick="openJobModal()" 
            class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 sm:px-5 py-2 rounded-xl shadow hover:from-green-600 hover:to-green-700 transition text-sm sm:text-base">
            + Post New Job
        </button>
    </div>

    <!-- Job Posting Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        ' . generateJobCard("Software Engineer", "Develop and maintain web apps.", 15, "Aug 30, 2025") . '
        ' . generateJobCard("Marketing Specialist", "Drive campaigns and promotions.", 8, "Sept 5, 2025") . '
        ' . generateJobCard("UI/UX Designer", "Design user-friendly interfaces.", 12, "Sept 12, 2025") . '
        ' . generateJobCard("HR Officer", "Handle recruitment and HR tasks.", 20, "Sept 15, 2025") . '
        ' . generateJobCard("Accountant", "Manage financial records and budgets.", 10, "Sept 18, 2025") . '
        ' . generateJobCard("Sales Associate", "Boost company revenue via sales.", 18, "Sept 22, 2025") . '
    </div>
</div>

<!-- Add New Job Modal -->
<div id="jobModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Post New Job</h2>
    <form>
      <label class="block mb-2 font-medium text-sm sm:text-base">Job Title</label>
      <input type="text" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" placeholder="Enter job title">

      <label class="block mb-2 font-medium text-sm sm:text-base">Description</label>
      <textarea class="w-full border p-2 rounded mb-3 text-sm sm:text-base" rows="3" placeholder="Enter job description"></textarea>

      <label class="block mb-2 font-medium text-sm sm:text-base">Deadline</label>
      <input type="date" class="w-full border p-2 rounded mb-4 text-sm sm:text-base">

      <div class="flex gap-2">
        <button type="submit" class="flex-1 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition text-sm sm:text-base">Save</button>
        <button type="button" onclick="closeJobModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition text-sm sm:text-base">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
function openJobModal() {
  document.getElementById("jobModal").classList.remove("hidden");
}
function closeJobModal() {
  document.getElementById("jobModal").classList.add("hidden");
}
</script>
';

function generateJobCard($title, $description, $applicants, $date) {
    return '
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 group">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2 group-hover:text-green-600 transition">' . $title . '</h2>
        <p class="text-gray-600 text-sm sm:text-base mb-4">' . $description . '</p>
        <div class="flex justify-between text-xs sm:text-sm text-gray-500 mb-4">
            <span>' . $applicants . ' Applicants</span>
            <span>' . $date . '</span>
        </div>
        <button class="w-full bg-green-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-green-600 transition shadow">
            View Applicants
        </button>
    </div>';
}

adminLayout($children);
?>
