import { watch, onMounted } from 'vue'
import { useInfinitePagination, useScrollLoader } from '@/Composables/useInfiniteScroll'

/**
 * Composable that wires together `useInfinitePagination` and `useScrollLoader`
 * for the common case of an infinite-scrolling list driven by a reactive
 * filters object. Automatically loads the first page on mount, reloads from
 * page 1 whenever `filtersRef` changes, and exposes a scroll handler to load
 * subsequent pages.
 *
 * @param {(params: Record<string, any>) => Promise<{
 *   data: any[],
 *   current_page: number,
 *   last_page: number
 * }>} dataFetcherFn - Async function that fetches a page of data, given
 *   the current filters merged with `{ page }` (e.g. a Laravel paginator
 *   response).
 * @param {import('vue').Ref<Record<string, any>> | import('vue').ComputedRef<Record<string, any>>} filtersRef
 *   - Reactive ref/computed holding the filter params to send with each
 *   request (e.g. search term, tags). Watched deeply; any change triggers
 *   a reset and reload from page 1.
 * @param {{ distance?: number }} [scrollOptions={}] - Options passed through
 *   to `useScrollLoader`.
 * @param {number} [scrollOptions.distance=100] - How many pixels from the
 *   bottom of the scrollable content to trigger loading the next page.
 *
 * @returns {{
 *   items: import('vue').Ref<any[]>,
 *   loading: import('vue').Ref<boolean>,
 *   onScroll: (el: HTMLElement | null) => void,
 *   reload: () => Promise<any>,
 * }}
 */
export function createInfiniteScroll(dataFetcherFn, filtersRef, scrollOptions = {}) {
    const {
        items,
        loading,
        initiatePaginatedData,
        resetDisplayedData
    } = useInfinitePagination(dataFetcherFn)

    /**
     * Loads a page of data using the current value of `filtersRef`.
     *
     * @param {boolean} [reset=false] - If true, resets to page 1 and
     *   replaces `items` instead of appending.
     * @returns {Promise<any>}
     */
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