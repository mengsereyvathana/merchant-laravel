export enum UserRoute {
    LOGIN = "login",
    LOGOUT = "logout",
    GET_USER = "user",
    REGISTER = "create_user_with_phone",
    LOGIN_WITH_PHONE = "log_with_phone",
}

export enum ProductRoute {
    LIST_PRODUCT = "user_scheme_price_list",
    PRODUCT_DETAIL = "user_scheme_price_list_detail",
    PRODUCT_CATEGORY = "user_scheme_price_list_by_category",
    SEARCH_PRODUCT = "search",
}

export enum CategoryRoute {
    LIST_CATEGORY = "list_category",
}

export enum SliderRoute {
    LIST_SLIDER = "list_slide",
}

export enum OrderRoute {
    LIST_ORDERED = "list_ordered",
    ADD_ORDER = "order",
    ADD_ORDER_DETAIL = "order_detail",
}

export enum CartRoute {
    LIST_CART = "list_cart",
    ADD_TO_CART = "add_to_cart",
    SUB_TO_CART = "sub_to_cart",
    DELETE_CART = "delete_cart",
}
