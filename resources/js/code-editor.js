import * as monaco from 'monaco-editor'
import EditorWorker from 'monaco-editor/esm/vs/editor/editor.worker?worker'
import JsonWorker from 'monaco-editor/esm/vs/language/json/json.worker?worker'
import CssWorker from 'monaco-editor/esm/vs/language/css/css.worker?worker'
import HtmlWorker from 'monaco-editor/esm/vs/language/html/html.worker?worker'
import TsWorker from 'monaco-editor/esm/vs/language/typescript/ts.worker?worker'

self.MonacoEnvironment = {
    getWorker(_, label) {
        switch (label) {
            case 'json':
                return new JsonWorker()
            case 'css':
                return new CssWorker()
            case 'html':
                return new HtmlWorker()
            case 'typescript':
            case 'javascript':
                return new TsWorker()
            default:
                return new EditorWorker()
        }
    },
}

const CodeEditor = {
    selector: '[data-code-editor]',

    createEditor(element) {
        if (element._MONACO_EDITOR) {
            console.warn('Editor already initialized.')
            return
        }

        const hiddenInput = element
            .closest('[data-form-group]')
            .querySelector('input[data-code-editor-value]')

        const options = JSON.parse(element.dataset.codeEditor)
        options.value = hiddenInput.value

        const editor = monaco.editor.create(element, {
            value: element.value,
            language: 'html',
            theme: 'vs-dark',
            automaticLayout: true,
            minimap: {
                enabled: false,
            },
            ...options,
        })

        editor.getModel().onDidChangeContent(() => {
            hiddenInput.value = editor.getValue()
        })

        element._MONACO_EDITOR = editor
    },
}

const elements = document.querySelectorAll(CodeEditor.selector)

elements.forEach(element => {
    CodeEditor.createEditor(element)
})

export default CodeEditor
