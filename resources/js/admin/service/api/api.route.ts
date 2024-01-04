export enum ProductRoute {
    GET_ALL = "/admin/product",
    GET = "/admin/product",
    GET_ALL_BY_CATEGORY = "/admin/product_by_category",
    SEARCH = "/admin/product/search",
    CREATE = "/admin/product",
    EDIT = "/admin/product/",
    DELETE = "/admin/product/",
}

export enum ProductSchemeRoute {
    GET_ALL = "/admin/product_scheme",
    GET = "/admin/product_scheme",
    SEARCH = "/admin/product_scheme/search",
    CREATE = "/admin/product_scheme",
    EDIT = "/admin/product_scheme/",
    DELETE = "/admin/product_scheme/",
}

export enum SlideshowRoute {
    GET_ALL = "/admin/slide",
    GET = "/admin/slide",
    SEARCH = "/admin/slide/search",
    CREATE = "/admin/slide",
    EDIT = "/admin/slide/",
    DELETE = "/admin/slide",
}

export enum CategoryRoute {
    GET_ALL = "/admin/category",
    GET = "/admin/category/",
    SEARCH = "/admin/category/search",
    CREATE = "/admin/category",
    EDIT = "/admin/category/",
    DELETE = "/admin/category/",
}

export enum OrderRoute {
    GET_ALL = "/admin/order",
    SEARCH = "/admin/order/search",
}

export enum AuthRoute {
    LOGIN = "/admin/login",
    LOGOUT = "/admin/logout",
    USER = "/user",
}
