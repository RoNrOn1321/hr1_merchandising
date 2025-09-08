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
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Module Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Leadership Training</h2>
        <span class="bg-green-100 text-green-600 px-3 py-1 text-sm rounded-full">Active</span>
      </div>
      <p class="text-gray-600 text-sm">Duration: 4 Weeks</p>
      <p class="text-gray-600 text-sm">Category: Professional Development</p>
      <p class="text-gray-600 text-sm">Start Date: Aug 10, 2025</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">View Module</button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Archive</button>
      </div>
    </div>

    <!-- Module Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Advanced Sales Techniques</h2>
        <span class="bg-yellow-100 text-yellow-600 px-3 py-1 text-sm rounded-full">Upcoming</span>
      </div>
      <p class="text-gray-600 text-sm">Duration: 2 Weeks</p>
      <p class="text-gray-600 text-sm">Category: Sales Training</p>
      <p class="text-gray-600 text-sm">Start Date: Sept 15, 2025</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">Edit</button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">Cancel</button>
      </div>
    </div>

    <!-- Module Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Customer Service Mastery</h2>
        <span class="bg-blue-100 text-blue-600 px-3 py-1 text-sm rounded-full">Completed</span>
      </div>
      <p class="text-gray-600 text-sm">Duration: 3 Weeks</p>
      <p class="text-gray-600 text-sm">Category: Customer Care</p>
      <p class="text-gray-600 text-sm">Start Date: June 5, 2025</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">View Details</button>
        <button class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600 transition">Archive</button>
      </div>
    </div>

  </div>
</div>

<!-- Modal for Adding New Module -->
<div id="moduleModal" class="fixed inset-0 bg-black bg-opacity-40 hidden flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-lg shadow-lg">
    <h2 class="text-xl font-bold text-gray-700 mb-4">Add New Learning Module</h2>
    <form>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Module Name</label>
        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter module name">
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Category</label>
        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter category">
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Duration</label>
        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 4 Weeks">
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 mb-2">Start Date</label>
        <input type="date" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
      </div>
      <div class="flex justify-end gap-2">
        <button type="button" onclick="closeModuleModal()" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save Module</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openModuleModal() {
    document.getElementById("moduleModal").classList.remove("hidden");
  }
  function closeModuleModal() {
    document.getElementById("moduleModal").classList.add("hidden");
  }
</script>
';

adminLayout($children);
?>
