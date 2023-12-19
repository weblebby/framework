const Taxonomy = {
    fieldSelector: '[data-taxonomy-field]',
    primaryGroupSelector: '[data-primary-taxonomy-group]',
    primarySelectSelector: '[data-primary-taxonomy-select]',
    checkboxSelector: '[data-taxonomy-checkbox]',

    displaySelect: fieldContainer => {
        fieldContainer
            .querySelector(Taxonomy.primaryGroupSelector)
            .classList.remove('fd-hidden')
    },

    hideSelect: fieldContainer => {
        fieldContainer
            .querySelector(Taxonomy.primaryGroupSelector)
            .classList.add('fd-hidden')
    },

    clearSelectOptions: fieldContainer => {
        const select = fieldContainer.querySelector(
            Taxonomy.primarySelectSelector,
        )

        while (select.options.length > 1) {
            select.remove(1)
        }
    },

    createOption: (fieldContainer, value, text) => {
        const select = fieldContainer.querySelector(
            Taxonomy.primarySelectSelector,
        )

        const option = document.createElement('option')
        option.value = value
        option.text = text

        if (select.dataset.primaryTaxonomySelect === value) {
            option.selected = true
        }

        select.appendChild(option)
    },

    onChange: fieldContainer => {
        const checkboxes = fieldContainer.querySelectorAll(
            Taxonomy.checkboxSelector,
        )

        const checkedCheckboxes = Array.from(checkboxes).filter(
            checkbox => checkbox.checked,
        )

        Taxonomy.clearSelectOptions(fieldContainer)

        checkedCheckboxes.map(checkbox => {
            Taxonomy.createOption(
                fieldContainer,
                checkbox.value,
                checkbox.dataset.taxonomyCheckbox,
            )
        })

        if (checkedCheckboxes.length > 0) {
            Taxonomy.displaySelect(fieldContainer)
        } else {
            Taxonomy.hideSelect(fieldContainer)
        }
    },
}

const checkboxes = document.querySelectorAll(Taxonomy.checkboxSelector)

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', e => {
        Taxonomy.onChange(e.target.closest(Taxonomy.fieldSelector))
    })
})

const fieldContainers = document.querySelectorAll(Taxonomy.fieldSelector)

fieldContainers.forEach(fieldContainer => {
    Taxonomy.onChange(fieldContainer)
})
