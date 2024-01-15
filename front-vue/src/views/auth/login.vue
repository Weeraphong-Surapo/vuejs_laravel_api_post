<template>
    <div>
        <main class="form-signin">
            <form @submit.prevent="handleSubmit">
                <!-- <img class="mb-4" src="/docs/5.1/assets/brand/bootstrap-logo.svg" alt="" width="72" height="57"> -->
                <h1 class="h3 mb-3 fw-normal text-center">ยินดีต้อนรับกลับ</h1>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" placeholder="name@example.com" v-model="form.email" />
                    <label for="floatingInput" class="text-dark">อีเมล</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" placeholder="Password" v-model="form.password" />
                    <label for="floatingPassword" class="text-dark">รหัสผ่าน</label>
                </div>

                <button class="w-100 btn btn-lg btn-primary my-3" type="submit" :disabled="loading">
                    <span v-if="!loading">Sign in</span>
                    <div v-else class="spinner-grow spinner-grow-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>

                <span class="">ยังไม่มีบัญชีใช่ไหม? <router-link :to="{ name: 'register' }">สมัครเลย</router-link></span>
                <p class="mt-5 mb-3 text-muted">
                    © Weeraphong Surapho
                    <a href="https://www.facebook.com/profile.php?id=100094706250539">Facebook</a>
                </p>
            </form>
        </main>
    </div>
</template>

<script setup>
import { ref } from "vue"
import { axiosPublic } from "@/common/axiosPublic";
import { useAuthStore } from "@/stores/auth"
import router from "@/router";


const authStore = useAuthStore()
const form = ref({
    email: '',
    password: '',
})

const loading = ref(false)

const handleSubmit = async () => {
    try {
        loading.value = true
        const response = await axiosPublic.post(`/auth/login`, form.value)
        localStorage.setItem('session', JSON.stringify(response.data.authorisation))
        await authStore.getUser()
        notify('เข้าสู่ระบบสำเร็จ')
        router.push({ name: 'home' })
    } catch (e) {
        console.log(e);
        const status = e.response.status
        if (status === 422) {
            notify('กรุณากรอกอีเมลและรหัสผ่าน','error')
        } else if (status === 401) {
            notify('อีเมลหรือรหัสไม่ถูกต้อง','error')
        }else{
            notify('เกิดข้อผิดพลาดบางอย่าง','error')
        }

    } finally {
        loading.value = false
    }
}

const notify = (title,icon="success") => {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        }
    });
    Toast.fire({
        icon,
        title
    });
}
</script>

<style lang="scss" scoped></style>