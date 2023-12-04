import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

const TextEditor = {
    selector: '[data-ckeditor]',

    init: element => {
        element._CKEDITOR = ClassicEditor.create(element)
    },
}

document
    .querySelectorAll(TextEditor.selector)
    .forEach(item => TextEditor.init(item))

export default TextEditor
