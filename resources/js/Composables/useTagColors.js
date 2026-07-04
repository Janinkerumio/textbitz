const colors = [
    'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
    'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
    'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300',
    'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300',
]

export function randomColor() {
    return colors[Math.floor(Math.random() * colors.length)]
}