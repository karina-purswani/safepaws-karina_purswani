<?php
session_start();

// Prevent cached version from loading on Back button
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0"); 

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: ../admin_login.html");
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Admin Dashboard — Safe Paws</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 font-inter">

  <!-- Navbar -->
  <nav class="sticky top-0 z-50 bg-gradient-to-r from-yellow-500 to-orange-600 shadow-md">
    <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
      <div class="flex items-center gap-3">
        <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" class="w-10 h-10" alt="logo">
        <h1 class="text-2xl font-bold text-white">Safe Paws</h1>
      </div>
      <ul class="flex gap-6 text-white font-medium">
        <b><a href="admin_logout.php" class="text-slate-100" style="font-size:20px;">Logout</a></b>
      </ul>
    </div>
  </nav>

  <div class="max-w-7xl mx-auto px-6 py-8 grid md:grid-cols-6 gap-6">
    <!-- SIDEBAR -->
    <aside class="md:col-span-1 bg-white rounded-2xl p-4 shadow h-fit">
      <nav class="space-y-2">
        <button class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-50" id="openComplaints">📢 Complaints Statistics</button>
        <button class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-50" id="openDogsTab">🐶 Manage Street Dogs</button>
        <button class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-50" id="openVaccination">💉 Vaccination Statistics</button>
      </nav>
    </aside>

    <!-- MAIN -->
    <section class="md:col-span-5 space-y-6">
      <!-- Cards row -->
      <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow">
          <h4 class="font-semibold">Complaints</h4>
          <p class="text-sm text-slate-600 mt-1">Review & manage citizen complaints.</p>
          <button id="openComplaintsBtn" class="mt-4 bg-emerald-600 text-white px-4 py-2 rounded">Open</button>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
          <h4 class="font-semibold">Street Dogs</h4>
          <p class="text-sm text-slate-600 mt-1">Add/update dog records and status.</p>
          <button id="openDogsBtn" class="mt-4 bg-sky-600 text-white px-4 py-2 rounded">Insert</button>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
          <h4 class="font-semibold">Vaccination Records</h4>
          <p class="text-sm text-slate-600 mt-1">Update vaccination & sterilization info.</p>
          <button id="openVaccinationBtn" class="mt-4 bg-amber-600 text-white px-4 py-2 rounded">Update</button>
        </div>
      </div>

      <!-- Dynamic content area -->
      <div id="panel" class="bg-white rounded-2xl p-6 shadow min-h-[300px]">
        <h3 class="font-semibold text-lg">Welcome Admin,</h3>
        <p class="text-slate-600 mt-2">Use this dashboard to manage complaints, update street dog records, and maintain health records. Keep our communities and animals safe together.</p>
      </div>
    </section>
  </div>

  <footer class="text-center text-sm text-slate-500 py-6">© 2025 Safe Paws</footer>

<script>
// --- Complaints (both sidebar and card) ---
function loadComplaints() {
    fetch("admin_complaints.php")
        .then(resp => resp.text())
        .then(html => document.getElementById("panel").innerHTML = html)
        .catch(err => console.error("Error loading complaints:", err));
}
document.getElementById("openComplaintsBtn").addEventListener("click", loadComplaints);
//document.getElementById("openComplaints").addEventListener("click", loadComplaints);

// --- Sidebar Manage Complaints: Show Complaints Statistics ---
function loadComplaintStatsPage() {
  const panel = document.getElementById("panel");
  if (!panel) return;

  panel.innerHTML = ""; // Clear existing content

  // 1️⃣ Load the HTML structure
  fetch("../complaint_stats.html")
    .then(res => res.text())
    .then(html => {
      panel.innerHTML = html;

      // 2️⃣ Load Chart.js dynamically
      const chartScript = document.createElement("script");
      chartScript.src = "https://cdn.jsdelivr.net/npm/chart.js";
      chartScript.onload = () => {
        // 3️⃣ Load our JS to fetch and render stats
        const loaderScript = document.createElement("script");
        loaderScript.src = "../js/complaint_stats_loader.js";
        document.body.appendChild(loaderScript);
      };
      document.body.appendChild(chartScript);
    })
    .catch(err => {
      console.error("Error loading complaint stats page:", err);
      panel.innerHTML = `<p class='text-red-600'>Failed to load complaint stats page.</p>`;
    });
}

// ✅ Only bind sidebar button to open stats
document.getElementById("openComplaints").addEventListener("click", loadComplaintStatsPage);

// --- Update complaint status helper (used inside admin_complaints.php via inline calls) ---
function updateStatus(complaint_id, newStatus) {
    fetch('update_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'complaint_id=' + complaint_id + '&status=' + encodeURIComponent(newStatus)
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === 'success') {
            loadComplaints(); // refresh complaints view
        } else {
            alert('Failed to update status.');
        }
    })
    .catch(err => console.error(err));
}

