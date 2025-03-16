<script setup>
import { Head, Link } from '@inertiajs/vue3'
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
import About from '@/Components/About.vue';
import ListPage from '@/Pages/ListPage.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
    items: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

</script>

<template>

    <Head title="HomePage" />

    <div>
        <header>
            <NavBar />
            <About />
        </header>

        <main class="mt-6">
            <ListPage :items="items" :categories="categories" />
        </main>

        <footer>
            <Footer />
        </footer>
    </div>
</template>
