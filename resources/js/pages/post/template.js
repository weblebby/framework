import api from '../../_api.js'
import { Tab } from '../../_tab.js'

const templateSelect = document.getElementById('template')
const templateOptions = templateSelect.querySelectorAll('option')

const fetchPostFields = async (postType, template) => {
    const url = window.Feadmin.Theme.postFieldsUrl
        .replace(':theme', '__ACTIVE')
        .replace(':template', template)

    const params = new URLSearchParams({
        type: postType,
    })

    const response = await api(`${url}?${params}`)

    return await response.json()
}

templateSelect.addEventListener('change', () => {
    const selectedOption = templateOptions[templateSelect.selectedIndex]

    document.querySelectorAll('[data-template-tab]').forEach(node => {
        node.remove()
    })

    const setTabs = async () => {
        const response = await fetchPostFields(
            templateSelect.dataset.postType,
            selectedOption.value,
        )

        response.tabs.forEach(tab => {
            Tab.create({
                container: 'post',
                id: tab.id,
                title: tab.title,
                content: tab.fields,
            })
        })
    }

    setTabs()
})
