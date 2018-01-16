import {Component, ViewEncapsulation, OnInit, OnDestroy, ViewContainerRef} from "@angular/core";
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import {Requisites} from "./../models/requisites.model";
import { Router, ActivatedRoute } from '@angular/router';
import { RequisitesService } from "./requisites.service";


@Component({
    selector: 'requisites',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'requisites.html',
    styles: ['requisites.scss'],
    providers: [RequisitesService]
})

export class RequisitesComponent implements OnInit, OnDestroy{
    public router: Router;
    private sub: any;
    public req: any = {
        shortName: '',
        fullName: ''
    };
    public name: string;
    id: number;
    public isAdmin: boolean = false;

    constructor(private route: ActivatedRoute, router:Router, private _requisitesService: RequisitesService, public toastr: ToastsManager, vcr: ViewContainerRef){
        this.router = router;
        this.toastr.setRootViewContainerRef(vcr);

        if(localStorage.getItem('currentUser') != undefined && localStorage.getItem('currentUser') != null){
            if(JSON.parse(localStorage.getItem('currentUser')).role ==  'super-admin'){
                this.isAdmin = true;
            }
        }
    }

    ngOnInit(){
        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id'];
            console.log(this.id);
        });

        this._requisitesService.getAll(this.id).subscribe((data) => {
            this.req = new Requisites(data);
        })
    }

    ngOnDestroy(){}

    sendReq(){
        this._requisitesService.editReq(this.id, this.req).subscribe((resp) => {
            this.toastr.success('Отправлено на проверку');
        }, (err) => {
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        }, () => {
            this.router.navigate(['pages/facilities-list']);
        })
        if(!this.isAdmin){
            this.changeStatus(true);
        }
    }

    changeStatus(status){
        this._requisitesService.changeStatus(this.req.sportCenterId, status).subscribe(() =>{
            this.toastr.success('Обновлено');

        }, (err)=>{
            this.toastr.error(JSON.parse(err._body)[0].message , err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        }, () => {
                this.router.navigate(['pages/facilities-list']);
            })
    }
}