import Scrollbar from 'smooth-scrollbar';

export class ScrollWeb {

    constructor(damping){
        this.damping = damping
    }

    get init(){
        const scrollbar = Scrollbar.init(document.querySelector('#website'), {
            damping: this.damping,
            continuousScrolling: false,
            alwaysShowTracks: true,
        });
        return scrollbar;
    }
}