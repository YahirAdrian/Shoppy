import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import posSale from './pos/sale';
import posSearch from './pos/search';

window.Alpine = Alpine;
window.Chart = Chart;
window.posSale = posSale;
window.posSearch = posSearch;

Alpine.start();
