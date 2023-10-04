import Scrollbar from 'smooth-scrollbar';

export class ScrollWeb {

    constructor(damping){
        this.damping = damping
    }

    get init(){
        const scrollbar = Scrollbar.init(document.querySelector('#content'), {
            damping: (this.damping / 100),
            renderByPixels: true,
            continuousScrolling: true,
            delegateTo: document,
            thumbMinSize: 15
            // alwaysShowTracks: true,
        });
        return scrollbar;
    }
}