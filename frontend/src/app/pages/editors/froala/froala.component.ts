import { Component, ViewEncapsulation, OnInit } from '@angular/core';
import { FroalaEditorCompnoent } from 'ng2-froala-editor/ng2-froala-editor';
import './froala.loader.ts';

@Component({
  selector: 'froala-editor',
  encapsulation: ViewEncapsulation.None,
  templateUrl: './froala.html'
})

export class FroalaComponent implements OnInit{
  
  public text: string = '<div>Hey we are testing Froala Editor</div>';
  public editor: any;

  public froalaOptions: any = {
     height: 350
  };

  constructor() {

  }

  ngOnInit() {

  }

  onFroalaModelChanged(event: any) {
    setTimeout(() => {
      this.text = event;
    });
  }

  onEditorInitialized(event?: any) {
    this.editor = FroalaEditorCompnoent.getFroalaInstance();
    this.editor.on('froalaEditor.focus', (e, editor) => {
      console.log("editor is focused");
    });
  }

}
