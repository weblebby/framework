import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

document
    .querySelectorAll('[data-ckeditor]')
    .forEach(item => ClassicEditor.create(item))
