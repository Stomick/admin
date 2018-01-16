import * as moment from 'moment/moment';
import {
    Component,
    ViewEncapsulation,
    OnInit,
    OnDestroy,
    OnChanges,
    Input,
    Output,
    EventEmitter,
    ViewContainerRef
} from "@angular/core";

import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {TimeTable} from './../../models/timeTable.model';
import {TimetableGroundService} from './timetableGround.service';


@Component({
    selector: 'timetableGround',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'timetableGround.html',
    providers: [TimetableGroundService]
})

export class TimeTableGroundComponent implements OnInit, OnDestroy, OnChanges {
    minTimeWork: number;
    maxTimeWork: number;
    bgVisible: boolean = true;
    tableTimeArray: TimeTable[] = [];
    daysRequest: any[];
    timeZone: number = new Date().getTimezoneOffset() * 60 * 1000;
    deleteModal: number;
    modalDelete: boolean = false;
    userData: any;


    @Input() playgroundData: any;
    @Input() days: any;
    @Input() bookings: any;
    @Output() refreshList = new EventEmitter();


    constructor(public timetableGroundService: TimetableGroundService, public toastr: ToastsManager, vcr: ViewContainerRef) {
        this.toastr.setRootViewContainerRef(vcr);
        moment.locale('ru');

    }

    // HARDCODE
    closeBookingModal() {
        window['_'].forEach(this.daysRequest, (day) => {
            window['_'].forEach(day.workTime, (time) => {
                time.avalabTime.forEach((hour)=>{
                    hour.showModal = false;
                })
            });
        });
    }

    showTime(time, day) {
        return !(time.workDay.indexOf(day.type) == -1)
    }

    isBooking(time) {
        return time.booking != null;
    }

    isDisabled(time) {
        return time.disabled;
    }

    isEnabled(time) {
        return !this.isDisabled(time);
    }

    showBooking($event, day, hour) {
        $event.stopPropagation();
        if (hour.showModal) {
            this.closeBookingModal();
        } else {
            this.closeBookingModal();
            hour.showModal = !hour.showModal;
        }
    }

    processTime($event, day, time) {
        $event.preventDefault();
        $event.target.blur();
        const fieldId = this.playgroundData.playingFieldId;
        const hour = time.hour;
        const date = day.dateFormatted;

        this.timetableGroundService.block(fieldId, date, hour).subscribe(
            (response) => {
                console.log('response', response);
                time.disabled = !time.disabled;
            },
            (error) => {
                console.log('error', error);
            }
        );
    }

    // HARDCODE

    ngOnInit() {
    }

    ngOnChanges() {
        let beginDay = this.days.start.getTime();
        let endDay = this.days.end.getTime();
        let indexDay = 0;
        let monthNames = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня",
            "Июля", "Августа", "Сентября", "Октября", "Ноября", "Декабря"
        ];

        this.daysRequest = [];

