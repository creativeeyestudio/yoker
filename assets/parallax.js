import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/all';
import { ScrollWeb } from './smoothScroll';

export class Parallax extends ScrollWeb {

    constructor(damping) {
        super(damping)
    }

    initParallax(){
        gsap.registerPlugin(ScrollTrigger);
        const scroller = document.querySelector('#website');
        const scrollWeb = new ScrollWeb(this.damping)

        ScrollTrigger.scrollerProxy("#website", {
            scrollTop(value) {
                if (arguments.length) {
                    scrollWeb.init.scrollTop = value;
                }
                return scrollWeb.init.scrollTop;
            }
        });
        
        scrollWeb.init.addListener(ScrollTrigger.update);
        
        ScrollTrigger.defaults({
            scroller: scroller
        });

        gsap.utils.toArray('.parallax-section').forEach((section, i) => {
            section.bg = section.querySelector(".parallax-element img");
            scrollWeb.init.addListener(({ offset }) => {  
                if (section.getBoundingClientRect().top < scrollWeb.init.containerEl.getBoundingClientRect().bottom) {
                    section.bg.style.top = -(section.getBoundingClientRect().top * 0.5) + section.bg.parentElement.offsetTop + 'px';
                }
            });
        });
    }
}