// --- Add Dog form (card Insert) ---
document.getElementById("openDogsBtn").addEventListener("click", function () {
    fetch("../dog_form.html")
        .then(response => response.text())
        .then(html => {
            document.getElementById("panel").innerHTML = html;

            const form = document.getElementById("addDogForm");
            if (!form) return;

            form.addEventListener("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch("add_dog.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(response => {
                    if (response.trim() === "success") {
                        alert("🐶 Dog details added successfully!");
                        form.reset();
                    } else if (response.trim() === "error_upload") {
                        alert("❌ Failed to upload image. Try again.");
                    } else {
                        alert("⚠️ Something went wrong: " + response);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Server error, please try again.");
                });
            });
        })
        .catch(err => console.error("Error loading dog form:", err));
});

// --- Dog list (sidebar Manage Street Dogs) ---
function loadDogList() {
    fetch("fetch_dogs_admin.php")
        .then(resp => resp.text())
        .then(html => document.getElementById("panel").innerHTML = html)
        .catch(err => console.error("Error loading dog records:", err));
}
document.getElementById("openDogsTab").addEventListener("click", loadDogList);

// --- IMPORTANT: Vaccination card "Update" now opens the Dog List (same as Manage Street Dogs) ---
document.getElementById("openVaccinationBtn").addEventListener("click", loadDogList);

// --- 🧩 Function called when "Update Vaccination" button is clicked in each dog card ---
function openVaccinationForm(dog_id) {
  const panel = document.getElementById("panel");
  if (!panel) return;

  panel.innerHTML = ""; // Clear current content

  // Load the admin-vax.html page dynamically with cache-busting
  fetch("../admin-vax.html?v=" + new Date().getTime())
    .then(res => res.text())
    .then(html => {
      panel.innerHTML = html;

      // Store selected dog_id
      sessionStorage.setItem("selectedDogId", dog_id);

      // ✅ Remove old vaccination form script if it exists
      const oldScript = document.querySelector("script[data-vax-form]");
      if (oldScript) oldScript.remove();

      // ✅ Load latest vaccination form JS file (with cache-busting)
      const script = document.createElement("script");
      script.src = "../js/admin_vax_form.js?v=" + new Date().getTime();
      script.dataset.vaxForm = "true";
      document.body.appendChild(script);
    })
    .catch(err => {
      console.error("Error loading admin-vax.html:", err);
      panel.innerHTML = `<p class="text-red-600">Failed to load vaccination form.</p>`;
    });
}

// --- Vaccination page (sidebar Vaccination Records) ---
// --- Vaccination page (sidebar Vaccination Records) ---
function loadVaccinationPage() {
  const panel = document.getElementById("panel");
  if (!panel) return;

  // 🧹 Clean up any leftover Chart.js canvases before loading new ones
  panel.innerHTML = "";

  // 1️⃣ Load the HTML structure
  fetch("../admin_vax_stats.html")
    .then(res => res.text())
    .then(html => {
      panel.innerHTML = html;

      // 2️⃣ Wait for Chart.js to load first
      const chartScript = document.createElement("script");
      chartScript.src = "https://cdn.jsdelivr.net/npm/chart.js";
      chartScript.onload = () => {
        // 3️⃣ Then load the chart-rendering JS
        const loaderScript = document.createElement("script");
        loaderScript.src = "../js/admin_vax_loader.js"; // relative path
        document.body.appendChild(loaderScript);
      };
      document.body.appendChild(chartScript);
    })
    .catch(err => {
      console.error("Error loading vaccination stats page:", err);
      panel.innerHTML = `<p class="text-red-600">Failed to load vaccination stats page.</p>`;
    });
}

document.getElementById("openVaccination").addEventListener("click", loadVaccinationPage);

// Attach to sidebar button
/*const vaxBtn = document.getElementById("openVaccination");
if (vaxBtn) {
    vaxBtn.addEventListener("click", loadVaccinationPage);
}*/
// --- Open vaccination form from dog list (called from fetch_dogs_admin.php update button) ---
// --- Vaccination page (sidebar Vaccination Records) ---
// Function to load the Vaccination Stats page
function loadVaccinationPage() {
  const panel = document.getElementById("panel");
  if (!panel) return;

  panel.innerHTML = ""; // clear current content

  // 1️⃣ Fetch the vaccination stats HTML with cache-busting
  fetch("../admin_vax_stats.html?v=" + new Date().getTime())
    .then(res => res.text())
    .then(html => {
      panel.innerHTML = html;

      // 2️⃣ Load Chart.js dynamically
      loadScript("https://cdn.jsdelivr.net/npm/chart.js", () => {
        // 3️⃣ Load the vaccination JS logic
        loadScript("../js/admin_vax_loader.js?v=" + new Date().getTime());
      });
    })
    .catch(err => {
      console.error("Error loading vaccination stats page:", err);
      panel.innerHTML = `<p class="text-red-600">Failed to load vaccination stats page.</p>`;
    });
}

// Helper function to load a script dynamically
function loadScript(src, callback) {
  const script = document.createElement("script");
  script.src = src;
  script.onload = () => {
    if (callback) callback();
  };
  script.onerror = () => console.error(`Failed to load script: ${src}`);
  document.body.appendChild(script);
}

// Attach click event to sidebar button
document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("openVaccination");
  if (btn) {
    btn.addEventListener("click", loadVaccinationPage);
  } else {
    console.error("Sidebar button #openVaccination not found");
  }
});



</script>

</body>
</html>
