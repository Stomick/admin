import { Component, ViewEncapsulation } from '@angular/core';

@Component({
  selector: 'file-uploader',
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./file-uploader.scss'],
  templateUrl: './file-uploader.html'
})

export class FileUploaderComponent {
    public file:any;
  
    fileChange(input){
        const reader = new FileReader();
        if (input.files.length) {       
            this.file = input.files[0].name;            
        }
    }

    removeFile():void{
        this.file = '';
    }

}

 
