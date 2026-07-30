<script setup>
import { fetchRecipientsByHistory } from '@/data/api/fetchViaAxios';
import { createInfiniteScroll } from '@/helpers/createInfiniteScroll';

const props = defineProps({
    historyId: {
        type: String
    }
})
</script>