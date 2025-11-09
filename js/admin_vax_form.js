(function initVaccinationForm() {
  console.log("🧩 admin_vax_form.js loaded");

  setTimeout(() => {
    const form = document.getElementById("updateVaxForm");
    if (!form) {
      console.error("❌ updateVaxForm not found in admin-vax.html");
      return;
    }

    // ✅ Pre-fill dog_id
    const storedDogId = sessionStorage.getItem("selectedDogId");
    if (storedDogId) {
      document.getElementById("vaxDogId").value = storedDogId;
      console.log("✅ Loaded Dog ID:", storedDogId);
    }

    // Prevent double-binding
    form.replaceWith(form.cloneNode(true));
    const freshForm = document.getElementById("updateVaxForm");

    // ✅ Submit handler
    freshForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const dogId = document.getElementById("vaxDogId").value.trim();
      const status = freshForm.querySelector("select[name='vaccination_status']").value.trim();
      const date = document.getElementById("vaxDate").value.trim();

      if (!dogId || !status || !date) {
        alert("⚠️ Please fill all fields before submitting.");
        return;
      }

      try {
        console.log("📤 Sending update request to server...");
        const res = await fetch("../php/update_vaccination.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `dog_id=${encodeURIComponent(dogId)}&vaccination_status=${encodeURIComponent(status)}&vaccination_date=${encodeURIComponent(date)}`
        });

        const text = await res.text();
        console.log("📥 Server Response:", text);

        if (text.trim() === "success") {
          const msg = document.createElement("p");
          msg.className = "text-green-600 mt-3 font-medium";
          msg.textContent = "✅ Vaccination record updated successfully!";
          freshForm.appendChild(msg);

          // ✅ Wait 2 seconds, then reload dog list or dashboard
          setTimeout(() => {
            // Option 1: If dog list is part of admin_dashboard panel
            if (window.openDogList) {
              openDogList(); // call your function if available
            } 
            // Option 2: Fallback — reload entire dashboard
            else {
              location.reload();
            }
          }, 2000);
        } 
        else {
          alert("❌ " + text);
        }

      } catch (err) {
        console.error("⚠️ Error submitting form:", err);
        alert("❌ Failed to update record. Check console for details.");
      }
    });

    console.log("✅ Vaccination form initialized successfully.");
  }, 300);
})();
