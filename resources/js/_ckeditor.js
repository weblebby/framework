const BaseEditor = import('./ckeditor/ckeditor')

const TextEditor = {
    selector: '[data-ckeditor]',
    basicSelector: '[data-basic-ckeditor]',

    init: element => {
        BaseEditor.then(({ default: { Editor } }) => {
            element._CKEDITOR = Editor.create(element)
        })
    },

    initBasic: element => {
        BaseEditor.then(({ default: { Editor } }) => {
            element._CKEDITOR = Editor.create(element, {
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
            })
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
