<script setup>
import { Head } from '@inertiajs/vue3'
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
import EditPage from '@/Pages/Profile/Edit.vue';

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
        </header>

        <main class="mt-6">
            <EditPage />
        </main>

        <footer>
            <Footer />
        </footer>
    </div>
</template>
