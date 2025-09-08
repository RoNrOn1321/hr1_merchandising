<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-6">

  <!-- Header Section -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-700">Job Offer & Acceptance Management</h1>
    <button onclick="openOfferModal()" 
      class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition">
      + Create Job Offer
    </button>
  </div>

  <!-- Job Offer Cards -->
  <div id="jobOffersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Job offers will be rendered here dynamically -->
  </div>
</div>

<!-- Create Offer Modal -->
<div id="offerModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl font-bold mb-4">Create Job Offer</h2>
    <input type="text" id="candidateName" placeholder="Candidate Name" class="w-full border p-2 mb-3 rounded">
    <input type="text" id="position" placeholder="Position" class="w-full border p-2 mb-3 rounded">
    <input type="number" id="salary" placeholder="Salary" class="w-full border p-2 mb-3 rounded">
    <input type="date" id="offerDate" placeholder="Offer Date" class="w-full border p-2 mb-3 rounded">
    <select id="status" class="w-full border p-2 mb-3 rounded">
      <option value="Pending">Pending</option>
      <option value="Accepted">Accepted</option>
      <option value="Rejected">Rejected</option>
    </select>
    <div class="flex justify-end gap-2">
      <button onclick="createOffer()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Save</button>
      <button onclick="closeOfferModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Cancel</button>
    </div>
  </div>
</div>

<!-- Reschedule / Edit Offer Modal -->
<div id="rescheduleModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
    <h2 class="text-xl font-bold mb-4">Reschedule / Update Offer</h2>
    <input type="date" id="rescheduleDate" class="w-full border p-2 mb-3 rounded" placeholder="New Offer Date">
    <select id="rescheduleStatus" class="w-full border p-2 mb-3 rounded">
      <option value="Pending">Pending</option>
      <option value="Accepted">Accepted</option>
      <option value="Rejected">Rejected</option>
    </select>
    <div class="flex justify-end gap-2">
      <button onclick="updateOffer()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition">Update</button>
      <button onclick="closeRescheduleModal()" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500 transition">Cancel</button>
    </div>
  </div>
</div>

<script>
const API_URL = "http://localhost/hr1_merchandising/api/job_offer.php";
let activeOfferId = null;

// Load job offers
async function loadJobOffers() {
  const container = document.getElementById("jobOffersContainer");
  container.innerHTML = "Loading...";
  try {
    const res = await fetch(API_URL);
    const offers = await res.json();
    container.innerHTML = "";

    if (offers.length === 0) {
      container.innerHTML = "<p class=\"text-gray-500\">No job offers yet.</p>";
      return;
    }

    offers.forEach(offer => {
      const card = document.createElement("div");
      card.className = "bg-white rounded-lg shadow-md p-5";

      const statusColor = offer.status === "Accepted" ? "bg-green-100 text-green-600"
                         : offer.status === "Pending" ? "bg-yellow-100 text-yellow-600"
                         : "bg-red-100 text-red-600";

      card.innerHTML = `
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-800">${offer.candidate_name}</h2>
          <span class="${statusColor} px-3 py-1 text-sm rounded-full">${offer.status}</span>
        </div>
        <p class="text-gray-600 text-sm">Position: ${offer.position}</p>
        <p class="text-gray-600 text-sm">Offered Salary: ₱${offer.salary}</p>
        <p class="text-gray-600 text-sm">Offer Date: ${offer.offer_date}</p>
        <div class="mt-4 flex justify-between gap-2">
          <button onclick="openRescheduleModal(${offer.id}, \'${offer.offer_date}\', \'${offer.status}\')" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">Reschedule</button>
          <button onclick="deleteOffer(${offer.id})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Revoke</button>
        </div>
      `;
      container.appendChild(card);
    });

  } catch (err) {
    container.innerHTML = "<p class=\"text-red-500\">Failed to load job offers.</p>";
    console.error(err);
  }
}

// Prefill Create Modal
function openOfferModal(name = "", position = "") {
  document.getElementById("candidateName").value = name;
  document.getElementById("position").value = position;
  document.getElementById("offerModal").classList.remove("hidden");
}

function closeOfferModal() { document.getElementById("offerModal").classList.add("hidden"); }

function openRescheduleModal(id, offerDate, status) {
  activeOfferId = id;
  document.getElementById("rescheduleDate").value = offerDate;
  document.getElementById("rescheduleStatus").value = status;
  document.getElementById("rescheduleModal").classList.remove("hidden");
}

function closeRescheduleModal() {
  activeOfferId = null;
  document.getElementById("rescheduleModal").classList.add("hidden");
}

// Create Offer
async function createOffer() {
  const payload = {
    candidate_name: document.getElementById("candidateName").value,
    position: document.getElementById("position").value,
    salary: Number(document.getElementById("salary").value),
    offer_date: document.getElementById("offerDate").value,
    status: document.getElementById("status").value
  };

  try {
    const res = await fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    alert(data.message);
    closeOfferModal();
    loadJobOffers();
  } catch (err) {
    alert("Failed to create job offer");
    console.error(err);
  }
}

// Update Offer
async function updateOffer() {
  if (!activeOfferId) return;

  const payload = {
    id: activeOfferId,
    offer_date: document.getElementById("rescheduleDate").value,
    status: document.getElementById("rescheduleStatus").value
  };

  try {
    const res = await fetch(API_URL, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    alert(data.message);
    closeRescheduleModal();
    loadJobOffers();
  } catch (err) {
    alert("Failed to update job offer");
    console.error(err);
  }
}

// Delete Offer
async function deleteOffer(id) {
  if (!confirm("Are you sure you want to revoke this offer?")) return;

  try {
    const res = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id })
    });
    const data = await res.json();
    alert(data.message);
    loadJobOffers();
  } catch (err) {
    alert("Failed to delete job offer");
    console.error(err);
  }
}

// Load offers on page load
document.addEventListener("DOMContentLoaded", loadJobOffers);
</script>
';

adminLayout($children);
?>
