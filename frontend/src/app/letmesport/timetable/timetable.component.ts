import {Component, OnInit, OnDestroy} from "@angular/core";
import { TimeTableService } from './timetable.service';
import { Router, ActivatedRoute } from '@angular/router';

@Component({
    selector: 'timetable',
    templateUrl: 'timetable.html',
    styles: ['timetable.scss'],
    providers: [TimeTableService]
})

export class TimeTableComponent implements OnInit, OnDestroy{
    public router: Router;
    private sub: any;
    id: number;
    centerName: string = '';
    playgrounds: any = [];
    bookings: any = [];
    date: any = {
        startDate: '',
        endDate: '',
        sendStartDate: 0,
        sendEndDate: 0,
        enterDateStart: '',
        enterDateEnd: '',
        currentDay: '',
        timeZone: 0
    };
    days: any = {
        start: this.date.startDate,
        end: this.date.endDate
    };


    constructor(private route: ActivatedRoute, router:Router, public timeTableService: TimeTableService){
        this.router = router;
    }

    getDataObj(startDate, endDate){
        this.timeTableService.getBooking(this.id, startDate, endDate).subscribe((data) =>{
            this.centerName = data.sportCenterName;
            this.playgrounds = data.playingFields;
            this.bookings = data.bookings;
            this.days.start = this.date.startDate;
            this.days.end = this.date.endDate;
        })
    }

    getPlayCenterData(){
        this.date.sendStartDate = this.date.startDate.setHours(0,0,0,0) - this.date.timeZone;
        this.date.sendEndDate = this.date.endDate.setHours(0,0,0,0) - this.date.timeZone;
        this.date.timeZone = 0;
        this.getDataObj(this.date.sendStartDate, this.date.sendEndDate);
    }

    convertDate(date, from){
        if(from){
            this.date.startDate = new Date(date.substr(0,4), parseInt(date.substr(5,2)) - 1, parseInt(date.substr(8,2)));
        }
        else{
            this.date.endDate = new Date(date.substr(0,4), parseInt(date.substr(5,2)) - 1, parseInt(date.substr(8,2)));
        }
    }

    ngOnInit(){
        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id'];
        });
        this.date.timeZone = new Date().getTimezoneOffset()*60*1000;
        this.date.startDate = new Date();
        let month = this.date.startDate.getMonth() + 1;
        let day = this.date.startDate.getDate();
        let nextDay = this.date.startDate.getDate() + 1;
        if(month < 10)
            month = '0' + month;
        if(day < 10)
            day = '0' + day;
        if(nextDay < 10)
            nextDay = '0' + nextDay;
        this.date.enterDateStart = this.date.startDate.getFullYear() + '-' + month + '-' + day;
        this.date.enterDateEnd = this.date.startDate.getFullYear() + '-' + month + '-' + nextDay;
        this.date.currentDay = this.date.startDate.getFullYear() + '-' + month + '-' + day;
        this.date.endDate = new Date();
        this.date.endDate.setDate(this.date.startDate.getDate()+1);
        this.getPlayCenterData();
    }

    ngOnDestroy(){}
}