        for (beginDay; beginDay <= endDay; beginDay += 24 * 60 * 60 * 1000) {
            let type: string;
            let date: any;
            let currentDate: string;
            date = new Date(beginDay);

            currentDate = date.getMonth();

            date.getDay() < 6 && date.getDay() > 0 ? type = 'work' : type = 'weekend';
            let tm = [];
            let timeArea = this.makeTimeArea();

            for (let i = 0, s = 0; i < timeArea.length; i++) {
                if (timeArea[i].type == type) {
                    tm[s] = this.checkUnavailableItem(moment(date).format('YYYY-MM-DD'), timeArea[i]);
                    s++;
                }
            }

            let nObjTime = new Object({
                id: indexDay,
                type: type,
                date: date.getDate() + ' ' + monthNames[date.getMonth()],
                dateFormatted: moment(date).format('YYYY-MM-DD'),
                dateDayFormatted: moment(date).format('DD MMMM, dddd'),
                workTime: tm
            });

            this.daysRequest.push(nObjTime);
            /*
            newTimeArray = [];

            this.bookings.forEach((item) => {
                let numberPhone = '+' + item.phone[0] + ' (' + item.phone.substr(1, 3) + ') ' + item.phone.substr(4);
                item.numberPhone = numberPhone;

                let bookTime = item.bookingDate * 1000 + this.timeZone;
                if (new Date(bookTime).getDate() == new Date(beginDay).getDate()) {
                    this.daysRequest[this.daysRequest.length - 1].workTime.forEach((time) => {
                        let currentDate = beginDay;
                        currentDate += time.hour * 60 * 60 * 1000;
                        if (bookTime == currentDate && item.playingFieldName == this.playgroundData.playingFieldName) {
                            time.booking = item;
                            time.showModal = false;
                        }
                    });
                }
            })
            */
            indexDay++;
        }
    }

    ngOnDestroy() {
    }

    makeTimeArea() {
        let timeAreas = [];

        for (let s = 0; s < this.playgroundData.availableTime.length; s++) {
            let start = this.playgroundData.availableTime[s].start_hour.split(':');
            let end = this.playgroundData.availableTime[s].end_hour.split(':');
            timeAreas[s] = {
                avaId: this.playgroundData.availableTime[s].id,
                plId: this.playgroundData.playingFieldId,
                avalabTime: [] = [{
                        showModal: false,
                        hour: '',
                        style : 't_grey',
                        bookId: 0
                    }],
                type: this.playgroundData.availableTime[s].type,
                price: this.playgroundData.availableTime[s].price
            };
            let min = parseInt(start[1]);
            let t = 0;

            for (let h = parseInt(start[0]); h < parseInt(end[0]); h++) {
                timeAreas[s].avalabTime[t] = {
                    hour: (h < 10 ? '0' + h + ':' + ((min == 30) ? '30' : '00') : h + ':' + ((min == 30) ? '30' : '00')),
                    style: 't_grey',
                    bookId: 0
                };
                if (t == 0 && min == 30) {
                    (min == 30) ? min = 0 : min = 30;
                    t++;
                    h++;
                    timeAreas[s].avalabTime[t] = {
                        hour: (h < 10 ? '0' + h + ':' + ((min == 30) ? '30' : '00') : h + ':' + ((min == 30) ? '30' : '00')),
                        style: 't_grey',
                        bookId: 0
                    };
                }
                (min == 30) ? min = 0 : min = 30;
                t++;
                timeAreas[s].avalabTime[t] = {
                    hour: (h < 10 ? '0' + h + ':' + ((min == 30) ? '30' : '00') : h + ':' + ((min == 30) ? '30' : '00')),
                    style: 't_grey',
                    bookId: 0
                };
                t++;
                (min == 30) ? min = 0 : min = 30;
            }
            timeAreas[s].avalabTime[t] = {
                hour: end.join(":"),
                style: 't_grey',
                bookId: 0
            };

        }
        return timeAreas;
    }

    checkUnavailableItem(date, arrTime) {
        let times = arrTime;

        this.bookings.forEach((bookitem) => {
            times.avalabTime.forEach((time) => {
                if (date == bookitem.bookingDate.date && bookitem.availableTimeId == times.avaId) {
                    bookitem.bookingDate.time.forEach((btime) => {
                        if (time.hour == btime) {
                            time.style = (bookitem.status)?'t_booking' :'t_blue';
                            time.bookId = bookitem;
                            window.console.log(time);
                        }
                    })
                }
            })
        });

        this.playgroundData.unavailableTimes.forEach((item) => {
            times.avalabTime.forEach((time) => {
                if (item.date == date) {
                    if (item.hour == time.hour) {
                        time.style = 't_cheked';
                    }

                }
            })
        });

        return times;
    }

    hideAllDrop() {
        this.bgVisible = true;
        this.daysRequest.forEach((item) => {
            item.workTime.forEach((time) => {
                time.showModal = false;
            })
        })
    }

    showDeleteModal(id) {
        this.deleteModal = id;
        this.modalDelete = true;
    }

    hideDeleteModal() {
        this.deleteModal = 0;
        this.modalDelete = false;
    }

    removeBook() {
        this.timetableGroundService.removeBooking(this.deleteModal).subscribe((response) => {
            this.toastr.success('Удалено');
            this.hideDeleteModal();
            this.refreshList.emit();
        }, (err) => {
            this.toastr.error(JSON.parse(err._body)[0].message, err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        })
    }

    timeBlock(e, fieldId, date) {
        if(!e.srcElement.classList.contains('t_booking')) {
            this.timetableGroundService.block(e, fieldId, date).subscribe((response) => {
                this.toastr.success('Обновленно');
            }, (err) => {
                this.toastr.error(JSON.parse(err._body)[0].message, err.statusText, {
                    allowHtml: true,
                    timeOut: 10000,
                });
            })
        }
    }
}