import { Component, ViewEncapsulation, Output, EventEmitter, OnInit, OnDestroy } from '@angular/core';
import { ModalService } from './modal.service';

@Component({
    selector: 'modalCreateFac',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'modal.html',
    styles: ['./modal.scss'],
    providers: [ModalService]
})

export class ModalComponent implements OnInit, OnDestroy{
    public modal: any;
    checkInput: boolean;
    public newFac: any = {
        name: '',
        admins: []
    };
    newAdminList : any = [];
    adminsList: any = [];
    showList: boolean = false;

    @Output() newFacility = new EventEmitter();

    constructor(private _modalService: ModalService){
        this.modal = {
            visible: false,
        }
    }

    ngOnInit(){
        this.getAdmins();
    }

    ngOnDestroy(){
        this.adminsList = [];
        this.newFac = {};
        this.newAdminList = [];
    }


    getAdmins(){
        this._modalService.getAdmin()
            .subscribe((data) => {
                this.adminsList = data.items;
            });
    }

    hideModal(){
        this.modal.visible = false;
        this.newFac.name = '';
        this.newFac.admins.splice(0, this.newFac.admins.length);
        this.newAdminList.splice(0, this.newAdminList.length);
        this.checkInput = false;
    }

    changeCheck(user){
        let findElem = false;
        this.newAdminList.forEach((item, key) => {
            if(item.id == user.id){
                this.newAdminList.splice(key, 1);
                findElem = true;
            }
        });
        if(!findElem){
            this.newAdminList.push(user);
        }
    }

    createFacility(){
        this.modal.visible = false;
        this.newAdminList.forEach((item) =>{
            this.newFac.admins.push(item.id);
        });
        this.newFacility.emit(this.newFac);
        this.newFac.name = '';
        this.newFac.admins.splice(0, this.newFac.admins.length);
        this.newAdminList.splice(0, this.newAdminList.length);
        this.checkInput = false;
    }
}