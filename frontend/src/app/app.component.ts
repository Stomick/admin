import './app.loader.ts';

import { Component, ViewEncapsulation } from '@angular/core';

@Component({
    selector: 'app',
    encapsulation: ViewEncapsulation.None,
    styleUrls:['./app.scss'],
    template:'<router-outlet></router-outlet>'
})

export class AppComponent {}

