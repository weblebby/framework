import api from './_api.js'
import Tagify from './_tagify.js'

import $ from 'jquery'
import './_jquery.nestable.js'

const drawer = document.getElementById('drawer-create-menu-item')
const dd = document.querySelector('.dd')
const parentIdInput = drawer.querySelector('input[name="parent_id"]')
const form = drawer.querySelector('form')
const defaultFormAction = form.action

const Navigation = {
    init: id => {
        $(dd).nestable({
            callback: async () => {
                const response = await api(`/navigations/${id}/sort`, {
                    method: 'POST',
                    body: JSON.stringify({
                        items: $('.dd').nestable('toArray'),
                    }),
                })

                const data = await response.json()

                if (response.ok) {
                    Feadmin.Toastr.add(data.message)
                }
            },
            handleClass: 'navigation-item',
            expandBtnHTML: `<button data-action="expand" class="dd-expand">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" class="w-5 h-5">
                    <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                </svg>
            </button>`,
            collapseBtnHTML: `<button data-action="collapse" class="dd-collapse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" class="w-5 h-5">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                </svg>
            </button>`,
        })
    },

    handleSmartMenu: () => {
        const { isSmartMenu } = Navigation.getFormElements()
        const custom = document.querySelector('[data-custom-item]')
        const smart = document.querySelector('[data-smart-item]')

        if (isSmartMenu.checked) {
            custom.classList.add('fd-hidden')
            smart.classList.remove('fd-hidden')
        } else {
            custom.classList.remove('fd-hidden')
            smart.classList.add('fd-hidden')
        }
    },

    onSmartTypeChange: async () => {
        const { smartType, smartCondition } = Navigation.getFormElements()

        if (!smartType.value) {
            document
                .querySelector('[data-form-group="smart_condition"]')
                .classList.add('fd-hidden')
            document
                .querySelector('[data-form-group="smart_filters"]')
                .classList.add('fd-hidden')
            document
                .querySelector('[data-form-group="smart_limit"]')
                .classList.add('fd-hidden')
            return
        }

        const response = await api(`/post-models?model=${smartType.value}`)

        const taxonomies = await response.json()

        smartCondition.innerHTML = `<option value="">Filtre yok</option>`

        taxonomies.forEach(taxonomy => {
            smartCondition.innerHTML += `<option value="${taxonomy.name}">${taxonomy.singular_name}</option>`
        })

        document
            .querySelector('[data-form-group="smart_limit"]')
            .classList.remove('fd-hidden')

        document
            .querySelector('[data-form-group="smart_condition"]')
            .classList.remove('fd-hidden')

        void Navigation.onSmartConditionChange()
    },

    onSmartConditionChange: async (filterValue = '') => {
        const elements = Navigation.getFormElements()
        const conditionValue = elements.smartCondition.value
        const smartFiltersGroup = document.querySelector(
            '[data-form-group="smart_filters"]',
        )

        elements.smartFilters.value = filterValue

        if (!conditionValue) {
            smartFiltersGroup.classList.add('fd-hidden')
            return
        }

        smartFiltersGroup.querySelector('label').textContent =
            elements.smartCondition.querySelector(
                `option[value="${conditionValue}"]`,
            ).textContent

        smartFiltersGroup.classList.remove('fd-hidden')

        if (elements.smartFilters?.__tagify) {
            elements.smartFilters.__tagify.destroy()
        }

        return Tagify.init(elements.smartFilters, {
            name: 'smart_filters[]',
            source: `/taxonomies/${conditionValue}`,
            map: {
                value: 'taxonomy_id',
                label: 'title',
            },
        })
    },

    handleLinkable: () => {
        const { linkable } = Navigation.getFormElements()
        const link = document.querySelector('[data-form-group="link"]')

        linkable.value === ''
            ? link.classList.remove('fd-hidden')
            : link.classList.add('fd-hidden')
    },

    getFormElements: () => {
        return {
            title: drawer.querySelector('input[name="title"]'),
            isSmartMenu: drawer.querySelector('input[name="is_smart_menu"]'),
            smartType: drawer.querySelector('select[name="smart_type"]'),
            smartCondition: drawer.querySelector(
                'select[name="smart_condition"]',
            ),
            smartLimit: drawer.querySelector('input[name="smart_limit"]'),
            smartFilters: drawer.querySelector('input[name="smart_filters"]'),
            smartViewAll: drawer.querySelector('input[name="smart_view_all"]'),
            link: drawer.querySelector('input[name="link"]'),
            isActive: drawer.querySelector('input[name="is_active"]'),
            openInNewTab: drawer.querySelector('input[name="open_in_new_tab"]'),
            linkable: drawer.querySelector('select[name="linkable"]'),
        }
    },
}

drawer.addEventListener(
    'drawer.open',
    async ({ detail: { related, item, isEdit, hasError } }) => {
        if (related) {
            parentIdInput.value = related.dataset.parentId || ''
        }

        form.action = isEdit
            ? form.dataset.editAction.replace(':id', item.id)
            : defaultFormAction

        form.querySelector('input[name="_method"]').value = isEdit
            ? 'PUT'
            : 'POST'

        const elements = Navigation.getFormElements()

        if (!hasError) {
            elements.title.value = item?.title || ''
            elements.isSmartMenu.checked = item?.type === 3
            elements.smartType.value = item?.smart_type || ''
            elements.smartLimit.value = item?.smart_limit || ''
            elements.smartViewAll.checked = item?.smart_view_all
            elements.link.value = item?.link || ''
            elements.isActive.checked = item?.is_active
            elements.openInNewTab.checked = item?.open_in_new_tab

            if (!isEdit) {
                elements.isActive.checked = true
            }

            if (item?.type === 1) {
                elements.linkable.value = 'homepage'
            } else if (item?.type === 2) {
                elements.linkable.value = JSON.stringify({
                    linkable_id: item?.linkable_id,
                    linkable_type: item?.linkable_type,
                })
            } else {
                elements.linkable.value = ''
            }
        }

        if (item?.smart_filters) {
            await Navigation.onSmartTypeChange()

            const option = elements.smartCondition.querySelector(
                `option[value="${item.smart_condition}"]`,
            )

            if (option) {
                option.selected = true
            }

            if (typeof item.smart_filters !== 'string') {
                item.smart_filters = JSON.stringify(item.smart_filters)
            }

            void Navigation.onSmartConditionChange(item.smart_filters)
        } else {
            elements.smartFilters.value = ''
            void Navigation.onSmartTypeChange()
            void Navigation.onSmartConditionChange()
        }

        Navigation.handleSmartMenu()
        Navigation.handleLinkable()
    },
)
drawer.addEventListener('drawer.hide', () => (parentIdInput.value = ''))

const elements = Navigation.getFormElements()

elements.isSmartMenu.addEventListener('change', Navigation.handleSmartMenu)
elements.linkable.addEventListener('change', Navigation.handleLinkable)
elements.smartType.addEventListener('change', Navigation.onSmartTypeChange)
elements.smartCondition.addEventListener(
    'change',
    () => void Navigation.onSmartConditionChange(),
)

dd.querySelectorAll('[data-toggle="edit"]').forEach(button => {
    button.addEventListener('click', () => {
        const item = JSON.parse(button.dataset.item)

        Feadmin.Drawer.open(drawer, {
            item,
            isEdit: true,
        })
    })
})

window.Feadmin.Navigation = Navigation
