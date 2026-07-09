import { ref } from 'vue'

/**
 * Composable for paginated data fetching with infinite-scroll semantics.
 * Tracks the current page, accumulated items, and whether more pages remain,
 * and appends or replaces the item list depending on whether a reset was requested.
 *
 * @param {(params: Record<string, any>) => Promise<{
 *   data: any[],
 *   current_page: number,
 *   last_page: number
 * }>} dataFetcherFn - Async function that fetches a page of data. Called with
 *   the given params merged with `{ page }`, and must resolve to an object
 *   containing `data` (the array of items), `current_page`, and `last_page`
 *   (e.g. a Laravel paginator response).
 *
 * @returns {{
 *   items: import('vue').Ref<any[]>,
 *   page: import('vue').Ref<number>,
 *   hasMore: import('vue').Ref<boolean>,
 *   loading: import('vue').Ref<boolean>,
 *   initiatePaginatedData: (params?: Record<string, any>, reset?: boolean) => Promise<any>,
 *   resetDisplayedData: () => void,
 * }}
 */
export function useInfinitePagination(dataFetcherFn) {
    const items = ref([])
    const page = ref(1)
    const hasMore = ref(true)
    const loading = ref(false)

    /**
     * Fetches the next page of data (or the first page, if `reset` is true)
     * and merges it into `items`. No-ops if a fetch is already in progress,
     * or if there are no more pages left and `reset` is not set.
     *
     * @param {Record<string, any>} [params={}] - Extra query params to send
     *   with the request (e.g. search term, filters). Merged with `{ page }`.
     * @param {boolean} [reset=false] - If true, replaces `items` with the
     *   fetched page instead of appending, and bypasses the `hasMore` guard.
     * @returns {Promise<any>} The raw paginator response from `dataFetcherFn`,
     *   or `undefined` if the call was skipped or an error was caught.
     */
    const initiatePaginatedData = async (params = {}, reset = false) => {
        if (loading.value) return
        if (!reset && !hasMore.value) return

        loading.value = true

        try {
            const data = await dataFetcherFn({
                ...params,
                page: page.value,
            })

            if (reset) {
                items.value = data.data
            } else {
                items.value.push(...data.data)
            }

            page.value = data.current_page + 1
            hasMore.value =
                data.current_page < data.last_page

            return data
        } catch (error) {
            console.error('Error fetching data: '+error)
        } finally {
            loading.value = false
        }
    }

    /**
     * Clears the current items and resets pagination state back to the
     * first page. Does not trigger a new fetch — call
     * `initiatePaginatedData(params, true)` afterward (or with `reset: true`)
     * to reload.
     *
     * @returns {void}
     */
    const resetDisplayedData = () => {
        items.value = []
        page.value = 1
        hasMore.value = true
    }

    return {
        items,
        page,
        hasMore,
        loading,
        initiatePaginatedData,
        resetDisplayedData,
    }
}

/**
 * Composable that detects when a scrollable element is near its bottom
 * and invokes a callback to load more data. Intended to be wired to a
 * container's native `scroll` event.
 *
 * @param {() => void} loadMoreCallback - Called when the scroll position
 *   is within `distance` pixels of the bottom of the element.
 * @param {{ distance?: number }} [options={}] - Configuration options.
 * @param {number} [options.distance=100] - How many pixels from the bottom
 *   of the scrollable content to trigger `loadMoreCallback`.
 *
 * @returns {{
 *   onScroll: (el: HTMLElement | null) => void,
 * }}
 */
export function useScrollLoader(loadMoreCallback, options = {}) {
    const distance = options.distance ?? 100

    const onScroll = (el) => {
        if (!el) return

        if (
            el.scrollTop + el.clientHeight >=
            el.scrollHeight - distance
        ) {
            loadMoreCallback()
        }
    }

    return {
        onScroll,
    }
}