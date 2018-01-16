import { Injectable } from '@angular/core';
import {Http, Headers} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()
export class AdvantagesAddService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    public getList(){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.get(API_PATH + 'advantages', {headers: headers});
    }

    public addToList(newItem){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.post(API_PATH + 'advantages', newItem, {headers: headers});
    }

    public editToList(editItem, id){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.put(API_PATH + 'advantages/' + id, editItem, {headers: headers});
    }

    public removeToList(id){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.delete(API_PATH + 'advantages/' + id, {headers: headers});
    }
}