import {Component, ViewEncapsulation, OnInit, ViewContainerRef} from '@angular/core';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {UserListService} from './userList.service';
import { User } from './../models/user.model';

@Component({
    selector: 'userList',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'userList.html',
    providers: [UserListService]
})

export class UserListComponent implements OnInit{
    data: User[];
    infoPages: any = {};
    public modalDelete: boolean = false;
    public currentId: number = null;

    constructor(private _userListService:UserListService, public toastr: ToastsManager, vcr: ViewContainerRef){
        this.toastr.setRootViewContainerRef(vcr);
    }

    ngOnInit(){
        this.getList(1);
    }

    getList(page){
        this._userListService.getUser(page)
            .subscribe((data: any) => {
            console.log(data);
                this.data = data.items;
                this.infoPages = data.meta;
            });
    }

    showUserModal(index){
        this.data.forEach((item) => {
            item.showDrop = false;
        });
        this.data[index].showDrop = true;
    }

    hideUserModal(){
        this.data.forEach((item) => {
            item.showDrop = false;
        });
    }

    showDeleteModal(id){
        this.modalDelete = true;
        this.currentId = id;
    }

    public remove(id){
        this._userListService.removeUser(id).subscribe(() =>{
            this.toastr.success('Удалён');
            this.getList(this.infoPages.currentPage);
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