import { ref, watch } from 'vue' 
import settings from '@/data/settings'

export const darkMode = ref(localStorage.getItem('darkMode') ?? settings.preferences.darkMode)

const applyTheme = (mode) => {
    const html = document.documentElement

    html.classList.remove('dark')

    if (mode === 'On') {
        html.classList.add('dark')
    } else if (mode === 'System') {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            html.classList.add('dark')
        }
    }
}

watch(
    darkMode,
    (mode) => {
        localStorage.setItem('darkMode', mode)
        applyTheme(mode)
    },
    { immediate: true}
)

export { applyTheme }