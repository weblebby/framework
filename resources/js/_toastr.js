const Toastr = {
    toastr: null,

    init() {
        this.toastr = document.createElement('div')
        this.toastr.classList.add('toastr')

        document.body.append(this.toastr)

        this.toastr.addEventListener('click', e => {
            const target = e.target

            if (target.classList.contains('toastr__item')) {
                this.remove(target)
            }
        })
    },

    add(message) {
        const toastrItem = document.createElement('div')
        toastrItem.classList.add('toastr__item')
        toastrItem.innerText = message

        this.toastr.appendChild(toastrItem)

        setTimeout(() => {
            toastrItem.classList.add('toastr__item--born')
        }, 10)

        setTimeout(() => {
            this.remove(toastrItem)
        }, 3000)
    },

    remove(toastrItem) {
        toastrItem.classList.remove('toastr__item--born')
        setTimeout(() => toastrItem.remove(), 300)
    },
}

Toastr.init()

window.Weblebby.Toastr = Toastr

export default Toastr
