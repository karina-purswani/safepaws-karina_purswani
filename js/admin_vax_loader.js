async function loadVaxStats() {
  try {
    const res = await fetch("../php/fetch_vax_stats.php");
    const data = await res.json();

    console.log("Vaccination stats data:", data);

    if (!data || typeof data !== "object") {
      throw new Error("Invalid data structure received");
    }

    // ---------- Vaccination Progress (Pie Chart) ----------
    const statusCtx = document.getElementById("statusChart");
    if (statusCtx) {
      new Chart(statusCtx, {
        type: "pie",
        data: {
          labels: ["Phase 1", "Phase 2", "Fully Vaccinated"],
          datasets: [
            {
              data: [data.phase1, data.phase2, data.fully],
              backgroundColor: ["#3B82F6", "#10B981", "#F59E0B"],
            },
          ],
        },
        options: {
          plugins: {
            title: { display: true, text: "Vaccination Progress" },
          },
        },
      });
    }

    // Helper function for area-based bar charts
    const createAreaChart = (canvasId, areaData, chartTitle, color) => {
      const ctx = document.getElementById(canvasId);
      if (!ctx || !areaData || Object.keys(areaData).length === 0) return;

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: Object.keys(areaData),
          datasets: [
            {
              label: chartTitle,
              data: Object.values(areaData),
              backgroundColor: color,
            },
          ],
        },
        options: {
          plugins: {
            title: { display: true, text: chartTitle },
          },
          scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } },
          },
        },
      });
    };

    // ---------- Area-wise Vaccination Charts ----------
    createAreaChart("areaPhase1Chart", data.area_phase1, "Area-wise Phase 1 Completed Dogs", "#3B82F6");
    createAreaChart("areaPhase2Chart", data.area_phase2, "Area-wise Phase 2 Completed Dogs", "#10B981");
    createAreaChart("areaFullyChart", data.area_fully, "Area-wise Fully Vaccinated Dogs", "#F59E0B");

  } catch (err) {
    console.error("Error fetching vaccination stats:", err);
    alert("Failed to load vaccination stats.");
  }
}

// Run after page load
if (document.readyState === "complete") loadVaxStats();
else window.addEventListener("load", loadVaxStats);
