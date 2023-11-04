import api from './_api.js'

document.querySelectorAll('[data-translation-input]').forEach(input => {
    input.addEventListener('change', ({ target }) => {
        api(window.Feadmin.Translation.routes.update, {
            method: 'POST',
            body: JSON.stringify({
                key: target.dataset.key,
                value: target.value,
                code: target.dataset.code,
                group: target.dataset.group,
            }),
        }).then(res => {
            if (res.status !== 200) {
                return
            }

            input.classList.add(
                'fd-ring',
                'fd-ring-green-500',
                'fd-ring-offset-2',
            )

            setTimeout(() => {
                input.classList.remove(
                    'fd-ring',
                    'fd-ring-green-500',
                    'fd-ring-offset-2',
                )
            }, 1000)
        })
    })
})
