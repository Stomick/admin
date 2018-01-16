export class Requisites{
    sportCenterName: string;
    sportCenterId: number;
    confirmationStatus: string;
    shortName: string;
    fullName: string;
    legalAddress: string;
    inn: string;
    kpp: string;
    ogrn: string;
    okpo: string;
    okato: string;
    okved: string;
    bank: string;
    bik: string;
    corrAccount: string;
    account: string;
    generalManager: string;
    email: string;
    webSite: string;
    fax: string;
    phone: string;
    payu_id: string;
    procent: number;

    constructor(obj){
        this.sportCenterName = obj.sportCenterName || '';
        this.sportCenterId = obj.sportCenterId || null;
        this.confirmationStatus = obj.confirmationStatus || '';
        this.shortName = obj.shortName || '';
        this.fullName = obj.fullName || '';
        this.legalAddress = obj.legalAddress || '';
        this.inn = obj.inn || '';
        this.kpp = obj.kpp || '';
        this.ogrn = obj.ogrn || '';
        this.okpo = obj.okpo || '';
        this.okato = obj.okato || '';
        this.okved = obj.okved || '';
        this.bank = obj.bank || '';
        this.bik = obj.bik || '';
        this.corrAccount = obj.corrAccount || '';
        this.account = obj.account || '';
        this.generalManager = obj.generalManager || '';
        this.email = obj.email || '';
        this.webSite = obj.webSite || '';
        this.fax = obj.fax || '';
        this.phone = obj.phone || '';
        this.payu_id = obj.payu_id || '';
        this.procent = obj.procent || 7.4;
    }

}