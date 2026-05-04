import { ref, watch } from "vue"
import ru from "./ru"
import uk from "./uk"
import en from "./en"

const messages = { ru, uk, en }

const normalizeLang = (lang) => {
    if (lang === "ru") return "uk"
    return messages[lang] ? lang : "uk"
}

export const currentLang = ref(normalizeLang(localStorage.getItem("lang")))

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
        const normalizedLang = normalizeLang(lang)

        if (!messages[normalizedLang]) return

        currentLang.value = normalizedLang
        localStorage.setItem("lang", normalizedLang)
        syncLang(normalizedLang)
    }

    watch(currentLang, (lang) => {
        const normalizedLang = normalizeLang(lang)

        if (!messages[normalizedLang]) return

        if (currentLang.value !== normalizedLang) {
            currentLang.value = normalizedLang
            return
        }

        localStorage.setItem("lang", normalizedLang)
        syncLang(normalizedLang)
    })

    return {
        t,
        setLang,
        currentLang,
    }
}
