import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import posSale from './pos/sale';
import posSearch from './pos/search';
import posStatus from './pos/status';
import posStartSession from './pos/start-session';
import initDashboardCharts from './admin/dashboard';

window.Alpine = Alpine;
window.Chart = Chart;
window.posSale = posSale;
window.posSearch = posSearch;
window.posStatus = posStatus;
window.posStartSession = posStartSession;
window.initDashboardCharts = initDashboardCharts;

Alpine.start();
