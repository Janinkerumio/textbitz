const colors = ['red', 'blue', 'green', 'yellow', 'purple'];

export default colors.flatMap(color => [
    `bg-${color}-100`,
    `bg-${color}-500`,
    `text-${color}-700`,
    `text-${color}-500`,
    `dark:bg-${color}-900/40`,
    `dark:text-${color}-300`,
]);