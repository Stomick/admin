import { Component, ViewEncapsulation, OnInit, OnDestroy, OnChanges, Input, Output, EventEmitter, ViewContainerRef } from '@angular/core';
import { Http, Headers, RequestOptions } from '@angular/http';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import 'rxjs/Rx';
import 'rxjs/add/operator/map';

@Component({
    selector: 'photo-upload',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'photo-upload.html',
    styles: ['./photo-upload.scss']
})

export class PhotoUploadComponent implements OnInit, OnChanges, OnDestroy{
    response: any;
    userData: any;
    isAdmin: boolean;
    photoSectionVisible = false;

    @Input() photoArray: any[];
    @Input() logoSrc: string;
    @Input() logo: string;
    @Output()
    newLogo:EventEmitter<any> = new EventEmitter();

    constructor(private _http:Http, public toastr: ToastsManager, vcr: ViewContainerRef){

        this.toastr.setRootViewContainerRef(vcr);

        this.userData = JSON.parse(localStorage.getItem('currentUser'));

        if(this.userData.role ==  'super-admin'){
            this.isAdmin = true;
        }
    }

    ngOnInit(){}
    ngOnChanges(){
        this.photoSectionVisible = false;

        this.photoArray.forEach((item) => {
            if(item.src != '../../../assets/img/placeground-empty.png')
                this.photoSectionVisible = true;
        })

        if (this.logoSrc != '../../../assets/img/placeground-empty.png')
            this.photoSectionVisible = true;
    }
    ngOnDestroy(){}

    addPhoto(){
        this.photoArray.push(new Object({
                src: '../../../assets/img/placeground-empty.png',
                value: null
        }));
    }

    fileChange(event, id) {
        let fileList: FileList = event.target.files;
        if (fileList.length > 0) {
            let file: File = fileList[0];
            let formData: FormData = new FormData();
            formData.append('file', file, file.name);
            let headers = new Headers();
            headers.delete('Content-Type');
            headers.append('Accept', 'application/json');
            headers.append('Authorization', 'Bearer ' + this.userData.token);
            let options = new RequestOptions({headers: headers});
            if (id == 'logo') {
                this._http.post(API_PATH + 'sport-centers/upload-logo', formData, options)
                    .subscribe(
                        (data) => {
                            this.toastr.success('Загружено');
                            this.response = data.json();
                            this.logoSrc = this.response.file.url;
                            this.logo = this.response.file.value;
                            this.newLogo.emit([this.logoSrc, this.logo]);
                        },
                        (err) => {
                            console.log(JSON.parse(err._body));
                            // this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                            //     allowHtml: true,
                            //     timeOut: 10000,
                            // });
                        },
                    )
            } else {
                this._http.post(API_PATH + 'sport-centers/upload-image', formData, options)
                    .subscribe(
                        (data) => {
                            this.toastr.success('Загружено');
                            this.response = data.json();
                            this.photoArray[id].src = this.response.file.url;
                            this.photoArray[id].value = this.response.file.value;
                        },
                        (err) => {
                            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                                allowHtml: true,
                                timeOut: 10000,
                            });
                        },
                        () => {
                            setTimeout(5000);
                        }
                    )
            }
        }
    }

    removePhoto(id){
        this.photoArray.splice(id, 1);
    }

}