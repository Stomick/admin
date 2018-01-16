import {Component, ViewEncapsulation, OnInit, OnDestroy, Input, Output, EventEmitter} from '@angular/core';
import {OnChanges} from "../../../../../node_modules/@angular/core/src/metadata/lifecycle_hooks";
import {WorkDay} from "../../models/workDay.model";
import {parseComment} from "typedoc/lib/converter/factories/comment";
import parseInt = require("core-js/fn/number/parse-int");

@Component({
    selector: 'worktime',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'worktime.html',
    styles: ['worktime.scss'],
})

export class WorktimeComponent implements OnInit, OnChanges, OnDestroy {
    isAdmin: boolean;

    public workStartHour: any[];
    public workEndHour: any[];
    public workMin: any[];
    public selectStartHour: number;
    public selectStartMin: number;
    public selectEndHour: number;
    public selectEndMin: number;

    @Input() workPossible: WorkDay[];
    @Input() typeDay: string;

    @Output()
    newWorkTime: EventEmitter<any> = new EventEmitter();

    constructor() {
        if (JSON.parse(localStorage.getItem('currentUser')).role == 'super-admin') {
            this.isAdmin = true;
        }
    }

    ngOnInit() {
    }

    ngOnChanges() {


        this.selectStartHour = 0;
        this.selectStartMin = 0;
        this.selectEndHour = 1;
        this.selectEndMin = 0;
        this.workStartHour = [];
        this.workEndHour = [];
        this.workMin = [];

        for (let i = 0; i < 24; i++) {
            this.workStartHour[i] = ( i < 10 ) ? '0' + i.toString() : i.toString();
            this.workEndHour[i] = ( (i + 1) < 10 ) ? '0' + (i + 1).toString() : (i + 1).toString();
        }

        this.workMin[0] = '00';
        this.workMin[1] = '30';

        /*
        if (this.workPossible.length == 0){
                this.workPossible.push(new WorkDay({
                    hour: 1,
                    price: null,
                    type: this.typeDay,
                    working: true,
                }))
        }
        */
    }

    sendData() {
        this.newWorkTime.emit(null);
    }

    deleteTime(i) {

        let start = parseInt(this.workPossible[i].start_hour.split(":")[0]);
        let end = parseInt(this.workPossible[i].end_hour.split(':')[0]);

        let arrStart = [];

        for (let i = 0, t = 0, s = start; i < this.workStartHour.length + (end - start); i++) {

            if (parseInt(this.workStartHour[0]) > s) {

                arrStart[i] = s < 10 ? '0' + s : s.toString();
                s++;

            } else {
                arrStart[i] = this.workStartHour[t];
                t++;

            }
        }

        let arrEnd = [];

        arrEnd[0] = end < 10 ? 0 + end : end.toString();

        for (let i = 1; i < this.workEndHour.length; i++) {
            arrEnd[i] = this.workEndHour[i - 1];
        }
        this.workStartHour = arrStart;
        this.workEndHour = arrEnd;

        this.workPossible.splice(i, 1);
        window.console.log(this.workPossible , 'there');
        this.sendData();

    }

    onChangeTime(h, type) {

        var hour = parseInt(h);

        switch (type) {
            case 'startHour':
                this.selectStartHour = hour;
                this.workEndHour = [];
                for (var i = (this.selectStartMin == 30 ? hour + 1 : hour), t = 0; i < 24; i++, t++) {
                    this.workEndHour[t] = ( i < 10 ) ? '0' + i.toString() : i.toString();
                }
                break;
            case 'startMin':
                this.selectStartMin = hour;
                if (this.selectStartMin == 30) {
                    this.workEndHour.splice(0, 1);
                    if (this.selectStartHour == this.selectEndHour + 1) {
                        this.selectEndMin = 30;
                    }
                }
                break;
            case 'endHour':
                this.selectEndHour = hour;
                break;
            case 'endMin':
                this.selectEndMin = hour;
                break;
        }
    }

    addWorkTime(e) {
        if (this.selectStartHour < 23) {

            this.workPossible.push(new WorkDay({
                hour: 0,
                start_hour: (this.selectStartHour < 10 ? '0' + this.selectStartHour : this.selectStartHour.toString()) + ':' + (this.selectStartMin == 30 ? '30' : '00'),
                end_hour: (this.selectEndHour < 10 ? '0' + this.selectEndHour : this.selectEndHour.toString()) + ':' + (this.selectEndMin == 30 ? '30' : '00'),
                price: null,
                type: this.typeDay,
                working: false,
            }));

            let start = this.selectEndHour;

            let end = this.selectEndHour + 1;

            if (this.selectStartMin == 30) {
                this.selectEndMin = 30;
                end = end + 1;
            }

            if (this.selectEndMin == 30) {
                this.selectStartMin = 30;
            }

            for (let i = 0; parseInt(this.workStartHour[i]) <= start ; i++) {
                this.workStartHour.splice(0, 1);

            }

            for (let i = 0; parseInt(this.workEndHour[i]) <= end ; i++) {
                this.workEndHour.splice(0, 1);
            }

            this.selectStartHour = parseInt(this.workStartHour[0]);
            this.selectEndHour = parseInt(this.workEndHour[0]);

        }
        else {
            this.workStartHour = [];
            this.workEndHour = [];
            this.workMin = [];
        }
    }

    changeTime(currentElem) {

        currentElem.working = !currentElem.working;
        this.sendData();

    }

    ngOnDestroy() {
    }

}
