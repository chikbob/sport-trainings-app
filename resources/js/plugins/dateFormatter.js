import { formatDate } from './formatters/date'
import { formatTime } from './formatters/time'
import { formatDateTime } from './formatters/dateTime'
import { detectLocale } from './formatters/locale'

export default {
    install(app) {
        const locale = detectLocale()

        app.config.globalProperties.$formatDate = (d) => formatDate(d, locale)
        app.config.globalProperties.$formatTime = (t) => formatTime(t)
        app.config.globalProperties.$formatDateTime = (dt) => formatDateTime(dt, locale)
    }
}
