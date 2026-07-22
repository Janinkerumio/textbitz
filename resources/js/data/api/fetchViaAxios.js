import '../../bootstrap.js'

const api = window.axios
const serverApiUrl = import.meta.env.SERVER_URL || ''

export const fetchContact = async (params) => {
    const response = await api.get(route('api.contacts'), {
        params: params
    })

    return response.data
}

export const fetchOneContact = async (id) => {
    const response = await api.get(route('api.contacts.show', id))

    return response.data
}

export const fetchTemplates = async (params) => { //might need query to exclude preloaded data
    const response = await api.get('/api/templates', {
        params: params
    })

    return response.data
}

export const fetchHistory = async (params) => {
    const response = await api.get('/api/history', {
        params: params
    })

    return response.data
}

export const preLoadTemplates = async () => {
    const response = await api.get('/api/templates/preload')

    return response.data
}