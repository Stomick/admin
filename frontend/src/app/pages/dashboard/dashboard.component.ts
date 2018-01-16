import { Component, ViewEncapsulation } from '@angular/core';
import { AppConfig } from "../../app.config";

import { DashboardService } from './dashboard.service';

@Component({
    selector: 'dashboard',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./dashboard.scss'],
    templateUrl: './dashboard.html',
    providers: [DashboardService] 
})

export class DashboardComponent  { 
    public config:any;
    public configFn:any; 
    public bgColor:any;
    public date = new Date(); 
    public weatherData:any;

    constructor(private _appConfig:AppConfig, private _dashboardService:DashboardService){
        this.config = this._appConfig.config;
        this.configFn = this._appConfig;
        this.weatherData = _dashboardService.getWeatherData();       
    }

}









// import {Component, ViewEncapsulation, OnInit} from '@angular/core';

// import { DashboardService } from './dashboard.service';
// import { AppService } from '../../../app/app.service';
// import {Http} from '@angular/http';

// interface IMember{
//     memberId: number;
//     name: string;
//     surname: string;
//     birthdate: string;
//     description: string;
//     imageUrl: string;
//     price: number;
//     starRating: number;
// }

// @Component({
//     selector: 'dashboard',
//     encapsulation: ViewEncapsulation.None,
//     styleUrls: ['./dashboard.css'],
//     templateUrl: './dashboard.html',
//     providers: [DashboardService, AppService]
// })

// export class DashboardComponent implements OnInit {
//     members: IMember[];
//    public showLoader:boolean = false;
//     _appService: AppService;
 
//     constructor(public http:Http, private dashboardService: DashboardService, public appService: AppService) {
//         this._appService = appService;
//     }
 
//     ngOnInit() {
//         this._appService.beforeRequest.subscribe(data => this.showLoader = true);
//         this._appService.afterRequest.subscribe(data => this.showLoader = false);
//        this.reload();
//     }

// //----------------------------------------
//     // reload() {     
//     //     this.dashboardService.getMembers()
//     //         .subscribe(members => {
//     //             this.members = members;                
//     //         });     
    
//     //  }
//     //---------------------------------------

//      reload() {
//         setTimeout(() => {
//                 this.http.get('api/members.json')
//                     .map(res => res.json())
//                     .subscribe(
//                     data => { this.members = data},
//                     err => console.log(err),
//                     () =>  console.log("complete")
//                 );
//         }, 500);
  
//   }


 
// }

