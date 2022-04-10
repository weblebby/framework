document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        const buttons = form.querySelectorAll('[type="submit"]')

        buttons.forEach(button => {
            button.classList.add('btn--loading')
            button.disabled = true
        })
    })
})

document.querySelectorAll('[data-like-submit]').forEach(button => {
    button.addEventListener('click', () => {
        button.classList.add('btn--loading')
        button.disabled = true
    })
})

document.querySelectorAll('[data-form-image]').forEach(element => {
    const input = element.querySelector('input[type="file"]')

    input.addEventListener('change', () => {
        const reader = new FileReader()
        reader.readAsDataURL(input.files[0])

        reader.onload = () => {
            let imgElem = element.querySelector('[data-image-wrapper] > img')

            if (imgElem) {
                imgElem.src = reader.result
                return
            }

            imgElem = document.createElement('img')
            imgElem.src = reader.result
            imgElem.classList.add('fd-w-full', 'fd-h-full', 'fd-object-cover')

            element.querySelector('[data-image-wrapper]').appendChild(imgElem)
        }
    })
})
