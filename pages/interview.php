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
  <div id="interviewList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Cards will be injected here -->
  </div>
</div>

<!-- Schedule / Reschedule Modal -->
<div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
  <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
    <h2 id="modalTitle" class="text-xl font-semibold mb-4">Schedule New Interview</h2>
    <form id="scheduleForm" class="space-y-4">
      <input type="hidden" id="interview_id">
      <input type="text" id="candidate_name" placeholder="Candidate Name" 
        class="w-full border p-2 rounded" required>
      <input type="text" id="position" placeholder="Position" 
        class="w-full border p-2 rounded" required>
      <input type="date" id="interview_date" 
        class="w-full border p-2 rounded" required>
      <input type="time" id="interview_time" 
        class="w-full border p-2 rounded" required>
      <select id="status" class="w-full border p-2 rounded">
        <option value="Pending">Pending</option>
        <option value="Completed">Completed</option>
      </select>
      <div class="flex justify-end space-x-2">
        <button type="button" onclick="closeScheduleModal()" 
          class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
        <button type="submit" 
          class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
      </div>
    </form>
  </div>
</div>

<script>
let editMode = false; // false = new interview, true = reschedule

// Load all interviews
async function loadInterviews() {
  let res = await fetch("http://localhost/hr1_merchandising/api/interview.php");
  let interviews = await res.json();
  console.log(interviews);

  let container = document.getElementById("interviewList");
  container.innerHTML = "";

  interviews.forEach(interview => {
    let statusColor = 
      interview.status === "Scheduled" ? "bg-green-100 text-green-600" :
      interview.status === "Pending" ? "bg-yellow-100 text-yellow-600" :
      interview.status === "Completed" ? "bg-blue-100 text-blue-600" :
      "bg-red-100 text-red-600";

    container.innerHTML += `
      <div class="bg-white rounded-lg shadow-md p-5">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-800">${interview.candidate_name}</h2>
          <span class="${statusColor} px-3 py-1 text-sm rounded-full">${interview.status}</span>
        </div>
        <p class="text-gray-600 text-sm">Position: ${interview.position}</p>
        <p class="text-gray-600 text-sm">Date: ${interview.interview_date}</p>
        <p class="text-gray-600 text-sm">Time: ${interview.interview_time}</p>
        <div class="mt-4 flex justify-between">
          <button onclick="rescheduleInterview(${interview.id}, \'${interview.candidate_name}\', \'${interview.position}\', \'${interview.interview_date}\', \'${interview.interview_time}\', \'${interview.status}\')" 
            class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition">
            Reschedule
          </button>
          <button onclick="deleteInterview(${interview.id})" 
            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 transition">
            Cancel
          </button>
        </div>
      </div>
    `;
  });
}

// Cancel/Delete interview
async function deleteInterview(id) {
  if (!confirm("Are you sure you want to cancel this interview?")) return;
  
  await fetch("http://localhost/hr1_merchandising/api/interview.php", {
    method: "DELETE",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ id })
  });
  
  loadInterviews();
}

// Modal control
function openScheduleModal() {
  editMode = false;
  document.getElementById("modalTitle").innerText = "Schedule New Interview";
  document.getElementById("scheduleForm").reset();
  document.getElementById("scheduleModal").classList.remove("hidden");
}
function closeScheduleModal() {
  document.getElementById("scheduleModal").classList.add("hidden");
}

// Reschedule (edit existing)
function rescheduleInterview(id, name, position, date, time, status) {
  editMode = true;
  document.getElementById("modalTitle").innerText = "Reschedule Interview";
  document.getElementById("interview_id").value = id;
  document.getElementById("candidate_name").value = name;
  document.getElementById("position").value = position;
  document.getElementById("interview_date").value = date;
  document.getElementById("interview_time").value = time;
  document.getElementById("status").value = status; // Ensure this matches the value in the dropdown
  document.getElementById("scheduleModal").classList.remove("hidden");
}

// Handle form submit (schedule or reschedule)
document.getElementById("scheduleForm").addEventListener("submit", async (e) => {
  e.preventDefault();

  let data = {
    candidate_name: document.getElementById("candidate_name").value,
    position: document.getElementById("position").value,
    interview_date: document.getElementById("interview_date").value,
    interview_time: document.getElementById("interview_time").value,
    status: document.getElementById("status").value
  };

  if (editMode) {
    // Update existing interview (PUT)
    data.id = document.getElementById("interview_id").value;
    await fetch("http://localhost/hr1_merchandising/api/interview.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
  } else {
    // New interview (POST)
    await fetch("http://localhost/hr1_merchandising/api/interview.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data)
    });
  }

  closeScheduleModal();
  loadInterviews();
});

loadInterviews();
</script>
';

adminLayout($children);
?>
