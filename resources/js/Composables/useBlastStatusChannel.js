import { computed, watchEffect } from 'vue'
import { subscribeToBlast } from '@/services/useBlastChannelManager'

export function useBlastStatusChannel(blastUuId, { recipients } = {}) {
    const blastState = subscribeToBlast(blastUuId)

    if (recipients) {
        watchEffect(() => {
            for (const r of recipients.value) {
                const patch = blastState.recipients[r.id]
                if (patch) Object.assign(r, patch)
            }
        })
    }

    return {
        status: computed(() => blastState.status),
        lastEventAt: computed(() => blastState.lastEventAt),
    }
}