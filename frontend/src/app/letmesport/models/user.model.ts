import {UserBooking} from './userBooking.model'

export class User{
    id: number;
    name: string;
    bookings: UserBooking[] = [];
    sum: number;
    phone: string;
    date: number;
    showDrop: boolean;

    constructor(obj){
        this.id = obj.id || null;
        this.name = obj.name || '';
        this.phone = obj.phone || '';
        this.date = obj.reformatDate || 0;
        this.sum = 0;
        this.showDrop = false;

        if(obj.bookings && obj.bookings.length !=0 ){
            obj.bookings.forEach((item) => {
                this.sum += item.price;
                this.bookings.push(new UserBooking(item));
            });
        }
    }
}