import { reactive } from 'vue'
import crossPlatformToast from '@/helpers/crossPlatformToast'
import { usePage } from '@inertiajs/vue3'

const toast = crossPlatformToast()
const page = usePage()

const state = reactive({})
let userChannel = null

function ensureState(blastUuid) {
    if (!state[blastUuid]) {
        state[blastUuid] = {
            status: null,
            recipients: {},
            lastEventAt: null,
        }
    }
    return state[blastUuid]
}

export function subscribeToUserChannel() {
    if (userChannel) return

    userChannel = window.Echo.private(`user.${page.props?.auth.user.remote_id}`)

    userChannel
        .listen('.BlastDeliveryStatusUpdate', (e) => {
            const blastState = ensureState(e.uuid)
            blastState.status = e.status
            blastState.lastEventAt = new Date()

            window.axios
                .patch(route('api.history.update', e.uuid), { status: e.status })
                .then(() => toast.success('A blast has been updated. Check the history'))
                .catch(() => {})
        })
        .listen('.BlastRecipientDeliveryStatusUpdate', (e) => {
            const blastState = ensureState(e.uuid)
            blastState.lastEventAt = new Date()

            blastState.recipients[e.recipient.id] = {
                ...(blastState.recipients[e.recipient.id] || {}),
                ...e.recipient,
            }

            window.axios
                .patch(route('api.recipients.update', e.recipient.id), {
                    status: e.recipient.status,
                    mobile_num: e.recipient.mobile_num,
                    sent_at: e.recipient.sent_at,
                    error_message: e.recipient.error_message,
                })
                .catch(() => {})
        })

    userChannel.error((error) => {
        console.error('user channel subscription failed', error)
    })
}

export function getBlastState(blastUuid) {
    return ensureState(blastUuid)
}