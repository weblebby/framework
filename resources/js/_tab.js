export const Tab = {
    containerSelector: '[data-tab-container]',
    singleContainerSelector: '[data-tab-container=":id"]',
    headerSelector: '[data-tab-header]',
    buttonSelector: '[data-tab-button]',
    singleButtonSelector: '[data-tab-button=":id"]',
    contentSelector: '[data-tab-content]',
    singleContentSelector: '[data-tab-content=":for"]',
    activeClassList: ['fd-bg-zinc-100', 'fd-font-medium'],

    buttonTemplateSelector: 'template[data-tab-button-template]',
    contentTemplateSelector: 'template[data-tab-content-template]',

    buttons: container => {
        return container.querySelectorAll(Tab.buttonSelector)
    },

    contents: container => {
        return container.querySelectorAll(Tab.contentSelector)
    },

    content: (container, id) => {
        if (!id.startsWith('tab-')) {
            id = `tab-${id}`
        }

        return container.querySelector(
            Tab.singleContentSelector.replace(':for', id),
        )
    },

    listenContainer: container => {
        const buttons = container.querySelectorAll(Tab.buttonSelector)

        buttons.forEach(button => {
            Tab.listenButton(button)
        })
    },

    listenButton: button => {
        button.addEventListener('click', e => {
            e.preventDefault()
            Tab.select(button)
        })
    },

    select: button => {
        const container = button.closest(Tab.containerSelector)

        Tab.buttons(container).forEach(button => {
            button.classList.remove(...Tab.activeClassList)
        })

        Tab.contents(container).forEach(content => {
            content.style.display = 'none'
        })

        const content = Tab.content(container, button.dataset.tabButton)
        content.style.removeProperty('display')

        button.classList.add(...Tab.activeClassList)
    },

    create: tab => {
        const container = document.querySelector(
            Tab.singleContainerSelector.replace(':id', tab.container),
        )
        const header = container.querySelector(Tab.headerSelector)

        const currentButton = header.querySelector(
            Tab.singleButtonSelector.replace(':id', `tab-${tab.id}`),
        )

        if (currentButton) {
            return {
                button: currentButton,
                content: Tab.content(container, tab.id),
            }
        }

        const buttonTemplate = document.importNode(
            document.querySelector(Tab.buttonTemplateSelector).cloneNode(true)
                .content,
            true,
        )
        const contentTemplate = document.importNode(
            document.querySelector(Tab.contentTemplateSelector).cloneNode(true)
                .content,
            true,
        )

        const clonedButton = buttonTemplate.querySelector(Tab.buttonSelector)
        const clonedContent = contentTemplate.querySelector(Tab.contentSelector)

        clonedButton.textContent = tab.title
        clonedButton.setAttribute('data-template-tab', 'data-template-tab')
        clonedButton.setAttribute(
            'data-tab-button',
            clonedButton.getAttribute('data-tab-button').replace(':id', tab.id),
        )

        clonedContent.innerHTML = tab.content
        clonedContent.setAttribute('data-template-tab', 'data-template-tab')
        clonedContent.setAttribute(
            'data-tab-content',
            clonedContent
                .getAttribute('data-tab-content')
                .replace(':for', tab.id),
        )

        buttonTemplate.childNodes.forEach(node => {
            header.appendChild(node.cloneNode(true))
        })

        contentTemplate.childNodes.forEach(node => {
            container.appendChild(node.cloneNode(true))
        })

        const buttons = container.querySelectorAll(Tab.buttonSelector)
        const lastButton = buttons[buttons.length - 1]

        Tab.listenButton(lastButton)

        return {
            button: lastButton,
            content: Tab.content(container, tab.id),
        }
    },
}

document.querySelectorAll(Tab.containerSelector).forEach(container => {
    Tab.listenContainer(container)
})

window.Feadmin.Tab = Tab
