const Drawer = {
    open: (element, detail = {}) => {
        setTimeout(() => element.classList.toggle('drawer--open'), 100)
        Drawer.overlay(element)

        const focusable = element.querySelector('[data-drawer-focus]')
        focusable?.focus()

        element.dispatchEvent(
            new CustomEvent('drawer.open', {
                detail,
            }),
        )
    },

    close: (element, detail = {}) => {
        element.classList.toggle('drawer--open')

        const overlay = document.querySelector('.drawer__overlay')
        overlay.classList.remove('drawer__overlay--fade-in')

        element.dispatchEvent(
            new CustomEvent('drawer.hide', {
                detail,
            }),
        )

        setTimeout(() => {
            overlay.remove()

            element.dispatchEvent(
                new CustomEvent('drawer.hidden', {
                    detail,
                }),
            )
        }, 300)
    },

    closeCurrent: () => {
        Drawer.close(document.querySelector('.drawer--open'))
    },

    overlay: element => {
        const overlay = document.createElement('div')
        overlay.classList.add('drawer__overlay')
        document.body.append(overlay)

        setTimeout(() => {
            overlay.classList.add('drawer__overlay--fade-in')
        }, 10)

        overlay.addEventListener('click', () => {
            Drawer.close(element, { related: overlay })
        })
    },
}

document.addEventListener('click', e => {
    const openTrigger = e.target.closest('[data-drawer]')

    if (openTrigger) {
        e.preventDefault()

        const drawer = document.querySelector(openTrigger.dataset.drawer)

        if (drawer.classList.contains('drawer--open')) {
            Drawer.close(drawer, { related: openTrigger })
        } else {
            Drawer.open(drawer, { related: openTrigger })
        }
    }
})

document.querySelectorAll('[data-drawer-close]').forEach(button => {
    button.addEventListener('click', e => {
        e.preventDefault()
        Drawer.close(button.closest('.drawer'), { related: button })
    })
})

window.Weblebby.Drawer = Drawer
