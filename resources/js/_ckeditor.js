import ClassicEditor from '@ckeditor/ckeditor5-build-classic'
import { GeneralHtmlSupport } from '@ckeditor/ckeditor5-html-support'

const TextEditor = {
    selector: '[data-ckeditor]',
    basicSelector: '[data-basic-ckeditor]',

    init: element => {
        element._CKEDITOR = ClassicEditor.create(element, {
            plugins: [GeneralHtmlSupport],
        })
    },

    initBasic: element => {
        element._CKEDITOR = ClassicEditor.create(element, {
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
    },
}

document
    .querySelectorAll(TextEditor.selector)
    .forEach(item => TextEditor.init(item))

document
    .querySelectorAll(TextEditor.basicSelector)
    .forEach(item => TextEditor.initBasic(item))

export default TextEditor
