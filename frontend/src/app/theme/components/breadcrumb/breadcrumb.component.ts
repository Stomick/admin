import { Component, ViewEncapsulation } from '@angular/core';

import { AppState } from "../../../app.state";

@Component({
    selector: 'breadcrumb',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./breadcrumb.scss'],
    templateUrl: './breadcrumb.html'
})

export class Breadcrumb {

    public activePageTitle:string = '';
    public isAdmin:boolean;

    constructor(private _state:AppState){
        if(JSON.parse(localStorage.getItem('currentUser')).role ==  'super-admin'){
            this.isAdmin = true;
        }

        this._state.subscribe('menu.activeLink', (activeLink) => {
            if (activeLink) {
                this.activePageTitle = activeLink;
            }
        });
    }

    public ngOnInit():void {
        if (!this.activePageTitle) {
            this.activePageTitle = 'dashboard';
        }
      
    }

}