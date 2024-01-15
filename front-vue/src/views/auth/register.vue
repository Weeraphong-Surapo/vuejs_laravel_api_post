<template>
    <div>
        <main class="form-signin">
            <form @submit.prevent="handleSubmit">

                <h1 class="h3 mb-3 fw-normal text-center">สมัครบัญชีใหม่</h1>

                <div class="form-floating mb-3">
                    <input type="text" class="form-control" placeholder="name@example.com" v-model="form.name" />
                    <label for="floatingInput">ชื่อผู้ใช้งาน</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" placeholder="name@example.com" v-model="form.email" />
                    <label for="floatingInput">อีเมล</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password"
                        v-model="form.password" />
                    <label for="floatingPassword">รหัสผ่าน</label>
                </div>
                <!-- <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password" />
                    <label for="floatingPassword">ยืนยันรหัสผ่าน</label>
                </div> -->
                <button class="w-100 btn btn-lg btn-primary my-3" type="submit" :disabled="loading">
                    <span v-if="!loading">Sign up</span>
                    <div v-else class="spinner-grow spinner-grow-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>

                <span class="">มีบัญชีแล้วใช่ไหม? <router-link :to="{ name: 'login' }">เข้าสู่ระบบเลย</router-link></span>
                <p class="mt-5 mb-3 text-muted">
                    © Weeraphong Surapho
                    <a href="https://www.facebook.com/profile.php?id=100094706250539">Facebook</a>
                </p>
            </form>
        </main>
    </div>
</template>

<script setup>
import { axiosPublic } from "@/common/axiosPublic";
import { ref } from "vue"
import { useAuthStore } from "@/stores/auth";
import router from "@/router";

const authStore = useAuthStore()
const form = ref({
    name: '',
    email: '',
    password: '',
    // password_confirmation: ''
})
const loading = ref(false)


const handleSubmit = async () => {
    try {
        if (form.value.password.length < 6) {
            notify('รหัสผ่านต้องไม่ต่ำกว่า 6 หลัก', 'error')

        } else {
            loading.value = true
            const response = await axiosPublic.post('/auth/register', form.value)
            localStorage.setItem('session', JSON.stringify(response.data.authorisation))
            localStorage.setItem('user', JSON.stringify(response.data.user))
            await authStore.getUser()
            notify('สมัครสมาชิกสำเร็จ')
            router.push({ name: 'home' })
        }

    } catch (e) {
        console.log(e);
        const status = e.response.status
        if (status === 422) {
            notify('กรุณากรอกอีเมลและรหัสผ่าน', 'error')
        } else if (status === 401) {
            notify('อีเมลหรือรหัสไม่ถูกต้อง', 'error')
        } else {
            notify('เกิดข้อผิดพลาดบางอย่าง', 'error')
        }
    } finally {
        loading.value = false

    }
}

const notify = (title, icon = "success") => {
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