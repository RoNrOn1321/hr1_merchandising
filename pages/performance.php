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
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Employee Performance Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-800">John Doe</h2>
        <span class="bg-green-100 text-green-600 px-3 py-1 text-sm rounded-full">Excellent</span>
      </div>
      <p class="text-gray-600 text-sm mb-2">Position: Sales Executive</p>
      <p class="text-gray-600 text-sm mb-2">Department: Marketing</p>
      <p class="text-gray-600 text-sm">Score: 95/100</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
          View Report
        </button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
          Archive
        </button>
      </div>
    </div>

    <!-- Another Employee Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-semibold text-gray-800">Jane Smith</h2>
        <span class="bg-yellow-100 text-yellow-600 px-3 py-1 text-sm rounded-full">Good</span>
      </div>
      <p class="text-gray-600 text-sm mb-2">Position: HR Specialist</p>
      <p class="text-gray-600 text-sm mb-2">Department: Human Resources</p>
      <p class="text-gray-600 text-sm">Score: 82/100</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
          Edit
        </button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
          Remove
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Modal for Adding Performance Record -->
<div id="performanceModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
    <h2 class="text-xl font-semibold text-gray-700 mb-4">Add Performance Record</h2>
    <form>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Employee Name</label>
        <input type="text" class="w-full border rounded px-3 py-2" placeholder="Enter name">
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Score</label>
        <input type="number" class="w-full border rounded px-3 py-2" placeholder="Enter score">
      </div>
      <div class="mb-4">
        <label class="block text-gray-600 text-sm mb-1">Performance Level</label>
        <select class="w-full border rounded px-3 py-2">
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
function openPerformanceModal() {
  document.getElementById("performanceModal").classList.remove("hidden");
}
function closePerformanceModal() {
  document.getElementById("performanceModal").classList.add("hidden");
}
</script>
';

adminLayout($children);
?>
