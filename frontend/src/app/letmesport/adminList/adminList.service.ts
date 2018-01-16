import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { Admin } from './../models/admin.model';

@Injectable()
export class AdminListService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    parseData(res: Response){
        let data = res.json();
        let responseData = {
            items: [],
            meta: {},
        };
        responseData.meta = data._meta;
        data.items.forEach(function(item){
            responseData.items.push(new Admin(item));
        });
        return responseData;
    }

    public getAdmin(page){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'admins?page=' + page, {headers: headers})
            .map(this.parseData);
    }

    public removeAdmin(id){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .delete(API_PATH + 'admins/' + id, {headers: headers})
    }

    public creatAdmin(data){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .post(API_PATH + 'admins', data, {headers: headers})
            .map((resp:Response)=>resp.json())
    }
}