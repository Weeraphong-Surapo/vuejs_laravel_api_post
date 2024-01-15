import { defineStore } from "pinia";
import { axiosPrivate } from "@/common/axiosPrivate";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.user,
    getDatauser:(state)=>state.user
  },
  actions: {
    async getUser() {
      try {
        const token = JSON.parse(localStorage.getItem("session"))?.token;
        const response = await axiosPrivate.get(`/auth/me`, {
          headers: {
            authorization: `Bearer ${token}`,
          },
        });
        this.user = response.data;
        localStorage.setItem('user', JSON.stringify(response.data))
      } catch (error) {
        localStorage.removeItem("user");
        localStorage.removeItem("session");
        this.user = null;
      }
    },
  },
});
