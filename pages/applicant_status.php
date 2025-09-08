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
            <table class="w-full border-collapse text-left min-w-[700px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-4 text-sm sm:text-base">Image</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Name</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Position</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Status</th>
                        <th class="py-3 px-4 text-center text-sm sm:text-base">Action</th>
                    </tr>
                </thead>
                <tbody id="applicantTableBody">
                    <!-- Rows will be inserted dynamically -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Update Applicant Status</h2>
    <input type="hidden" id="currentApplicantId">
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-3">Confirm Delete</h2>
    <p class="text-gray-700 mb-4">Are you sure you want to delete this applicant?</p>
    <div class="flex gap-2">
      <button onclick="confirmApplicantDelete()" class="flex-1 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">Delete</button>
      <button onclick="closeDeleteModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition">Cancel</button>
    </div>
  </div>
</div>

<!-- Alert Modal -->
<div id="alertModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6">
    <h3 class="text-lg font-semibold mb-2">Notification</h3>
    <p id="alertMessage" class="text-gray-700 mb-4"></p>
    <button onclick="closeAlert()" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition">OK</button>
  </div>
</div>

<!-- Boxicons CDN -->
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<script>
const apiUrl = "http://localhost/hr1_merchandising/api/applicant_status.php";
let pendingDeleteId = null;

// Load applicants
async function loadApplicants() {
  try {
    const response = await fetch(apiUrl);
    const applicants = await response.json();
    const tbody = document.getElementById("applicantTableBody");
    tbody.innerHTML = "";

    applicants.forEach(applicant => {
      const imgCell = applicant.image_url
        ? `<img src="${applicant.image_url}" alt="${applicant.name}" class="h-10 w-10 rounded-full object-cover border border-gray-200" />`
        : `<div class=\"h-10 w-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 text-[10px]\">No Img</div>`;
      const row = `
        <tr class="border-b hover:bg-gray-50 text-sm sm:text-base">
          <td class="py-3 px-4">
            <div class="flex items-center">${imgCell}</div>
          </td>
          <td class="py-3 px-4">${applicant.name}</td>
          <td class="py-3 px-4">${applicant.position}</td>
          <td class="py-3 px-4">
            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs sm:text-sm">${applicant.status}</span>
          </td>
          <td class="py-3 px-4 text-center">
            <div class="flex justify-center gap-3">
              <button onclick="openModal(${applicant.id}, \'${applicant.status}\')" class="text-green-500 hover:text-green-700">
                <i class="bx bx-edit-alt text-lg sm:text-xl"></i>
              </button>
              <button onclick="deleteApplicant(${applicant.id})" class="text-red-500 hover:text-red-700">
                <i class="bx bx-trash text-lg sm:text-xl"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
      tbody.innerHTML += row;
    });
  } catch (error) {
    console.error("Error loading applicants:", error);
  }
}

// Open modal
function openModal(id, currentStatus) {
  document.getElementById("currentApplicantId").value = id;
  document.getElementById("newStatus").value = currentStatus;
  document.getElementById("statusModal").classList.remove("hidden");
}

// Close modal
function closeModal() {
  document.getElementById("statusModal").classList.add("hidden");
}

// Save status update
async function saveStatus() {
  const id = document.getElementById("currentApplicantId").value;
  const newStatus = document.getElementById("newStatus").value;

  try {
    const response = await fetch(`${apiUrl}?id=${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ status: newStatus })
    });

    const result = await response.json();
    openAlert(result.success || result.error || "Operation completed");
    closeModal();
    loadApplicants(); // refresh table
  } catch (error) {
    console.error("Error updating status:", error);
  }
}

// Delete applicant
function deleteApplicant(id) {
  pendingDeleteId = id;
  document.getElementById("deleteModal").classList.remove("hidden");
}

function closeDeleteModal() {
  pendingDeleteId = null;
  document.getElementById("deleteModal").classList.add("hidden");
}

async function confirmApplicantDelete() {
  if (!pendingDeleteId) return;
  try {
    const response = await fetch(`${apiUrl}?id=${pendingDeleteId}`, { method: "DELETE" });
    const result = await response.json();
    closeDeleteModal();
    openAlert(result.success || result.error || "Operation completed");
    loadApplicants();
  } catch (error) {
    console.error("Error deleting applicant:", error);
  }
}

// Load applicants when page loads
window.onload = loadApplicants;

// Alert helpers
function openAlert(message) {
  document.getElementById("alertMessage").textContent = message;
  document.getElementById("alertModal").classList.remove("hidden");
}
function closeAlert() {
  document.getElementById("alertModal").classList.add("hidden");
}
</script>
';

adminLayout($children);
?>
