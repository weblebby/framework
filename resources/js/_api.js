export default async function api(path, options = {}) {
    options.headers = options.headers || {}

    options.headers['Content-Type'] = 'application/json'
    options.headers['Accept'] = 'application/json'
    options.headers['X-CSRF-TOKEN'] = document.querySelector(
        'meta[name="csrf-token"]',
    ).content

    options.credentials = 'include'

    let url

    if (path.startsWith('http')) {
        url = path
    } else {
        path = path.replace(/^\/+/g, '')
        url = `${window.Feadmin.API.baseUrl}/${path}`
    }

    const response = await fetch(url, options)

    if (!response.ok) {
        const error = new Error()
        error.response = response
        throw error
    }

    return response.json()
}
