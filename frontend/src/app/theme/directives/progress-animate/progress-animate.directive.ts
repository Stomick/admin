import { Directive, ElementRef } from '@angular/core';


@Directive ({
  selector: '[progress-animate]'
})

export class ProgressAnimate {
  public element: any;

  constructor(private _elementRef: ElementRef) {
    this.element = jQuery(_elementRef.nativeElement);
  }


 ngOnInit(): void { 
    let elem = this.element,
        progress = 0,   
        timeout = 0,     
        increment = 3, 
        maxprogress = elem.attr('value'); 
    function animate() {
        setTimeout(() => {
            progress += increment;
            if(progress < maxprogress) {
                elem.attr('value', progress);
                animate();
            }
        }, timeout);
    };
    animate();
  }

}
