const ConditionalField = {
    containerSelector: '[data-conditional-field-item]',

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
        } else {
            container.classList.add('fd-hidden')
        }
    },

    compare(value, conditionValue, operator) {
        switch (operator) {
            case '!==':
                return value !== conditionValue
            case '<':
                return value < conditionValue
            case '<=':
                return value <= conditionValue
            case '>':
                return value > conditionValue
            case '>=':
                return value >= conditionValue
            case '===':
            default:
                return value === conditionValue
        }
    },

    parseConditions(container) {
        const conditions = container.dataset.conditionalFieldItem

        if (!conditions) {
            return
        }

        return JSON.parse(conditions)
    },
}

ConditionalField.listen(document)

export default ConditionalField
