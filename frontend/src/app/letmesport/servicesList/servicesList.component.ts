import { Component, ViewEncapsulation, Input} from '@angular/core';
import {Service} from "../models/service.model";

@Component({
    selector: 'servicesList',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'servicesList.html',
    styles: ['servicesList.scss'],
})

export class ServiceListComponent{
    id: number;
    isAdmin: boolean;

    @Input() services: Service[];

    constructor(){
        if(JSON.parse(localStorage.getItem('currentUser')).role ==  'super-admin'){
            this.isAdmin = true;
        }
    }

    addService(){
        let newService = new Service(0);
        this.services.push(newService);
    }

    removeService(id){
        this.services.splice(id,1);
    }
}