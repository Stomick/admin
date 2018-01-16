export class Service{
    name: string;
    description: string;
    price: number;

    constructor(obj){
        this.name = obj.name || '';
        this.description = obj.description || '';
        this.price = obj.price || 0;
    }
}