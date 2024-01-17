const Collapsible = {
    containerSelector: '[data-collapsible]',
    toggleSelector: '[data-collapse-toggle]',
    contentSelector: '[data-collapse-content]',
    iconSelector: '[data-collapse-icon]',

    init: () => {
        document
            .querySelectorAll(Collapsible.containerSelector)
            .forEach(container => {
                const toggle = container.querySelector(
                    Collapsible.toggleSelector,
                )

                toggle?.addEventListener('click', e => {
                    e.preventDefault()

                    Collapsible.toggle(
                        toggle.closest(Collapsible.containerSelector),
                    )
                })
            })
    },

    toggle: (container, open = null) => {
        if (open === null) {
            container.setAttribute(
                'aria-expanded',
                container.getAttribute('aria-expanded') === 'true'
                    ? 'false'
                    : 'true',
            )
        } else if (open) {
            container.setAttribute('aria-expanded', 'true')
        } else {
            container.setAttribute('aria-expanded', 'false')
        }
    },

    open: container => {
        Collapsible.toggle(container, true)
    },

    close: container => {
        Collapsible.toggle(container, false)
    },
}

Collapsible.init()

export default Collapsible
