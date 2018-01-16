import {Component, ViewEncapsulation} from '@angular/core';
import { Router } from '@angular/router';
import {AuthGuard} from './../../../pages/login/authGuard/authGuard.service'
import {AppState} from '../../../app.state';

@Component({
    selector:'navbar',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./navbar.scss'],
    templateUrl: './navbar.html'
})

export class Navbar{
    public isMenuCollapsed:boolean = false;
    public router: Router;
    userData: any = {
        name: '',
        role: '',
    }

    constructor(private _state:AppState, router:Router, private _auth:AuthGuard) {
        this.router = router;
        this._state.subscribe('menu.isCollapsed', (isCollapsed) => {
            this.isMenuCollapsed = isCollapsed;
        });

        let currentUser = JSON.parse(localStorage.getItem('currentUser'));
        this.userData.name = currentUser.name;
        switch (currentUser.role){
            case 'super-admin':
                this.userData.role = 'Cупер администратор';
                break;
            case 'admin':
                this.userData.role = 'Администратор';
            default:'';
        }
    }

    public toggleMenu() {
        this.isMenuCollapsed = !this.isMenuCollapsed;
        this._state.notifyDataChanged('menu.isCollapsed', this.isMenuCollapsed);
    }

    // routerLink="/login"

    public logOut(){
        localStorage.removeItem('currentUser');
        this.router.navigate(['/login']);

    }
}