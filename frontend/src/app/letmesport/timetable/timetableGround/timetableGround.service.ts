import { Injectable } from '@angular/core';
import {Http, Headers,Response, URLSearchParams} from '@angular/http';
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Injectable()

export class TimetableGroundService {
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

    public removeBooking(id){
        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .delete(API_PATH + 'bookings/' + id, {headers: headers})
    }

    public block(e , fieldId, date) {
        let hour =  e.srcElement.innerText;
        window.console.log(hour);
        if(e.srcElement.classList.contains('t_cheked')){
            e.srcElement.classList.remove('t_cheked');
        }else{
            e.srcElement.classList.add('t_cheked');
        }

        let headers = new Headers();
        this.createAuthorizationHeader(headers);
        return this._http
            .post(
                API_PATH + 'bookings/block?fieldId=' + fieldId + '&date=' + date + '&hour=' + hour,
                JSON.stringify({}),
                {headers: headers}
            );
    }
}