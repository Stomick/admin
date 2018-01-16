import {WorkDay} from './workDay.model';

export class Playground{
    id: number;
    name: string;
    info: string;
    availableTimes: WorkDay[] = [];
    collapse: boolean;

    constructor(obj){
        this.id = obj.id || null;
        this.info = obj.info || null;
        this.name = obj.name || '';
        this.collapse = obj.collapse || true;
        if(obj.availableTimes && obj.availableTimes.length !=0 )
            obj.availableTimes.forEach((item) => {
                this.availableTimes.push(new WorkDay(item));
        });
    }
}