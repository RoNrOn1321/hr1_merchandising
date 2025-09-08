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
    <div id="jobCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Jobs will be loaded dynamically -->
    </div>
</div>

<!-- Add/Edit Job Modal -->
<div id="jobModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md sm:max-w-lg p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4" id="jobModalTitle">Post New Job</h2>
    <form id="jobForm">
      <input type="hidden" id="jobId">

      <label class="block mb-2 font-medium text-sm sm:text-base">Job Title</label>
      <input type="text" id="jobTitle" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" required>

      <label class="block mb-2 font-medium text-sm sm:text-base">Description</label>
      <textarea id="jobDescription" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" rows="3" required></textarea>

      <label class="block mb-2 font-medium text-sm sm:text-base">Requirements</label>
      <textarea id="jobRequirements" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" rows="3" required></textarea>

      <label class="block mb-2 font-medium text-sm sm:text-base">Location</label>
      <input type="text" id="jobLocation" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" required>

      <label class="block mb-2 font-medium text-sm sm:text-base">Department</label>
      <input type="text" id="jobDepartment" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" required>

      <label class="block mb-2 font-medium text-sm sm:text-base">Employment Type</label>
      <select id="jobType" class="w-full border p-2 rounded mb-3 text-sm sm:text-base" required>
        <option value="Full-time">Full-time</option>
        <option value="Part-time">Part-time</option>
        <option value="Contract">Contract</option>
        <option value="Temporary">Temporary</option>
      </select>

      <label class="block mb-2 font-medium text-sm sm:text-base">Salary Range</label>
      <input type="text" id="jobSalary" class="w-full border p-2 rounded mb-3 text-sm sm:text-base">

      <label class="block mb-2 font-medium text-sm sm:text-base">Status</label>
      <select id="jobStatus" class="w-full border p-2 rounded mb-4 text-sm sm:text-base">
        <option value="Draft">Draft</option>
        <option value="Open">Open</option>
        <option value="Closed">Closed</option>
      </select>

      <div class="flex gap-2">
        <button type="submit" class="flex-1 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition text-sm sm:text-base">Save</button>
        <button type="button" onclick="closeJobModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition text-sm sm:text-base">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- View Job Modal -->
<div id="jobViewModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
    <h2 class="text-2xl font-bold mb-4" id="viewJobTitle"></h2>
    <p class="text-gray-700 mb-2"><strong>Description:</strong> <span id="viewJobDescription"></span></p>
    <p class="text-gray-700 mb-2"><strong>Requirements:</strong> <span id="viewJobRequirements"></span></p>
    <p class="text-gray-700 mb-2"><strong>Location:</strong> <span id="viewJobLocation"></span></p>
    <p class="text-gray-700 mb-2"><strong>Department:</strong> <span id="viewJobDepartment"></span></p>
    <p class="text-gray-700 mb-2"><strong>Employment Type:</strong> <span id="viewJobType"></span></p>
    <p class="text-gray-700 mb-2"><strong>Salary:</strong> <span id="viewJobSalary"></span></p>
    <p class="text-gray-700 mb-4"><strong>Status:</strong> <span id="viewJobStatus"></span></p>

    <div class="flex gap-2">
      <button onclick="closeViewModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition">Close</button>
    </div>
  </div>
</div>

<!-- Confirm Delete Modal -->
<div id="confirmModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-lg font-bold mb-4">Confirm Delete</h2>
    <p class="text-gray-700 mb-4">Are you sure you want to delete this job posting?</p>
    <div class="flex gap-2">
      <button onclick="confirmDelete()" class="flex-1 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">Delete</button>
      <button onclick="closeConfirmModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition">Cancel</button>
    </div>
  </div>
</div>

<script>
let jobs = [];
let jobToDelete = null;
const API_URL = "http://localhost/hr1_merchandising/api/job_posting.php";

// Load jobs on page load
document.addEventListener("DOMContentLoaded", loadJobs);

// Load all jobs
async function loadJobs() {
  try {
    const response = await fetch(API_URL);
    jobs = await response.json();
    renderJobs();
  } catch (error) {
    console.error("Error loading jobs:", error);
    alert("Failed to load jobs. Please try again.");
  }
}

