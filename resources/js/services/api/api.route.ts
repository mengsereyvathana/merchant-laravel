export enum ProductRoute {
    LIST_PRODUCT = "/product_scheme",
    PRODUCT_DETAIL = "/product_scheme_detail",
    PRODUCT_CATEGORY = "/product_scheme_by_category",
    SEARCH_PRODUCT = "/product/search",
}

export enum CategoryRoute {
    LIST_CATEGORY = "/category",
}

export enum SliderRoute {
    LIST_SLIDER = "/slide",
}

export enum OrderRoute {
    LIST_ORDERED = "/order",
    ADD_ORDER = "/order",
    ADD_ORDER_DETAIL = "/order_detail",
}

export enum CartRoute {
    LIST_CART = "/cart",
    ADD_TO_CART = "/add_to_cart",
    SUB_TO_CART = "/subtract_from_cart",
    DELETE_CART = "/delete_cart",
}

export enum UserRoute {
    LOGIN = "/login",
    LOGOUT = "/logout",
    GET_USER = "/user",
    REGISTER = "/create_user_with_phone",
    LOGIN_WITH_PHONE = "/log_with_phone",
}
