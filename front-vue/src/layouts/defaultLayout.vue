<template>
    <div class="">
        <nav-bar />
        <router-view class="router-view" v-slot="{ Component }">
            <Transition name="page-slide" mode="out-in">
                <component :is="Component" />
            </Transition>
        </router-view>
    </div>
</template>

<script setup>

</script>

<style scoped>
.page-slide-enter-active,
.page-slide-leave-active {
    transition: 400ms ease all;
}

.page-slide-enter-from,
.page-slide-leave-to {
    opacity: 0;
    transform: translateY(60px);
}
</style>