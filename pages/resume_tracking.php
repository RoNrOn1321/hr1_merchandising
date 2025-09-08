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

    <!-- Resume Cards (loaded dynamically) -->
    <div id="resumeCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
</div>

<!-- Upload Resume Modal -->
<div id="uploadModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Upload Resume</h2>
    <form id="resumeForm" enctype="multipart/form-data">
      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Applicant Name</label>
      <input id="applicantName" type="text" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base" placeholder="Enter name" required>

      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Position</label>
      <input id="positionInput" type="text" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base" placeholder="Enter position" required>

      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Status</label>
      <select id="statusInput" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base">
        <option value="Under Review" selected>Under Review</option>
        <option value="Shortlisted">Shortlisted</option>
        <option value="Interviewed">Interviewed</option>
        <option value="Hired">Hired</option>
        <option value="Rejected">Rejected</option>
      </select>

      <label class="block mb-2 text-gray-700 text-sm sm:text-base">Applicant Image</label>
      <input id="imageInput" name="image" type="file" accept="image/*" class="w-full border rounded-lg p-2 mb-4 text-sm sm:text-base">

      <button type="submit" class="w-full bg-indigo-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-indigo-600 transition">
        Save
      </button>
    </form>
    <button onclick="closeUploadModal()" class="w-full mt-3 bg-red-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-red-600 transition">
      Cancel
    </button>
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

<script>
const API_URL = "http://localhost/hr1_merchandising/api/resume_tracking.php";
let resumes = [];

document.addEventListener("DOMContentLoaded", () => {
  loadResumes();
  const form = document.getElementById("resumeForm");
  form.addEventListener("submit", handleSubmit);
});

async function loadResumes() {
  try {
    const response = await fetch(API_URL);
    if (!response.ok) {
      const text = await response.text();
      throw new Error(`HTTP ${response.status}: ${text}`);
    }
    const data = await response.json();
    resumes = Array.isArray(data) ? data : (data?.data || []);
    renderResumes();
  } catch (e) {
    console.error("Failed to load resumes", e);
    openAlert("Failed to load resumes.");
  }
}

function renderResumes() {
  const container = document.getElementById("resumeCards");
  container.innerHTML = "";
  resumes.forEach(r => {
    const badgeMap = {
      "Under Review": "bg-yellow-100 text-yellow-600",
      "Shortlisted": "bg-blue-100 text-blue-600",
      "Interviewed": "bg-purple-100 text-purple-600",
      "Hired": "bg-teal-100 text-teal-600",
      "Rejected": "bg-red-100 text-red-600",
    };
    const badgeClass = badgeMap[r.status] || "bg-gray-100 text-gray-600";
    const card = document.createElement("div");
    card.className = "bg-white p-4 sm:p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100";
    const created = r.created_at ? new Date(r.created_at).toLocaleDateString() : "";
    card.innerHTML = `
      <div class="w-full mb-3 flex justify-center">
        <div class="h-24 w-24 rounded-full overflow-hidden bg-gray-100 border border-gray-200">
          ${r.image_url ? `<img src="${r.image_url}" alt="${r.name}" class="h-full w-full object-cover">` : `<div class=\"h-full w-full flex items-center justify-center text-gray-400 text-xs\">No Image</div>`}
        </div>
      </div>
      <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-1">${r.name}</h2>
      <p class="text-gray-600 text-sm sm:text-base mb-3">${r.position}</p>
      <div class="flex justify-between items-center text-xs sm:text-sm mb-4">
        <span class="text-gray-500">${created}</span>
        <span class="px-2 py-1 text-xs sm:text-sm font-medium rounded-full ${badgeClass}">${r.status}</span>
      </div>
      <div class="w-full inline-block text-center bg-slate-100 text-slate-700 py-2 text-sm sm:text-base rounded-lg">
        Applicant
      </div>
    `;
    container.appendChild(card);
  });
}

async function handleSubmit(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append("name", document.getElementById("applicantName").value);
  formData.append("position", document.getElementById("positionInput").value);
  formData.append("status", document.getElementById("statusInput").value);
  const imageFile = document.getElementById("imageInput").files[0];
  if (imageFile) formData.append("image", imageFile);
  try {
    const res = await fetch(API_URL, {
      method: "POST",
      body: formData,
    });
    if (!res.ok) throw new Error("Failed to create resume");
    closeUploadModal();
    // Reset form
    document.getElementById("resumeForm").reset();
    await loadResumes();
  } catch (e) {
    console.error(e);
    openAlert("Failed to save resume.");
  }
}

function openUploadModal() {
  document.getElementById("uploadModal").classList.remove("hidden");
}
function closeUploadModal() {
  document.getElementById("uploadModal").classList.add("hidden");
}

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
