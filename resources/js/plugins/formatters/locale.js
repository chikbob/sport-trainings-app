export function detectLocale() {
    const lang = navigator.language || navigator.userLanguage

    if (lang.startsWith('ru')) return 'ru'
    return 'uk'
}
