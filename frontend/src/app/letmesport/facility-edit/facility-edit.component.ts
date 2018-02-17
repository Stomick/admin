import {Component, ViewEncapsulation, OnInit, OnDestroy, ViewContainerRef} from '@angular/core';
import {Response} from '@angular/http';
import {Router, ActivatedRoute} from '@angular/router';
import {ToastsManager} from "ng2-toastr/ng2-toastr";
import 'rxjs/Rx';
import {FacilityEditService} from './facilities-edit.service';
import {Service} from "../models/service.model";
import {Playground} from "../models/playground.model";

@Component({
    selector: 'facility-edit',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'facility-edit.html',
    styles: ['facility-edit.scss'],
    providers: [FacilityEditService]
})

export class FacilityEditComponent implements OnInit, OnDestroy {
    public router: Router;
    data: any = '';
    advantagesList: any = [];
    services: Service[] = [];
    playgroundArray: Playground[] = [];
    playGroundIs: boolean = false;
    photoArray: any[] = [];
    logo: string = '';
    logoSrc: string = '';
    id: number;
    private sub: any;
    isAdmin: boolean;

    constructor(private _facilityEditService: FacilityEditService,
                private route: ActivatedRoute,
                router: Router,
                public toastr: ToastsManager,
                vcr: ViewContainerRef) {
        this.toastr.setRootViewContainerRef(vcr);

        this.router = router;
        if (JSON.parse(localStorage.getItem('currentUser')).role == 'super-admin') {
            this.isAdmin = true;
        }
    }

    parsePhotoData() {
        if (this.data.logoSrc == null) {
            this.data.logoSrc = '../../../assets/img/placeground-empty.png'
        }
        this.data.images.forEach(function (item) {
            if (item.src == null) {
                item.src = '../../../assets/img/placeground-empty.png';
            }
        })
    }

    unparsePhotoData() {
        if (this.logoSrc == '../../../assets/img/placeground-empty.png') {
            this.logoSrc = null
        }
        this.photoArray.forEach(function (item) {
            if (item.src == '../../../assets/img/placeground-empty.png') {
                item.src = null;
            }
        })
    }

    ngOnInit() {

        this.sub = this.route.params.subscribe(params => {
            this.id = +params['id'];
        });

        this._facilityEditService.getAll(this.id)
            .subscribe((data: Response) => {
                this.data = data.json();
                this.advantagesList = this.data.advantageIds;
                this.services = this.data.services;
                this.data.playingFields.forEach((item) => {
                    this.playgroundArray.push(new Playground(item))
                });
                // this.playgroundArray = this.data.playingFields;
                this.parsePhotoData();
                this.photoArray = this.data.images;
                this.logoSrc = this.data.logoSrc;
                this.logo = this.data.logo;
                if (this.playgroundArray.length > 0 && this.playgroundArray) {
                    this.playGroundIs = true
                }
            });
    }

    ngOnDestroy() {
        this.sub.unsubscribe();
        this.data = '';
    }

    saveLogo(event) {
        this.data.logoSrc = event[0];
        this.data.logo = event[1];
    }

    change() {
        this.data.serviceModels = this.data.services;
        this.data.playingFieldModels = this.playgroundArray;
        window.console.log(this.playgroundArray);
        let itemTimes= [];
        let i = 0;

        this.data.playingFieldModels.forEach((item) => {
            item.availableTimeModels = item.availableTimes;
            itemTimes[i++] = item.availableTimes;
        });

        for( let i = 0 ; i < this.data.playingFields.length ; i++)
        {
            this.data.playingFields[i].availableTimes = itemTimes[i];
        }

        let adminArray = [];
        this.data.admins.forEach((item) => {
            adminArray.push(item.id)
        });

        this.data.admins = adminArray;
        this.unparsePhotoData();

        this._facilityEditService.rewrite(this.id, this.data).subscribe(() => {
            this.toastr.success('Обновлено');
        }, (err) => {
            this.toastr.error(JSON.parse(err._body)[0].message, err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
        /*() => {
            this.router.navigate(['pages/facilities-list']);
        });*/
    }

    AddPlayGround() {
        var pl;
        pl = new Playground({
            name: '',
            collapse: false
        });
        window.console.log(pl);
        this.playgroundArray.push(pl);
        this.playGroundIs = true;
    }

    changeStatus(status) {
        this._facilityEditService.changeStatus(this.id, status).subscribe((response) => {
            this.toastr.success('Обновлено');
        }, (err) => {
            this.toastr.error(JSON.parse(err._body)[0].message, err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        }, () => {
            this.router.navigate(['pages/facilities-list']);
        })
    }

    removePlayground(index) {
        this.playgroundArray.splice(index, 1);
        this._facilityEditService.deletePlayfileds(this.id, this.data.playingFields[index].id).subscribe((response) => {
            window.console.log(response);
            this.toastr.success('Корт удалён');
        }, (err) => {
            this.toastr.error(JSON.parse(err._body)[0].message, err.statusText, {
                allowHtml: true,
                timeOut: 10000,
            });
        });
    }
}
