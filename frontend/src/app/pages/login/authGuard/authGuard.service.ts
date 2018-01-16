import { Injectable } from '@angular/core';
import {CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot} from '@angular/router';
import {Http, Response} from '@angular/http';
import {Observable} from 'rxjs/Observable';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()
export class AuthGuard implements CanActivate {
    public userData:any;

    constructor(private http: Http) {
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    // public isAuth() : boolean {
    //     return this.userData.token != null;
    // }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot):boolean {
        let roles = route.data["roles"] as Array<string>;
        return (roles == null || roles.indexOf(this.userData.role) != -1);
    }

    public auth(email: string, password: string):Observable<any>{

        return this.http.post(API_PATH + 'auth/admin-signin', {email: email, password: password})
            .map((resp:Response)=>resp.json())
            .catch((error:any) =>{return Observable.throw(error);});
    }
}