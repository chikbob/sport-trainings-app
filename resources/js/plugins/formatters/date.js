export function formatDate(dateString, locale = 'uk') {
    if (!dateString) return ''

    const monthsUk = [
        'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня',
        'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'
    ]
    const monthsRu = [
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
    ]

    const months = locale === 'ua' ? monthsRu : monthsUk

    const date = new Date(dateString)
    const day = date.getDate().toString().padStart(2, '0')
    const month = months[date.getMonth()]
    const year = date.getFullYear()

    const yearSuffix = locale === 'ua' ? 'года' : 'року'

    return `${day} ${month} ${year} ${yearSuffix}`
}
