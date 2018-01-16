import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { User } from './../models/user.model';

@Injectable()
export class UserListService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    parseData(res: Response){
        let data = res.json();
        data.items.forEach((obj) => {
            var date = new Date(obj.createdAt*1000);
            var formatted = new Date(date.toISOString());
            obj.reformatDate = (formatted.getMonth()+1) + '.' + ('0' + formatted.getDate()).slice(-2) + '.' + formatted.getFullYear();
        });
        let responseData = {
            items: [],
            meta: {},
        };
        responseData.meta = data._meta;
        data.items.forEach(function(item){
            responseData.items.push(new User(item));
        });
        return responseData;
    }

    public getUser(page){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'users?page=' + page, {headers: headers})
            .map(this.parseData);
    }

    public removeUser(id){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .delete(API_PATH + 'users/' + id, {headers: headers})
    }
}