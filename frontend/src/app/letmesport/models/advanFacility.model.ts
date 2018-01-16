export class AdvanFacility{
    id: number;
    name: string;
    check: boolean;
    iconSrc: string;

    constructor(obj){
        this.id = obj.id || null;
        this.name = obj.name || 'новая';
        this.iconSrc = obj.iconSrc || null;
        this.check = false;
    }
}