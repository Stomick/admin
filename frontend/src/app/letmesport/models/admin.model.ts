export class Admin{
    id: number;
    name: string;
    email: string;
    phone: string;
    date: string;

    constructor(obj){
        this.id = obj.id || null;
        this.name = obj.name || '';
        this.email = obj.email || '';
        this.phone = obj.phone || '';
        this.date = obj.date || '';
    }
}