import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()
export class RequisitesService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    parseData(res: Response){
        let data = res.json();
        console.log(data);
        return data;
    }

    public getAll(id){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'company-details/' + id, {headers: headers})
            .map(this.parseData);
    }

    public editReq(id, req){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .put(API_PATH + 'company-details/' + id, req, {headers: headers})
            .map((resp:Response)=>{
                if(resp.text() != ""){
                    resp.json()
                }
            });
    }

    public changeStatus(id, data){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'sport-centers/change-confirmation-status?id=' + id + '&confirmationStatus=' + data, {headers: headers})
    }
}