import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import { Cookie } from "@/services/helper/index";
import { transition } from "@/store/transition";
import { Http } from "@/services/api/api.service";
import { IUserDetail } from "@/types/UserDetail";
import { useStore } from "@/use/useStore";
import { AUTH_STORE } from "@/store/constants";
import { adminAuthService } from '@/admin/service/api/modules/auth-admin.api';

const routes: Array<RouteRecordRaw> = [
    {
        path: "/",
        component: () => import("../views/PublicMain.vue"),
        children: [
            {
                path: "/",
                name: "home",
                component: () => import("../views/HomePage.vue"),
            },
            {
                path: "/my_account",
                name: "my_account",
                component: () => import("../views/AccountPage.vue"),
            },
            {
                path: "/cart",
                name: "cart",
                component: () => import("../views/CartPage.vue"),
            },
            {
                path: "/my_order",
                name: "my_order",
                component: () => import("../views/OrderPage.vue"),
            },
            {
                path: "/contact",
                name: "contact",
                component: () => import("../views/ContactPage.vue"),
            },
            {
                path: "/category",
                name: "category",
                component: () => import("../views/CategoryPage.vue"),
            },
            {
                path: "/product_detail/:id",
                name: "product_detail",
                component: () => import("../views/ProductDetailPage.vue"),
            },
            {
                path: "/product_category/:id",
                name: "product_category",
                component: () => import("../views/ProductCategoryPage.vue"),
            },
        ],
        meta: {
            userAuth: true,
        },
    },
    {
        path: "/admin",
        component: () => import("../admin/views/AdminView.vue"),
        children: [
            {
                path: "/admin/",
                name: "dashboard",
                component: () => import("../admin/views/DashboardView.vue"),
            },
            // Product
            {
                path: "/admin/add_product",
                name: "add_product",
                component: () => import("../admin/views/AddProductView.vue"),
            },
            {
                path: "/admin/edit_product/:id",
                name: "edit_product",
                component: () => import("../admin/views/EditProductView.vue"),
            },
            {
                path: "/admin/show_product",
                name: "show_product",
                component: () => import("../admin/views/ProductView.vue"),
            },
            // Product Scheme
            {
                path: "/admin/add_product_scheme/:id",
                name: "add_product_scheme",
                component: () => import("../admin/views/AddProductSchemeView.vue"),
            },
            {
                path: "/admin/show_product_scheme",
                name: "show_product_scheme",
                component: () => import("../admin/views/ProductSchemeView.vue"),
            },
            {
                path: "/admin/edit_product_scheme/:id",
                name: "edit_product_scheme",
                component: () => import("../admin/views/EditProductSchemeView.vue"),
            },
            // Slideshow
            {
                path: "/admin/show_slideshow",
                name: "show_slideshow",
                component: () => import("../admin/views/SlideshowView.vue"),
            },
            {
                path: "/admin/add_slideshow",
                name: "add_slideshow",
                component: () => import("../admin/views/AddSlideshowView.vue"),
            },
            {
                path: "/admin/edit_slideshow/:id",
                name: "edit_slideshow",
                component: () => import("../admin/views/EditSlideshowView.vue"),
            },
            // Category
            {
                path: "/admin/add_category",
                name: "add_category",
                component: () => import("../admin/views/AddCategoryView.vue"),
            },
            {
                path: "/admin/show_category",
                name: "show_category",
                component: () => import("../admin/views/CategoryView.vue"),
            },
            {
                path: "/admin/edit_category/:id",
                name: "edit_category",
                component: () => import("../admin/views/EditCategoryView.vue"),
            },
            // Order
            {
                path: "/admin/show_order_delivered",
                name: "show_order_delivered",
                component: () => import("../admin/views/OrderDeliveredView.vue"),
            },
            //Profile
            {
                path: "/admin/show_profile",
                name: "show_profile",
                component: () => import("../admin/views/ProfileVIew.vue"),
            },
        ],
        meta: {
            adminAuth: true,
        },
    },
    // {
    //     path: "/login",
    //     name: "login",
    //     component: () => import("../views/auth/LoginPage.vue"),
    //     meta: {
    //         requireAuth: false,
    //     },
    // },
    {
        path: "/phone_login",
        name: "phone_login",
        component: () => import("../views/auth/PhoneLoginPage.vue"),
        meta: {
            userAuth: false,
        },
    },
    {
        path: "/register",
        name: "register",
        component: () => import("../views/auth/RegisterPage.vue"),
        meta: {
            userAuth: false,
        },
    },
    {
        path: "/verify/:email",
        name: "verify",
        component: () => import("../views/auth/VerifyPage.vue"),
        meta: {
            userAuth: false,
        },
    },
    {
        path: "/form_register/:number",
        name: "form_register",
        component: () => import("../views/auth/FormRegister.vue"),
        meta: {
            userAuth: false,
        },
    },

    //admin

    {
        path: "/admin/login",
        name: "adminLogin",
        component: () => import("../admin/views/auth/LoginView.vue"),
        meta: {
            adminAuth: false,
        },
    },

    {
        path: "/:notFound(.*)*",
        component: () => import("../components/NotFoundComponent.vue"),
        meta: {
            userAuth: false,
        },
    },
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

const getCurrentUserWithFirebase = () => {
    return new Promise((resolve, reject) => {
        const removeListener = onAuthStateChanged(getAuth(), (user) => {
            removeListener();
            resolve(user?.phoneNumber);
        },
            reject,
        );
    });
};

const getCurrentUserWithToken = () => {
    return new Promise((resolve, reject) => {
        const httpAuth = new Http();
        httpAuth.get<IUserDetail>("user", true).then((response) => {
            if (response) {
                resolve(response.data.data.phone);
            } else {
                reject;
            }
        }).catch((error) => {
            Cookie.delete('token');
            Cookie.delete('session_id');
            console.log(error);
            reject;
        });
    });
};

let isAuth = false;

router.beforeEach(async (to, from, next) => {
    const user_id = Cookie.get("session_id") || null;
    const token = Cookie.get("token") || null;
    const store = useStore();

    store.dispatch(AUTH_STORE.ACTIONS.SET_USER_ID, user_id);

    if (token && isAuth === false) {
        if (await getCurrentUserWithFirebase() === await getCurrentUserWithToken()) {
            isAuth = true;
        }
    }

    let toDepth = to.path.split("/").length;
    let fromDepth = from.path.split("/").length;

    if (to.path == "/") {
        toDepth -= 1;
    }
    if (from.path == "/") {
        fromDepth -= 1;
    }
    const transitionTo = toDepth < fromDepth ? "slide-right" : "slide-left";
    transition.value = transitionTo;

    if (to.matched.some(record => record.meta.adminAuth)) {
        if (await adminAuthService.isAuthenticated()) {
            next();
        } else {
            next("admin/login")
        }
    } else {
        if (to.matched.some(record => record.meta.userAuth)) {
            if (token && isAuth) {
                next();
            } else {
                next("/phone_login");
            }
        } else {
            next();
        }
    }
});

export default router;
