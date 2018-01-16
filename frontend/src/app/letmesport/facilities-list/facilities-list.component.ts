import { Component, ViewEncapsulation, OnInit, OnDestroy, ViewContainerRef} from '@angular/core';
import { Router } from '@angular/router';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import { FacilitiesListService } from './facilities-list.service';
import {Facility} from "../models/facility.model";

@Component({
    selector: 'facilities-list',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'facilities-list.html',
    styles: ['facilities-list.scss'],
    providers: [FacilitiesListService]
})

export class FacilitiesListComponent implements OnInit, OnDestroy{
    public router: Router;
    data: Facility[];
    responseData: Facility;
    infoPages: any = {};
    adminArray: any;
    editIdItem: number;
    editIndexItem: number;
    bgVisible: boolean = true;
    public modal: any;
    public currentId: number = null;
    public isAdmin: boolean = false;

    constructor(private _facilitiesListService:FacilitiesListService, public toastr: ToastsManager, vcr: ViewContainerRef, router:Router){
        this.router = router;

        if(localStorage.getItem('currentUser') != undefined && localStorage.getItem('currentUser') != null){
            if(JSON.parse(localStorage.getItem('currentUser')).role ==  'super-admin'){
                this.isAdmin = true;
            }
        }

        this.toastr.setRootViewContainerRef(vcr);
    }

    ngOnInit(){
        this.getList(1);
        this.modal = {
            delete: false,
            editAdmin: false,
        }
    }

    ngOnDestroy(){
        this.data = [];
    }

    getList(page){
        this._facilitiesListService.getAll(page)
            .subscribe((data: any) => {
                this.data = data.items;
                this.infoPages = data.meta;
            }, (err) => {
                console.log(JSON.parse(err._body));
                if (err.status == 401){
                    localStorage.removeItem('currentUser');
                    this.router.navigate(['/login']);
                } else{
                    this.toastr.error(JSON.parse(err._body).message , err.statusText, {
                        allowHtml: true,
                        timeOut: 10000,
                    });
                }
            });
    }

    showDeleteModal(id){
        this.modal.delete = true;
        this.currentId = id;
    }

    public handleOutput($event){
        this.responseData = new Facility($event);
        this.responseData.admins = $event.admins;
        this._facilitiesListService.writeFacility(this.responseData).subscribe(() =>
        {
            this.getList(this.infoPages.currentPage);
            this.toastr.success('Создано');
        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
    }

    public remove(id){
        this._facilitiesListService.removeFacility(id).subscribe(() =>
        {
            this.toastr.success('Удалено');
        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        }, () => {
            this.getList(this.infoPages.currentPage);
            this.currentId = null;
            this.modal.delete = false;
        });

    }

    goToPage($event){
        this.getList($event);
    }

    closeEditAdmin(){
        this.modal.editAdmin = false;
    }

    saveEditAdmin(event){
        this.data[this.editIndexItem].admins = event;
        this.modal.editAdmin = false;
        this._facilitiesListService.editFacAdmin(this.data[this.editIndexItem].admins, this.editIdItem).subscribe(() =>{
            this.toastr.success('Добавлено');
            this.getList(this.infoPages.currentPage);
        }, (err)=> {
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        })
    }

    addAdminFast(array, id, index){
        this.editIdItem = id;
        this.editIndexItem = index;
        this.adminArray = array;
        this.modal.editAdmin = true;
    }

    hideAllDrop(){
        this.bgVisible = true;
        this.data.forEach((item) =>{
            item.controlVisible = true
        })
    }
}