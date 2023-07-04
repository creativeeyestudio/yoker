import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/all';
import { ScrollWeb } from './smoothScroll';

export class Parallax extends ScrollWeb {
    constructor(damping, scrollImgSpeed) {
        super(damping);
        this.scrollImgSpeed = scrollImgSpeed;
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
            const pointSectionBg = section.bg.parentElement.offsetTop;
            const pointScrollWeb = scrollWeb.init.containerEl.getBoundingClientRect().bottom;
            
            scrollWeb.init.addListener(() => {  
                if (section.getBoundingClientRect().top < pointScrollWeb) {
                    section.bg.style.top = -(section.getBoundingClientRect().top * (this.scrollImgSpeed / 10)) + pointSectionBg + 'px';
                }
            });
        });
    }
}