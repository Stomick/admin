import { Component, ViewEncapsulation, ChangeDetectorRef } from '@angular/core';

@Component({
  selector: 'multiple-image-uploader',
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./multiple-image-uploader.scss'],
  templateUrl: './multiple-image-uploader.html'
})

export class MultipleImageUploaderComponent { 
    public images: string[] = [];
  
    constructor( private changeDetectorRef: ChangeDetectorRef ) {
    }    
    
    fileChange(input){
        this.readFiles(input.files);
    }
    
    readFile(file, reader, callback){
        reader.onload = () => {
            callback(reader.result);
        }
        reader.readAsDataURL(file);
    }
    
    readFiles(files, index=0){
        let reader = new FileReader();
        
        if (index in files){
            this.readFile(files[index], reader, (result) =>{
                this.images.push(result);
                this.readFiles(files, index+1);
            });
        }else{
            this.changeDetectorRef.detectChanges();
        }
    }

    removeImage(index):void{
        this.images.splice(index, 1);
    }

}

 
