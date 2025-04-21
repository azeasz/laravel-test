import './bootstrap';
import { createApp } from 'vue';
import React from 'react';
import ReactDOM from 'react-dom';
import MapComponent from './components/MapComponent';

// Inisialisasi aplikasi Vue
const app = createApp({});
import ExampleComponent from './components/ExampleComponent.vue';
app.component('example-component', ExampleComponent);
app.mount('#app');

// Inisialisasi aplikasi React
if (document.getElementById('map-container')) {
    ReactDOM.render(<MapComponent />, document.getElementById('map-container'));
}
