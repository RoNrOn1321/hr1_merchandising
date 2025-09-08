<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-6">

  <!-- Header Section -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-700">Performance Tracking</h1>
    <button onclick="openPerformanceModal()" 
      class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition">
      + Add Performance Record
    </button>
  </div>

  <!-- Performance Cards -->
  <div id="performanceContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Performance cards will be loaded dynamically here -->
  </div>
</div>

<!-- Modal for Adding / Editing Performance Record -->
<div id="performanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
    <h2 class="text-xl font-semibold text-gray-700 mb-4" id="performanceModalTitle">Add Performance Record</h2>
    <form id="performanceForm">
      <input type="hidden" id="performanceId" value="">
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Employee Name</label>
        <input type="text" id="employeeName" class="w-full border rounded px-3 py-2" placeholder="Enter name" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Position</label>
        <input type="text" id="employeePosition" class="w-full border rounded px-3 py-2" placeholder="Enter position" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Department</label>
        <input type="text" id="employeeDept" class="w-full border rounded px-3 py-2" placeholder="Enter department" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Score</label>
        <input type="number" id="employeeScore" class="w-full border rounded px-3 py-2" placeholder="Enter score" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Performance Level</label>
        <select id="performanceLevel" class="w-full border rounded px-3 py-2">
          <option>Excellent</option>
          <option>Good</option>
          <option>Average</option>
          <option>Needs Improvement</option>
        </select>
      </div>
      <div class="flex justify-end gap-3">
        <button type="button" onclick="closePerformanceModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
const API_URL = "http://localhost/hr1_merchandising/api/performance.php";

document.addEventListener("DOMContentLoaded", loadPerformanceRecords);

function openPerformanceModal(record = null) {
  document.getElementById("performanceModal").classList.remove("hidden");
  if(record) {
    document.getElementById("performanceModalTitle").innerText = "Edit Performance Record";
    document.getElementById("performanceId").value = record.id;
    document.getElementById("employeeName").value = record.employee_name;
    document.getElementById("employeePosition").value = record.position;
    document.getElementById("employeeDept").value = record.department;
    document.getElementById("employeeScore").value = record.score;
    document.getElementById("performanceLevel").value = record.performance_level;
  } else {
    document.getElementById("performanceModalTitle").innerText = "Add Performance Record";
    document.getElementById("performanceForm").reset();
    document.getElementById("performanceId").value = "";
  }
}

function closePerformanceModal() {
  document.getElementById("performanceModal").classList.add("hidden");
}

// Load performance records dynamically
async function loadPerformanceRecords() {
  try {
    const res = await fetch(API_URL);
    const records = await res.json();
    const container = document.getElementById("performanceContainer");
    container.innerHTML = "";
    records.forEach(record => {
  const card = document.createElement("div");
  card.className = "bg-white rounded-lg shadow-md p-5";
  card.innerHTML = `
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-lg font-semibold text-gray-800">${record.employee_name}</h2>
      <span class="px-3 py-1 text-sm rounded-full ${record.performance_level === "Excellent" ? "bg-green-100 text-green-600" : record.performance_level === "Good" ? "bg-yellow-100 text-yellow-600" : record.performance_level === "Average" ? "bg-blue-100 text-blue-600" : "bg-red-100 text-red-600"}">${record.performance_level}</span>
    </div>
    <p class="text-gray-600 text-sm mb-2">Position: ${record.position}</p>
    <p class="text-gray-600 text-sm mb-2">Department: ${record.department}</p>
    <p class="text-gray-600 text-sm">Score: ${record.score}/100</p>
    <div class="mt-4 flex justify-between">
      <button class="editBtn bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">Edit</button>
      <button class="removeBtn bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Remove</button>
    </div>
  `;
  
  // Add edit listener
  card.querySelector(".editBtn").addEventListener("click", () => openPerformanceModal(record));
  // Add delete listener
  card.querySelector(".removeBtn").addEventListener("click", () => deletePerformance(record.id));

  container.appendChild(card);
});

  } catch(e) {
    alert("Failed to load records: " + e.message);
  }
}

// Submit form (Add / Edit)
document.getElementById("performanceForm").addEventListener("submit", async function(e){
  e.preventDefault();
  const id = document.getElementById("performanceId").value;
  const payload = {
    employee_name: document.getElementById("employeeName").value,
    position: document.getElementById("employeePosition").value,
    department: document.getElementById("employeeDept").value,
    score: document.getElementById("employeeScore").value,
    performance_level: document.getElementById("performanceLevel").value
  };
  try {
    const res = await fetch(API_URL, {
      method: id ? "PUT" : "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(id ? {...payload, id: id} : payload)
    });
    const result = await res.json();
    alert(result.message);
    closePerformanceModal();
    loadPerformanceRecords();
  } catch(e) {
    alert("Failed to save record: " + e.message);
  }
});

// Delete record
async function deletePerformance(id) {
  if(!confirm("Are you sure you want to remove this record?")) return;
  try {
    const res = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({id})
    });
    const result = await res.json();
    alert(result.message);
    loadPerformanceRecords();
  } catch(e) {
    alert("Failed to remove record: " + e.message);
  }
}
</script>
';

adminLayout($children);
?>
