import { formatDate } from './date'
import { formatTime } from './time'

export function formatDateTime(dateTimeString, locale = 'uk') {
    if (!dateTimeString) return ''

    const [datePart, timePart] = dateTimeString.split(' ')
    const date = new Date(datePart)
    const today = new Date()

    const isSameDay = (a, b) =>
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate()

    const tomorrow = new Date()
    tomorrow.setDate(today.getDate() + 1)

    const yesterday = new Date()
    yesterday.setDate(today.getDate() - 1)

    const time = formatTime(timePart)

    // ------- УКРАИНСКИЙ -------
    if (locale === 'uk') {
        if (isSameDay(date, today)) return `сьогодні о ${time}`
        if (isSameDay(date, tomorrow)) return `завтра о ${time}`
        if (isSameDay(date, yesterday)) return `вчора о ${time}`

        return `${formatDate(datePart, locale)} о ${time}`
    }

    // ------- РУССКИЙ -------
    if (locale === 'ru') {
        if (isSameDay(date, today)) return `сегодня в ${time}`
        if (isSameDay(date, tomorrow)) return `завтра в ${time}`
        if (isSameDay(date, yesterday)) return `вчера в ${time}`

        return `${formatDate(datePart, locale)} в ${time}`
    }
}
