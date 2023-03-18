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
import Scrollbar from 'smooth-scrollbar';
import AOS from 'aos';


// Initialisation de VueJS
// -----------------------------------------------
Vue.createApp({}).mount('#website');


// Smooth Scrollbar
// -----------------------------------------------
const scrollbar = Scrollbar.init(document.querySelector('#content'), {
    damping: 0.05,
    continuousScrolling: false,
    alwaysShowTracks: true,
});

const links = document.querySelectorAll('a[href*=\\#]');
links.forEach(link => {
    link.setAttribute('data-no-swup', '');
    link.addEventListener('click', function(){
        const href = link.getAttribute('href');
        const target = document.querySelector(href);
        if (target) {
            scrollbar.scrollIntoView(target, {
                offsetTop: -scrollbar.containerEl.scrollTop,
            });
            return false;
        }
    });
})

// AOS
// -----------------------------------------------
AOS.init();