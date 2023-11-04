export default function api(path, options = {}) {
    options.headers = options.headers || {}

    options.headers['Content-Type'] = 'application/json'
    options.headers['Accept'] = 'application/json'
    options.headers['X-CSRF-TOKEN'] = document.querySelector(
        'meta[name="csrf-token"]',
    ).content

    path = path.replace(/\/$/, '')

    return fetch(`${window.Feadmin.API.baseUrl}/${path}`, options)
}
