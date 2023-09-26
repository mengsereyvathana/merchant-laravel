import { createRouter, createWebHistory, RouteRecordRaw } from "vue-router";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import { Cookie } from "@/services/helper/index";
import { transition } from "@/store/transition";
import { Http } from "@/services/api/ApiDataService";
import { IUserDetail } from "@/types/UserDetail";
import { useStore } from "@/use/useStore";
import { AUTH_STORE } from "@/store/constants";
import { httpAuth } from "@/services/api/http.common";

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
            requireAuth: true,
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
            requireAuth: false,
        },
    },
    {
        path: "/register",
        name: "register",
        component: () => import("../views/auth/RegisterPage.vue"),
        meta: {
            requireAuth: false,
        },
    },
    {
        path: "/verify/:email",
        name: "verify",
        component: () => import("../views/auth/VerifyPage.vue"),
        meta: {
            requireAuth: false,
        },
    },
    {
        path: "/form_register/:number",
        name: "form_register",
        component: () => import("../views/auth/FormRegister.vue"),
        meta: {
            requireAuth: false,
        },
    },
    {
        path: "/:notFound(.*)*",
        component: () => import("../components/NotFoundComponent.vue"),
        meta: {
            requireAuth: false,
        },
    },
];

// const getCurrentUserWithEmail = async (): Promise<boolean> => {
//     try {
//         const response = await HttpAuth.get<IUserDetail>(`user`);
//         if (response) {
//             return true;
//         } else {
//             return false;
//         }
//     } catch (error) {
//         console.log(error);
//         return false;
//     }
// };

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
});

const getCurrentUserWithPhone = () => {
    return new Promise((resolve, reject) => {
        const removeListener = onAuthStateChanged(
            getAuth(),
            (user) => {
                removeListener();
                resolve(user);
            },
            reject
        );
    });
};

const getCurrentUserWithEmail = () => {
    return new Promise((resolve, reject) => {
        try {
            const httpAuth = new Http();
            httpAuth
                .get<IUserDetail>(`user`)
                .then((response) => {
                    if (response) {
                        resolve(response.data.data.phone);
                        return response.data.data.phone;
                    } else {
                        resolve(false);
                    }
                })
                .catch((error) => {
                    console.log(error);
                    resolve(false);
                });
        } catch (error) {
            console.log(error);
            resolve(false);
            reject;
        }
    });
};

router.beforeEach(async (to, from, next) => {
    const user_id = Cookie.get("session_id") || null;
    const token = Cookie.get("token") || null;
    const store = useStore();

    store.dispatch(AUTH_STORE.ACTIONS.SET_USER_ID, user_id);
    store.dispatch(AUTH_STORE.ACTIONS.SET_TOKEN, token);

    httpAuth.defaults.headers.common["Authorization"] = `Bearer ${token}`;

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
    if (to.matched.some((record) => record.meta.requireAuth)) {
        if (token) {
            if (store.getters[AUTH_STORE.GETTERS.GET_TOKEN] !== token) {
                if (
                    (await getCurrentUserWithPhone()) &&
                    (await getCurrentUserWithEmail())
                ) {
                    next();
                } else {
                    next("/phone_login");
                }
            } else {
                next();
            }
        } else {
            next("/phone_login");
        }
    } else {
        next();
    }
});

export default router;
