import { Component, ViewEncapsulation } from '@angular/core';

@Component({
  selector: 'components',  
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./components.scss'],
  templateUrl: './components.html'
})

export class ComponentsComponent {    

    ngOnInit(): void {
        jQuery('[data-toggle="tooltip"]').tooltip();
        jQuery('[data-toggle="popover"]').popover();
    }
}
