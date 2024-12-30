<template>
    <label for="folder-select" class="">Choose a folder:</label>
    <select name="folder" id="folder-select" class="mx-2" v-model="syncFolder">
        <option value="">(All contents)</option>
        <option v-for="(folder, index) in folders" :key="folder" :value="folder">{{ folder }}</option>
    </select>


    <button class="my-2 px-4 py-2 bg-ediarum-red border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
            @click="syncExistDB"
            :class="syncInProgress ? 'opacity-25 cursor-not-allowed' : ''"
    >
        <template v-if="syncInProgress">Syncing...</template>
        <template v-else>Start Sync</template>
    </button>
    <div
        ref="logContainer"
        @scroll="onScroll"
        class="h-150 w-200 bg-black text-white overflow-auto">
        <p class="text-left font-bold" v-for="(m, index) in messages" :key="index" :class="m.status? 'text-green-500':'text-red-500'">{{ m.content }}</p>
        <p class="text-left pl-2">{{ spinnerChar}}</p>
    </div>
</template>

<script setup lang="ts">
import {nextTick, onBeforeUnmount, onMounted, ref, watch} from "vue";
const baseURL = import.meta.env.VITE_APP_URL || 'http://localhost:8000'
const props = defineProps<{
    projectId: Number;
    folders: String[];
}>();

const syncFolder = ref('');
const messages = ref<string[]>([]);
const syncInProgress = ref(false);

let eventSource: EventSource;
let eventSourceClosedByClient = false;

const spinnerChar = ref('')
const spinnerChars = ['|', '/', '-', '\\']


let spinnerIndex
let lastMessageTime
const SPINNER_DELAY = 200
let SPINNER_ACTIVE = false

const logContainer = ref(null)
const autoScroll = ref(true)


function onScroll() {
    const el = logContainer.value
    if (!el) return

    const nearBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 200
    autoScroll.value = nearBottom
}

watch([() => messages.value.length, spinnerChar], () => {
    const el = logContainer.value
    if (!el || !autoScroll.value) return
    el.scrollTop = el.scrollHeight
})

function scrollToBottom() {
    nextTick(() => {
        const el = logContainer.value
        if (!el) return
        el.scrollTop = el.scrollHeight
    })
}

const spinnerInterval = setInterval(() => {
    const now = Date.now()
    if (SPINNER_ACTIVE && now - lastMessageTime > SPINNER_DELAY) {
        // Only show spinner if we have waited longer than SPINNER_DELAY
        spinnerChar.value = spinnerChars[spinnerIndex % spinnerChars.length]
        spinnerIndex++
    } else {
        spinnerChar.value = '' // hide spinner if message just arrived
    }
}, 100)
onBeforeUnmount(() => {
    if (eventSource) eventSource.close()
    clearInterval(spinnerInterval)
})

function initSpinner(){
    spinnerIndex = 0
    lastMessageTime = Date.now()
    SPINNER_ACTIVE = true

}

function syncExistDB(){
    if(syncInProgress.value) return;
    syncInProgress.value = true
    messages.value = [] // clear previous messages
    if (eventSource) {
        // Close any existing connection before starting a new one
        eventSource.close()
        eventSource = null
    }
    eventSourceClosedByClient = false
    eventSource = new EventSource(baseURL + `/project/${props.projectId}/push-to-existdb?folder=` + encodeURIComponent(syncFolder.value));
    console.log("EventSource created:", eventSource);

    initSpinner()
    eventSource.onmessage = function(event) {

        // console.log("New message from server:", event.data);
        if (event.data === '[DONE]') {
            SPINNER_ACTIVE = false
            spinnerChar.value = ''
            console.log('Sync complete');
            eventSourceClosedByClient = true
            eventSource.close(); // optional, close after finished
            messages.value.push({"status": true, "content": "Sync complete"});
            syncInProgress.value = false
            return;
        }
        messages.value.push({"status": true, "content": event.data});
        lastMessageTime = Date.now()
        spinnerChar.value = ''
    };

    eventSource.onerror = function(err) {
        if(eventSourceClosedByClient) return;
        SPINNER_ACTIVE = false
        console.error("EventSource failed:", err);
        messages.value.push({"status": false, "content": "ERROR: Event Source Failed:" + JSON.stringify(err)});
        eventSource.close();
        syncInProgress.value = false
        scrollToBottom()
    };
    eventSource.addEventListener("app-error", (event) => {
        const errorMessage = event.data || "Unknown server error";
        console.error("Server error:", errorMessage);
        messages.value.push({ status: false, content: `❌ ${errorMessage}` });
        syncInProgress.value = false;
        SPINNER_ACTIVE = false;
        eventSourceClosedByClient = true
        eventSource.close();
        scrollToBottom()
    });
    console.log("Syncing with ExistDB");
}




</script>
