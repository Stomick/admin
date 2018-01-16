import { Component, ViewEncapsulation } from '@angular/core';

@Component({
    selector: 'tabs-accordions',
    encapsulation: ViewEncapsulation.None,
    templateUrl: './tabs-accordions.html'
})

export class TabsAccordionsComponent  { 
     ngOnInit(): void {
        jQuery('.nav-tabs').on('shown.bs.tab', 'a', (e) => {
            if (e.relatedTarget) {
                jQuery(e.relatedTarget).removeClass('active');
            }
        });


        // jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        //     var target = jQuery(e.target).attr("href") // activated tab
        //     alert(target);
        // });

       
    }
}