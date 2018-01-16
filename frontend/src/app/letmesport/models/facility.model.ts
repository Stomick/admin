export class Facility{
    id: number;
    name: string;
    admins: any;
    adminModels: any;
    confirmationStatus: string;
    active: string;
    controlVisible: boolean;
    companyDetails: any;

    constructor(obj){
        this.id = obj.id || null;
        this.name = obj.name || 'Без имени';
        this.adminModels = obj.admins || [];
        this.admins = [];
        this.controlVisible = true;
        this.companyDetails = obj.companyDetails || {};

        switch (obj.confirmationStatus){
            case 'false':{
                this.confirmationStatus = 'Не прошли проверку';
                break;
            }
            case 'true':{
                this.confirmationStatus = 'Одобрено';
                break;
            }
            case 'empty':{
                this.confirmationStatus = 'Не заполнены';
                break;
            }
            case 'waiting':{
                this.confirmationStatus = 'Ожидается проверка';
                break;
            }
            default:{
                this.confirmationStatus = 'Не заполнены';
                break;
            }
        }

        switch (obj.approvementStatus){
            case 'not active':{
                this.active = 'Не опубликована';
                break;
            }
            case 'active':{
                this.active = 'Опубликована';
                break;
            }
            case 'moder':{
                this.active = 'Запрос на публикацию';
                break;
            }
            default:{
                this.active = 'Не опубликована';
                break;
            }
        }
    }
}