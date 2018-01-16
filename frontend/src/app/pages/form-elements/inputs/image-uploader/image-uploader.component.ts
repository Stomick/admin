import { Component, ViewEncapsulation } from '@angular/core';

@Component({
  selector: 'image-uploader',
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./image-uploader.scss'],
  templateUrl: './image-uploader.html'
})

export class ImageUploaderComponent {
    public image:any;
  
    fileChange(input){
        const reader = new FileReader();
        if (input.files.length) {
            const file = input.files[0];
            reader.onload = () => {
                this.image = reader.result;
            }
            reader.readAsDataURL(file);           
        }
    }

    removeImage():void{
        this.image = '';
    }

}

 
