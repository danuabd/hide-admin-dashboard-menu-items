"use strict";
"use strict";
document.addEventListener("DOMContentLoaded", function () {
  const hdmiBypassForm = document.getElementById("hdmi-settings-form");
  const hdmiBypassToggle = document.getElementById("hdmi-bypass-toggle");
  const hdmiBypassSettingsView = document.getElementById(
    "hdmi-bypass-settings"
  );
  const hdmiBypassInput = document.getElementById("hdmi-bypass-key");

  // Toggle visibility on load if checked
  if (hdmiBypassToggle.checked) {
    hdmiBypassSettingsView.style.display = "block";
    hdmiBypassInput.required = true;
  }

  hdmiBypassForm.addEventListener("change", function (e) {
    if (e.target.closest("input").id !== hdmiBypassToggle.id) return;

    hdmiBypassSettingsView.style.display = hdmiBypassToggle.checked
      ? "block"
      : "none";
    hdmiBypassInput.required = hdmiBypassToggle.checked ? true : false;
  });

  hdmiBypassForm.addEventListener("input", function () {
    const value = hdmiBypassInput.value.trim();
    const hasInvalidChars = /[^a-zA-Z0-9_-]/.test(value);

    if (!value) {
      hdmiBypassInput.setCustomValidity("Please enter a query parameter key.");
    } else if (hasInvalidChars) {
      hdmiBypassInput.setCustomValidity(
        "Only letters, numbers, underscores, and hyphens are allowed."
      );
    } else {
      // Clear any previous error if valid
      hdmiBypassInput.setCustomValidity("");
    }
  });
});
