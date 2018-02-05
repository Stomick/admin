export class UserBooking{
    id: number;
    year: number;
    sportCenterName: string;
    month: number;
    day: number;
    hour: number;
    start_hour: string;
    end_hour: string;
    playingFieldName: string;
    services: any;
    price: number;

    constructor(obj){
        this.id = obj.id || null;
        this.year = obj.year || 0;
        this.month = obj.month || 0;
        this.day = obj.day || 0;
        this.hour = obj.hour || 0;
        this.playingFieldName = obj.playingFieldName || '';
        this.services = obj.services || null;
        this.price = obj.price || 0;
        this.start_hour = obj.start_hour || '';
        this.end_hour = obj.end_hour || '';
        this.sportCenterName = obj.sportCenterName || '';
    }
}