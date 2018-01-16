import { Component, ViewEncapsulation } from '@angular/core';
import { DynamicTablesService } from './dynamic-tables.service';

@Component({
  selector: 'dynamic-tables',
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./dynamic-tables.scss'],
  templateUrl: './dynamic-tables.html',
  providers: [DynamicTablesService]
})

export class DynamicTablesComponent {
    public data: any;

    constructor(private _dynamicTablesService:DynamicTablesService){
        this.data = _dynamicTablesService.getAll();
    } 

}
