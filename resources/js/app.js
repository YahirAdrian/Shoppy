import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import posSale from './pos/sale';

window.Alpine = Alpine;
window.Chart = Chart;
window.posSale = posSale;

Alpine.start();
