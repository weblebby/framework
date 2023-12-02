import BaseTagify from '@yaireo/tagify'
import api from './_api.js'

const Tagify = {
    selector: '[data-tagify]',
    containerSelector: '[data-tagify-container]',

    init: (input, options) => {
        const tagify = new BaseTagify(input, {
            whitelist: [],
            tagTextProp: 'label',
            dropdown: {
                enabled: 1,
                position: 'label',
                mapValueTo: 'label',
                highlightFirst: true,
                searchKeys: ['label'],
            },
        })

        tagify.on('change', e => {
            if (!e.detail.value) return
            const tags = JSON.parse(e.detail.value)

            tagify.DOM.input.parentNode
                .querySelectorAll('[data-tagify-hidden]')
                .forEach(input => input.remove())

            tags.forEach(tag => {
                const input = document.createElement('input')
                input.type = 'hidden'
                input.name = options.name
                input.value = tag.value
                input.setAttribute('data-tagify-hidden', true)

                tagify.DOM.input.parentNode.insertBefore(
                    input,
                    tagify.DOM.input,
                )
            })
        })

        tagify.on('input', e => Tagify.onInput(e.detail.value, tagify, options))

        return tagify
    },

    onInput: (value, tagify, options) => {
        tagify.settings.whitelist = []
        tagify.loading(true).dropdown.hide.call(tagify)

        const params = new URLSearchParams({
            [options?.searchKey || 'term']: value,
        })

        api(`${options.source}?${params}`)
            .then(response => response.json())
            .then(data => {
                tagify.settings.whitelist = [
                    ...data.map(item => ({
                        value: item[options?.map?.value || 'value'],
                        label: item[options?.map?.label || 'label'],
                    })),
                    ...tagify.value,
                ]

                tagify.loading(false).dropdown.show.call(tagify, value)
            })
    },
}

document.querySelectorAll(Tagify.selector).forEach(input => {
    const options = JSON.parse(input.dataset.tagify)
    Tagify.init(input, options)
})

export default Tagify
