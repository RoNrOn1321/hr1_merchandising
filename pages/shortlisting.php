<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-4 sm:p-6 w-full ">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Candidate Shortlisting & Screening</h1>
        <button onclick="openFilterModal()" 
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm sm:text-base">
            <i class="bx bx-filter-alt text-lg sm:text-xl"></i> 
            <span class="hidden sm:inline">Filter</span>
        </button>
    </div>

    <!-- Shortlisted Candidates Table -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Shortlisted Candidates</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left min-w-[700px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-4 text-sm sm:text-base">Image</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Name</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Position</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Screening Score</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Status</th>
                        <th class="py-3 px-4 text-center text-sm sm:text-base">Action</th>
                    </tr>
                </thead>
                <tbody id="shortlistTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit / Delete Modal -->
<div id="candidateModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl sm:text-2xl font-bold mb-4">Update Candidate</h2>
        <input type="text" id="candidateName" 
            class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" 
            placeholder="Candidate Name">
        <input type="text" id="candidatePosition"
            class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base"
            placeholder="Position">
        <input type="number" id="candidateScore"
            class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base"
            placeholder="Screening Score" min="0" max="100">
        <select id="candidateStatus" 
            class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base">
            <option value="Shortlisted">Shortlisted</option>
            <option value="Pending Interview">Pending Interview</option>
            <option value="Under Review">Under Review</option>
            <option value="Rejected">Rejected</option>
        </select>
        <textarea id="candidateNotes" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" rows="3" placeholder="Notes (optional)"></textarea>
        <div class="flex justify-between gap-3">
            <button onclick="saveCandidate()" 
                class="w-1/2 bg-blue-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-blue-600 transition">
                Save Changes
            </button>
            <button onclick="closeModal()" 
                class="w-1/2 bg-red-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-red-600 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Confirm Delete</h2>
    <p class="text-gray-700 mb-4">Are you sure you want to remove this shortlisted entry?</p>
    <div class="flex gap-2">
      <button onclick="confirmDelete()" class="flex-1 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">Delete</button>
      <button onclick="closeDeleteModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition">Cancel</button>
    </div>
  </div>
</div>

<!-- Create Shortlist Modal -->
<div id="createModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Add to Shortlist</h2>
    <input type="text" id="createName" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" placeholder="Applicant Name (or leave if using ID)">
    <input type="number" id="createApplicantId" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" placeholder="Applicant ID (optional)">
    <input type="text" id="createPosition" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" placeholder="Position">
    <input type="number" id="createScore" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" placeholder="Screening Score" min="0" max="100">
    <select id="createStatus" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base">
      <option value="Under Review">Under Review</option>
      <option value="Shortlisted">Shortlisted</option>
      <option value="Pending Interview">Pending Interview</option>
      <option value="Rejected">Rejected</option>
    </select>
    <textarea id="createNotes" class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" rows="3" placeholder="Notes (optional)"></textarea>
    <div class="flex gap-2">
      <button onclick="createShortlist()" class="flex-1 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">Add</button>
      <button onclick="closeCreateModal()" class="flex-1 bg-gray-400 text-white py-2 rounded-lg hover:bg-gray-500 transition">Cancel</button>
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

<script>
const API_URL = "http://localhost/hr1_merchandising/api/shortlisting.php";
let shortlists = [];
let activeShortlistId = null;
let pendingDeleteId = null;

document.addEventListener("DOMContentLoaded", loadShortlists);

async function loadShortlists() {
  try {
    const res = await fetch(API_URL);
    shortlists = await res.json();
    renderShortlists();
  } catch (e) {
    openAlert("Failed to load shortlists");
  }
}

function renderShortlists() {
  const tbody = document.getElementById("shortlistTableBody");
  tbody.innerHTML = "";

  if (!Array.isArray(shortlists) || shortlists.length === 0) {
    tbody.innerHTML = `<tr><td class="py-6 px-4 text-center text-gray-500" colspan="6">No shortlisted candidates yet.</td></tr>`;
    return;
  }

  shortlists.forEach(row => {
    const badgeMap = {
      "Shortlisted": "bg-green-100 text-green-700",
      "Pending Interview": "bg-yellow-100 text-yellow-700",
      "Under Review": "bg-blue-100 text-blue-700",
      "Rejected": "bg-red-100 text-red-700",
    };
    const badge = badgeMap[row.shortlist_status] || "bg-gray-100 text-gray-700";
    const imgCell = row.image_url ? 
      `<img src="${row.image_url}" alt="${row.name}" class="h-10 w-10 rounded-full object-cover border border-gray-200" />` : 
      `<div class="h-10 w-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400 text-[10px]">No Img</div>`;

    // Safely pass all fields to edit modal
    const data = JSON.stringify({
      id: row.id,
      name: row.name,
      position: row.position,
      screening_score: row.screening_score,
      shortlist_status: row.shortlist_status,
      notes: row.notes || ""
    }).replace(/"/g, "&quot;");

    const tr = document.createElement("tr");
    tr.className = "border-b hover:bg-gray-50 text-sm sm:text-base";
    tr.innerHTML = `
      <td class="py-3 px-4"><div class="flex items-center">${imgCell}</div></td>
      <td class="py-3 px-4">${row.name}</td>
      <td class="py-3 px-4">${row.position}</td>
      <td class="py-3 px-4">${row.screening_score}%</td>
      <td class="py-3 px-4"><span class="px-3 py-1 rounded-full text-xs sm:text-sm ${badge}">${row.shortlist_status}</span></td>
      <td class="py-3 px-4 text-center">
        <div class="flex justify-center gap-3">
          <button onclick="openModal(${data})" class="text-blue-500 hover:text-blue-700 text-lg sm:text-xl"><i class="bx bx-edit"></i></button>
          <button onclick="openDeleteModal(${row.id})" class="text-red-500 hover:text-red-700 text-lg sm:text-xl"><i class="bx bx-trash"></i></button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// Edit Modal
function openModal(candidate) {
  activeShortlistId = candidate.id;
  document.getElementById("candidateName").value = candidate.name;
  document.getElementById("candidatePosition").value = candidate.position;
  document.getElementById("candidateScore").value = candidate.screening_score;
  document.getElementById("candidateStatus").value = candidate.shortlist_status;
  document.getElementById("candidateNotes").value = candidate.notes;
  document.getElementById("candidateModal").classList.remove("hidden");
}

function closeModal() {
  activeShortlistId = null;
  document.getElementById("candidateModal").classList.add("hidden");
}

async function saveCandidate() {
  if (!activeShortlistId) return;

  const payload = {
    name: document.getElementById("candidateName").value,
    position: document.getElementById("candidatePosition").value,
    screening_score: Number(document.getElementById("candidateScore").value),
    shortlist_status: document.getElementById("candidateStatus").value,
    notes: document.getElementById("candidateNotes").value
  };

  try {
    const res = await fetch(`${API_URL}?id=${activeShortlistId}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });

    const result = await res.json();

    if (!res.ok) throw new Error(result.error || "Failed to update candidate");

    closeModal();
    openAlert(result.message || "Candidate updated successfully");
    loadShortlists();
  } catch (e) {
    openAlert(e.message || "Failed to update candidate");
  }
}

// Delete Modal
function openDeleteModal(id) {
  pendingDeleteId = id;
  document.getElementById("deleteModal").classList.remove("hidden");
}

function closeDeleteModal() {
  pendingDeleteId = null;
  document.getElementById("deleteModal").classList.add("hidden");
}

async function confirmDelete() {
  if (!pendingDeleteId) return;
  try {
    const res = await fetch(`${API_URL}?id=${pendingDeleteId}`, { method: "DELETE" });
    if (!res.ok) throw new Error("Delete failed");
    closeDeleteModal();
    openAlert("Shortlist deleted");
    loadShortlists();
  } catch (e) {
    openAlert("Failed to delete shortlist");
  }
}

// Alert Modal
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
