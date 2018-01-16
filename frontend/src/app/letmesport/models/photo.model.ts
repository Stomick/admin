export class Photo{
    image: string;

    constructor(obj){
        this.image = obj.image || '../../../assets/img/placeground-empty.png';
    }
}