const columnsElem = document.getElementById('form-columns')
const columnTemplate = document.getElementById('form-column-template')
const newColumnButton = document.querySelector('[data-toggle="new-column"]')
const templateElem = document.getElementById('template')

const Form = {
    columns: {
        lastKey: 0,

        add(data) {
            const column = columnTemplate.content.cloneNode(true)

            if (data?.id) {
                const idInput = document.createElement('input')

                idInput.type = 'hidden'
                idInput.name = `columns[${this.lastKey}][id]`
                idInput.value = data.id

                column.appendChild(idInput)
            }

            column.querySelectorAll('[data-form-group]').forEach(group => {
                group.dataset.formGroup = group.dataset.formGroup.replace(
                    ':key',
                    this.lastKey
                )
            })

            column.querySelectorAll('input, select').forEach(element => {
                const name = element.name
                    .replace('columns[:key][', '')
                    .replace(']', '')

                element.id = element.id.replace(':key', this.lastKey)
                element.name = element.name.replace(':key', this.lastKey)

                if (data?.[name]) {
                    element.type === 'checkbox'
                        ? (element.checked = data[name])
                        : (element.value = data[name])
                }
            })

            columnsElem.appendChild(column)

            this.lastKey++
            this.watch()
        },

        remove(element) {
            element.remove()
            this.watch()
        },

        reset() {
            this.removeAll()
            this.add()
            this.watch()
        },

        removeAll() {
            columnsElem.innerHTML = ''
            this.lastKey = 0
        },

        watch() {
            removeButtonElem = columnsElem.children[0].querySelector(
                '[data-toggle="remove-column"]'
            )

            if (columnsElem.children.length === 1) {
                removeButtonElem.setAttribute('disabled', 'disabled')
                removeButtonElem.classList.add('disabled')
            } else {
                removeButtonElem.removeAttribute('disabled')
                removeButtonElem.classList.remove('disabled')
            }
        },
    },
}

newColumnButton.addEventListener('click', () => {
    Form.columns.add()
})

document.addEventListener('click', e => {
    const button = e.target.closest('[data-toggle="remove-column"]')

    if (button) {
        Form.columns.remove(button.parentElement)
    }
})

templateElem?.addEventListener('change', ({ target }) => {
    let template = target.querySelector(':checked').dataset.template

    if (!template) {
        Form.columns.reset()
        return
    }

    Form.columns.removeAll()

    template = JSON.parse(template)

    template.columns.forEach(column => {
        Form.columns.add(column)
    })
})

DefaultFormColumns.forEach(column => {
    Form.columns.add(column)
})

if (DefaultFormColumns.length <= 0) {
    Form.columns.add()
}
