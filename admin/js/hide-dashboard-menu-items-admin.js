"use strict";
document.addEventListener("DOMContentLoaded", function () {
  const hdmiForm = document.getElementById("hdmi__form");
  const hdmiBypassToggle = document.getElementById("hdmi__bypass-toggle");
  const hdmiBypassSettingsView = document.getElementById(
    "hdmi__bypass-settings"
  );
  const hdmiBypassInput = document.getElementById("hdmi__bypass-key");

  if (hdmiBypassToggle.checked) {
    enableByPassKey();
  }

  hdmiForm.addEventListener("change", function (e) {
    if (e.target.closest("input").id !== hdmiBypassToggle.id) return;

    if (hdmiBypassToggle.checked) {
      enableByPassKey();
    } else {
      disableByPassKey();
    }
  });

  hdmiForm.addEventListener("input", function () {
    const value = hdmiBypassInput.value.trim();
    const hasInvalidChars = /[^a-zA-Z0-9_-]/.test(value);

    if (!value) {
      hdmiBypassInput.setCustomValidity("Please enter a query parameter key.");
    } else if (value.length < 4 || value.length > 12) {
      hdmiBypassInput.setCustomValidity(
        `Please lengthen this text to 4 characters or more (you are currently using ${value.length} characters.)`
      );
    } else if (hasInvalidChars) {
      hdmiBypassInput.setCustomValidity(
        "Only letters, numbers, underscores, and hyphens are allowed."
      );
    } else {
      hdmiBypassInput.setCustomValidity("");
    }
  });

  function enableByPassKey() {
    hdmiBypassSettingsView.style.display = "block";
    hdmiBypassInput.required = true;
    hdmiBypassInput.removeAttribute("disabled");
  }

  function disableByPassKey() {
    hdmiBypassSettingsView.style.display = "none";
    hdmiBypassInput.required = false;
    hdmiBypassInput.setAttribute("disabled", "");
  }
});
