import { Component, ViewEncapsulation } from '@angular/core';
import { IconsService } from './icons.service';

@Component({
  selector: 'icons',  
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./icons.scss'],
  templateUrl: './icons.html',
  providers: [IconsService]
})

export class IconsComponent {
    public bgColor:string;
    public icons:any;

    constructor(private _iconsService:IconsService){
        this.icons = _iconsService.getAll();
    }    

    public changeBg(param):void {
        this.bgColor = param;
    }

}
