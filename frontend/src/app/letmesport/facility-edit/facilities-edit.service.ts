import { Injectable } from '@angular/core';
import { Http, Headers } from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()
export class FacilityEditService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    public getAll(id){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.get(API_PATH + 'sport-centers/' + id, {headers: headers});
    }

    public rewrite(id, obj){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.put(API_PATH + 'sport-centers/' + id, obj, {headers: headers});
    }

    public changeStatus(id, data){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .put(API_PATH + 'sport-centers/' + id, {approvementStatus:data}, {headers: headers})
    }

    public deletePlayfileds(id, playingFieldsId){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .delete(API_PATH + 'playing-fields/'  + playingFieldsId, {headers: headers})
    }
}