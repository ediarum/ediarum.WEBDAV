<template>
        <Terminal :projectId="projectId" :folders="folders" />
</template>

<script lang="ts">
import {defineComponent} from 'vue';
import Terminal from './components/Terminal.vue';

export default defineComponent({
    name: 'App',
    props: {
        projectId: {
            type: Number,
            required: true
        },
        folders: {
            type: Array as () => string[],
            required: true,
            default: []
        }
    },
    components: {
        Terminal
    }
});
</script>
