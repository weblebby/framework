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
    rowIterationLabelSelector: '[data-repeated-field-row-iteration-label]',
    rowContentSelector: '[data-repeated-field-row-content]',
    templateSelector: '[data-repeated-field-template]',
    addRowSelector: '[data-repeated-field-add-row]',
    handleRowSelector: '[data-repeated-field-handle-row]',
    collapseRowSelector: '[data-repeated-field-collapse-row]',
    removeRowSelector: '[data-repeated-field-remove-row]',
    emptyInputSelector: '[data-repeated-field-empty-input]',
    formGroupSelector: '[data-form-group]',
    imageWrapperSelector: '[data-image-wrapper]',

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

    getLastIndexOfRow(row) {
        const rows = row.closest(RepeatedField.rowsSelector)
        return Number(rows.getAttribute('data-last-row-index'))
    },

    parseName(row, key, options = {}) {
        const container = row.closest(RepeatedField.itemSelector)
        let index = Number(row.getAttribute('data-row-index'))

        if (options.indexByDomOrder) {
            index = RepeatedField.getIndexOfRow(row)
        }

        if (options.selectedRowIndex === undefined) {
            options.selectedRowIndex = index
        }

        if (!options.dottedName) {
            options.dottedName = row.querySelector(
                `[data-form-field-key="${key}"]`,
            ).dataset.originalFormGroup
        }

        options.dottedName = options.dottedName.replace(
            /(.*)(\*.*)/,
            (match, group1, group2) => {
                return group1 + group2.replace(/\*/, index)
            },
        )

        const computedName = convertDottedToInputName(options.dottedName)
        const id = inputNameToId(computedName)

        const parentRow = container.closest(RepeatedField.rowSelector)

        if (parentRow) {
            return RepeatedField.parseName(parentRow, key, options)
        }

        return {
            id,
            key,
            index: options.selectedRowIndex,
            computedName,
            dottedName: options.dottedName,
        }
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
            { dottedName: containerName },
        )

        RepeatedField.addToRemoveList(
            container,
            `${dottedName}.${Number(row.getAttribute('data-row-index'))}`,
        )

        row.remove()

        RepeatedField.resetRowIndexes(rows)
    },

    setRowIndexes: (row, options) => {
        if (options.mode === 'add') {
            const rowsEl = row.closest(RepeatedField.rowsSelector)
            let index = 0

            if (rowsEl.hasAttribute('data-last-row-index')) {
                index = Number(rowsEl.getAttribute('data-last-row-index')) + 1
            }

            rowsEl.setAttribute('data-last-row-index', index)
            row.setAttribute('data-row-index', index)

            RepeatedField.setRowInputs(row, options)
            RepeatedField.prepareConditionalFields(row)

            ConditionalField.listen(row)
        }

        RepeatedField.setIteration(row, options)
    },

    setRowInputs: (row, options) => {
        row.querySelectorAll('[name]').forEach(input => {
            const formGroup = input.closest(RepeatedField.formGroupSelector)

            const { id, key, computedName, dottedName } =
                RepeatedField.parseName(row, formGroup.dataset.formFieldKey)

            const formLabel = formGroup.querySelector('label')
            if (formLabel) formLabel.setAttribute('for', id)

            formGroup.dataset.formGroup = dottedName
            input.setAttribute('name', computedName)
            input.setAttribute('id', id)

            const value = options?.fields?.[key] || ''

            if (value) {
                input.setAttribute('value', value)
                input?._CKEDITOR?.then(editor => {
                    editor.setData(value)
                })

                const imageWrapperEl = formGroup.querySelector(
                    RepeatedField.imageWrapperSelector,
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

            if (
                options?.errors?.[dottedName] &&
                !formGroup.classList.contains('fd-has-error')
            ) {
                formGroup.classList.add('fd-has-error')

                const errorSpan = document.createElement('span')
                errorSpan.classList.add('fd-text-xs', 'fd-text-red-500')
                errorSpan.innerText = options.errors[dottedName][0]

                input.parentNode.parentNode.appendChild(errorSpan)
            }
        })
    },

    resetRowIndexes: (rows, mode = 'reset') => {
        rows.querySelectorAll(RepeatedField.rowSelector).forEach(row => {
            RepeatedField.setRowIndexes(row, {
                mode,
            })
        })
    },

    onAddRow: (row, options) => {
        RepeatedField.initPluginsBeforeSetRowIndexes(row)

        RepeatedField.setRowIndexes(row, {
            ...options,
            mode: 'add',
        })

        RepeatedField.initPluginsAfterSetRowIndexes(row, options)
    },

    initPluginsBeforeSetRowIndexes: row => {
        row.querySelectorAll(TextEditor.selector).forEach(input => {
            TextEditor.init(input)
        })

        row.querySelectorAll(Form.imageSelector).forEach(container => {
            Form.handleImageInput(container.querySelector('input[type="file"]'))
        })

        // Don't get selector from "CodeEditor.selector".
        // We need to import conditionally for performance issues.
        const codeEditorElements = row.querySelectorAll('[data-code-editor]')

        if (codeEditorElements.length > 0) {
            import('./code-editor.js').then(m => {
                codeEditorElements.forEach(element => {
                    if (!element._MONACO_EDITOR) {
                        m.default.createEditor(element)
                    }
                })
            })
        }
    },

    initPluginsAfterSetRowIndexes: (row, options) => {
        row.querySelectorAll(Form.checkboxSelector).forEach(input => {
            Form.handleCheckbox(input)
        })

        RepeatedField.listenCollapseRowButton(row)
        RepeatedField.listenRemoveRowButton(row)
        RepeatedField.removeEmptyInputIfNoRows(
            row.closest(RepeatedField.rowsSelector),
        )

        if (options?.collapse === false) {
            const collapseRowButton = row.querySelector(
                RepeatedField.collapseRowSelector,
            )
            RepeatedField.handleCollapseRowButton(collapseRowButton)
        }
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

    reorderRows: rowsEl => {
        rowsEl.querySelectorAll('[name]').forEach(input => {
            const row = input.closest(RepeatedField.rowSelector)
            const formGroup = input.closest(RepeatedField.formGroupSelector)
            const itemContainer = rowsEl.closest(RepeatedField.itemSelector)

            const { dottedName } = RepeatedField.parseName(
                row,
                formGroup.dataset.formFieldKey,
                {
                    indexByDomOrder: true,
                },
            )

            const oldName = formGroup.dataset.formGroup
            const newName = dottedName

            if (oldName === newName) {
                // Remove hiddenInputEl if early created and name is not changed
                const hiddenInputEl = itemContainer.querySelector(
                    `input[name="_reordered_fields[${oldName}]"]`,
                )
                hiddenInputEl?.remove()
                return
            }

            // Create hiddenInputEl if not created yet
            const hiddenInputEl = document.createElement('input')
            hiddenInputEl.setAttribute('type', 'hidden')
            hiddenInputEl.setAttribute('name', `_reordered_fields[${oldName}]`)
            hiddenInputEl.setAttribute('value', newName)

            itemContainer.appendChild(hiddenInputEl)
        })
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
            const originalSource = e.dragEvent.data.originalSource
            const over = e.dragEvent.data.over

            if (originalSource?.parentElement !== over?.parentElement) {
                e.cancel()
            }
        })

        sortable.on('sortable:stop', e => {
            setTimeout(() => {
                const originalSource = e.dragEvent.data.originalSource
                const rowsEl = originalSource.closest(
                    RepeatedField.rowsSelector,
                )

                RepeatedField.reorderRows(rowsEl)
                RepeatedField.resetRowIndexes(rowsEl, 'sort')
            }, 250)
        })
    },

    prepareConditionalFields: row => {
        row.querySelectorAll(ConditionalField.containerSelector).forEach(
            conditionalFieldContainer => {
                const conditions = ConditionalField.parseConditions(
                    conditionalFieldContainer,
                )

                const computedConditions = conditions.map(condition => {
                    const { id } = RepeatedField.parseName(row, condition.key)

                    return {
                        ...condition,
                        key: id,
                    }
                })

                conditionalFieldContainer.dataset.conditionalFieldItem =
                    JSON.stringify(computedConditions)
            },
        )
    },

    setIteration: (row, options) => {
        row.querySelectorAll(RepeatedField.rowIterationSelector).forEach(
            rowIteration => {
                rowIteration.innerText = `${
                    RepeatedField.getIndexOfRow(row) + 1
                }.`
            },
        )

        if (options.mode !== 'add') {
            return
        }

        row.querySelectorAll(RepeatedField.rowIterationLabelSelector).forEach(
            rowIterationLabel => {
                const firstFormGroup =
                    RowIteration.findFirstFormGroupForIterationLabel(row)

                if (!firstFormGroup) return

                if (
                    RowIteration.handleImageField(
                        firstFormGroup,
                        rowIterationLabel,
                    )
                ) {
                    return
                }

                if (
                    RowIteration.handleTextField(
                        firstFormGroup,
                        rowIterationLabel,
                    )
                ) {
                    return
                }

                if (
                    RowIteration.handleSelectField(
                        firstFormGroup,
                        rowIterationLabel,
                    )
                ) {
                    return
                }
            },
        )
    },
}

