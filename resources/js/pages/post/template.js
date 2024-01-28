import api from '../../_api.js'
import { Tab } from '../../_tab.js'
import ConditionalField from '../../_conditional-field.js'

;(() => {
    const templateSelect = document.getElementById('template')
    if (!templateSelect) return

    const templateOptions = templateSelect.querySelectorAll('option')

    const fetchPostFields = async (postType, template) => {
        const url = window.Feadmin.Theme.postFieldsUrl
            .replace(':theme', '__ACTIVE')
            .replace(':template', template)

        const params = new URLSearchParams({
            type: postType,
        })

        return await api(`${url}?${params}`)
    }

    const handleOnChange = () => {
        const selectedOption = templateOptions[templateSelect.selectedIndex]

        document.querySelectorAll('[data-template-tab]').forEach(node => {
            node.remove()
        })

        const setTabs = async () => {
            if (!selectedOption.value) return

            const response = await fetchPostFields(
                templateSelect.dataset.postType,
                selectedOption.value,
            )

            response.tabs.forEach(tab => {
                const createdTab = Tab.create({
                    container: 'post',
                    id: tab.id,
                    title: tab.title,
                    content: tab.fields,
                })

                ConditionalField.listen(createdTab.content)
            })
        }

        setTabs()
    }

    templateSelect.addEventListener('change', handleOnChange)
    handleOnChange()
})()
