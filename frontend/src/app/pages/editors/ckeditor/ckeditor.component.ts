import { Component, ViewEncapsulation } from '@angular/core';

window['CKEDITOR_BASEPATH'] = '//cdn.ckeditor.com/4.6.0/standard/';
import "ckeditor";


@Component({
  selector: 'ckeditor-component',
  encapsulation: ViewEncapsulation.None,
  template: require('./ckeditor.html')
})

export class Ckeditor {
  public ckeditorContent:string;
  public config:any;

  constructor() {
    this.ckeditorContent = '<div>Hey we are testing CKEditor</div>';
    this.config = {
        uiColor: '#F0F3F4',
        height: '350'
    };
  }

  onChange(event: any) {
    setTimeout(() => {
      this.ckeditorContent = event;
    });
  }
  onReady(event: any) { }
  onFocus(event: any) { 
      console.log("editor is focused");
  }
  onBlur(event: any) { }



}
