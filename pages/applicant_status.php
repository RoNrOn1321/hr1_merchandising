<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-4 sm:p-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Applicant Status Tracking</h1>
    </div>

    <!-- Status Table -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Current Applicants</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left min-w-[600px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-4 text-sm sm:text-base">Name</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Position</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Status</th>
                        <th class="py-3 px-4 text-center text-sm sm:text-base">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ' . generateApplicantRow("John Doe", "Software Engineer", "Under Review") . '
                    ' . generateApplicantRow("Jane Smith", "UI/UX Designer", "Interview Scheduled") . '
                    ' . generateApplicantRow("Michael Lee", "HR Officer", "Shortlisted") . '
                    ' . generateApplicantRow("Anna Johnson", "Marketing Specialist", "Hired") . '
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Update Applicant Status</h2>
    <select id="newStatus" class="w-full border border-gray-300 rounded-lg p-2 mb-4 text-sm sm:text-base">
        <option>Under Review</option>
        <option>Interview Scheduled</option>
        <option>Shortlisted</option>
        <option>Hired</option>
        <option>Rejected</option>
    </select>
    <button onclick="saveStatus()" class="w-full bg-blue-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-blue-600 transition">
      Save Changes
    </button>
    <button onclick="closeModal()" class="w-full mt-2 bg-red-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-red-600 transition">
      Cancel
    </button>
  </div>
</div>

<!-- Boxicons CDN -->
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<script>
function openModal() {
  document.getElementById("statusModal").classList.remove("hidden");
}
function closeModal() {
  document.getElementById("statusModal").classList.add("hidden");
}
function saveStatus() {
  alert("Applicant status updated to: " + document.getElementById("newStatus").value);
  closeModal();
}
function editApplicant(name) {
  alert("Edit details for " + name);
}
function deleteApplicant(name) {
  if (confirm("Are you sure you want to delete " + name + "?")) {
    alert(name + " deleted successfully!");
  }
}
</script>
';

function generateApplicantRow($name, $position, $status) {
    return '
    <tr class="border-b hover:bg-gray-50 text-sm sm:text-base">
        <td class="py-3 px-4">' . $name . '</td>
        <td class="py-3 px-4">' . $position . '</td>
        <td class="py-3 px-4">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm">' . $status . '</span>
        </td>
        <td class="py-3 px-4 text-center">
            <div class="flex justify-center gap-3">
                <button onclick="openModal()" class="text-green-500 hover:text-green-700">
                    <i class="bx bx-edit-alt text-lg sm:text-xl"></i>
                </button>
                <button onclick="deleteApplicant(\'' . $name . '\')" class="text-red-500 hover:text-red-700">
                    <i class="bx bx-trash text-lg sm:text-xl"></i>
                </button>
            </div>
        </td>
    </tr>';
}

adminLayout($children);
?>
