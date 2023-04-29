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
            section.bg = section.querySelector(".parallax-element");
            scrollWeb.init.addListener(({ offset }) => {  
                section.bg.style.top = offset.y - section.bg.parentElement.offsetTop + 'px';
            });
            if(i) {
                section.bg.style.backgroundPosition = `50% ${innerHeight / 2}px`;
                gsap.to(section.bg, {
                    backgroundPosition: `50% ${-innerHeight / 2}`,
                    ease: "none",
                    scrollTrigger: {
                        trigger: section,
                        scrub: true
                    }
                });
            } else {
                section.bg.style.backgroundPosition = "50% 0px"; 
                gsap.to(section.bg, {
                    backgroundPosition: `50% ${-innerHeight / 2}px`,
                    ease: "none",
                    scrollTrigger: {
                        trigger: section,
                        start: "top top",
                        scrub: true
                    }
                });
            }
        });
    }
}