/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css'

// stimulus
import './bootstrap.js';

import '@tabler/core';
import '@tabler/core/dist/css/tabler.min.css';
// bootstrap 5
// import bootstrap from 'bootstrap'; // bootstrap javascript
// import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;
// import 'bootstrap/dist/css/bootstrap.min.css'
// import 'bootswatch/dist/sandstone/bootstrap.min.css'
// import 'bootswatch/dist/materia/bootstrap.min.css'

// font-awesome free
// import '@fortawesome/fontawesome-free/css/fontawesome.min.css'
// import '@fortawesome/fontawesome-free'
//
// import {library} from "@fortawesome/fontawesome-svg-core"
// import { fas } from '@fortawesome/free-solid-svg-icons'
// library.add(fas)

// :-( doesn't work
// import 'bootstrap-icons/font/bootstrap-icons.min.css'

console.log('This log comes from assets/app.js, stimulus and bootstrap')
//
// import jquery from 'jquery';
// console.log('local jquery');
// const $ = jquery;
//
// import DataTables from 'datatables.net-bs5'
// import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';
// import 'datatables.net-select';
// import 'datatables.net-select-bs5';
// import 'datatables.net-searchpanes';

import 'datatables.net-scroller-bs5';
import 'datatables.net-scroller-bs5/css/scroller.bootstrap5.min.css';
