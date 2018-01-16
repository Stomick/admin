import {Component, ViewEncapsulation, Output, EventEmitter} from '@angular/core';

@Component({
    selector: 'createAdvan',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'createAdvan.html',
})

export class CreateAdvan{
    public createVisible: boolean = false;
    public advanReq: any;
    newPoint: any = {
        id: null,
        name: '',
        icon: {
            url: '../../../../assets/img/camera-icon.png',
            value: null,
            title: ''
        }
    };
    response: any;

    @Output() newAdvan = new EventEmitter();
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
        this.createVisible = false;
    }

    sentPoint(){
        this.advanReq = {
            name: this.newPoint.name,
            icon: this.newPoint.icon.value,
            action: 'create'
        };
        this.newAdvan.emit(this.advanReq);
        this.resetInputData();
    }

    hideModal(){
        this.resetInputData();
        this.closeModal.emit();
    }
}