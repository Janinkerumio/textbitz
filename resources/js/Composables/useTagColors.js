const palettes = [
    {
        light: 'bg-red-100 text-red-700 dark:bg-red-200/80 dark:text-red-800',
        bold: 'bg-red-500 text-white dark:bg-red-600',
        border: 'border-red-500',
    },
    {
        light: 'bg-blue-100 text-blue-700 dark:bg-blue-200/80 dark:text-blue-800',
        bold: 'bg-blue-500 text-white dark:bg-blue-600',
        border: 'border-blue-500',
    },
    {
        light: 'bg-green-100 text-green-700 dark:bg-green-200/80 dark:text-green-800',
        bold: 'bg-green-500 text-white dark:bg-green-600',
        border: 'border-green-500',
    },
    {
        light: 'bg-yellow-100 text-yellow-700 dark:bg-yellow-200/80 dark:text-orange-800',
        bold: 'bg-yellow-500 text-white dark:bg-yellow-600',
        border: 'border-yellow-500',
    },
    {
        light: 'bg-purple-100 text-purple-700 dark:bg-purple-200/80 dark:text-purple-800',
        bold: 'bg-purple-500 text-white dark:bg-purple-600',
        border: 'border-purple-500',
    },
]

function hashString(str) {
    let hash = 0

    for (const c of str) {
        hash += c.charCodeAt(0)
    }

    return hash
}

function buildClasses(palette, options = {}) {
    const {
        bordered = false,
        bold = false,
    } = options

    return [
        bold ? palette.bold : palette.light,
        bordered && palette.border,
    ]
        .filter(Boolean)
        .join(' ')
}

export function randomColor(options = {}) {
    const palette = palettes[Math.floor(Math.random() * palettes.length)]
    return buildClasses(palette, options)
}

export function colorForTag(tag, options = {}) {
    const palette = palettes[hashString(tag) % palettes.length]
    return buildClasses(palette, options)
}