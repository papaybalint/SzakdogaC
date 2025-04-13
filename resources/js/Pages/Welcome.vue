<script setup>
import { Head } from '@inertiajs/vue3'
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
import About from '@/Components/About.vue';
import ListPage from '@/Pages/ListPage.vue';
import Pic from '@/Components/Pic.vue';

defineProps({
    auth: {
        type: Object,
        required: true,
    },
    nav: {
        type: Object,
        required: true,
    },
});

</script>

<template>

    <div>
        <header>
            <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth"/>
            <Pic />
            <About />
        </header>

        <main class="mt-6">
            <ListPage v-if="nav.isLoggedIn" :items="nav.items" :categories="nav.categories" :auth="auth" />
        </main>

        <footer>
            <Footer />
        </footer>
    </div>
</template>
