const BaseEditor = import('./ckeditor/ckeditor')

const TextEditor = {
    selector: '[data-ckeditor]',
    basicSelector: '[data-basic-ckeditor]',

    init: element => {
        BaseEditor.then(({ default: { Editor } }) => {
            const editor = (element._CKEDITOR = Editor.create(element))

            element.dispatchEvent(
                new CustomEvent('ckeditor:load', {
                    detail: { editor },
                }),
            )
        })
    },

    initBasic: element => {
        BaseEditor.then(({ default: { Editor } }) => {
            const editor = (element._CKEDITOR = Editor.create(element, {
                toolbar: [
                    'undo',
                    'redo',
                    '|',
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'link',
                    '|',
                    'bulletedList',
                    'numberedList',
                ],
            }))

            element.dispatchEvent(
                new CustomEvent('ckeditor:load', {
                    detail: { editor },
                }),
            )
        })
    },
}

document
    .querySelectorAll(TextEditor.selector)
    .forEach(item => TextEditor.init(item))

document
    .querySelectorAll(TextEditor.basicSelector)
    .forEach(item => TextEditor.initBasic(item))

export default TextEditor
