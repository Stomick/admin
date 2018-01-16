import { Component, ViewEncapsulation, Output, EventEmitter, Input, OnChanges } from '@angular/core';

@Component({
    selector: 'editAdvan',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'editAdvan.html',
})

export class EditAdvan implements OnChanges{
    public editVisible: boolean = false;
    public advanReq: any;
    newPoint: any;
    response: any;

    ngOnChanges(){
        this.newPoint = {
            id: this.editElem.id,
            name: this.editElem.name,
            icon: {
                url: this.editElem.iconSrc,
                value: this.editElem.icon,
                title: this.editElem.iconName,
            }
        }
    }


    @Input() editElem: any;
    @Output() editItem = new EventEmitter();
    @Output() closeModal = new EventEmitter();

    constructor(){}

    resetInputData(){
        this.newPoint = {
            id: null,
            name: '',
            icon: {
                url: '../../../../assets/img/camera-icon.png',
                value: null,
                title: ''
            }
        };
        this.editVisible = false;
    }

    sentPoint(){
        this.advanReq = {
            id: this.newPoint.id,
            name: this.newPoint.name,
            icon: this.newPoint.icon.value,
            action: 'edit'
        };
        this.editItem.emit(this.advanReq);
        this.editVisible = false;
        this.resetInputData();
    }

    hideModal(){
        this.closeModal.emit();
    }
}