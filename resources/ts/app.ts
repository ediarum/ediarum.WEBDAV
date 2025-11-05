import { createApp } from 'vue';
import App from './App.vue';
import '../css/app.css'
import axios from "axios";


axios.defaults.baseURL = import.meta.env.VITE_APP_URL || 'http://localhost:8000';

const el = document.getElementById('app')
const projectId = Number(el?.dataset.projectId)
const folders =JSON.parse(el?.dataset.folders || '[]')

createApp(App, {projectId, folders}).mount('#app');
