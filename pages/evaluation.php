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
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <!-- Feedback Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">John Doe</h2>
        <span class="bg-green-100 text-green-600 px-3 py-1 text-sm rounded-full">Excellent</span>
      </div>
      <p class="text-gray-600 text-sm">Department: Sales</p>
      <p class="text-gray-600 text-sm">Evaluator: Jane Smith</p>
      <p class="text-gray-600 text-sm">Date: Aug 20, 2025</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
          View Details
        </button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
          Delete
        </button>
      </div>
    </div>

    <!-- Feedback Card -->
    <div class="bg-white rounded-lg shadow-md p-5">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Mary Ann</h2>
        <span class="bg-yellow-100 text-yellow-600 px-3 py-1 text-sm rounded-full">Good</span>
      </div>
      <p class="text-gray-600 text-sm">Department: HR</p>
      <p class="text-gray-600 text-sm">Evaluator: Mark Lee</p>
      <p class="text-gray-600 text-sm">Date: Aug 18, 2025</p>
      <div class="mt-4 flex justify-between">
        <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 transition">
          Edit
        </button>
        <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
          Delete
        </button>
      </div>
    </div>

  </div>
</div>

<!-- Modal -->
<div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
    <h2 class="text-xl font-semibold mb-4">Add New Feedback</h2>
    <form>
      <label class="block mb-2 text-gray-700">Employee Name</label>
      <input type="text" class="w-full border rounded p-2 mb-4" placeholder="Enter name">

      <label class="block mb-2 text-gray-700">Department</label>
      <input type="text" class="w-full border rounded p-2 mb-4" placeholder="Enter department">

      <label class="block mb-2 text-gray-700">Rating</label>
      <select class="w-full border rounded p-2 mb-4">
        <option>Excellent</option>
        <option>Good</option>
        <option>Needs Improvement</option>
      </select>

      <label class="block mb-2 text-gray-700">Feedback</label>
      <textarea class="w-full border rounded p-2 mb-4" rows="3" placeholder="Write feedback..."></textarea>

      <div class="flex justify-end gap-3">
        <button type="button" onclick="closeFeedbackModal()" 
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition">Cancel</button>
        <button type="submit" 
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
function openFeedbackModal() {
  document.getElementById("feedbackModal").classList.remove("hidden");
  document.getElementById("feedbackModal").classList.add("flex");
}
function closeFeedbackModal() {
  document.getElementById("feedbackModal").classList.add("hidden");
  document.getElementById("feedbackModal").classList.remove("flex");
}
</script>
';

adminLayout($children);
?>
