<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-6">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-700">Reports & Analytics</h1>
    <button onclick="openReportModal()" 
      class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition">
      + Generate Report
    </button>
  </div>

  <!-- Report Summary Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <!-- Training Completion -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Training Completion Rate</h2>
      <p class="text-3xl font-bold text-blue-600">92%</p>
      <p class="text-sm text-gray-500 mt-1">Compared to 85% last quarter</p>
    </div>

    <!-- Performance Growth -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Employee Performance Growth</h2>
      <p class="text-3xl font-bold text-green-600">+15%</p>
      <p class="text-sm text-gray-500 mt-1">Overall improvement this year</p>
    </div>

    <!-- Feedback Collected -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <h2 class="text-lg font-semibold text-gray-800 mb-2">Feedback Collected</h2>
      <p class="text-3xl font-bold text-yellow-600">348</p>
      <p class="text-sm text-gray-500 mt-1">Reports generated this month</p>
    </div>
  </div>

  <!-- Reports Table -->
  <div class="bg-white rounded-lg shadow-md p-5">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Reports</h2>
    <table class="min-w-full text-left text-sm text-gray-700">
      <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
        <tr>
          <th class="py-3 px-4">Report Name</th>
          <th class="py-3 px-4">Category</th>
          <th class="py-3 px-4">Date Generated</th>
          <th class="py-3 px-4">Status</th>
          <th class="py-3 px-4">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b hover:bg-gray-50">
          <td class="py-3 px-4">Q3 Training Report</td>
          <td class="py-3 px-4">Learning & Development</td>
          <td class="py-3 px-4">Aug 20, 2025</td>
          <td class="py-3 px-4"><span class="bg-green-100 text-green-600 px-2 py-1 rounded text-xs">Completed</span></td>
          <td class="py-3 px-4">
            <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">View</button>
          </td>
        </tr>
        <tr class="border-b hover:bg-gray-50">
          <td class="py-3 px-4">Employee Feedback Summary</td>
          <td class="py-3 px-4">Evaluation</td>
          <td class="py-3 px-4">Aug 10, 2025</td>
          <td class="py-3 px-4"><span class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded text-xs">Pending</span></td>
          <td class="py-3 px-4">
            <button class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition">View</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal for Generating Report -->
<div id="reportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg w-96 p-6">
    <h2 class="text-xl font-semibold mb-4">Generate New Report</h2>
    <label class="block mb-2 text-gray-700 text-sm">Report Name</label>
    <input type="text" class="w-full border rounded p-2 mb-3" placeholder="Enter report name">
    <label class="block mb-2 text-gray-700 text-sm">Category</label>
    <select class="w-full border rounded p-2 mb-3">
      <option>Learning & Development</option>
      <option>Performance Tracking</option>
      <option>Evaluation & Feedback</option>
    </select>
    <div class="flex justify-end gap-2 mt-4">
      <button onclick="closeReportModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
      <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Generate</button>
    </div>
  </div>
</div>

<script>
  function openReportModal() {
    document.getElementById("reportModal").classList.remove("hidden");
  }
  function closeReportModal() {
    document.getElementById("reportModal").classList.add("hidden");
  }
</script>
';

adminLayout($children);
?>
