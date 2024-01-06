import api from './_api.js'
import Toastr from './_toastr.js'

const Localization = {
    localize: async (code, key, value) => {
        try {
            await api(window.Feadmin.Translation.routes.update, {
                method: 'POST',
                body: JSON.stringify({
                    key: key,
                    value: value,
                    code: code,
                }),
            })
        } catch (err) {
            const data = await err.response.json()

            if (err.response.status === 422) {
                const message = Object.values(data.errors)?.[0]?.[0]
                message && Toastr.add(message)
            } else {
                Toastr.add('Something went wrong')
            }
        }
    },

    localizeFromInput: async input => {
        const { code, key } = input.dataset
        void Localization.localize(code, key, input.value, input)
        Localization.highlightInput(input)
    },

    highlightInput: input => {
        if (!input) return

        input.classList.add('fd-ring', 'fd-ring-green-500', 'fd-ring-offset-2')

        setTimeout(() => {
            input.classList.remove(
                'fd-ring',
                'fd-ring-green-500',
                'fd-ring-offset-2',
            )
        }, 1000)
    },
}

document.querySelectorAll('[data-translation-input]').forEach(input => {
    input.addEventListener('change', ({ target: input }) => {
        void Localization.localizeFromInput(input)
    })

    input.addEventListener('keyup', ({ target: input }) => {
        // If enter key is pressed
        if (event.keyCode === 13) {
            void Localization.localizeFromInput(input)
        }
    })
})
