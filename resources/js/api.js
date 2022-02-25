export default function api(url, options = {}) {
    options.headers = options.headers || {}

    options.headers['Content-Type'] = 'application/json'
    options.headers['Accept'] = 'application/json'
    options.headers['X-CSRF-TOKEN'] = document.querySelector(
        'meta[name="csrf-token"]'
    ).content

    return fetch(url, options)
}
