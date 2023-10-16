export class Pagination {
    private pageNumber: number;
    private itemsPerPage: number;
    private totalItems: number;
    private totalPages: number;
    constructor(pn: number, ipp: number, ti: number, tp: number) {
        this.pageNumber = pn;
        this.itemsPerPage = ipp;
        this.totalItems = ti;
        this.totalPages = tp;
    }
    setPagination() {

    }
}