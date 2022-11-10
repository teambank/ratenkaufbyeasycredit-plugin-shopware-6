import Plugin from 'src/plugin-system/plugin.class';

export default class EasyCreditRatenkaufMarketing extends Plugin {
    init() {
        this.initMarketing();
    }

    initMarketing() {

        this.body = document.querySelector('body');

        this.bar = document.querySelector('easycredit-box-top');
        if ( this.bar ) {
            this.body.classList.add('easycredit-box-top');
        }

        this.card = document.querySelector('.easycredit-box-listing');
        if ( this.card ) {
            var siblings = n => [...n.parentElement.children].filter(c=>c!=n);
            var siblingsCard = siblings(this.card);

            var position = this.card.querySelector('easycredit-box-listing').getAttribute('position');
            var previousPosition = ( typeof position === undefined ) ? null : Number( position - 1 );
            var appendAfterPosition = ( typeof position === undefined ) ? null : Number( position - 2 );

            if ( !position || previousPosition <= 0 ) {
                // do nothing
            } else if ( appendAfterPosition in siblingsCard ) {
                siblingsCard[appendAfterPosition].after(this.card);
            } else {
                this.card.parentElement.append(this.card);
            }
        }

    }
}
