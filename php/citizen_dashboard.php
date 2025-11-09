<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: citizen_login.html");
    exit();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Citizen Dashboard — Safe Paws</title>
  <script src="https://cdn.tailwindcss.com"></script>

  </script>
</head>
<body class="min-h-screen bg-slate-50 font-inter">

  <!-- Navbar -->
 <nav class="sticky top-0 z-50 bg-gradient-to-r from-yellow-500 to-orange-600 shadow-md">
  <div class="max-w-7xl mx-auto px-6 py-3 flex justify-between items-center">
    <!-- Logo -->
    <div class="flex items-center gap-3">
      <img src="https://cdn-icons-png.flaticon.com/512/616/616408.png" class="w-10 h-10" alt="logo">
      <h1 class="text-2xl font-bold text-white">Safe Paws</h1>
    </div>

    <!-- Profile Dropdown -->
    <div class="relative">
      <!-- Trigger Button -->
    <!-- Profile Button -->
<button id="profileBtn" class="flex items-center gap-2 focus:outline-none">
  <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center border-2 border-white text-white font-bold text-lg uppercase">
    <?php echo htmlspecialchars(substr($_SESSION['email'], 0, 1)); ?>
  </div>
  <span class="text-white font-semibold hidden md:inline"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="6 9 12 15 18 9"></polyline>
  </svg>
</button>


      <!-- Dropdown Panel -->
      <div id="profileDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg p-4 z-50 hidden">
        <div class="text-sm text-gray-800 mb-2">
          <p class="font-bold text-lg">Your Account</p>
          <p class="text-xs text-slate-500 mt-1">Logged in as:</p>
          <p class="break-words"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
        </div>
        <hr class="my-3">
        <a href="logout.php" class="block w-full text-left text-sm text-red-600 font-semibold hover:underline mb-2">Logout</a>
        <form method="post" action="remove_account.php" onsubmit="return confirm('Are you sure you want to remove your account?');">
          <button type="submit" class="w-full text-left text-sm text-gray-600 font-semibold hover:text-red-600">Remove Account</button>
        </form>
      </div>
    </div>
  </div>
</nav>
  <div class="max-w-7xl mx-auto px-6 py-8 grid md:grid-cols-6 gap-6">
    <!-- SIDEBAR -->
    <aside class="md:col-span-1 bg-white rounded-2xl p-4 shadow h-fit">
      <nav class="space-y-2">
        <button class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-50 active:bg-slate-100" data-tab="complaints">📢 Complaints</button>
        <button class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-50" data-tab="dogs">🐶 Street Dogs</button>
        <button class="w-full text-left px-3 py-2 rounded-md hover:bg-slate-50" data-tab="vax">💉 Vaccination Status</button>
      </nav>
    </aside>

    <!-- MAIN -->
    <section class="md:col-span-5 space-y-6">
      <!-- Cards row -->
      <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow">
          <h4 class="font-semibold">File Complaint</h4>
          <p class="text-sm text-slate-600 mt-1">Report a stray dog sighting or issue.</p>
          <button id="openComplaint" class="mt-4 bg-emerald-600 text-white px-4 py-2 rounded">New</button>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
          <h4 class="font-semibold">Street Dogs</h4>
          <p class="text-sm text-slate-600 mt-1">View registered dogs & statuses.</p>
          <button id="openDogs" class="mt-4 bg-sky-600 text-white px-4 py-2 rounded">View</button>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow">
          <h4 class="font-semibold">Vaccination Check</h4>
          <p class="text-sm text-slate-600 mt-1">Lookup dog by Location to check vaccination status of dogs from your locality.</p>
          <button id="openVax" class="mt-4 bg-amber-600 text-white px-4 py-2 rounded">Check</button>
        </div>
      </div>

      <!-- Dynamic content area -->
      <div id="panel" class="bg-white rounded-2xl p-6 shadow min-h-[300px]">
        <!-- default content -->
        <h3 class="font-semibold text-lg">Welcome Nashik Citizens,</h3>
        <p class="text-slate-600 mt-2">Here , You can file complaints, view dog records, and check vaccination status of street dogs in your regions. Let's balance the awesome relationship between us and this pets.</p>
      </div>

      <!-- Your Complaints list -->
      <div class="bg-white rounded-2xl p-6 shadow">
        <h4 class="font-semibold">Your Complaints</h4>
        <div id="myComplaints" class="mt-4 grid gap-3">
          <?php
          $conn = new mysqli("localhost", "root", "", "safepaws");
          $email = $_SESSION['email'];
          $result = $conn->query("SELECT * FROM complaints WHERE citizen_email='$email' ORDER BY created_at DESC");

          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo "
                  <div class='border rounded-lg p-4 bg-slate-50'>
                      <p><b>Name:</b> {$row['citizen_name']}</p>
                      <p><b>Area:</b> {$row['area']}</p>
                      <p><b>Status:</b> {$row['status']}</p>
                      <p><b>Complaint:</b> {$row['description']}</p>
                      <small class='text-slate-500'>Filed on: {$row['created_at']}</small>
                  </div>";
              }
          } else {
              echo "<p class='text-slate-500'>No complaints filed yet.</p>";
          }
          $conn->close();
          ?>
        </div>
      </div>
    </section>
  </div>

  <footer class="text-center text-sm text-slate-500 py-6">© 2025 Safe Paws</footer>
   <script>
