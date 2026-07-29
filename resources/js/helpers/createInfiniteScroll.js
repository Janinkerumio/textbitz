import { watch, onMounted } from 'vue'
import { useInfinitePagination, useScrollLoader } from '@/Composables/useInfiniteScroll'

export function createInfiniteScroll(dataFetcherFn, filtersRef, scrollOptions = {}) {
    const {
        items,
        loading,
        initiatePaginatedData,
        resetDisplayedData
    } = useInfinitePagination(dataFetcherFn)

    const load = (reset = false) => {
        return initiatePaginatedData(filtersRef.value, reset)
    }

    const { onScroll } = useScrollLoader(() => load(), scrollOptions)

    watch(
        filtersRef,
        () => {
            resetDisplayedData()
            load(true)
        },
        { deep: true }
    )

    onMounted(() => {
        load()
    })

    return {
        items,
        loading,
        onScroll,
        reload: () => load(true),
    }
}