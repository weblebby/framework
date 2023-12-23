import TextEditor from './_ckeditor.js'
import Form from './_form.js'
import ConditionalField from './_conditional-field.js'
import { convertDottedToInputName, inputNameToId } from './lib/utils.js'
import Sortable from '@shopify/draggable/build/esm/Sortable/Sortable'

const RepeatedField = {
    itemSelector: '[data-repeated-field-item]',
    singleItemSelector: '[data-repeated-field-item=":id"]',
    rowsSelector: '[data-repeated-field-rows]',
    rowSelector: '[data-repeated-field-row]',
    rowIterationSelector: '[data-repeated-field-row-iteration]',
    templateSelector: '[data-repeated-field-template]',
    addRowSelector: '[data-repeated-field-add-row]',
    handleRowSelector: '[data-repeated-field-handle-row]',
    collapseRowSelector: '[data-repeated-field-collapse-row]',
    rowContentSelector: '[data-repeated-field-row-content]',
    removeRowSelector: '[data-repeated-field-remove-row]',
    emptyInputSelector: '[data-repeated-field-empty-input]',
    formGroupSelector: '[data-form-group]',

    findContainer: containerName => {
        return document.querySelector(
            RepeatedField.singleItemSelector.replace(':id', containerName),
        )
    },

    getRowsElement: container => {
        return container.querySelector(RepeatedField.rowsSelector)
    },

    getRowsCount: container => {
        return RepeatedField.getRowsElement(container).children.length
    },

    getMaxRow: container => {
        const maxRow = Number(container.dataset.maxRow)
        return maxRow === -1 ? Infinity : maxRow
    },

    getIndexOfRow(row) {
        const rows = row.closest(RepeatedField.rowsSelector)
        return Array.from(rows.children).indexOf(row)
    },

    parseName(row, key, dottedName = null) {
        const container = row.closest(RepeatedField.itemSelector)
        const index = RepeatedField.getIndexOfRow(row)

        if (!dottedName) {
            dottedName = row.querySelector(`[data-form-field-key="${key}"]`)
                .dataset.originalFormGroup
        }

        dottedName = dottedName.replace(
            /(.*)(\*.*)/,
            (match, group1, group2) => {
                return group1 + group2.replace(/\*/, index)
            },
        )

        const computedName = convertDottedToInputName(dottedName)
        const id = inputNameToId(computedName)

        const parentRow = container.closest(RepeatedField.rowSelector)

        if (parentRow) {
            return RepeatedField.parseName(parentRow, key, dottedName)
        }

        return { id, key, computedName, dottedName }
    },

    getTemplate: container => {
        const templates = container.querySelectorAll(
            RepeatedField.templateSelector,
        )

        return templates[templates.length - 1]
    },

    addRow: options => {
        const itemContainer =
            typeof options.itemContainer === 'string'
                ? RepeatedField.findContainer(options.itemContainer)
                : options.itemContainer

        const rowsEl = RepeatedField.getRowsElement(itemContainer)
        const maxRow = RepeatedField.getMaxRow(itemContainer)

        if (RepeatedField.getRowsCount(itemContainer) >= maxRow) {
            return
        }

        const template = RepeatedField.getTemplate(itemContainer)
        const row = template.cloneNode(true).content.children[0]

        rowsEl.appendChild(row)
        RepeatedField.onAddRow(row, options)

        if (RepeatedField.getRowsCount(itemContainer) >= maxRow) {
            RepeatedField.disableAddRowButton(itemContainer)
        }

        Object.keys(options?.fields || {}).forEach(key => {
            const value = options?.fields?.[key]

            if (Array.isArray(value)) {
                value.forEach((value, index) => {
                    const childItemContainers = itemContainer.querySelectorAll(
                        RepeatedField.singleItemSelector.replace(
                            ':id',
                            `${options?.dottedName}.*.${key}`,
                        ),
                    )

                    const childItemContainer =
                        childItemContainers[options.index]

                    RepeatedField.addRow({
                        itemContainer: childItemContainer,
                        fields: value,
                        errors: options?.errors,
                        dottedName: `${options?.dottedName}.*.${key}`,
                        index,
                    })
                })
            }
        })
    },

    removeRow: e => {
        e.preventDefault()

        const container = e.target.closest(RepeatedField.itemSelector)
        const containerName = container.dataset.repeatedFieldItem
        const rows = e.target.closest(RepeatedField.rowsSelector)

        if (rows.children.length <= 1) {
            rows.classList.add('fd-hidden')
        }

        RepeatedField.enableAddRowButton(containerName)

        const row = e.target.closest(RepeatedField.rowSelector)

        const { dottedName } = RepeatedField.parseName(
            row.parentElement.closest(RepeatedField.rowSelector) || row,
            undefined,
            containerName,
        )

        RepeatedField.addToRemoveList(
            container,
            `${dottedName}.${RepeatedField.getIndexOfRow(row)}`,
        )

        row.remove()

        RepeatedField.resetRowIndexes(rows)
    },

    setRowIndexes: (row, options) => {
        row.querySelectorAll('[name]').forEach(input => {
            const formGroup = input.closest(RepeatedField.formGroupSelector)

            const { id, key, computedName, dottedName } =
                RepeatedField.parseName(row, formGroup.dataset.formFieldKey)

            input.setAttribute('name', computedName)
            input.setAttribute('id', id)

            const value = options?.fields?.[key] || ''
            if (value && options.mode === 'add') {
                input.setAttribute('value', value)
                input?._CKEDITOR?.then(editor => {
                    editor.setData(value)
                })

                const imageWrapperEl = formGroup.querySelector(
                    '[data-image-wrapper]',
                )

                if (imageWrapperEl) {
                    const imgEl = document.createElement('img')
                    imgEl.classList.add(
                        'fd-w-full',
                        'fd-h-full',
                        'fd-object-cover',
                    )
                    imgEl.setAttribute('src', value)
                    imgEl.setAttribute('alt', 'Uploaded image')
                    imageWrapperEl.append(imgEl)
                }

                if (input.tagName === 'SELECT') {
                    const optionEl = input.querySelector(
                        `option[value="${value}"]`,
                    )
                    optionEl?.setAttribute('selected', 'selected')
                }
            }

            formGroup.dataset.formGroup = dottedName

            const formLabel = formGroup.querySelector('label')
            if (formLabel) formLabel.setAttribute('for', id)

            if (
                options?.errors?.[dottedName] &&
                !formGroup.classList.contains('fd-has-error') &&
                options.mode === 'add'
            ) {
                formGroup.classList.add('fd-has-error')

                const errorSpan = document.createElement('span')
                errorSpan.classList.add('fd-text-xs', 'fd-text-red-500')
                errorSpan.innerText = options.errors[dottedName][0]

                input.parentNode.parentNode.appendChild(errorSpan)
            }
        })

        row.querySelectorAll(RepeatedField.rowIterationSelector).forEach(
            rowIteration => {
                rowIteration.innerText = `${
                    RepeatedField.getIndexOfRow(row) + 1
                }.`
            },
        )

        if (options.mode === 'add') {
            row.querySelectorAll(ConditionalField.containerSelector).forEach(
                conditionalFieldContainer => {
                    const conditions = ConditionalField.parseConditions(
                        conditionalFieldContainer,
                    )

                    const computedConditions = conditions.map(condition => {
                        const { id } = RepeatedField.parseName(
                            row,
                            condition.key,
                        )

                        return {
                            ...condition,
                            key: id,
                        }
                    })

                    conditionalFieldContainer.dataset.conditionalFieldItem =
                        JSON.stringify(computedConditions)
                },
            )

            ConditionalField.listen(row)
        }
    },

    resetRowIndexes: rows => {
        rows.querySelectorAll(RepeatedField.rowSelector).forEach(row => {
            RepeatedField.setRowIndexes(row, {
                mode: 'reset',
            })
        })
    },

    onAddRow: (row, options) => {
        RepeatedField.initPlugins(row)
        RepeatedField.setRowIndexes(row, {
            ...options,
            mode: 'add',
        })
        RepeatedField.listenCollapseRowButton(row)
        RepeatedField.listenRemoveRowButton(row)
        RepeatedField.removeEmptyInputIfNoRows(
            row.closest(RepeatedField.rowsSelector),
        )

        row.querySelectorAll(RepeatedField.itemSelector).forEach(item => {
            RepeatedField.initializeSortable(item)
        })

        if (options?.collapse === false) {
            const collapseRowButton = row.querySelector(
                RepeatedField.collapseRowSelector,
            )
            RepeatedField.handleCollapseRowButton(collapseRowButton)
        }
    },

    initPlugins: row => {
        row.querySelectorAll(TextEditor.selector).forEach(input => {
            TextEditor.init(input)
        })

        row.querySelectorAll(Form.imageSelector).forEach(container => {
            Form.handleImageInput(container.querySelector('input[type="file"]'))
        })
    },

    listenRemoveRowButton: row => {
        const removeRowButton = row.querySelector(
            RepeatedField.removeRowSelector,
        )
        removeRowButton.addEventListener('click', RepeatedField.removeRow)
    },

    listenCollapseRowButton: row => {
        const collapseRowButton = row.querySelector(
            RepeatedField.collapseRowSelector,
        )
        collapseRowButton?.addEventListener('click', e => {
            e.preventDefault()
            RepeatedField.handleCollapseRowButton(collapseRowButton)
        })
    },

    handleCollapseRowButton: button => {
        const row = button.closest(RepeatedField.rowSelector)
        const rowContent = row.querySelector(RepeatedField.rowContentSelector)
        const isCollapsed = rowContent.classList.contains('fd-hidden')

        if (isCollapsed) {
            rowContent.classList.remove('fd-hidden')
            button.style.transform = 'rotate(90deg)'
        } else {
            rowContent.classList.add('fd-hidden')
            button.style.transform = 'rotate(0deg)'
        }
    },

    enableAddRowButton: containerName => {
        const button = RepeatedField.findContainer(containerName).querySelector(
            RepeatedField.addRowSelector,
        )

        button.removeAttribute('disabled')
    },

    disableAddRowButton: itemContainer => {
        const button = itemContainer.querySelector(RepeatedField.addRowSelector)

        button.setAttribute('disabled', 'disabled')
    },

    addToRemoveList: (container, key) => {
        const input = document.createElement('input')
        input.setAttribute('type', 'hidden')
        input.setAttribute('name', '_deleted_fields[]')
        input.setAttribute('value', key)

        container.appendChild(input)
    },

    removeEmptyInputIfNoRows: rows => {
        if (rows.children.length <= 0) {
            return
        }

        rows.classList.remove('fd-hidden')

        RepeatedField.removeEmptyInput(
            rows.closest(RepeatedField.itemSelector).dataset.repeatedFieldItem,
        )
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

    initializeSortable: container => {
        const rowWrapperEl = container.querySelector(RepeatedField.rowsSelector)

        const sortable = new Sortable(rowWrapperEl, {
            draggable: RepeatedField.rowSelector,
            handle: RepeatedField.handleRowSelector,
            mirror: {
                constrainDimensions: true,
            },
        })

        sortable.on('sortable:sort', e => {
            // check e.dragEvent.data.originalSource and e.dragEvent.data.over is on same level
            const originalSource = e.dragEvent.data.originalSource
            const over = e.dragEvent.data.over

            if (originalSource?.parentElement !== over?.parentElement) {
                e.cancel()
            }
        })
    },
}

document
    .querySelectorAll(RepeatedField.itemSelector)
    .forEach(RepeatedField.initializeSortable)

document.addEventListener('click', e => {
    const button = e.target.closest(RepeatedField.addRowSelector)
    if (!button) return

    e.preventDefault()

    const itemContainer = button.closest(RepeatedField.itemSelector)

    RepeatedField.addRow({
        itemContainer,
        collapse: false,
    })
})

window.Feadmin.RepeatedField = RepeatedField
