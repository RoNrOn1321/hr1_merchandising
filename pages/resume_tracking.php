<?php
include '../layout/adminLayout.php';

$children = '
<div class="">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Resume Collection & Tracking</h1>
        <button onclick="openUploadModal()" 
            class="bg-indigo-500 text-white px-4 sm:px-5 py-2 rounded-lg shadow hover:bg-indigo-600 transition text-sm sm:text-base">
            + Upload Resume
        </button>
    </div>

    <!-- Resume Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        ' . generateResumeCard("John Doe", "Software Engineer", "Aug 25, 2025", "Reviewed") . '
        ' . generateResumeCard("Jane Smith", "UI/UX Designer", "Aug 26, 2025", "Pending") . '
        ' . generateResumeCard("Mark Lee", "HR Officer", "Aug 27, 2025", "Shortlisted") . '
        ' . generateResumeCard("Anna Cruz", "Marketing Specialist", "Aug 28, 2025", "Interviewed") . '
        ' . generateResumeCard("Chris Evans", "Accountant", "Aug 29, 2025", "Hired") . '
        ' . generateResumeCard("Sophia Reyes", "Sales Associate", "Aug 30, 2025", "Rejected") . '
    </div>
</div>

<!-- Upload Resume Modal -->
<div id="uploadModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Upload Resume</h2>
    <form>
      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Applicant Name</label>
      <input type="text" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base" placeholder="Enter name">

      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Position</label>
      <input type="text" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base" placeholder="Enter position">

      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Upload File</label>
      <input type="file" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base">

      <button type="submit" class="w-full bg-indigo-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-indigo-600 transition">
        Submit
      </button>
    </form>
    <button onclick="closeUploadModal()" class="w-full mt-3 bg-red-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-red-600 transition">
      Cancel
    </button>
  </div>
</div>

<script>
function openUploadModal() {
  document.getElementById("uploadModal").classList.remove("hidden");
}
function closeUploadModal() {
  document.getElementById("uploadModal").classList.add("hidden");
}
</script>
';

function generateResumeCard($name, $position, $date, $status) {
    $statusColors = [
        "Reviewed" => "bg-green-100 text-green-600",
        "Pending" => "bg-yellow-100 text-yellow-600",
        "Shortlisted" => "bg-blue-100 text-blue-600",
        "Interviewed" => "bg-purple-100 text-purple-600",
        "Hired" => "bg-teal-100 text-teal-600",
        "Rejected" => "bg-red-100 text-red-600",
    ];

    $badgeClass = $statusColors[$status] ?? "bg-gray-100 text-gray-600";

    return '
    <div class="bg-white p-4 sm:p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-1">' . $name . '</h2>
        <p class="text-gray-600 text-sm sm:text-base mb-3">' . $position . '</p>
        <div class="flex justify-between items-center text-xs sm:text-sm mb-4">
            <span class="text-gray-500">' . $date . '</span>
            <span class="px-2 py-1 text-xs sm:text-sm font-medium rounded-full ' . $badgeClass . '">' . $status . '</span>
        </div>
        <button class="w-full bg-indigo-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-indigo-600 transition shadow">
            View Resume
        </button>
    </div>';
}

adminLayout($children);
?>
