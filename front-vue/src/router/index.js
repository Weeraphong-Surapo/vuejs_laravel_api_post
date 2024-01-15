import { createRouter, createWebHistory } from "vue-router";
import defaultLayout from "@/layouts/defaultLayout.vue";
import { useAuthStore } from "@/stores/auth";
import NProgress from 'nprogress';


const RoutePublic = [
  {
    path: "/",
    name: "home",
    component: () => import("@/views/HomeView.vue"),
  },
  {
    path: "/auth/login",
    name: "login",
    component: () => import("@/views/auth/login.vue"),
  },
  {
    path: "/auth/register",
    name: "register",
    component: () => import("@/views/auth/register.vue"),
  },
];

const RouteSecret = [
  {
    path: "/user/my-post",
    name: "mypost",
    component: () => import("@/views/user/Mypost.vue"),
  },
  {
    path: "/user/my-account",
    name: "myaccount",
    component: () => import("@/views/user/Myaccount.vue"),
  },
  {
    path: "/edit-post/:id",
    name: "editpost",
    component: () => import("@/views/user/Editpost.vue"),
  },
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      redirect: "/",
      component: defaultLayout,
      children: RoutePublic,
      meta: {
        requiresAuth: false,
      },
    },
    {
      path: "/",
      redirect: "/",
      component: defaultLayout,
      children: RouteSecret,
      meta: {
        requiresAuth: true,
      },
    },
  ],
});

router.beforeResolve((to, from, next) => {
  // If this isn't an initial page load.
  if (to.name) {
      // Start the route progress bar.
      NProgress.start()
  }
  next()
})

router.afterEach(() => {
  // Complete the animation of the route progress bar.
  NProgress.done()
})

router.beforeEach(async (to, from, next) => {
  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
  await useAuthStore().getUser();
  if (requiresAuth && !useAuthStore().isAuthenticated) {
    next("/auth/login");
  } else {
    if (
      (useAuthStore().isAuthenticated && to.fullPath == "/auth/register") ||
      (useAuthStore().isAuthenticated && to.fullPath == "/auth/login")
    ) {
      next({ name: "home" });
    }
    next();
  }
});

export default router;
