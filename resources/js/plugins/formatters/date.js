export function formatDate(dateString, locale = 'uk') {
    if (!dateString) return ''

    const date = new Date(dateString)
    const localeMap = {
        ru: 'ru-RU',
        uk: 'uk-UA',
        en: 'en-US',
    }

    return new Intl.DateTimeFormat(localeMap[locale] || 'en-US', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    }).format(date)
}
