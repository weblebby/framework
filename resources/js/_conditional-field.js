const ConditionalField = {
    containerSelector: '[data-conditional-field-item]',
    activeContainerSelector: '[data-conditional-field-item][data-active="1"]',
    hiddenContainerSelector:
        '[data-conditional-field-item]:not([data-active="1"])',

    init(container) {
        const conditions = ConditionalField.parseConditions(container)

        if (!conditions) {
            return
        }

        conditions.forEach(condition => {
            const { key, value, operator } = condition

            const input = document.getElementById(key)

            if (!input) {
                return
            }

            input.addEventListener('change', () => {
                const show = ConditionalField.compare(
                    input.value,
                    value,
                    operator,
                )

                ConditionalField.toggle(container, show)
            })

            const show = ConditionalField.compare(input.value, value, operator)
            ConditionalField.toggle(container, show)
        })

        ConditionalField.clearHiddenInputs(container)
    },

    listen(container) {
        container
            .querySelectorAll(ConditionalField.containerSelector)
            .forEach(conditionalFieldContainer => {
                ConditionalField.init(conditionalFieldContainer)
            })
    },

    toggle(container, show) {
        if (show) {
            container.classList.remove('fd-hidden')
            container.dataset.active = '1'
        } else {
            container.classList.add('fd-hidden')
            delete container.dataset.active
        }
    },

    /**
     * Make sure to this method to sync with validateCondition method of FieldValidationService.php
     */
    compare(value, conditionValue, operator) {
        if (Array.isArray(conditionValue)) {
            value = value.toString()
            conditionValue = conditionValue.map(v => v.toString())
        }

        switch (operator) {
            case '===':
                return value === conditionValue
            case '!==':
                return value !== conditionValue
            case '==':
                return value == conditionValue
            case '!=':
                return value != conditionValue
            case '>':
                return value > conditionValue
            case '>=':
                return value >= conditionValue
            case '<':
                return value < conditionValue
            case '<=':
                return value <= conditionValue
            case 'in':
                return conditionValue.includes(value)
            case 'not_in':
                return !conditionValue.includes(value)
            case 'between':
                return value >= conditionValue[0] && value <= conditionValue[1]
            case 'not_between':
                return value < conditionValue[0] || value > conditionValue[1]
            case 'contains':
                return value.includes(conditionValue)
            case 'not_contains':
                return !value.includes(conditionValue)
            case 'starts_with':
                return value.startsWith(conditionValue)
            case 'ends_with':
                return value.endsWith(conditionValue)
            case 'regex':
                return new RegExp(conditionValue).test(value)
            case 'not_regex':
                return !new RegExp(conditionValue).test(value)
            default:
                return false
        }
    },

    parseConditions(container) {
        const conditions = container.dataset.conditionalFieldItem

        if (!conditions) {
            return
        }

        return JSON.parse(conditions)
    },

    clearHiddenInputs(container) {
        const form = container.closest('form')

        form.addEventListener('submit', e => {
            const hiddenConditionalFields = form.querySelectorAll(
                ConditionalField.hiddenContainerSelector,
            )

            hiddenConditionalFields.forEach(field => {
                field.remove()
            })
        })
    },
}

ConditionalField.listen(document)

export default ConditionalField
