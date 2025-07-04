import './bootstrap';

// Tambahkan ini untuk jQuery
import $ from 'jquery';
window.$ = $;
window.jQuery = $;

// DataTables JS dan CSS
import 'datatables.net-dt/js/dataTables.dataTables';
import logo from '../images/logo-bsm.png';

// Alpine.js
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
window.Alpine = Alpine;

Alpine.plugin(focus);
Alpine.start();