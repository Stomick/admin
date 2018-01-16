import { Injectable } from '@angular/core';
import {Http, Headers,Response} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()
export class TimeTableService {
    userData: any;

    constructor(private _http:Http){
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    createAuthorizationHeader(headers: Headers) {
        headers.append('Authorization', 'Bearer ' + this.userData.token);
    }

    parseData(response){
        return response.json();
    }

    public getBooking(id, startDate, endDate){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .get(API_PATH + 'bookings/schedule?id=' + id + '&startDate=' + startDate + '&endDate=' + endDate, {headers: headers}).map(this.parseData)
    }
}