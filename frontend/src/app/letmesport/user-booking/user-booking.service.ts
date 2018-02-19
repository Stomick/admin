import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';
import { User } from '../models/user.model';

@Injectable()
export class UserBookingService {
    userData: any;

    constructor(private _http: Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }
    getUserId(){
        return this.userData.id;
    }
    parseData(res: Response){
        let data = res.json();
        data.items.forEach((obj) => {
            let date = new Date(obj.createdAt*1000);
            let formatted = new Date(date.toISOString());
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

    public getCenterList(){
        var headers = new Headers();
        let dateObj = new Date();
        this.createAuthorizationHeader(headers);
        return this._http.get(API_PATH + 'sport-centers/list?year=' + dateObj.getFullYear(), {headers: headers});
    }

    public getAll(id){
        var headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http.get(API_PATH + 'sport-centers/' + id, {headers: headers});
    }

    public getBooking(id, startDate, endDate){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'bookings/schedule?id=' + id + '&startDate=' + startDate + '&endDate=' + endDate, {headers: headers});//.map(this.parseData)
    }


}