// Render jobs to the page
function renderJobs() {
  const jobCards = document.getElementById("jobCards");
  jobCards.innerHTML = "";

  jobs.forEach((job) => {
    const jobCard = document.createElement("div");
    jobCard.className =
      "bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition";
    jobCard.innerHTML = `
        <div class="p-4">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-bold text-lg text-gray-800">${job.title}</h3>
                    <p class="text-gray-600 text-sm">${job.department} • ${job.location}</p>
                </div>
                <span class="px-2 py-1 text-xs rounded-full ${
                  job.status === "Open"
                    ? "bg-green-100 text-green-800"
                    : job.status === "Closed"
                    ? "bg-red-100 text-red-800"
                    : "bg-gray-100 text-gray-800"
                }">
                    ${job.status}
                </span>
            </div>
            <div class="mt-4 flex gap-2">
                <button onclick="viewJob(${job.id})" class="text-blue-500 hover:text-blue-700">
                    <i class="bx bx-show"></i> View
                </button>
                <button onclick="editJob(${job.id})" class="text-yellow-500 hover:text-yellow-700">
                    <i class="bx bx-edit"></i> Edit
                </button>
                <button onclick="showDeleteConfirm(${job.id})" class="text-red-500 hover:text-red-700">
                    <i class="bx bx-trash"></i> Delete
                </button>
            </div>
        </div>
    `;
    jobCards.appendChild(jobCard);
  });
}

// Form submission handler
async function handleSubmit(event) {
  event.preventDefault();

  const jobId = document.getElementById("jobId").value;
  const jobData = {
    title: document.getElementById("jobTitle").value,
    description: document.getElementById("jobDescription").value,
    requirements: document.getElementById("jobRequirements").value,
    location: document.getElementById("jobLocation").value,
    department: document.getElementById("jobDepartment").value,
    employment_type: document.getElementById("jobType").value,
    salary_range: document.getElementById("jobSalary").value,
    status: document.getElementById("jobStatus").value,
  };

  try {
    const url = jobId ? `${API_URL}?id=${jobId}` : API_URL;
    const method = jobId ? "PUT" : "POST";

    const response = await fetch(url, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(jobData),
    });

    if (!response.ok) {
      throw new Error("Failed to save job");
    }

    closeJobModal();
    loadJobs();
  } catch (error) {
    console.error("Error saving job:", error);
    alert("Failed to save job. Please try again.");
  }
}

// Open modal for new job
function openJobModal() {
  document.getElementById("jobModalTitle").textContent = "Post New Job";
  document.getElementById("jobForm").reset();
  document.getElementById("jobId").value = "";
  document.getElementById("jobModal").classList.remove("hidden");
}

// Close job modal
function closeJobModal() {
  document.getElementById("jobModal").classList.add("hidden");
}

// View job details
async function viewJob(id) {
  const job = jobs.find((j) => j.id == id);
  if (!job) return;

  document.getElementById("viewJobTitle").textContent = job.title;
  document.getElementById("viewJobDescription").textContent = job.description;
  document.getElementById("viewJobRequirements").textContent = job.requirements;
  document.getElementById("viewJobLocation").textContent = job.location;
  document.getElementById("viewJobDepartment").textContent = job.department;
  document.getElementById("viewJobType").textContent = job.employment_type;
  document.getElementById("viewJobSalary").textContent =
    job.salary_range || "Not specified";
  document.getElementById("viewJobStatus").textContent = job.status;

  document.getElementById("jobViewModal").classList.remove("hidden");
}

// Close view modal
function closeViewModal() {
  document.getElementById("jobViewModal").classList.add("hidden");
}

// Edit job
async function editJob(id) {
  const job = jobs.find((j) => j.id == id);
  if (!job) return;

  document.getElementById("jobModalTitle").textContent = "Edit Job Posting";
  document.getElementById("jobId").value = job.id;
  document.getElementById("jobTitle").value = job.title;
  document.getElementById("jobDescription").value = job.description;
  document.getElementById("jobRequirements").value = job.requirements;
  document.getElementById("jobLocation").value = job.location;
  document.getElementById("jobDepartment").value = job.department;
  document.getElementById("jobType").value = job.employment_type;
  document.getElementById("jobSalary").value = job.salary_range || "";
  document.getElementById("jobStatus").value = job.status;

  document.getElementById("jobModal").classList.remove("hidden");
}

// Show delete confirmation
function showDeleteConfirm(id) {
  jobToDelete = id;
  document.getElementById("confirmModal").classList.remove("hidden");
}

// Close confirm modal
function closeConfirmModal() {
  jobToDelete = null;
  document.getElementById("confirmModal").classList.add("hidden");
}

// Delete job
async function confirmDelete() {
  if (!jobToDelete) return;

  try {
    const response = await fetch(`${API_URL}?id=${jobToDelete}`, {
      method: "DELETE",
    });

    if (!response.ok) {
      throw new Error("Failed to delete job");
    }

    closeConfirmModal();
    loadJobs();
  } catch (error) {
    console.error("Error deleting job:", error);
    alert("Failed to delete job. Please try again.");
  }
}

document.getElementById("jobForm").addEventListener("submit", handleSubmit);
</script>
';

adminLayout($children);
?>
