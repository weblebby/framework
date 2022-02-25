const Drawer = {
    open: (element, detail = {}) => {
        setTimeout(() => element.classList.toggle('drawer--open'), 100)
        Drawer.overlay(element)

        const focus = element.querySelector('[data-drawer-focus]')
        focus?.focus()

        element.dispatchEvent(
            new CustomEvent('drawer.open', {
                detail,
            })
        )
    },

    close: (element, detail = {}) => {
        element.classList.toggle('drawer--open')

        const overlay = document.querySelector('.drawer__overlay')
        overlay.classList.remove('drawer__overlay--fade-in')

        element.dispatchEvent(
            new CustomEvent('drawer.hidden', {
                detail,
            })
        )

        setTimeout(() => {
            overlay.remove()
        }, 300)
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

document.querySelectorAll('[data-drawer]').forEach(button => {
    button.addEventListener('click', e => {
        e.preventDefault()

        const drawer = document.querySelector(button.dataset.drawer)
        Drawer.open(drawer, { related: button })
    })
})

document.querySelectorAll('[data-drawer-close]').forEach(button => {
    button.addEventListener('click', e => {
        e.preventDefault()
        Drawer.close(button.closest('.drawer'), { related: button })
    })
})

window.Drawer = Drawer