// Complaint Form Loader (Top Button)
document.getElementById("openComplaint").addEventListener("click", () => {
  fetch("../complaint_form.html?nocache=" + new Date().getTime())
    .then(res => res.text())
    .then(html => {
      document.getElementById("panel").innerHTML = html;
      const form = document.querySelector("#complaintForm");
      if (form) {
        form.action = "submit_complaint.php";
        form.method = "POST";
        form.enctype = "multipart/form-data";
      }
    });
});

// Dog List Loader (Top Button)
function loadDogRecords() {
  fetch("fetch_dogs.php?nocache=" + new Date().getTime())
    .then(res => res.text())
    .then(html => {
      document.getElementById("panel").innerHTML = `
        <h3 class='font-semibold text-lg mb-4'>Registered Street Dogs</h3>
        <div class='grid gap-4'>${html}</div>
      `;
    })
    .catch(() => {
      document.getElementById("panel").innerHTML = "<p class='text-red-500'>⚠️ Error loading dog records.</p>";
    });
}
document.getElementById("openDogs").addEventListener("click", loadDogRecords);

//search
const openVaxBtn = document.getElementById("openVax");
  if (openVaxBtn) {
    openVaxBtn.addEventListener("click", () => {
      console.log("✅ Vaccination Check clicked");

      fetch("fetch_areas.php")
        .then(res => res.json())
        .then(areas => {
          const options = areas.map(a => `<option value='${a}'>`).join('');
          document.getElementById("panel").innerHTML = `
            <h3 class='font-semibold text-lg mb-4'>Search Vaccinated Dogs by Area</h3>
            <div class="flex gap-2 mb-4">
              <input list="areaList" id="areaInput" placeholder="Search vaccinated dogs of your area"
                     class="border px-3 py-2 rounded w-full">
              <datalist id="areaList">${options}</datalist>
              <button id="searchAreaBtn" class="bg-amber-600 text-white px-4 py-2 rounded">Search</button>
            </div>
            <div id="searchResults" class="grid gap-4"></div>
          `;

          const searchBtn = document.getElementById("searchAreaBtn");
          searchBtn.addEventListener("click", () => {
            const area = document.getElementById("areaInput").value.trim();
            if (area === "") {
              alert("Please select an area");
              return;
            }

            fetch(`fetch_vax_dogs.php?area=${encodeURIComponent(area)}`)
              .then(res => res.text())
              .then(html => {
                document.getElementById("searchResults").innerHTML = html;
              })
              .catch(err => console.error("❌ Error fetching vaccination data:", err));
          });
        })
        .catch(err => console.error("❌ Error fetching areas:", err));
    });
  }

// SIDEBAR BUTTONS
document.querySelector("[data-tab='complaints']").addEventListener("click", () => {
  const complaintsSection = document.querySelector("#myComplaints");
  if (complaintsSection) {
    complaintsSection.scrollIntoView({ behavior: "smooth" });
  }
});

document.querySelector("[data-tab='dogs']").addEventListener("click", loadDogRecords);

document.querySelector("[data-tab='vax']").addEventListener("click", () => {
  document.getElementById("openVax").click();
});

// Profile Dropdown
const profileBtn = document.getElementById('profileBtn');
const profileDropdown = document.getElementById('profileDropdown');
profileBtn.addEventListener('click', e => {
  e.stopPropagation();
  profileDropdown.classList.toggle('hidden');
});
document.addEventListener('click', e => {
  if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target))
    profileDropdown.classList.add('hidden');
});

// Auto-refresh complaints
setInterval(() => {
  fetch('fetch_complaints.php')
  .then(res => res.text())
  .then(html => document.getElementById('myComplaints').innerHTML = html);
}, 20000);
</script>



  
</body>
</html>