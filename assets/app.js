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

import { ScrollWeb } from './smoothScroll';
import { Parallax } from './parallax';
import * as Vue from 'vue';
import AOS from 'aos';

// Variables
// -----------------------------------------------
var values = {
    damping: 0.05
}

// Instantieur
// -----------------------------------------------
document.addEventListener('DOMContentLoaded', function(){
    Vue.createApp({}).mount('#website');
    AOS.init();
    scrollWeb();
    parallax();
})

// Smooth Scrollbar
// -----------------------------------------------
function scrollWeb() {
    let scrollWeb = new ScrollWeb(values.damping, values.divScroller);
    scrollWeb.init;
    return scrollWeb;
}

function parallax(){
    let parallax = new Parallax(values.divScroller);
    parallax.initParallax();
    return parallax;
}