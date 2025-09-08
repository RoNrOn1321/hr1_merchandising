<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-6">
  <!-- Header -->
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-700">Interview Scheduling & Management</h1>
    <button onclick="openScheduleModal()" 
      class="bg-blue-500 text-white px-4 py-2 rounded-md shadow hover:bg-blue-600 transition">
      + Schedule Interview
    </button>
  </div>

  <!-- Interview List -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Interview Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">John Doe</h2>
        <span class="bg-green-100 text-green-600 px-3 py-1 text-sm rounded-full">Scheduled</span>
      </div>
      <p class="text-gray-600 text-sm">Position: Software Developer</p>
      <p class="text-gray-600 text-sm">Date: Aug 28, 2025</p>
      <p class="text-gray-600 text-sm">Time: 10:00 AM</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
          Reschedule
        </button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
          Cancel
        </button>
      </div>
    </div>

    <!-- More Cards -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Jane Smith</h2>
        <span class="bg-yellow-100 text-yellow-600 px-3 py-1 text-sm rounded-full">Pending</span>
      </div>
      <p class="text-gray-600 text-sm">Position: Marketing Specialist</p>
      <p class="text-gray-600 text-sm">Date: Aug 30, 2025</p>
      <p class="text-gray-600 text-sm">Time: 2:00 PM</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
          Confirm
        </button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
          Cancel
        </button>
      </div>
    </div>

  </div>
</div>
';

adminLayout($children);
?>
