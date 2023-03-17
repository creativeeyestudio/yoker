/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/web/app.scss';

// start the Stimulus application
import './bootstrap';
import * as Vue from 'vue';
import { Scrollbar } from 'smooth-scrollbar/scrollbar';
import AOS from 'aos';

// Initialisation de VueJS
// -----------------------------------------------
Vue.createApp({}).mount('#website');
// console.log(Vue);


// Smooth Scrollbar
// -----------------------------------------------
Scrollbar.init(
    document.querySelector('#website')
)


// AOS
// -----------------------------------------------
AOS.init();