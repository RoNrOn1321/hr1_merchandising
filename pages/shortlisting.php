<?php
include '../layout/adminLayout.php';

$children = '
<div class="p-4 sm:p-6 w-full ">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Candidate Shortlisting & Screening</h1>
        <button onclick="openFilterModal()" 
            class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition flex items-center gap-2 text-sm sm:text-base">
            <i class="bx bx-filter-alt text-lg sm:text-xl"></i> 
            <span class="hidden sm:inline">Filter</span>
        </button>
    </div>

    <!-- Shortlisted Candidates Table -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6">
        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 mb-4">Shortlisted Candidates</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left min-w-[600px]">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-4 text-sm sm:text-base">Name</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Position</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Screening Score</th>
                        <th class="py-3 px-4 text-sm sm:text-base">Status</th>
                        <th class="py-3 px-4 text-center text-sm sm:text-base">Action</th>
                    </tr>
                </thead>
                <tbody>
                    ' . generateCandidateRow("John Doe", "Software Engineer", 85, "Shortlisted") . '
                    ' . generateCandidateRow("Jane Smith", "UI/UX Designer", 90, "Pending Interview") . '
                    ' . generateCandidateRow("Michael Lee", "HR Officer", 78, "Under Review") . '
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit / Delete Modal -->
<div id="candidateModal" class="fixed inset-0 hidden bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
        <h2 class="text-xl sm:text-2xl font-bold mb-4">Update Candidate</h2>
        <input type="text" id="candidateName" 
            class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base" 
            placeholder="Candidate Name">
        <select id="candidateStatus" 
            class="w-full border border-gray-300 rounded-lg p-2 mb-3 text-sm sm:text-base">
            <option>Shortlisted</option>
            <option>Pending Interview</option>
            <option>Under Review</option>
            <option>Rejected</option>
        </select>
        <div class="flex justify-between gap-3">
            <button onclick="saveCandidate()" 
                class="w-1/2 bg-blue-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-blue-600 transition">
                Save Changes
            </button>
            <button onclick="closeModal()" 
                class="w-1/2 bg-red-500 text-white py-2 text-sm sm:text-base rounded-lg hover:bg-red-600 transition">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById("candidateModal").classList.remove("hidden");
}
function closeModal() {
    document.getElementById("candidateModal").classList.add("hidden");
}
function saveCandidate() {
    alert("Candidate updated: " + document.getElementById("candidateName").value + " - " + document.getElementById("candidateStatus").value);
    closeModal();
}
function deleteCandidate(name) {
    if(confirm("Are you sure you want to remove " + name + "?")) {
        alert(name + " has been removed.");
    }
}
</script>
';

function generateCandidateRow($name, $position, $score, $status) {
    return '
    <tr class="border-b hover:bg-gray-50 text-sm sm:text-base">
        <td class="py-3 px-4">' . $name . '</td>
        <td class="py-3 px-4">' . $position . '</td>
        <td class="py-3 px-4">' . $score . '%</td>
        <td class="py-3 px-4"><span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs sm:text-sm">' . $status . '</span></td>
        <td class="py-3 px-4 text-center">
            <div class="flex justify-center gap-3">
                <button onclick="openModal()" class="text-blue-500 hover:text-blue-700 text-lg sm:text-xl"><i class="bx bx-edit"></i></button>
                <button onclick="deleteCandidate(\'' . $name . '\')" class="text-red-500 hover:text-red-700 text-lg sm:text-xl"><i class="bx bx-trash"></i></button>
            </div>
        </td>
    </tr>';
}

adminLayout($children);
?>
