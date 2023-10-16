export enum ProductRoute {
    GET_ALL = "/admin/list",
    GET = "detail_list",
    GET_ALL_BY_CATEGORY = "user_scheme_price_list_by_category",
    SEARCH = "/admin/search_product",
    CREATE = "/admin/add_list",
    EDIT = "/admin/update_list/",
    DELETE = "delete_list",
}

export enum ProductSchemeRoute {
    GET_ALL = "/admin/user_scheme_price_list",
    GET = "/admin/user_scheme_price_list_detail",
    SEARCH = "/admin/search_scheme",
    CREATE = "/admin/user_scheme_price_list_add",
    EDIT = "/admin/user_scheme_price_list_update",
    DELETE = "/admin/user_scheme_price_list_delete",
}

export enum SlideshowRoute {
    GET_ALL = "/admin/list_slide",
    GET = "/admin/detail_slide",
    SEARCH = "/admin/search_slide",
    CREATE = "/admin/add_slide",
    EDIT = "/admin/update_slide/",
    DELETE = "/admin/delete_slide",
}

export enum CategoryRoute {
    GET_ALL = "/admin/list_category",
    GET = "/admin/detail_category/",
    SEARCH = "/admin/search_category",
    CREATE = "/admin/add_category",
    EDIT = "/admin/update_category/",
    DELETE = "/admin/delete_category/",
}

export enum OrderRoute {
    GET_ALL = "/admin/list_ordered",
    SEARCH = "/admin/search_order",
    CREATE = "",
    EDIT = "",
    DELETE = "",
}

export enum AuthRoute {
    LOGIN = "/admin/login",
    LOGOUT = "/admin/logout",
    USER = "/user",
}