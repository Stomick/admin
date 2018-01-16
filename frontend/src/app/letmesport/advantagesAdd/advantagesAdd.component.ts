import { Component, ViewEncapsulation, OnInit, OnDestroy, ViewContainerRef } from '@angular/core';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {Response} from '@angular/http';
import 'rxjs/Rx';
import {AdvantagesAddService} from './advantagesAdd.service';

@Component({
    selector: 'advantagesAdd',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'advantagesAdd.html',
    styles: ['advantagesAdd.scss'],
    providers: [AdvantagesAddService]
})

export class AdvantagesAddComponent implements OnInit, OnDestroy{
    list: any = [];
    newAdvanItem: any = {};
    editElem: any = {};
    editVisible: boolean = false;
    createVisible: boolean = false;
    deleteModalVisible: boolean;
    deleteItem: number;

    constructor(public advantagesAddService:AdvantagesAddService, public toastr: ToastsManager, vcr: ViewContainerRef){
        this.toastr.setRootViewContainerRef(vcr);
    }

    ngOnInit(){
        this.getList();
    }

    callEdit(elem){
        this.editElem = elem;
    }

    getList(){
        this.advantagesAddService.getList()
            .subscribe((data: Response) => {
                this.list = data.json();
            });
    }

    ngOnDestroy() {
        this.list = [];
    }

    public createAdvan($event){
        this.newAdvanItem = new Object($event);
        this.createVisible = false;
        this.advantagesAddService.addToList(this.newAdvanItem).subscribe((data) => {
            this.toastr.success('Создано');
            this.newAdvanItem = data;
            this.getList()
        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
    }

    public editAdvan($event){
        this.newAdvanItem = new Object($event);
        this.editVisible = false;
        this.advantagesAddService.editToList(this.newAdvanItem, this.newAdvanItem.id).subscribe((data) =>
        {
            this.toastr.success('Изменено');
            this.newAdvanItem = data;
            this.getList()
        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
    }

    removeItem(id){
        this.deleteModalVisible = true;
        this.deleteItem = id;
    }

    remove(id){
        this.deleteModalVisible = false;
        this.advantagesAddService.removeToList(id).subscribe(() => {
            this.getList();
            this.toastr.success('Удалено');
        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
    }
}