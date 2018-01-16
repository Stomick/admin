import { Component, ViewEncapsulation } from '@angular/core';

@Component({
  selector: '[google-maps]', 
  encapsulation: ViewEncapsulation.None,
  styleUrls: ['./google-maps.scss'],
  templateUrl: './google-maps.html'
})

export class GoogleMapsComponent{
    lat: number = 45.421530;
    lng: number = -75.697193;
    zoom: number = 7;
}