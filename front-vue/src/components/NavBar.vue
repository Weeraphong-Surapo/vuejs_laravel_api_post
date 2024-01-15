<template>
    <nav class="navbar sticky-top bg-body-tertiary bg-primary">
        <div class="container-fluid">
            <router-link class="navbar-brand" :to="{ name: 'home' }">คนว่างๆ</router-link>
            <button @click="toggleDarkMode" class="btn btn-outline-dark " type="button">
                <i class="fa-regular fa-moon" v-if="isDarkMode"></i>
                <i class="fa-regular fa-sun" v-else></i>
                </button>
            <router-link v-if="!authStore.isAuthenticated" :to="{ name: 'login' }" class=" btn btn-primary btn-sm ">Login |
                Register</router-link>

            <div v-else class="btn-group dropstart">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    {{ name }}
                </button>
                <ul class="dropdown-menu">
                    <li><router-link :to="{ name: 'myaccount' }" class="dropdown-item active">ข้อมูลส่วนตัว</router-link>
                    </li>
                    <li><router-link :to="{ name: 'mypost' }" class="dropdown-item">โพสของฉัน</router-link></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><button type="button" @click="handleLogout" class="dropdown-item">ออกจากระบบ</button></li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { axiosPrivate } from '@/common/axiosPrivate';
import { useAuthStore } from '@/stores/auth';
import { computed,ref } from 'vue';
import router from '@/router';

const authStore = useAuthStore()
const isDarkMode = ref(false);

const name = computed(() => {
    return authStore.getDatauser.name
})

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;

    // Add or remove the 'darkMode' class from the body
    document.body.classList.toggle('darkMode', isDarkMode.value);
};

const handleLogout = async () => {
    try {
        const response = await axiosPrivate.post('/auth/logout')
        await authStore.getUser()
        router.push({ name: 'login' })
    } catch (e) {
        console.log(e);
    }
}
</script>

<style  >
body.darkMode {
    background-color: #333;
    color: #fff;
}
</style>