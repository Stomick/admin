import { Component, ViewEncapsulation } from '@angular/core';
import { Router } from '@angular/router';
import { Location } from '@angular/common';

import { AppState } from '../app.state';

@Component({
    selector: 'pages',
    encapsulation: ViewEncapsulation.None,
    providers: [AppState],
    styleUrls: ['./pages.scss'],
    template: `
        <navbar></navbar>
    <div class="c-align">
        <div class="container-fluid">         
            <div class="row"> 
               
                <div class="main-wrapper"  [ngClass]="{'menu-collapsed': isMenuCollapsed}"> 
                    <div class="az-overlay" *ngIf="!isMenuCollapsed" (click)="hideMenu()"></div>

                    <div class="main">
                        <breadcrumb></breadcrumb>
                        <router-outlet></router-outlet>
                    </div> 

                    <footer></footer>

                    <back-top position="200"></back-top>

                </div>
            </div>
        </div>
    </div>
    `
})

export class PagesComponent {
    isMenuCollapsed:boolean = false;
    public router: Router;
    authCheck:string;
    currentUser: any;
  
    constructor(private _state:AppState, private _location:Location, router:Router) {
        this._state.subscribe('menu.isCollapsed', (isCollapsed) => {
            this.isMenuCollapsed = isCollapsed;
        });

        this.router = router;
        this.currentUser = JSON.parse(localStorage.getItem('currentUser') || null);
        this.authCheck = this.currentUser.token;
    }

    public isAuth(){
        if(this.authCheck == null){
            this.router.navigate(['login']);
        }
    }

    ngOnInit() {
        this.getCurrentPageName();
    }
    

    getCurrentPageName():void{       
        var url = this._location.path();
       // var currentPage = url.substring(url.lastIndexOf('/') + 1);
        //this._state.notifyDataChanged('menu.activeLink', currentPage); 
        setTimeout(function(){
            window.scrollTo(0, 0);
            jQuery('a[href="#' + url + '"]').closest("li").closest("ul").closest("li").addClass("sidebar-item-expanded");      
        });
    }

     public hideMenu():void{
         this._state.notifyDataChanged('menu.isCollapsed', true);    
     }


}