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

        Tagify.handleHiddenInputs(tagify, options.name, tagify.value)

        tagify.on('change', e => {
            tagify.DOM.input.parentNode
                .querySelectorAll('[data-tagify-hidden]')
                .forEach(input => input.remove())

            if (!e.detail.value) return

            Tagify.handleHiddenInputs(tagify, options.name, e.detail.value)
        })

        tagify.on('input', e => Tagify.onInput(e.detail.value, tagify, options))

        return tagify
    },

    onInput: (value, tagify, options) => {
        if (!options.source) return

        if (
            !options.source.startsWith('http://') &&
            !options.source.startsWith('https://')
        ) {
            const path = options.source.startsWith('/')
                ? options.source
                : `/${options.source}`

            options.source = window.Weblebby.API.baseUrl + options.source
        }

        tagify.settings.whitelist = []
        tagify.loading(true).dropdown.hide.call(tagify)

        const url = new URL(options.source)

        const params = new URLSearchParams({
            ...Object.fromEntries(url.searchParams),
            [options?.searchKey || 'term']: value,
            _locale: options.locale || document.documentElement.lang,
        })

        api(`${url.origin}${url.pathname}?${params.toString()}`).then(data => {
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

    handleHiddenInputs: (tagify, name, tags) => {
        if (typeof tags === 'string') {
            tags = JSON.parse(tags)
        }

        tags.forEach(tag => {
            Tagify.createHiddenInput(tagify, name, tag.value)
            Tagify.createHiddenInput(tagify, `visualized_${name}`, tag.label)
        })
    },

    createHiddenInput: (tagify, name, value) => {
        const input = document.createElement('input')
        input.type = 'hidden'
        input.name = name
        input.value = value
        input.setAttribute('data-tagify-hidden', true)

        tagify.DOM.input.parentNode.insertBefore(input, tagify.DOM.input)
    },
}

document.querySelectorAll(Tagify.selector).forEach(input => {
    let options = {}

    try {
        options = JSON.parse(input.dataset.tagify)
    } catch (e) {
        //
    }

    if (!options.name) {
        options.name = input.name
    }

    Tagify.init(input, options)
})

export default Tagify
