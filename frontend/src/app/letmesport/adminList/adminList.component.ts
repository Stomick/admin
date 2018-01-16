import { Component, ViewEncapsulation, OnInit, OnDestroy, ViewContainerRef} from '@angular/core';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {AdminListService} from './adminList.service';
import {Admin} from "../models/admin.model";

@Component({
    selector: 'adminList',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'adminList.html',
    providers: [AdminListService]
})

export class AdminListComponent implements OnInit, OnDestroy{
    data: Admin[];
    infoPages: any = {};
    public modalDelete: boolean = false;
    public currentId: number = null;

    constructor(private _adminListService:AdminListService, public toastr: ToastsManager, vcr: ViewContainerRef){
        this.toastr.setRootViewContainerRef(vcr);
    }

    ngOnInit(){
        this.getList(1);
    }

    ngOnDestroy(){
        this.data = [];
    }

    getList(page){
        this._adminListService.getAdmin(page)
            .subscribe((data: any) => {
                this.data = data.items;
                this.infoPages = data.meta;
            });
    }

    showDeleteModal(id){
        this.modalDelete = true;
        this.currentId = id;
    }

    public handleOutput($event){
        this._adminListService.creatAdmin($event).subscribe((response) =>
            {
                this.toastr.success('Создан');
                this.getList(this.infoPages.currentPage);
            }, (err)=>{
                this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                    allowHtml: true,
                    timeOut: 10000,
                });
        });
    }

    public remove(id){
        this._adminListService.removeAdmin(id).subscribe(() =>
        {
            this.getList(this.infoPages.currentPage);
            this.toastr.success('Удалён');
        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
        this.currentId = null;
        this.modalDelete = false;
    }

    goToPage($event){
        let targetPage = $event;
        this.getList(targetPage);
    }

}