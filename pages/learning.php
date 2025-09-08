<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-6">

  <!-- Header Section -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-700">Learning Modules</h1>
    <button onclick="openModuleModal()" 
      class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition">
      + Add New Module
    </button>
  </div>

  <!-- Learning Module Cards -->
  <div id="modulesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Module cards will be loaded dynamically here -->
  </div>
</div>

<!-- Modal for Adding / Editing Module -->
<div id="moduleModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold text-gray-700 mb-4" id="moduleModalTitle">Add New Learning Module</h2>
    <form id="moduleForm">
      <input type="hidden" id="moduleId" value="">
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Module Name</label>
        <input type="text" id="moduleName" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter module name" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Category</label>
        <input type="text" id="moduleCategory" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter category" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Duration</label>
        <input type="text" id="moduleDuration" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 4 Weeks" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Start Date</label>
        <input type="date" id="moduleStartDate" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Status</label>
        <select id="moduleStatus" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
          <option value="Upcoming">Upcoming</option>
          <option value="Active">Active</option>
          <option value="Completed">Completed</option>
        </select>
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModuleModal()" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600" id="moduleSaveBtn">Save Module</button>
      </div>
    </form>
  </div>
</div>

<script>
const API_URL = "http://localhost/hr1_merchandising/api/learning.php";

document.addEventListener("DOMContentLoaded", loadModules);

// Open/Close modal
function openModuleModal(module = null) {
  document.getElementById("moduleModal").classList.remove("hidden");
  if(module) {
    document.getElementById("moduleModalTitle").innerText = "Edit Module";
    document.getElementById("moduleId").value = module.id;
    document.getElementById("moduleName").value = module.module_name;
    document.getElementById("moduleCategory").value = module.category;
    document.getElementById("moduleDuration").value = module.duration;
    document.getElementById("moduleStartDate").value = module.start_date;
    document.getElementById("moduleStatus").value = module.status;
  } else {
    document.getElementById("moduleModalTitle").innerText = "Add New Learning Module";
    document.getElementById("moduleForm").reset();
    document.getElementById("moduleId").value = "";
  }
}

function closeModuleModal() {
  document.getElementById("moduleModal").classList.add("hidden");
}

// Load modules
async function loadModules() {
  try {
    const res = await fetch(API_URL);
    const modules = await res.json();
    const container = document.getElementById("modulesContainer");
    container.innerHTML = "";
    modules.forEach(module => {
      container.innerHTML += `
      <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-800">${module.module_name}</h2>
          <span class="px-3 py-1 text-sm rounded-full ${module.status === "Active" ? "bg-green-100 text-green-600" : (module.status === "Upcoming" ? "bg-yellow-100 text-yellow-600" : "bg-blue-100 text-blue-600")}" >${module.status}</span>
        </div>
        <p class="text-gray-600 text-sm">Duration: ${module.duration}</p>
        <p class="text-gray-600 text-sm">Category: ${module.category}</p>
        <p class="text-gray-600 text-sm">Start Date: ${module.start_date}</p>
        <div class="mt-4 flex justify-between">
          <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition edit-btn" 
                  data-module=\'${JSON.stringify(module)}\'>
            Edit
          </button>
          <button onclick="deleteModule(${module.id})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Archive</button>
        </div>
      </div>
      `;
    });

    // Attach click events to Edit buttons
    document.querySelectorAll(".edit-btn").forEach(btn => {
      btn.addEventListener("click", function() {
        const module = JSON.parse(this.getAttribute("data-module"));
        openModuleModal(module);
      });
    });
    
  } catch(e) {
    alert("Failed to load modules: " + e.message);
  }
}

// Submit form
document.getElementById("moduleForm").addEventListener("submit", async function(e){
  e.preventDefault();
  const id = document.getElementById("moduleId").value;
  const payload = {
    module_name: document.getElementById("moduleName").value,
    category: document.getElementById("moduleCategory").value,
    duration: document.getElementById("moduleDuration").value,
    start_date: document.getElementById("moduleStartDate").value,
    status: document.getElementById("moduleStatus").value
  };
  try {
    const res = await fetch(API_URL, {
      method: id ? "PUT" : "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(id ? {...payload, id: id} : payload)
    });
    const result = await res.json();
    alert(result.message);
    closeModuleModal();
    loadModules();
  } catch(e) {
    alert("Failed to save module: " + e.message);
  }
});

// Delete module
async function deleteModule(id) {
  if(!confirm("Are you sure you want to archive this module?")) return;
  try {
    const res = await fetch(API_URL, {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({id})
    });
    const result = await res.json();
    alert(result.message);
    loadModules();
  } catch(e) {
    alert("Failed to archive module: " + e.message);
  }
}
</script>
';

adminLayout($children);
?>
