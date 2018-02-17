import {Component, ViewEncapsulation, OnInit, ViewContainerRef} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {Response} from '@angular/http';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {UserBookingService} from './user-booking.service';
import { User } from '../models/user.model';
import {Service} from "../models/service.model";
import {Playground} from "../models/playground.model";

@Component({
    selector: 'user-booking',
    templateUrl: 'user-booking.html',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./user-booking.scss'],
    providers: [UserBookingService]
})

export class UserBookingComponent implements OnInit{

    public users : User[];
    public sArrMonths : any[];
    public monthNames = [
        "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
        "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
    ];
    public router: Router;
    public isAdmin : boolean;
    public currentMonths : boolean;
    private sub: any;
    id: number;
    data: any = '';
    sportCentrList = [];
    advantagesList: any = [];
    services: Service[] = [];
    playgroundArray: Playground[] = [];
    playGroundIs: boolean = false;
    photoArray: any[] = [];
    logo: string = '';
    logoSrc: string = '';
    days : number;
    dayArray = [];
    timeArray = [];
    idsport : number;
    dateObj : any;
    startT = '00:00';
    endT = '24:00';
    constructor(private _userbookingservice: UserBookingService,
                private route: ActivatedRoute,
                router: Router,
                public toastr: ToastsManager,
                vcr: ViewContainerRef) {
        this.toastr.setRootViewContainerRef(vcr);

        this.router = router;
        if (JSON.parse(localStorage.getItem('currentUser')).role == 'super-admin') {
            this.isAdmin = true;
        }
    };

    ngOnInit(){
        this.dateObj = new Date();
        this.sArrMonths = [];

        for(let i = 0; i < this.monthNames.length ; i++){
            this.sArrMonths[i] = this.monthNames[i];
        }

        this.currentMonths = true;

        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id'];
        });

        this.setTable(this.dateObj.getUTCMonth() + 1);

        window.console.log(this.timeArray , this.dayArray);

        this._userbookingservice.getCenterList().subscribe((data: Response) =>{
            this.sportCentrList = data.json();
        });
    }

    setTable(mnt = null){

        let startT = parseInt(this.startT.split(':')[0]);
        let endT = parseInt(this.endT.split(':')[1]) >= 30 ? parseInt(this.endT.split(':')[0]) + 1: parseInt(this.endT.split(':')[0]);
        window.console.log( startT, endT);

        let curDay = this.daysInMonth(parseInt(mnt)+1, this.dateObj.getFullYear());
        let  mounth = parseInt(mnt) < 9 ? '0' + (parseInt(mnt) + 1) : (parseInt(mnt) + 1);
        this.dayArray = [];
        for( let d = 0; d < curDay ; d++) {
            this.dayArray[d] = [];
            this.dayArray[d]['day'] = d < 9 ? '0' + (d + 1) + '.' + mounth + '.' + this.dateObj.getFullYear() : (d + 1) + '.' + mounth + '.' + this.dateObj.getFullYear().toString();
            this.dayArray[d]['time'] = [];
            for (let t = 0; t < 48; t++){
                this.dayArray[d]['time'][t] = '';
            }
        }
        let boolsMin = false;

        for (let t = startT, i = 0; t < endT; i++){
             if(i != 0) {
                 if (boolsMin == false) {
                     this.timeArray[i] = t < 10 ? '0' + t + ':' + '00': t + ':' + '00';
                     boolsMin = true;
                 }
                 else {
                     this.timeArray[i] = t < 10 ? '0' + t + ':' + '30': t + ':' + '30';
                     boolsMin = false;
                     t++;
                 }
             }else {
                 this.timeArray[i] = '';
             }

        }
    }

    daysInMonth (month, year) {
        return new Date(year, month, 0).getDate();
    }

    parsePhotoData() {
        if (this.data.logoSrc == null) {
            this.data.logoSrc = '../../../assets/img/placeground-empty.png'
        }
        this.data.images.forEach(function (item) {
            if (item.src == null) {
                item.src = '../../../assets/img/placeground-empty.png';
            }
        })
    }
    getPlayGround($event){
        let start : string;
        let end : string;
        this.idsport = $event.target.value;
        this.playgroundArray = [];
        this._userbookingservice.getAll(this.idsport)
            .subscribe((data: Response) => {
                this.data = data.json();
                this.advantagesList = this.data.advantageIds;
                this.services = this.data.services;
                this.startT = this.data.start_work;
                this.endT = this.data.end_work;
                this.data.playingFields.forEach((item) => {
                    this.playgroundArray.push(new Playground(item))
                });
                // this.playgroundArray = this.data.playingFields;
                this.parsePhotoData();
                this.photoArray = this.data.images;
                this.logoSrc = this.data.logoSrc;
                this.logo = this.data.logo;
                if (this.playgroundArray.length > 0 && this.playgroundArray) {
                    this.playGroundIs = true
                }
                if(this.playgroundArray.length == 0){
                    this.getBooking();
                }
                this.setTable(this.dateObj.getUTCMonth() + 1);
            });

    }

    getBooking($event = null){
        let startDate = new Date(this.dateObj.getFullYear() , this.dateObj.getMonth(),0);
        let endDate = new Date(this.dateObj.getFullYear() , this.dateObj.getMonth(),this.daysInMonth(this.dateObj.getUTCMonth() + 1 , this.dateObj.getFullYear()));
        this._userbookingservice.getBooking(this.idsport, startDate.getTime,endDate.getTime());
    }
    getBoockingInMonths($event){
        this.setTable($event.target.value);
    }
}