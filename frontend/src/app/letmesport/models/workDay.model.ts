export class WorkDay{
    hour: number;
    start_hour: string;
    end_hour: string;
    price: number;
    type: string;
    working: boolean;
    id: number;

    constructor(obj){
        this.hour = obj.hour || 0;
        this.start_hour = obj.start_hour || "00:00";
        this.end_hour = obj.end_hour || "24:00";
        this.price = obj.price || 0;
        this.type = obj.type || 'work';
        this.working = obj.working || false;
        this.id = obj.id || null;
    }
}