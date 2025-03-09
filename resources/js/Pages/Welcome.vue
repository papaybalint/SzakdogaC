<script setup>
import { Head, Link } from '@inertiajs/vue3'
import NavBar from '@/Components/NavBar.vue';

import BooksPage from '@/Components/Pages/BooksPage.vue';
import HomePage from '@/Components/Pages/HomePage.vue';

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
});

</script>

<template>
    <Head title="HomePage" />
    
    <div>
        <header>
            <NavBar/>
        </header>

        <main class="mt-6">
            <HomePage/>
        </main>

        <footer>

        </footer>
    </div>
</template>