const RowIteration = {
    findFirstFormGroupForIterationLabel: row => {
        const formGroups = row.querySelectorAll(RepeatedField.formGroupSelector)

        const filteredFormGroups = Array.from(formGroups).filter(formGroup => {
            return (
                !formGroup.querySelector('input[type="checkbox"]') &&
                !formGroup.querySelector('input[type="radio"]')
            )
        })

        return filteredFormGroups?.[0]
    },

    handleImageField: (formGroup, rowIterationLabel) => {
        const imageWrapperEl = formGroup.querySelector(
            RepeatedField.imageWrapperSelector,
        )
        if (!imageWrapperEl) return

        const fileEl = formGroup.querySelector('input[type="file"]')
        if (!fileEl) return

        const imgEl = imageWrapperEl.querySelector('img')

        rowIterationLabel.querySelector('img')?.remove()

        const newImgEl = document.createElement('img')
        newImgEl.classList.add(
            'fd-w-12',
            'fd-h-12',
            'fd-border',
            'fd-rounded-lg',
            'fd-object-cover',
        )
        newImgEl.setAttribute('src', imgEl?.src || '')
        newImgEl.setAttribute('alt', 'Uploaded image')

        if (imgEl) {
            newImgEl.classList.remove('fd-hidden')
        } else {
            newImgEl.classList.add('fd-hidden')
        }

        rowIterationLabel.innerHTML = ''
        rowIterationLabel.append(newImgEl)

        fileEl?.addEventListener('image:loaded', ({ detail }) => {
            newImgEl.classList.remove('fd-hidden')
            newImgEl.setAttribute('src', detail.imgElem.src)
        })

        return true
    },

    handleTextField: (formGroup, rowIterationLabel) => {
        const textField = formGroup.querySelector(
            'input[name]:not([type="checkbox"]):not([type="radio"]):not([type="file"]), textarea[name]',
        )

        if (!textField) return

        const handleChange = value => {
            rowIterationLabel.textContent =
                value.length > 20 ? value.substring(0, 20) + '...' : value
        }

        handleChange(textField.value)

        textField.addEventListener('input', () => {
            handleChange(textField.value)
        })

        return true
    },

    handleSelectField: (formGroup, rowIterationLabel) => {
        const selectField = formGroup.querySelector('select[name]')

        if (!selectField) return

        const handleChange = value => {
            const optionEl = selectField.querySelector(
                `option[value="${value}"]`,
            )

            rowIterationLabel.textContent = optionEl?.textContent
        }

        handleChange(selectField.value)

        selectField.addEventListener('change', e => {
            handleChange(e.target.value)
        })

        return true
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
