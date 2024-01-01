const Form = {
    imageSelector: '[data-form-image]',

    handleSubmitters: form => {
        const buttons = form.querySelectorAll('[type="submit"]')

        buttons.forEach(button => {
            button.classList.add('btn--loading')
            button.disabled = true
        })
    },

    handleSingleSubmitter: button => {
        button.classList.add('btn--loading')
        button.disabled = true
    },

    handleImageInput: input => {
        const container = input.closest(Form.imageSelector)

        const onChange = () => {
            const reader = new FileReader()
            const file = input.files[0]
            reader.readAsDataURL(file)

            if (!file.type.match('image.*')) {
                window.Feadmin.Toastr.add('Please select an image file.')
            }

            reader.onload = () => {
                let imgElem = container.querySelector(
                    '[data-image-wrapper] > img',
                )

                if (!imgElem) {
                    imgElem = document.createElement('img')
                    imgElem.classList.add(
                        'fd-w-full',
                        'fd-h-full',
                        'fd-object-cover',
                    )
                }

                imgElem.src = reader.result

                container
                    .querySelector('[data-image-wrapper]')
                    .appendChild(imgElem)

                if (!file.type.match('image.*')) {
                    imgElem.remove()
                } else {
                    const event = new CustomEvent('image:loaded', {
                        detail: {
                            file,
                            imgElem,
                        },
                    })

                    input.dispatchEvent(event)
                }
            }
        }

        input.addEventListener('change', onChange)

        return {
            destroy: () => {
                input.removeEventListener('change', onChange)
            },
        }
    },
}

document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', () => {
        Form.handleSubmitters(form)
    })
})

document.querySelectorAll('[data-like-submit]').forEach(button => {
    button.addEventListener('click', () => {
        Form.handleSingleSubmitter(button)
    })
})

document.querySelectorAll(Form.imageSelector).forEach(element => {
    Form.handleImageInput(element.querySelector('input[type="file"]'))
})

export default Form
