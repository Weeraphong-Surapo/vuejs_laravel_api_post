<template>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm border-0">
                <form @submit.prevent="handleSubmit">
                    <div class="card-body">
                        <div class="grid-container">
                            <div class="col-4">
                                <img width="60" height="60" :src="image" alt=""
                                    style="border-radius: 50%; object-fit:cover;" />
                            </div>
                            <div class="col-8">
                                <span>{{ name }}</span>
                                <p class="text-muted" style="font-size: 14px">
                                    {{ email }}
                                </p>
                            </div>
                        </div>
                        <textarea id="" cols="30" rows="2" class="form-control mt-1" placeholder="เขียนอะไรสักอย่างสิ"
                            v-model="form.text"></textarea>
                        <!-- <img v-if="form.imageDataUrl" :src="form.imageDataUrl" alt="Selected Image" width="100"
                            class="my-2"> -->
                        <div>
                            <img id="myImg" v-if="form.imageDataUrl" class="w-100 mt-2" height="270"
                                style="object-fit: cover" :src="form.imageDataUrl" @click="openModal" alt="" />

                            <div id="myModal" v-show="modalVisible" class="modal">
                                <span class="close" @click="closeModal">&times;</span>
                                <img id="img01" :src="form.imageDataUrl" alt="" class="img-fluid" />
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <div class="grid-container-img">
                            <button v-if="!form.imageDataUrl" @click="triggerFileInput" type="button"
                                class="btn btn-info text-white">
                                <input type="file" ref="fileInputRef" class="d-none" @change="handleFileChange">+
                                อัพโหลดรูปภาพ
                            </button>
                            <button v-else @click="cancelImage" type="button" class="btn btn-danger">
                                ยกเลิกรูปภาพ
                            </button>
                            <button type="submit" class="btn btn-success" :disabled="loading">
                                <span v-if="!loading">โพสต์</span>
                                <span v-else>กำลังโพสต์...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, defineProps, computed } from "vue";
import axiosPrivate from "@/common/axiosPrivate";
import { useAuthStore } from "@/stores/auth";
import router from '@/router'

const authStore = useAuthStore()

const name = computed(() => {
    return authStore.getDatauser?.name
})
const email = computed(() => {
    return authStore.getDatauser?.email
})
const image = computed(() => {
    return authStore.getDatauser?.image
})

const props = defineProps(['getAllPost'])

const form = ref({
    text: '',
    imageDataUrl: '',
    image: ''
})
const fileInputRef = ref(null);

const loading = ref(false)
const modalVisible = ref(false);

// Function to open the modal
const openModal = (src, alt) => {
    modalVisible.value = true;
};

// Function to close the modal
const closeModal = () => {
    modalVisible.value = false;
};
const handleFileChange = (event) => {
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
}

const triggerFileInput = () => {
    fileInputRef.value.click();
};

const handleSubmit = async () => {
    try {
        
        loading.value = true
        let fd = new FormData;
        fd.append('file', form.value.image)
        fd.append('text', form.value.text)
        const response = await axiosPrivate.post(`/post`, fd, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        })
        form.value = {
            text: '',
            imageDataUrl: '',
            image: ''
        }
        notify("สร้างโพสต์สำเร็จ")

        await props.getAllPost()
    } catch (e) {
        console.log(e);
        if (e.response.status === 401) {
            router.push({name:'login'})
            notify("โปรดเข้าสู่ระบบ", 'warning')
        } else {
            notify("เกิดข้อผิดพลาด", 'error')
        }
        router.push({ name: 'login' })
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

<style  scoped>
.btn.show {
    border-color: none !important;
}

#myImg {
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
}

#myImg:hover {
    opacity: 0.7;
}

/* The Modal (background) */
.modal {
    display: block;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 45px;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.9);
    /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
}

/* Caption of Modal Image */
#caption {
    margin: auto;
    display: block;
    width: 80%;
    max-width: 700px;
    text-align: center;
    color: #ccc;
    padding: 10px 0;
    height: 150px;
}

/* Add Animation */
.modal-content,
#caption {
    -webkit-animation-name: zoom;
    -webkit-animation-duration: 0.6s;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
    from {
        -webkit-transform: scale(0)
    }

    to {
        -webkit-transform: scale(1)
    }
}

@keyframes zoom {
    from {
        transform: scale(0)
    }

    to {
        transform: scale(1)
    }
}

/* The Close Button */
.close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #f1f1f1;
    font-size: 40px;
    font-weight: bold;
    transition: 0.3s;
}

.close:hover,
.close:focus {
    color: #bbb;
    text-decoration: none;
    cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px) {
    .modal-content {
        width: 100%;
    }
}
</style>