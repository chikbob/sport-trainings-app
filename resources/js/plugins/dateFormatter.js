import { formatDate } from './formatters/date'
import { formatTime } from './formatters/time'
import { formatDateTime } from './formatters/dateTime'
import { currentLang } from '../i18n/useI18n'

export default {
    install(app) {
        const getLocale = () => currentLang.value || 'ru'

        app.config.globalProperties.$formatDate = (d) => formatDate(d, getLocale())
        app.config.globalProperties.$formatTime = (t) => formatTime(t)
        app.config.globalProperties.$formatDateTime = (dt) => formatDateTime(dt, getLocale())
    }
}
