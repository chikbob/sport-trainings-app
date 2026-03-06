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

    const labels = {
        uk: { today: 'сьогодні', tomorrow: 'завтра', yesterday: 'вчора', at: 'о' },
        ru: { today: 'сегодня', tomorrow: 'завтра', yesterday: 'вчера', at: 'в' },
        en: { today: 'today', tomorrow: 'tomorrow', yesterday: 'yesterday', at: 'at' },
    }

    const l = labels[locale] || labels.en

    if (isSameDay(date, today)) return `${l.today} ${l.at} ${time}`
    if (isSameDay(date, tomorrow)) return `${l.tomorrow} ${l.at} ${time}`
    if (isSameDay(date, yesterday)) return `${l.yesterday} ${l.at} ${time}`

    return `${formatDate(datePart, locale)} ${l.at} ${time}`
}
