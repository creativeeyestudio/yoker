import Scrollbar from 'smooth-scrollbar';
import AOS from 'aos';

export class ScrollWeb {

    constructor(damping){
        this.damping = damping
    }

    get init(){
        const container = document.querySelector('#content');
        const scrollbar = Scrollbar.init(container, {
            damping: (this.damping / 100),
            renderByPixels: true,
            continuousScrolling: true,
            delegateTo: document,
            thumbMinSize: 15
        });

        scrollbar.track.xAxis.element.remove();

        AOS.init({
            duration: 1000,
            delay: 200,
            disable: window.innerWidth < 1200,
        });
      
        [].forEach.call(document.querySelectorAll('[data-aos]'), (el) => {
          scrollbar.addListener(() => {
            if (scrollbar.isVisible(el)) {
              el.classList.add('aos-animate');
            } else {
              el.classList.remove('aos-animate');
            }
          });
        });

        // Scroll au click d'une ancre
        const navLinks = document.querySelectorAll('a[href^="#"]');
        navLinks.forEach(btn => {
            btn.addEventListener('click', function(){
                const margin = 0;
                const target = btn.getAttribute('href') || btn.getAttribute('data-link');
                const anchor = document.querySelector(target);
                const offset = container.getBoundingClientRect().top - anchor.getBoundingClientRect().top;
                scrollbar.scrollIntoView(anchor, { 
                    offset, 
                    offsetTop: margin
                });
                return false;
            })
        })

        return scrollbar;
    }
}