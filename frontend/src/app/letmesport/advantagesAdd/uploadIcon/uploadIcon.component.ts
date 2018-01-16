import { Component, ViewEncapsulation, Input, OnChanges, ViewContainerRef } from '@angular/core';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import { Http, Headers, RequestOptions } from '@angular/http';
import 'rxjs/Rx';

@Component({
    selector: 'uploadIcon',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'uploadIcon.html',
})

export class UploadIcon implements OnChanges{
    uploadRes: any;
    userData: any;

    @Input() newPointIcon: any;

    constructor(private _http:Http, public toastr: ToastsManager, vcr: ViewContainerRef){
        this.toastr.setRootViewContainerRef(vcr);
        this.userData = JSON.parse(localStorage.getItem('currentUser'));
    }

    ngOnChanges(){}

    fileChange(event) {
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

            this._http.post(API_PATH + 'advantages/upload-icon', formData, options)
                .subscribe(
                    (data) => {
                        this.toastr.success('Загружено');
                        this.uploadRes = data.json();
                        this.newPointIcon.url = this.uploadRes.file.url;
                        this.newPointIcon.value = this.uploadRes.file.value;
                        this.newPointIcon.title = this.uploadRes.file.name;
                    },
                    (err) => {
                        this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                            allowHtml: true,
                            timeOut: 10000,
                        });
                    }
                )
        }
    }
}