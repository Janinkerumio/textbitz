/**
 * Provides prepend/update/remove operations over an existing reactive list ref.
 * Does not create its own state — operates on the ref you pass in.
 * 
 * @param {import('vue').Ref<Array>} items - A reactive ref containing the list.
 * @returns {{
 *      prependData: (item: object, updateOnly?: boolean) => void,
 *      removeData: (id: number|string) => void
 * }}
 */
export function useDataList(items) {
    const prependData = (item, updateOnly = false, matchKey = 'id') => {
        if(updateOnly) {
            const index = items.value.findIndex(data => data[matchKey] === item[matchKey])
            if (index !== -1) {
                items.value[index] = { ...items.value[index], ...item }
            }
        } else {
            items.value.unshift(item)
        }
    }

    const removeData = (id) => {
        const index = items.value.findIndex((data) => data.id === id)
        if (index !== -1) items.value.splice(index, 1)
    }

    return { prependData, removeData }
}