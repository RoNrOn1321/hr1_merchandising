<?php
include '../layout/adminLayout.php';

$children = '
<div class="bg-gray-100 min-h-screen">
  <!-- Page Header -->
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
  </div>

  <!-- Dashboard Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white shadow rounded-lg p-5 text-center">
      <h2 class="text-lg font-semibold text-gray-700">Total Employees</h2>
      <p class="text-3xl font-bold text-blue-600 mt-2">120</p>
    </div>
    <div class="bg-white shadow rounded-lg p-5 text-center">
      <h2 class="text-lg font-semibold text-gray-700">Departments</h2>
      <p class="text-3xl font-bold text-green-600 mt-2">8</p>
    </div>
    <div class="bg-white shadow rounded-lg p-5 text-center">
      <h2 class="text-lg font-semibold text-gray-700">Training Sessions</h2>
      <p class="text-3xl font-bold text-purple-600 mt-2">15</p>
    </div>
    <div class="bg-white shadow rounded-lg p-5 text-center">
      <h2 class="text-lg font-semibold text-gray-700">Pending Requests</h2>
      <p class="text-3xl font-bold text-red-600 mt-2">5</p>
    </div>
  </div>

  <!-- Charts Section -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white shadow rounded-lg p-5">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Monthly Training Sessions</h2>
      <div class="relative w-full h-64">
        <canvas id="trainingChart" class="w-full h-full"></canvas>
      </div>
    </div>
    <div class="bg-white shadow rounded-lg p-5">
      <h2 class="text-lg font-semibold text-gray-700 mb-4">Department Performance</h2>
      <div class="relative w-full h-64">
        <canvas id="performanceChart" class="w-full h-full"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctxLine = document.getElementById("trainingChart").getContext("2d");
const trainingChart = new Chart(ctxLine, {
  type: "line",
  data: {
    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
    datasets: [{
      label: "Training Sessions",
      data: [5, 7, 3, 6, 8, 4],
      backgroundColor: "rgba(59, 130, 246, 0.2)",
      borderColor: "rgba(59, 130, 246, 1)",
      borderWidth: 2,
      fill: true,
      tension: 0.4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { labels: { color: "#374151" } }
    },
    scales: {
      x: { ticks: { color: "#6B7280" } },
      y: { ticks: { color: "#6B7280" } }
    }
  }
});

const ctxBar = document.getElementById("performanceChart").getContext("2d");
const performanceChart = new Chart(ctxBar, {
  type: "bar",
  data: {
    labels: ["HR", "Sales", "IT", "Finance", "Marketing"],
    datasets: [{
      label: "Performance Score",
      data: [85, 70, 90, 75, 80],
      backgroundColor: "rgba(16, 185, 129, 0.6)",
      borderColor: "rgba(16, 185, 129, 1)",
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { labels: { color: "#374151" } }
    },
    scales: {
      x: { ticks: { color: "#6B7280" } },
      y: { ticks: { color: "#6B7280" } }
    }
  }
});
</script>
';

adminLayout($children);
?>
