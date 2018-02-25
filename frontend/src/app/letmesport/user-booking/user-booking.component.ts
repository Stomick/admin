import {Component, ViewEncapsulation, OnInit, ViewContainerRef} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {Response} from '@angular/http';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {UserBookingService} from './user-booking.service';
import {User} from '../models/user.model';
import {Service} from "../models/service.model";
import {Playground} from "../models/playground.model";

@Component({
    selector: 'user-booking',
    templateUrl: 'user-booking.html',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./user-booking.scss'],
    providers: [UserBookingService]
})

export class UserBookingComponent implements OnInit {

    public users: User[];
    public sArrMonths: any[];
    public monthNames = [
        "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
        "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"
    ];
    public router: Router;
    public isAdmin: boolean;
    public currentMonths: boolean;
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
    days: number;
    dayArray = [];
    timeArray = [];
    idsport: number;
    dateObj = new Date();
    startT = '00:00';
    endT = '24:00';
    selectMount = new Date().getUTCMonth();
    bookings: any;
    selectedPlayFields = 0;
    playingFields: any;

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

    ngOnInit() {
        this.dateObj = new Date();
        this.selectMount = new Date().getUTCMonth();
        this.sArrMonths = [];

        for (let i = 0; i < this.monthNames.length; i++) {
            this.sArrMonths[i] = this.monthNames[i];
        }

        this.currentMonths = true;

        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id'];
        });

        this.setTable(this.dateObj.getUTCMonth());


        this._userbookingservice.getCenterList().subscribe((data: Response) => {
            this.sportCentrList = data.json();
        });
    }

    setTable(mnt = null, books = null) {
        window.console.log('SetTable');

        if (books != undefined) {

            this.bookings = [];
            for (let i in books.bookings){
                window.console.log(books.bookings[i]);
            }

            this.playingFields = books.playingFields[this.selectedPlayFields];
        }

        let startT = parseInt(this.startT.split(':')[0]);
        let endT = parseInt(this.endT.split(':')[1]) >= 30 ? parseInt(this.endT.split(':')[0]) + 1 : parseInt(this.endT.split(':')[0]);

        let curDay = this.daysInMonth(parseInt(mnt) + 1, this.dateObj.getFullYear());
        let mounth = parseInt(mnt) < 9 ? '0' + (parseInt(mnt) + 1) : (parseInt(mnt) + 1);

        this.dayArray = [];
        let boolsMin = false;
        this.timeArray = [];


        for (let t = startT, index = 0; t < endT; index++) {
            if (index != 0) {
                if (boolsMin == false) {
                    this.timeArray[index] = t < 10 ? '0' + t + ':' + '00' : t + ':' + '00';
                    boolsMin = true;
                }
                else {
                    this.timeArray[index] = t < 10 ? '0' + t + ':' + '30' : t + ':' + '30';
                    boolsMin = false;
                    t++;
                }
            } else {
                this.timeArray[index] = '';
            }
        }
        this.timeArray[this.timeArray.length] = this.endT;

        for (let d = 0; d < curDay; d++) {
            this.dayArray[d] = [];
            let day = d < 9 ? '0' + (d + 1) + '.' + mounth + '.' + this.dateObj.getFullYear() : (d + 1) + '.' + mounth + '.' + this.dateObj.getFullYear().toString();
            let dateN = new Date(day.split('.').reverse().join("-"));

            this.dayArray[d]['day'] = {
                'data': day,
                'type': dateN.getDay() == 0 || dateN.getDay() == 6 ? 'weekend' : 'work'
            };

            this.dayArray[d]['time'] = [];
            for (let t = 0; t < this.timeArray.length - 1; t++) {
                this.dayArray[d]['time'][t] = {
                    'style': books != undefined ? this.checkTime(day, t, this.dayArray[d].day.type, this.timeArray[t]) : '',
                    'info': ''
                };
            }
        }


    }

    daysInMonth(month, year) {
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

    getPlayGround($event, idsport) {
        let start: string;
        let end: string;
        let setMontch = this.selectMount;

        this.idsport = $event != null ? $event.target.value : idsport;
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
                this._userbookingservice.getBooking(this.idsport,
                    new Date(this.dateObj.getFullYear(), setMontch, 0).getTime(),
                    new Date(this.dateObj.getFullYear(), setMontch,
                        this.daysInMonth(setMontch, this.dateObj.getFullYear())).getTime())
                    .subscribe((data: Response) => {
                        this.setTable(setMontch, data.json());
                    });

            });
    }

    getBookingInPlayGround($event) {
        this.selectedPlayFields = $event.target.value;
        let setMontch = this.selectMount == 0 ? this.dateObj.getUTCMonth() : this.selectMount;
        this.setTable(setMontch);
    }

    getBooking(sportcenterId, start, end) {
        this._userbookingservice.getBooking(sportcenterId, start, end)
            .subscribe((data: Response) => {
                return this.bookings = data.json();
            });
    }

    getBoockingInMonths($event) {
        this.selectMount = $event.target.value;
        this.idsport == undefined ? this.setTable(this.selectMount) : this.getPlayGround(null, this.idsport);
    }

    checkTime(Day, indexTime, type, time) {
        let t_time = new Date(Day.split('.').reverse().join("-") + " " + time).getTime();let time_start = true;
        let start_string_time = '';
        let end_string_time = '';

        if (this.playingFields.availableTime.length > 0) {
            let aval = this.playingFields.availableTime;
            for (let a = 0; a < aval.length; a++) {
                if (aval[a].type == type) {
                   if(time_start){
                       window.console.log((aval[a].start_hour.split(":")[0] - 1) + ":" + aval[a].start_hour.split(":")[1]);
                       start_string_time = (aval[a].start_hour.split(":")[0] - 1) +":"+ aval[a].start_hour.split(":")[1];
                       end_string_time = aval[a].end_hour;
                       time_start = false;
                   }else {
                       end_string_time = aval[a].end_hour;
                   }
                }
            }
            let s_time = new Date(Day.split('.').reverse().join("-") + " " + start_string_time).getTime();
            let e_time = new Date(Day.split('.').reverse().join("-") + " " + end_string_time).getTime();
            if (t_time <= s_time || t_time >= e_time) {
                return 't_disabled';
            }
        }

        if (this.bookings.length > 0) {
            window.console.log();
        }

        if (this.playingFields.length > 0) {
            window.console.log(this.playingFields[this.selectedPlayFields]);
        }

    }
}

