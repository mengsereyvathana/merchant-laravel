import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";

import "vuetify/styles";
import '@mdi/font/css/materialdesignicons.css'
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import router from "./router";
import { store } from "./store";

import firebaseConfig from "@/config/firebase.config";
import { initializeApp } from "firebase/app";
import "firebase/auth";

import { rippleInit } from "@/directive/index";

import "../css/app.css";
import "sweetalert2/dist/sweetalert2.css";
import Swal from "sweetalert2";

const vuetify = createVuetify({
    components,
    directives,

});

initializeApp(firebaseConfig);

window.Swal = Swal;

const app = createApp(App).use(vuetify).use(store).use(router);

app.directive("ripple-init", rippleInit);

app.mount("#app");
