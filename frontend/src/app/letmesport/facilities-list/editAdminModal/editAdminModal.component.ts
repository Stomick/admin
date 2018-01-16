import { Component, ViewEncapsulation, Input, Output, EventEmitter, OnInit, OnDestroy, OnChanges } from '@angular/core';
import { EditAdminModalService } from './editAdminModal.service';

@Component({
    selector: 'modalEditAdmin',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'editAdminModal.html',
    styles: ['editAdminModal.scss'],
    providers: [EditAdminModalService]
})

export class EditAdminModalComponent implements OnInit, OnDestroy, OnChanges{
    adminsList: any = [];
    newAdminList: any = [];
    showList: boolean = false;

    @Input() adminArray: any;
    @Output() newAdmin = new EventEmitter();
    @Output() closeModal = new EventEmitter();

    constructor(private _editAdminModalService: EditAdminModalService){}

    ngOnInit(){
    }

    ngOnChanges(){
        this.getAdmins();
    }

    ngOnDestroy(){
        this.adminsList = [];
    }


    getAdmins(){
        if(this.adminArray == undefined){
            this.adminArray = [];
        }
        this._editAdminModalService.getAdmin()
            .subscribe((data) => {
                this.adminsList = data.items;
                this.adminsList.forEach((item) =>{
                   item.checked = false;
                });
                this.adminArray.forEach((item) =>{
                    this.adminsList.forEach((subitem) =>{
                        if(item.id == subitem.id){
                            subitem.checked = true;
                        }
                    });
                });
            });
    }

    hideModal(){
        this.closeModal.emit();
    }

    changeCheck(user){
        let findElem = false;
        this.adminArray.forEach((item, key) => {
            if(item.id == user.id){
                this.adminArray.splice(key, 1);
                findElem = true;
            }
        });
        if(!findElem){
            this.adminArray.push(user);
        }
    }

    removeAdmin(user){
        this.adminArray.forEach((item, key) => {
            if(item.id == user.id){
                this.adminArray.splice(key, 1);
            }
        });
        this.adminsList.forEach((item) => {
            if(item.id == user.id){
                item.checked = false;
            }
        })
    }

    editAdmin(){
        this.adminArray.forEach((item) =>{
            this.newAdminList.push(item.id);
        });
        this.newAdmin.emit(this.newAdminList);
        this.newAdminList = [];
    }
}