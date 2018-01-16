import { Component, ViewEncapsulation, Input, Output, EventEmitter, OnChanges } from '@angular/core';

@Component({
    selector: 'paginations',
    encapsulation: ViewEncapsulation.None,
    templateUrl: 'paginations.html',
    styles: ['./paginations.scss']
})

export class PaginationsComponent implements OnChanges{
    pages: number[];
    visible: boolean = true;

    @Input() infoPages: any;
    @Output() goToPage = new EventEmitter();

    ngOnChanges(){
        let currentPage = this.infoPages.currentPage;
        let pageCount = this.infoPages.pageCount;
        this.pages = [];
        switch (pageCount){
            case currentPage:
                switch (currentPage) {
                    case 1:
                        this.pages.push(currentPage);
                        break;
                    case 2:
                        this.pages.push(1, currentPage);
                        break;
                    case 3:
                        this.pages.push(1, 2, currentPage);
                    case 4:
                        this.pages.push(1, 2, currentPage - 1, currentPage);
                }
                break;
            case currentPage + 1:
                switch (currentPage) {
                    case 1:
                        this.pages.push(currentPage, pageCount);
                        break;
                    case 2:
                        this.pages.push(1, currentPage, pageCount);
                        break;
                    case 3:
                        this.pages.push(1, 2, currentPage, pageCount);
                        break;
                    default:
                        this.pages.push(1, 2, currentPage - 1, currentPage, pageCount);
                }
                break;
            case currentPage + 2:
                switch (currentPage) {
                    case 1:
                        this.pages.push(currentPage, currentPage + 1, pageCount);
                        break;
                    case 2:
                        this.pages.push(1, currentPage, currentPage + 1, pageCount);
                        break;
                    case 3:
                        this.pages.push(1, 2, currentPage, currentPage + 1, pageCount);
                        break;
                    default:
                        this.pages.push(1, 2, currentPage - 1, currentPage, currentPage + 1, pageCount);
                }
                break;
            default:
                switch (currentPage) {
                    case 1:
                        this.pages.push(currentPage, currentPage + 1, pageCount - 1, pageCount);
                        break;
                    case 2:
                        this.pages.push(1, currentPage, currentPage + 1, pageCount - 1, pageCount);
                        break;
                    case 3:
                        this.pages.push(1, 2, currentPage, currentPage + 1, pageCount - 1, pageCount);
                        break;
                    default:
                        this.pages.push(1, 2, currentPage - 1, currentPage, currentPage + 1, pageCount - 1, pageCount);
                }
        }
    }

    stepPage(targetPage){
        if(this.infoPages.currentPage != targetPage)
        this.goToPage.emit(targetPage);
    }
}