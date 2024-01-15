import "./assets/main.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { useAuthStore } from "@/stores/auth";
import '../node_modules/nprogress/nprogress.css' 



import NavBar from "@/components/NavBar.vue";

import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.component("nav-bar", NavBar);

app.use(createPinia());
app.use(router);

// useAuthStore()
//   .getUser()
//   .then(() => {
    app.mount("#app");
  // });
