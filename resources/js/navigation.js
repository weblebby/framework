import api from './_api.js'

window.$ = window.jQuery = require('jquery')
require('nestable2')

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

                if (data.success === true) {
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

    handleIsSmartMenu: () => {
        const manuel = document.querySelector('[data-manuel-item]')
        const smart = document.querySelector('[data-smart-item]')

        if (item_is_smart_menu.checked) {
            manuel.classList.add('fd-hidden')
            smart.classList.remove('fd-hidden')
        } else {
            manuel.classList.remove('fd-hidden')
            smart.classList.add('fd-hidden')
        }
    },

    handleLinkable: () => {
        const link = document.querySelector('[data-form-group="link"]')

        item_linkable.value === ''
            ? link.classList.remove('fd-hidden')
            : link.classList.add('fd-hidden')
    },
}

drawer.addEventListener(
    'drawer.open',
    ({ detail: { related, item, isEdit, isError } }) => {
        if (related) {
            parentIdInput.value = related.dataset.parentId || ''
        }

        form.action = isEdit
            ? form.dataset.editAction.replace(':id', item.id)
            : defaultFormAction

        form.querySelector('input[name="_method"]').value = isEdit
            ? 'PUT'
            : 'POST'

        const title = drawer.querySelector('input[name="title"]')
        const isSmartMenu = drawer.querySelector('input[name="is_smart_menu"]')
        const smartType = drawer.querySelector('select[name="smart_type"]')
        const smartLimit = drawer.querySelector('input[name="smart_limit"]')
        const link = drawer.querySelector('input[name="link"]')
        const isActive = drawer.querySelector('input[name="is_active"]')
        const openInNewTab = drawer.querySelector(
            'input[name="open_in_new_tab"]',
        )
        const linkable = drawer.querySelector('select[name="linkable"]')

        if (!isError) {
            title.value = item?.title || ''
            isSmartMenu.checked = item?.type === 3
            smartType.value = item?.smart_type || ''
            smartLimit.value = item?.smart_limit || ''
            link.value = item?.link || ''
            isActive.checked = item?.is_active
            openInNewTab.checked = item?.open_in_new_tab

            if (!isEdit) {
                isActive.checked = true
            }

            if (item?.type === 1) {
                item_linkable.value = 'homepage'
            } else if (item?.type === 2) {
                item_linkable.value = JSON.stringify({
                    linkable_id: item?.linkable_id,
                    linkable_type: item?.linkable_type,
                })
            } else {
                item_linkable.value = ''
            }
        }

        Navigation.handleIsSmartMenu()
        Navigation.handleLinkable()
    },
)
drawer.addEventListener('drawer.hide', () => (parentIdInput.value = ''))

item_is_smart_menu.addEventListener('change', Navigation.handleIsSmartMenu)
item_linkable.addEventListener('change', Navigation.handleLinkable)

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
