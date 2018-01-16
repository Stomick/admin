import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import {Observable} from 'rxjs/Observable';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { Facility } from './../models/facility.model';

@Injectable()
export class FacilitiesListService {
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
            responseData.items.push(new Facility(item));
        });
        return responseData;
    }

    public getAll(page){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'sport-centers?page=' + page, {headers: headers})
            .map(this.parseData);
    }

    public getAdmin(){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'admins', {headers: headers})
            .map((resp:Response)=>resp.json());
    }

    public writeFacility(newFac){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .post(API_PATH + 'sport-centers', newFac, {headers: headers})
            .map((resp:Response)=>{
            if(resp.text() != ""){
                resp.json()
            }
        });
    }

    public editFacAdmin(data,id){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .put(API_PATH + 'sport-centers/' + id, {admins: data}, {headers: headers})
            .map((resp:Response)=>{
                if(resp.text() != ""){
                    resp.json()
                }
            });
    }

    public removeFacility(id){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .delete(API_PATH + 'sport-centers/' + id, {headers: headers})
    }
}