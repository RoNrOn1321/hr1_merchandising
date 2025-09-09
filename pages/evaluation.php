<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-6">

  <!-- Header Section -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-700">Evaluation & Feedback</h1>
    <button onclick="openFeedbackModal()" 
      class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition">
      + New Feedback
    </button>
  </div>

  <!-- Feedback Cards -->
  <div id="feedbackContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cards will be loaded dynamically -->
  </div>
</div>

<!-- Modal -->
<div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4" id="feedbackModalTitle">Add New Feedback</h2>
    <form id="feedbackForm">
      <input type="hidden" id="feedbackId" value="">
      <label class="block mb-2 text-gray-700">Employee Name</label>
      <input type="text" id="employeeName" class="w-full border rounded p-2 mb-4" placeholder="Enter name" required>

      <label class="block mb-2 text-gray-700">Department</label>
      <input type="text" id="employeeDept" class="w-full border rounded p-2 mb-4" placeholder="Enter department" required>

      <label class="block mb-2 text-gray-700">Rating</label>
      <select id="feedbackRating" class="w-full border rounded p-2 mb-4">
        <option value="5">Excellent</option>
        <option value="4">Good</option>
        <option value="3">Average</option>
        <option value="2">Needs Improvement</option>
        <option value="1">Poor</option>
      </select>

      <label class="block mb-2 text-gray-700">Feedback</label>
      <textarea id="feedbackText" class="w-full border rounded p-2 mb-4" rows="3" placeholder="Write feedback..." required></textarea>

      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeFeedbackModal()" 
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const API_URL = "http://localhost/hr1_merchandising/api/evaluation.php";

document.addEventListener("DOMContentLoaded", loadFeedbackRecords);

// Open/Close Modal
function openFeedbackModal(record = null) {
  document.getElementById("feedbackModal").classList.remove("hidden");
  document.getElementById("feedbackModal").classList.add("flex");
  if(record) {
    document.getElementById("feedbackModalTitle").innerText = "Edit Feedback";
    document.getElementById("feedbackId").value = record.id;
    document.getElementById("employeeName").value = record.employee_name;
    document.getElementById("employeeDept").value = record.department;
    document.getElementById("feedbackRating").value = record.rating;
    document.getElementById("feedbackText").value = record.feedback_text;
  } else {
    document.getElementById("feedbackModalTitle").innerText = "Add New Feedback";
    document.getElementById("feedbackForm").reset();
    document.getElementById("feedbackId").value = "";
  }
}

function closeFeedbackModal() {
  document.getElementById("feedbackModal").classList.add("hidden");
  document.getElementById("feedbackModal").classList.remove("flex");
}

// Load Feedback Records
async function loadFeedbackRecords() {
  try {
    const res = await fetch(API_URL);
    const records = await res.json();
    const container = document.getElementById("feedbackContainer");
    container.innerHTML = "";
    records.forEach(record => {
      const ratingClass = record.rating >= 4 ? "bg-green-100 text-green-600" 
        : record.rating >= 3 ? "bg-yellow-100 text-yellow-600" 
        : "bg-red-100 text-red-600";
      const ratingText = record.rating === 5 ? "Excellent" 
        : record.rating === 4 ? "Good" 
        : record.rating === 3 ? "Average"
        : record.rating === 2 ? "Needs Improvement"
        : "Poor";
      container.innerHTML += `
      <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-800">${record.employee_name}</h2>
          <span class="px-3 py-1 text-sm rounded-full ${ratingClass}">${ratingText}</span>
        </div>
        <p class="text-gray-600 text-sm">Department: ${record.department}</p>
        <p class="text-gray-600 text-sm">Evaluator: ${record.evaluator}</p>
        <p class="text-gray-600 text-sm">Date: ${record.date}</p>
        <div class="mt-4 flex justify-between">
          <button onclick=\'openFeedbackModal(${JSON.stringify(record)})\' class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">Edit</button>
          <button onclick="deleteFeedback(${record.id})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Delete</button>
        </div>
      </div>`;
    });
  } catch(e) {
    alert("Failed to load feedback: " + e.message);
  }
}

// Add/Edit Feedback
document.getElementById("feedbackForm").addEventListener("submit", async function(e) {
  e.preventDefault();
  const id = document.getElementById("feedbackId").value;
  const payload = {
    employee_name: document.getElementById("employeeName").value.trim(),
    department: document.getElementById("employeeDept").value.trim(),
    rating: document.getElementById("feedbackRating").value.trim(),
    feedback_text: document.getElementById("feedbackText").value.trim()
  };

  // Validate payload before sending
  if (!payload.employee_name || !payload.department || !payload.rating || !payload.feedback_text) {
    alert("All fields are required.");
    return;
  }

  if (!["5", "4", "3", "2", "1"].includes(payload.rating)) {
    alert("Invalid rating value.");
    return;
  }

  try {
    const res = await fetch(API_URL, {
      method: id ? "PUT" : "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(id ? { ...payload, id: id } : payload)
    });
    const result = await res.json();

    if (res.ok) {
      alert(result.message);
      closeFeedbackModal();
      loadFeedbackRecords();
    } else {
      alert(result.error || "Failed to save feedback.");
    }
  } catch (e) {
    alert("Failed to save feedback: " + e.message);
  }
});

// Delete Feedback
async function deleteFeedback(id) {
  if(!confirm("Are you sure you want to delete this feedback?")) return;
  try {
    const res = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({id})
    });
    const result = await res.json();
    alert(result.message);
    loadFeedbackRecords();
  } catch(e) {
    alert("Failed to delete feedback: " + e.message);
  }
}
</script>
';

adminLayout($children);
?>
