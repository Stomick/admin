import { Component, ViewEncapsulation, Output, EventEmitter} from '@angular/core';
import {Admin} from "../../models/admin.model";

@Component({
    selector: 'adminCreateModal',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'adminCreateModal.html'
})

export class AdminCreateComponent{
    newAdmin: Admin = new Admin({});
    modal: boolean = false;

    @Output() sentNewAdmin = new EventEmitter();

    constructor(){}

    createAdmin(){
        this.modal = false;
        this.sentNewAdmin.emit(this.newAdmin);
    }
}