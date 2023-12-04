export const inputNameToDotted = name => {
    return name.replace(/\[\]/g, '').replace(/\[/g, '.').replace(/\]/g, '')
}

export const inputNameToId = name => {
    return inputNameToDotted(name).replace(/\./g, '_')
}

export const convertDottedToInputName = expression => {
    const parts = expression.split('.')
    let result = ''

    parts.forEach((part, index) => {
        if (index === 0) {
            result += part
            return
        }

        if (part === '*') {
            result += '[]'
            return
        }

        result += `[${part}]`
    })

    return result
}
