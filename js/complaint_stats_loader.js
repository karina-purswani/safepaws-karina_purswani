async function loadComplaintStats() {
  const chartCtx = document.getElementById("complaintChart");
  const progressContainer = document.getElementById("complaintProgressBars");

  if (!chartCtx || !progressContainer) {
    console.error("Chart elements not found — cannot render complaint stats.");
    return;
  }

  try {
    const res = await fetch("../php/fetch_complaint_stats.php");
    const data = await res.json();
    const stats = data.stats;
    const total = data.total || 1; // prevent divide-by-zero

    // 🎯 PIE CHART
    new Chart(chartCtx, {
      type: "pie",
      data: {
        labels: Object.keys(stats),
        datasets: [
          {
            data: Object.values(stats),
            backgroundColor: [
              "#F59E0B", // Pending
              "#10B981", // Approved
              "#3B82F6", // In Progress
              "#8B5CF6", // Resolved
              "#EF4444"  // Rejected
            ],
          },
        ],
      },
      options: {
        plugins: {
          title: { display: true, text: "Complaints Distribution" },
          legend: { position: "bottom" },
        },
      },
    });

    // 🎚️ PROGRESS BARS
    progressContainer.innerHTML = "";
    Object.entries(stats).forEach(([status, count]) => {
      const percentage = ((count / total) * 100).toFixed(1);
      const colors = {
        "Pending": "bg-amber-400",
        "Approved": "bg-emerald-500",
        "In Progress": "bg-blue-500",
        "Resolved": "bg-violet-500",
        "Rejected": "bg-red-500",
      };
      progressContainer.innerHTML += `
        <div>
          <div class="flex justify-between mb-1 text-sm">
            <span class="font-medium text-gray-700">${status}</span>
            <span>${count} (${percentage}%)</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="${colors[status] || 'bg-gray-400'} h-3 rounded-full" style="width:${percentage}%"></div>
          </div>
        </div>
      `;
    });
  } catch (err) {
    console.error("Error fetching complaint stats:", err);
    alert("Failed to load complaint statistics.");
  }
}

loadComplaintStats();
