<template>
    <div>
        <section class="container" style="margin-top: 20px;">

            <div class="card shadow-sm border-0" >

                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2" v-if="form.user">
                        <div class="grid-container">
                            <div class="col-12">
                                <img width="60" height="60" :src="form.user?.image" alt="" />
                            </div>
                            <div class="col-12">
                                <span>{{ form.user?.name }}</span>
                                <p class="text-muted" style="font-size: 14px">
                                    {{ form.created_at }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <i class="fas fa-trash-alt btn btn-warning btn-sm" @click="removePost"></i>
                        </div>
                    </div>

                    <textarea name="" id="" cols="30" rows="2" class="form-control" v-model="form.text"></textarea>
                    <img v-if="hasChangeFile" class="w-100 mt-2" height="270" style="object-fit: cover"
                        :src="form.imageDataUrl" alt="" />
                    <img v-if="form.image && !hasChangeFile" class="w-100 mt-2" height="270" style="object-fit: cover"
                        :src="form.image" alt="" />
                    <div class="grid-container mt-3">
                        <button v-if="!form.image" @click="triggerFileInput" type="button" class="btn btn-secondary">
                            <input type="file" ref="fileInputRef" class="d-none" @change="handleFileChange">+
                            อัพโหลดรูปภาพ
                        </button>
                        <button v-else @click="cancelImage" type="button" class="btn btn-danger">
                            ยกเลิกรูปภาพ
                        </button>
                        <button @click="handleSubmit" class="btn btn-success" type="submit">อัพเดต</button>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import router from "@/router"
import { axiosPrivate } from "@/common/axiosPrivate"
import { ref, onMounted, computed } from 'vue'

const form = ref({
    text: '',
    image: '',
    imageDataUrl: ''
})
const hasChangeFile = ref(false)
const fileInputRef = ref(null);
const postId = computed(() => {
    return router.currentRoute.value.params?.id;
})

const handleFileChange = (event) => {
    hasChangeFile.value = true
    const file = event.target.files[0];
    form.value.image = file

    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            form.value.imageDataUrl = e.target.result;
        };

        reader.readAsDataURL(file);
    }
};

const cancelImage = () => {
    form.value.image = ''
    form.value.imageDataUrl = ''
    hasChangeFile.value = false
}

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const removePost = async () => {
    try {
        const response = await axiosPrivate.delete(`/post/${postId.value}`)
        notify('ลบโพสต์สำเร็จ')
        router.replace({ name: 'home' })
    } catch (e) {
        console.log(e);
        notify('เกิดข้อผิดพลาด', 'error')
    }
}
const handleSubmit = async () => {
    try {
        let fd = new FormData;
        fd.append('file', form.value.image)
        fd.append('text', form.value.text)
        const response = await axiosPrivate.post(`post/${form.value.id}/update`, fd, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        })
        notify('อัพเดตสำเร็จ')
    } catch (e) {
        console.log(e);
        if (e.response.status === 401) {
            notify("โปรดเข้าสู่ระบบ", 'warning')
        } else {
            notify("เกิดข้อผิดพลาด", 'error')
        }
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

onMounted(async () => {
    try {
        const response = await axiosPrivate.get(`post/${postId.value}`)
        form.value = response.data.data
    } catch (e) {
        console.log(e);
    }

});
</script>

<style lang="scss" scoped></style>