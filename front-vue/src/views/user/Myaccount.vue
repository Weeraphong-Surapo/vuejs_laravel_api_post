<template>
    <div>
        <section class="container" style="margin-top: 20px">
            <h1>บัญชีผู้ใช้งาน</h1>



            <div class="col-12 d-flex justify-content-center mb-3">
                <img width="120" height="120" :src="imageDataUrl !== '' ? imageDataUrl : image" alt="" style="object-fit: cover;border-radius:50%;">
            </div>
            <form @submit.prevent="updateProfile">
                <div class="row">
                    <div class="border-bottom border-3 border-primary mb-3"></div>
                    <div class="col-12 col-lg-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" placeholder="ชื่อผู้ใช้งาน" v-model="name" />
                            <label>ชื่อผู้ใช้งาน</label>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" placeholder="อีเมล" v-model="email" disabled />
                            <label>อีเมล</label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mt-3">
                        <button v-if="!hasChangeFile" class="btn btn-secondary" @click="triggerFileInput" type="button">
                            <input type="file" class="form-control d-none" ref="fileInputRef" @change="handleFileChange" />
                            + อัพโหลดโปรไฟล์
                        </button>
                        <button type="button" class="btn btn-danger" @click="cancelImage" v-else>
                            ยกเลิกไฟล์
                        </button>
                        <button type="submit" class="btn btn-success" :disabled="loading">
                            <span v-if="!loading">บันทึก</span>
                            <span v-else>โปรดรอ...</span>
                        </button>
                    </div>
                </div>

            </form>
            <!-- <div class="col-12 mt-3">
                    <button class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                </div> -->
        </section>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import axiosPrivate from "@/common/axiosPrivate"
import {useAuthStore} from "@/stores/auth"

const authStore = useAuthStore()
const email = computed(() => {
    return JSON.parse(localStorage.getItem('user'))?.email
})
// const nameComputed = computed(() => {
//      name.value = 
// })
const image = computed(() => {
    return JSON.parse(localStorage.getItem('user'))?.image
})
const copyImage = computed(() => {
    return JSON.parse(localStorage.getItem('user'))?.image
})

const selectedFile = ref(null)
const name = ref(JSON.parse(localStorage.getItem('user'))?.name)
const fileInputRef = ref(null);
const hasChangeFile = ref(false)
const imageDataUrl = ref("")
const loading = ref(false)

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const updateProfile = async () => {
    try {
        loading.value = true
        let fd = new FormData;
        fd.append('file', selectedFile.value)
        fd.append('name', name.value)
        const response = await axiosPrivate.post('/user/profile', fd, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        })
        hasChangeFile.value = false
        await authStore.getUser()
        notify('อัพเดตสำเร็็จ')
    } catch (e) {
        console.log(e);
    } finally {
        loading.value = false
    }
}

const handleFileChange = (event) => {
    hasChangeFile.value = true
    const file = event.target.files[0];
    selectedFile.value = file

    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            imageDataUrl.value = e.target.result;
        };

        reader.readAsDataURL(file);
    }
};

const cancelImage = () => {
    image.value = copyImage.value
    imageDataUrl.value = ''
    hasChangeFile.value = false
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