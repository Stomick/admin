import { Component, ViewEncapsulation, ViewContainerRef } from '@angular/core';
import { Router } from '@angular/router';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import { AuthGuard } from '../../pages/login/authGuard/authGuard.service';
import { FormGroup, FormControl, AbstractControl, FormBuilder, Validators} from '@angular/forms';
import {Observable} from 'rxjs/Observable';

@Component({
    selector: 'login',
    encapsulation: ViewEncapsulation.None,
    styleUrls: ['./login.scss'],
    templateUrl: './login.html',
    providers: [AuthGuard]
})

export class LoginComponent {
    public router: Router;
    public form:FormGroup;
    public email:AbstractControl;
    public password:AbstractControl;
    public user: any;

    constructor(router:Router, fb:FormBuilder, private _authGuard:AuthGuard, public toastr: ToastsManager, vcr: ViewContainerRef) {
        this.toastr.setRootViewContainerRef(vcr);

        this.router = router;

        if(localStorage.getItem('currentUser') != undefined && localStorage.getItem('currentUser') != null){
            this.router.navigate(['pages/facilities-list']);
        }

        this.form = fb.group({
            'email': ['', Validators.compose([Validators.required, emailValidator])],
            'password': ['', Validators.compose([Validators.required, Validators.minLength(5)])]
        });

        this.email = this.form.controls['email'];
        this.password = this.form.controls['password'];
    }

    public onSubmit():void {
        if (this.form.valid) {
            this._authGuard.auth(this.form.value['email'], this.form.value['password'])
                .subscribe((data) =>
                {
                    this.user = data;
                    localStorage.setItem('currentUser', JSON.stringify(this.user));
                    this.router.navigate(['pages/facilities-list']);

                }, (err)=>{
                    this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                        allowHtml: true,
                        timeOut: 10000,
                    });
                })
        }
    }
}


export function emailValidator(control: FormControl): {[key: string]: any} {
    var emailRegexp = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/;    
    if (control.value && !emailRegexp.test(control.value)) {
        return {invalidEmail: true};
    }
}