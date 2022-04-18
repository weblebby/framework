export function t(key, group = 'default', params = {}) {
    const finded = window.Feadmin.Translation.list.find(
        item => item.group === group && item.key === key
    )

    let value = finded?.value || key

    Object.keys(params).forEach(key => {
        value = value.replace(`:${key}`, params[key])
    })

    return value
}
