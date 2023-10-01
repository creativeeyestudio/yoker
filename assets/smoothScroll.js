import Scrollbar from 'smooth-scrollbar';

export class ScrollWeb {

    constructor(damping){
        this.damping = damping
        this.scrollbarOptions = {
            damping: this.damping / 100,
            thumbMinSize: 20
        }
    }

    get init(){
        const contentElement = document.querySelector('#content');
        if (!contentElement) {
            throw new Error('Element with ID "content" not found');
        } else {
            const scrollbar = Scrollbar.init(contentElement, this.scrollbarOptions);
            return scrollbar;
        }
    }
}