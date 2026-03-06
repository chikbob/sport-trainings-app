import { ref, watch } from "vue"
import ru from "./ru"
import uk from "./uk"
import en from "./en"

const messages = { ru, uk, en }

export const currentLang = ref(localStorage.getItem("lang") || "ru")

const syncLang = (lang) => {
    if (typeof document !== "undefined") {
        document.cookie = `lang=${lang}; path=/`
    }
}

syncLang(currentLang.value)

export function useI18n() {
    const t = (key) => {
        const parts = key.split(".")
        let value = messages[currentLang.value]

        for (const p of parts) {
            value = value?.[p]
            if (!value) return key
        }
        return value
    }

    const setLang = (lang) => {
        if (!messages[lang]) return
        currentLang.value = lang
        localStorage.setItem("lang", lang)
        syncLang(lang)
    }

    watch(currentLang, (lang) => {
        if (!messages[lang]) return
        localStorage.setItem("lang", lang)
        syncLang(lang)
    })

    return {
        t,
        setLang,
        currentLang,
    }
}
