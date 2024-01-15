<template>
  <div class="col-12 mb-4">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex justify-content-between mb-1">
          <div class="grid-container">
            <div class="col-12">
              <img width="60" height="60" :src="post.user.image" alt="" style="border-radius: 50%; object-fit:cover;" />
            </div>
            <div class="col-12">
              <span>{{ post.user.name }}</span>
              <p class="text-muted" style="font-size: 14px">
                {{ post.created_at }}
              </p>
            </div>
          </div>
          <div class="dropdown" v-if="post.user.id === userId">
            <button class="btn btn-sm p-0 btn-option" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span style="color: #74C0FC;font-weight:900">...</span>
            </button>
            <ul class="dropdown-menu">
              <li>
                <router-link :to="{ name: 'editpost', params: { id: post.id } }" class="dropdown-item">แก้ไข</router-link>
              </li>
              <li><button class="dropdown-item" type="button" @click="removePost(post.id)">ลบ</button></li>
            </ul>
          </div>
        </div>

        <span class="">{{ post.text }}</span>

        <div>
          <img id="myImg" v-if="post.image" class="w-100 mt-2" height="270" style="object-fit: cover" :src="post.image"
            @click="openModal" :alt="post.text" />

          <div id="myModal" v-show="modalVisible" class="modal">
            <span class="close" @click="closeModal">&times;</span>
            <div class="d-flex justify-content-center">
              <img id="img01" :src="post.image" :alt="post.text" class="img-fluid" />
            </div>
            <div id="caption">{{ post.text }}</div>
          </div>
        </div>

      </div>
      <div class="card-footer">
        <div class="row">
          <button class="btn btn-secondary" type="button" @click="openComment" v-if="!commentVisible">{{
            post?.comment.length }} คอมเม้นต์</button>
          <button class="btn btn-danger" type="button" @click="closeComment" v-else>ปิดคอมเม้น</button>


          <div class="comment p-2" v-if="commentVisible">
            <div class="grid-container my-2 " v-for="(comment, index) in post.comment" :key="index">
              <div class="col-4">
                <img width="60" height="60" style="border-radius: 50%; object-fit:cover;" :src="comment.user?.image"
                  alt="" />
              </div>
              <div class="col-12 ">
                <div :class="`card ${comment.id === form.id ? 'shadow' : ''}`">
                  <div class="card-body py-1 w-100">
                    <div class="d-flex justify-content-between">

                      <span class="h6 pt-1">{{ comment.user?.name }}</span>

                      <div class="dropdown" v-if="post.user.id === userId || comment.user?.id === userId">
                        <button class="btn btn-sm p-0 btn-option" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <span style="color: #74C0FC;font-weight:900">...</span>
                        </button>
                        <ul class="dropdown-menu">
                          <!-- <li>
                            <button type="button" @click="editPostComment(index)" class="dropdown-item">แก้ไข</button>
                          </li> -->
                          <li><button class="dropdown-item" type="button"
                              @click="removePostComment(comment.id, index)">ลบ</button>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <span class="d-block" style="font-size: 14px;">
                      {{ comment.text }}
                    </span>
                    <!-- <span class="d-block" style="font-size: 14px;" v-else>
                      {{ form.text }}
                    </span> -->
                  </div>
                  <div class="card-footer">
                    <span class="text-muted" style="font-size: 14px;">{{ comment.created_at }}</span>

                  </div>
                </div>
              </div>
            </div>


            <div class="grid-container">
              <img width="60" height="60" :src="post.user.image" style="border-radius: 50%; object-fit:cover;" alt="" />
              <form @submit.prevent="handleSubmit" class="d-flex">
                <div class="input-group">
                  <textarea v-model="form.text" name="" id="" cols="30" placeholder="เขียนความคิดเห็น"
                    class="form-control"></textarea>
                  <button class="input-group-text bg-primary text-white" type="submit"><i
                      class="fa-regular fa-paper-plane"></i></button>
                  <!-- <button class="input-group-text bg-secondary text-white " type="button"
                    @click="canCelComment">x</button> -->
                </div>
              </form>
            </div>


          </div>


        </div>
      </div>
    </div>


    <!-- Modal -->
  </div>
</template>

<script setup>
import { computed, defineProps, ref } from 'vue';
import { axiosPrivate } from '@/common/axiosPrivate';
import { useAuthStore } from '@/stores/auth';
import router from '@/router';

const props = defineProps(['post', 'getAllPost'])

const authStore = useAuthStore()
const post = computed(() => {
  return props.post
})

const form = ref({
  text: '',
})



const userId = computed(() => {
  return JSON.parse(localStorage.getItem('user'))?.id
})


const modalVisible = ref(false);
const commentVisible = ref(false)
// const ModeEditComment = ref(false)

const openComment = () => {
  commentVisible.value = true
}
const closeComment = () => {
  commentVisible.value = false
}

// Function to open the modal
const openModal = (src, alt) => {
  modalVisible.value = true;
};

const canCelComment = () => {
  ModeEditComment.value = false
  form.value.value = {}
}

// Function to close the modal
const closeModal = () => {
  modalVisible.value = false;
};

const removePost = async (id) => {
  try {
    const response = await axiosPrivate.delete(`/post/${id}`)
    notify('ลบโพสต์สำเร็จ')
    await props.getAllPost()
  } catch (e) {
    console.log(e);
    if (e.response.status === 401) {
      notify("โปรดเข้าสู่ระบบ", 'warning')
    } else {
      notify("เกิดข้อผิดพลาด", 'error')
    }
  }
}

// const editPostComment = async (id) => {
//   ModeEditComment.value = true
//   if (post.value.comment && post.value.comment[id]) {
//     // Create a copy of the comment to avoid modifying the original
//     form.value = post.value.comment[id] 
//     console.log(form.value);
//   } else {
//     console.error('Comment not found');
//     // Handle the case where the comment is not found
//   }
// }

const removePostComment = async (id, index) => {
  try {
    post.value.comment.splice(index, 1)
    const response = await axiosPrivate.delete(`user-comment/${id}/delete`)
    notify('ลบคอมเม้นต์สำเร็จ')
  } catch (e) {
    console.log(e);
    if (e.response.status === 401) {
      router.push({ name: 'login' })
      notify("โปรดเข้าสู่ระบบ", 'warning')
    } else {
      notify("เกิดข้อผิดพลาด", 'error')
    }
  }
}

const handleSubmit = async () => {
  try {
    form.value.post_id = props.post.id
    const response = await axiosPrivate.post('/user-comment', form.value,)
    notify('คอมเม้นต์สำเร็จ')
    const updatedPost = response.data;

    post.value.comment = updatedPost.comments
  } catch (e) {
    console.log(e);
    if (e.response.status === 401) {
      router.push({ name: 'login' })
      notify("โปรดเข้าสู่ระบบ", 'warning')
    } else {
      notify("เกิดข้อผิดพลาด", 'error')
    }
  } finally {
    // commentVisible.value = false
    form.value.text = ""
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

<style scoped>
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