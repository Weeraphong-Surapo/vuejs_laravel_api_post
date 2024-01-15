<template>
  <div>
    <section class="container-fluid" style="margin-top: 20px;">

      <form-post :getAllPost="getAllPost" v-if="authStore.isAuthenticated" />


      <div class="row">
        <div class="col-2 d-none d-lg-block">
          <div class="box-frind fixed">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h5 class="border-bottom border-3 mb-3">ผู้ใช้งานในระบบ</h5>
                <p style="line-height:12px;" v-for="(user,index) in users" :key="index">คุณ: {{ user.name }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 col-lg-8">
          <div>
            <div v-if="loading" class="d-flex justify-content-center">
              <div class="spinner-border text-primary " role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
          </div>
          <div>
            <div v-if="posts.length >= 1">
              <card-post v-for="(post, index) in posts" :key="index" :post="post" :getAllPost="getAllPost" />
            </div>
            <div v-else>
              <h1 class="border-bottom border-primary border-2 text-center">ยังไม่มีโพสต์ใดๆเลย</h1>
            </div>
          </div>
        </div>
        <div class="col-2 d-none d-lg-block">
          <div class="box-contact">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h5 class="border-bottom border-3 mb-3">ช่องทางติดต่อ</h5>
                <h6><a href="https://web.facebook.com/profile.php?id=100094706250539" target="_blank"><i
                      class="fa-brands fa-facebook"> ส่วนตัว</i></a>
                </h6>
                <h6><a href="https://web.facebook.com/profile.php?id=61554278654925" target="_blank"><i
                      class="fa-brands fa-facebook"></i> เพจ</a></h6>
                <h6> <a href="#" target="_blank">โทร 0925562767</a></h6>

              </div>
            </div>
          </div>
        </div>
      </div>




    </section>

    <!-- <div class=""> -->
    <!-- <button class="btn btn-warning box-add">^</button> -->

  </div>
</template>

<script setup>
import { ref } from 'vue';
import FormPost from '@/components/FormPost.vue';
import CardPost from '../components/CardPost.vue';
import { axiosPublic } from '@/common/axiosPublic';
import { useAuthStore } from '@/stores/auth';
import { onMounted } from 'vue';

const posts = ref([])
const loading = ref(false)
const authStore = useAuthStore()
const users = ref([])

const getAllPost = async () => {
  try {
    loading.value = true
    const response = await axiosPublic.get('/post')
    posts.value = response.data.data
    await getAllUser()
  } catch (e) {
    console.log(e);
  } finally {
    loading.value = false
  }
}

const getAllUser = async () => {
  try {
    const response = await axiosPublic.get('/user-all')
    users.value = response.data
  } catch (e) {
    console.log(e);
  }
}


onMounted(async () => {
  await getAllPost()
})
</script>

<style  scoped>
/* .box-frind {
  position: absolute;
  top: 77px;
  right: 20px;
  display: none;
  display: block;
}

.box-contact {
  position: absolute;
  top: 77px;
  left: 20px;
  display: none;
  display: block;
} */
</style>