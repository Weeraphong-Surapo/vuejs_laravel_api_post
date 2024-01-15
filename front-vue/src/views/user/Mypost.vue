<template>
    <div>
        <section class="container" style="margin-top: 20px">
            <div class="d-flex justify-content-center" v-if="loading">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div class="row" v-else>
                <div class="" v-if="posts.length < 1">
                    <h1 class="text-center border-4 border-bottom border-primary">คุณยังไม่เคยโพสต์</h1>
                </div>

                <card-post v-else v-for="(post, index) in posts" :key="index" :post="post" />
            </div>
        </section>

        <!-- <div class=""> -->
    </div>
</template>

<script setup>
import CardPost from '@/components/CardPost.vue';
import { axiosPrivate } from "@/common/axiosPrivate";
import { onMounted, ref } from 'vue';

const posts = ref([])
const loading = ref(false)

const getMyPosts = async () => {
    try {
        loading.value = true
        const resopnse = await axiosPrivate.get('/post/mypost')
        posts.value = resopnse.data.data
    } catch (e) {
        console.log(e);
    } finally {
        loading.value = false

    }
}

onMounted(() => {
    getMyPosts()
})

</script>

<style lang="scss" scoped></style>