import {Component, ViewEncapsulation} from '@angular/core';

import {MessagesService} from './messages.service';

@Component({
    selector: 'messages',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./messages.scss'],
    templateUrl: './messages.html',
    providers: [MessagesService]
})

export class Messages{     
    public messages:Array<Object>;
    public notifications:Array<Object>;
    public tasks:Array<Object>;

    constructor (private _messagesService:MessagesService){
        this.messages = _messagesService.getMessages();
        this.notifications = _messagesService.getNotifications();
        this.tasks = _messagesService.getTasks();
    }

}