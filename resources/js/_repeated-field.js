const RepeatedField = {
    itemSelector: '[data-repeated-field-item]',
    singleItemSelector: '[data-repeated-field-item=":id"]',
    rowsSelector: '[data-repeated-field-rows]',
    rowSelector: '[data-repeated-field-row]',
    templateSelector: '[data-repeated-field-template]',
    addRowSelector: '[data-repeated-field-add-row]',
    removeRowSelector: '[data-repeated-field-remove-row]',
    emptyInputSelector: '[data-repeated-field-empty-input]',

    findContainer: containerName => {
        return document.querySelector(
            RepeatedField.singleItemSelector.replace(':id', containerName),
        )
    },

    addRow: options => {
        const container = RepeatedField.findContainer(options.container)
        const rows = container.querySelector(RepeatedField.rowsSelector)

        let maxRow = Number(container.dataset.maxRow)
        maxRow = maxRow === -1 ? Infinity : maxRow

        if (rows.children.length >= maxRow) {
            return
        }

        const index = rows.children.length
        const template = document.querySelector(RepeatedField.templateSelector)

        const row = document.createElement('div')
        row.innerHTML = template.innerHTML
        row.querySelectorAll('[name]').forEach(input => {
            let originalName = input.getAttribute('name')
            let newName = `${container.dataset.repeatedFieldItem}[${index}][${originalName}]`
            const dottedName = `${container.dataset.repeatedFieldItem}.${index}.${originalName}`

            if (originalName.includes('[]')) {
                originalName = originalName.replace('[]', '')
                newName = `${container.dataset.repeatedFieldItem}[${index}][${originalName}][]`
            }

            input.setAttribute('value', options?.fields?.[originalName] || '')
            input.setAttribute('name', newName)
            input.setAttribute(
                'id',
                newName
                    .replace(/\[]/g, '')
                    .replace(/\[/g, '_')
                    .replace(/]/g, ''),
            )

            if (options?.errors?.[dottedName]) {
                const errorSpan = document.createElement('span')
                errorSpan.classList.add('fd-text-xs', 'fd-text-red-500')
                errorSpan.innerText = options.errors[dottedName][0]

                input.parentNode.parentNode.appendChild(errorSpan)
            }
        })

        rows.appendChild(row)
        RepeatedField.onAddRow(row)

        if (rows.children.length >= maxRow) {
            RepeatedField.disableAddRowButton(options.container)
        }
    },

    removeRow: e => {
        e.preventDefault()

        const containerName = e.target.closest(RepeatedField.itemSelector)
            .dataset.repeatedFieldItem

        const rows = e.target.closest(RepeatedField.rowsSelector)

        if (rows.children.length <= 1) {
            rows.classList.add('fd-hidden')
            RepeatedField.addEmptyInput(containerName)
        }

        RepeatedField.enableAddRowButton(containerName)

        const row = e.target.closest(RepeatedField.rowSelector)
        row.parentNode.remove()
    },

    onAddRow: row => {
        const removeRowButton = row.querySelector(
            RepeatedField.removeRowSelector,
        )
        removeRowButton.addEventListener('click', RepeatedField.removeRow)

        const rows = row.closest(RepeatedField.rowsSelector)

        if (rows.children.length > 0) {
            rows.classList.remove('fd-hidden')
            RepeatedField.removeEmptyInput(
                row.closest(RepeatedField.itemSelector).dataset
                    .repeatedFieldItem,
            )
        }
    },

    enableAddRowButton: containerName => {
        const button = RepeatedField.findContainer(containerName).querySelector(
            RepeatedField.addRowSelector,
        )

        button.removeAttribute('disabled')
    },

    disableAddRowButton: containerName => {
        const button = RepeatedField.findContainer(containerName).querySelector(
            RepeatedField.addRowSelector,
        )

        button.setAttribute('disabled', 'disabled')
    },

    addEmptyInput: containerName => {
        const container = RepeatedField.findContainer(containerName)

        const input = document.createElement('input')
        const selector = RepeatedField.emptyInputSelector.replace(/\[|]/g, '')

        input.setAttribute('type', 'hidden')
        input.setAttribute('name', container.dataset.repeatedFieldItem)
        input.setAttribute(selector, selector)

        container.appendChild(input)
    },

    removeEmptyInput: containerName => {
        const container = RepeatedField.findContainer(containerName)
        const input = container.querySelector(RepeatedField.emptyInputSelector)
        input?.remove()
    },
}

const addRowButtons = document.querySelectorAll(RepeatedField.addRowSelector)
addRowButtons.forEach(button => {
    button.addEventListener('click', e => {
        e.preventDefault()

        RepeatedField.addRow({
            container: button.closest(RepeatedField.itemSelector).dataset
                .repeatedFieldItem,
        })
    })
})

window.Feadmin.RepeatedField = RepeatedField
