const modal = document.getElementById("addModal");

function toggleModal(show) {
  if (show) {
    modal.classList.remove("hidden");
  } else {
    modal.classList.add("hidden");
  }
}

function toggleCheckboxes(source) {
  const checkboxes = document.querySelectorAll('input[name="property_ids[]"]');

  checkboxes.forEach((checkbox) => {
    checkbox.checked = source.checked;
  });

  updateCount();
}

function updateCount() {
  const countElement = document.getElementById("selected-count");
  if (countElement) {
    countElement.textContent = document.querySelectorAll(
      'input[name="property_ids[]"]:checked'
    ).length;
  }
}

document.addEventListener("change", (event) => {
  if (event.target.matches('input[name="property_ids[]"]')) {
    updateCount();
  }
});
