import { Injectable } from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import {Observable} from 'rxjs/Observable';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import {AdvanFacility} from './../models/advanFacility.model';

@Injectable()
export class AdvantagesListService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    parseData(response: Response){
        let data = response.json();
        return data.map(function(item){
            return new AdvanFacility(item);
        });
    }

    public getList():Observable<AdvanFacility[]>{
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'advantages', {headers: headers})
            .map(this.parseData)
    }
}