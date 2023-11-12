const Modal = {
    open(modal, detail = {}) {
        modal.style.removeProperty('display')
        document.body.classList.add('fd-overflow-hidden')

        if (detail.related?.dataset?.action) {
            this.setFormAction(modal, detail.related)
        }

        modal.querySelector('[data-modal-autofocus]')?.focus()

        modal.dispatchEvent(
            new CustomEvent('modal.open', {
                detail,
            }),
        )
    },

    close(modal, detail = {}) {
        modal.style.setProperty('display', 'none')
        document.body.classList.remove('fd-overflow-hidden')

        modal.dispatchEvent(
            new CustomEvent('modal.close', {
                detail,
            }),
        )
    },

    setFormAction(modal, related) {
        const form = modal.querySelector('form')
        form.action = related.dataset.action
    },
}

const closeTriggers = document.querySelectorAll('[data-modal-close]')
const overlay = document.querySelector('[data-modal]')

document.addEventListener('click', e => {
    const openTrigger = e.target.closest('[data-modal-open]')

    if (openTrigger) {
        e.preventDefault()

        Modal.open(document.querySelector(openTrigger.dataset.modalOpen), {
            related: openTrigger,
        })
    }
})

closeTriggers.forEach(trigger => {
    const modal = trigger.closest('[data-modal]')

    trigger.addEventListener('click', () =>
        Modal.close(modal, { related: trigger }),
    )
})

overlay?.addEventListener('click', e => {
    if (e.target === overlay) {
        Modal.close(overlay)
    }
})

window.Feadmin.Modal = Modal
