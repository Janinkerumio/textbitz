import '../../bootstrap.js'

const api = window.axios
const serverApiUrl = import.meta.env.SERVER_URL || ''

export const fetchContact = async (params) => {
    const response = await api.get(route('api.contacts'), {
        params: params
    })

    return response.data
}