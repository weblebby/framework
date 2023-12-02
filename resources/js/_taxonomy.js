const Taxonomy = {
    primaryGroupSelector: '[data-primary-taxonomy-group]',
    primarySelectSelector: '[data-primary-taxonomy-select]',
    taxonomyCheckboxSelector: '[data-taxonomy-checkbox]',

    displaySelect: () => {
        document
            .querySelector(Taxonomy.primaryGroupSelector)
            .classList.remove('fd-hidden')
    },

    hideSelect: () => {
        document
            .querySelector(Taxonomy.primaryGroupSelector)
            .classList.add('fd-hidden')
    },

    clearSelectOptions: () => {
        const select = document.querySelector(Taxonomy.primarySelectSelector)
        select.innerHTML = ''
    },

    createOption: (value, text) => {
        const option = document.createElement('option')
        option.value = value
        option.text = text

        const select = document.querySelector(Taxonomy.primarySelectSelector)
        select.appendChild(option)
    },

    onChange: e => {
        const checkboxes = document.querySelectorAll(
            Taxonomy.taxonomyCheckboxSelector,
        )

        const checkedCheckboxes = Array.from(checkboxes).filter(
            checkbox => checkbox.checked,
        )

        Taxonomy.clearSelectOptions()

        checkedCheckboxes.map(checkbox => {
            Taxonomy.createOption(
                checkbox.value,
                checkbox.dataset.taxonomyCheckbox,
            )
        })

        if (checkedCheckboxes.length > 0) {
            Taxonomy.displaySelect()
        } else {
            Taxonomy.hideSelect()
        }
    },
}

const primarySelect = document.querySelector(Taxonomy.primarySelectSelector)

document
    .querySelectorAll(Taxonomy.taxonomyCheckboxSelector)
    .forEach(checkbox => {
        checkbox.addEventListener('change', e => {
            Taxonomy.onChange(e)
        })
    })
