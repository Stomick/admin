import * as moment from 'moment/moment';

export class TimeTable{
    id: number;
    type: string;
    hour: number;
    formattedHour: string;
    warning: boolean;
    booking: any;
    showModal: boolean;
    workDay: string[];
    disabled: boolean;

    constructor(obj){
        this.id = obj.id || null;
        this.type = obj.type || '';
        this.hour = obj.hour || 0;
        this.formattedHour = moment(obj.hour, 'H').format('HH:mm') || moment(0, 'H').format('HH:mm');
        this.warning = obj.warning || false;
        this.booking = null;
        this.workDay = obj.workDay || [];
        this.showModal = false;
        this.disabled = obj.disabled || false;
    }
}