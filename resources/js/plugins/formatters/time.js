export function formatTime(timeString) {
    if (!timeString) return ''

    const parts = timeString.split(':')
    return `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`
}
