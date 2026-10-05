// Bootstrap CSS is imported via resources/css/app.css (avoid double-loading that overrides custom styles)
// Bootstrap JS
import 'bootstrap';

// Alpine.js with Collapse Plugin
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

window.Alpine = Alpine;
Alpine.plugin(collapse);
Alpine.start();
