const statusHint = document.querySelector('[data-status-hint]')
const statusSelect = document.getElementById('status')
const statusOptions = statusSelect.querySelectorAll('option')

statusSelect.addEventListener('change', () => {
    const selectedOption = statusOptions[statusSelect.selectedIndex]
    statusHint.innerHTML = selectedOption.dataset.hint
})

statusHint.innerHTML = statusOptions[statusSelect.selectedIndex].dataset.hint
