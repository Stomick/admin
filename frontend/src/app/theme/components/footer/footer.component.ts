import {Component, ViewEncapsulation} from '@angular/core';

import {AppState} from '../../../app.state';

@Component({
    selector:'footer',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./footer.scss'],
    templateUrl: './footer.html'
})

export class Footer{
    public isMenuCollapsed:boolean = false;

    constructor(private _state:AppState) {
        this._state.subscribe('menu.isCollapsed', (isCollapsed) => {
            this.isMenuCollapsed = isCollapsed;
        });
    }

    public toggleMenu() {
        this.isMenuCollapsed = !this.isMenuCollapsed; 
        this._state.notifyDataChanged('menu.isCollapsed', this.isMenuCollapsed);   
    }
}