/**
 * selectStatus: Updates the hidden status input and toggles the status pill buttons.
 * @param {string} value - The status value to select ("New", "In Progress", "Complete").
 */
function selectStatus(value) {
  document.getElementById('status-input').value = value;
  // Target only the status container.
  const container = document.getElementById('status-container');
  const buttons = container.querySelectorAll('button');
  buttons.forEach(button => {
      if (button.textContent.trim() === value) {
          button.classList.remove('PILL-INACTIVE');
          button.classList.add('PILL-ACTIVE');
      } else {
          button.classList.remove('PILL-ACTIVE');
          button.classList.add('PILL-INACTIVE');
      }
  });
}

/**
* selectPriority: Updates the hidden priority input and toggles the priority pill buttons.
* @param {string} value - The priority value to select ("Urgent", "Moderate", "Low").
*/
function selectPriority(value) {
  document.getElementById('priority-input').value = value;
  // Target only the priority container.
  const container = document.getElementById('priority-container');
  const buttons = container.querySelectorAll('button');
  buttons.forEach(button => {
      if (button.textContent.trim() === value) {
          button.classList.remove('PILL-INACTIVE');
          button.classList.add('PILL-ACTIVE');
      } else {
          button.classList.remove('PILL-ACTIVE');
          button.classList.add('PILL-INACTIVE');
      }
  });
}
