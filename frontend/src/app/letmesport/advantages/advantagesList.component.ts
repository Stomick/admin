import { Component, ViewEncapsulation, OnInit, OnDestroy, Input } from '@angular/core';
import {AdvantagesListService} from './advantagesList.service';
import {AdvanFacility} from './../models/advanFacility.model';
import {OnChanges} from "../../../../node_modules/@angular/core/src/metadata/lifecycle_hooks";

@Component({
    selector: 'advantagesList',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'advantagesList.html',
    styles: ['./advantagesList.scss'],
    providers: [AdvantagesListService]
})

export class AdvantagesListComponent implements OnInit, OnDestroy, OnChanges{
    list: AdvanFacility[];
    isAdmin: boolean;
    isChecked: boolean;

    @Input() advantagesList: any[];

    constructor(public advantagesService:AdvantagesListService){
        if(JSON.parse(localStorage.getItem('currentUser')).role ==  'super-admin'){
            this.isAdmin = true;
        }
    }

    ngOnInit(){
    }

    ngOnChanges(){
        this.isChecked = false;
        this.getAll();
    }

    ngOnDestroy() {}

    getAll(){
        this.advantagesService.getList()
                .subscribe((data: AdvanFacility[]) => {
                    this.list = data;
                    this.advantagesList.forEach((item) => {
                        this.list.forEach((subitem) => {
                            if (item == subitem.id){
                                subitem.check = true;
                                this.isChecked = true;
                            }
                        })
                    });
            })
    }

    changeCheck(id){
        let findElemIndex = this.advantagesList.indexOf(id);
        if(findElemIndex < 0){
            this.advantagesList.push(id);
        }else{
            this.advantagesList.splice(findElemIndex, 1);
        }
        findElemIndex = null;
    }
}