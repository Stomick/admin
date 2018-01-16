import { Component, Input, OnInit, OnDestroy, OnChanges} from '@angular/core';
import {Playground} from "../../models/playground.model";
import {WorkDay} from "../../models/workDay.model";

@Component({
    selector: 'playground',
    templateUrl: 'playground.html',
    styles: ['./playground.scss'],
})

export class PlaygroundComponent implements OnInit, OnDestroy, OnChanges{
    public weekday: WorkDay[] = [];
    public weekend: WorkDay[] = [];
    public isAdmin: boolean;
    public collapse: boolean = true;
    @Input() playground: Playground;

    constructor(){
        if(JSON.parse(localStorage.getItem('currentUser')).role ==  'super-admin'){
            this.isAdmin = true;
        }
    }

    ngOnInit(){}

    ngOnChanges(){
        let weekdayTemp = this.weekday = [];
        let weekendTemp = this.weekend = [];
        this.playground.availableTimes.forEach(function (item) {
            if(item.type == 'work'){
                weekdayTemp.push(new WorkDay(item))
            } else{
                weekendTemp.push(new WorkDay(item))
            }
        });
    }

    updateTime(event){
        this.playground.availableTimes = [];
        this.playground.availableTimes = this.weekday.concat(this.weekend);
    }

    ngOnDestroy(){}


}