import {Component, ViewEncapsulation, OnInit, OnDestroy, OnChanges, Input} from "@angular/core";

@Component({
    selector: 'bookings',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'bookings.html',
})

export class BookingsComponent implements OnInit, OnDestroy, OnChanges{
    @Input() bookings: any;

    constructor(){}

    ngOnInit(){}
    ngOnChanges(){
        let monthNames = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня",
            "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"
        ];

        this.bookings.forEach((item)=>{
            let timeZone = new Date().getTimezoneOffset()*60*1000;
            let newDate = new Date(item.bookingDate*1000 + timeZone);
            let bookHour = newDate.getHours();
            item.parseDate = ' C ' + item.bookingDate.time[0] + ' До ' +  item.bookingDate.time[item.bookingDate.time.length - 1] + ' '+ item.bookingDate.date ;
        })
    }
    ngOnDestroy(){}
}