// Bootstrap JS
import 'bootstrap';

// Alpine.js with Collapse Plugin
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();
