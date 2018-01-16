import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import {Observable} from 'rxjs/Observable';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()
export class ModalService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    public getAdmin(){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.get(API_PATH + 'admins', {headers: headers}).map((resp:Response)=>resp.json());
    }
}