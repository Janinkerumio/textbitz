import '../../bootstrap.js'

const api = window.axios
const serverApiUrl = import.meta.env.VITE_SERVER_URL || ''

export const fetchHistoryDashboard = async () => {
    const response = await api.get(route('api.dashboard.history'))

    return response.data
}

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

export const fetchTemplates = async (params) => {
    const response = await api.get(route('api.templates'), {
        params: params
    })

    return response.data
}

export const fetchOneTemplate = async (id) => {
    const response = await api.get(route('api.templates.show', id))

    return response.data
}

export const fetchHistory = async (params) => {
    const response = await api.get(route('api.history'), {
        params: params
    })

    return response.data
}

export const fetchRecipientsByHistory = async (id, params = null) => {
    const response = await api.get(route('api.recipients.by-history', id), {
        params: params
    })

    return response.data
}

export const preLoadTemplates = async () => {
    const response = await api.get('/api/templates/preload')

    return response.data
}