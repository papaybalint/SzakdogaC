<script setup>
import { Head } from '@inertiajs/vue3'
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';

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

    <Head title="Tartalom Hozzáadása" />

    <div>
        <header>
            <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth" />
        </header>

        <main class="mt-6">
            <div class="container mx-auto p-6">
                <h1 class="text-3xl font-bold mb-4">Kölcsönzéseim</h1>
                <div v-if="borrowedItems.length > 0" class="flex flex-wrap -mx-2">
                    <div class="w-full md:w-1/2 lg:w-1/3 px-2 mb-4" v-for="borrowing in borrowedItems"
                        :key="borrowing.id">
                        <div class="bg-white rounded-lg shadow-lg p-4">
                            <!-- Kölcsönző neve -->
                            <h2 class="text-xl font-bold mb-2">Kölcsönző: {{ borrowing.user ? borrowing.user.first_name
                                : 'Nincs adat' }} {{ borrowing.user.last_name }}</h2>
                            <h2 class="text-xl font-bold mb-2">Kölcsönzés azonosító: {{ borrowing.id }}</h2>
                            <p class="text-gray-700">Kölcsönzés dátuma: {{ borrowing.borrowed_date }}</p>
                            <p class="text-gray-700">Esedékesség: {{ borrowing.due_date }}</p>

                            <h3 class="text-lg font-semibold mt-2">Médiák:</h3>
                            <ul class="list-disc pl-5">
                                <!-- Kölcsönzött médiák listázása -->
                                <li v-for="item in borrowing.items" :key="item.id">
                                    {{ item.title }}
                                </li>
                            </ul>

                            <!-- Törlés gomb -->
                            <button @click="deleteBorrowing(borrowing.id)"
                                class="mt-4 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">
                                Törlés
                            </button>

                        </div>
                    </div>
                </div>

                <p v-else class="text-gray-700">Nincsenek kölcsönzéseid.</p>
            </div>
        </main>

        <footer>
            <Footer />
        </footer>
    </div>

</template>


<script>
import axios from 'axios';

export default {
    name: 'BorrowedMedia',
    data() {
        return {
            borrowedItems: []
        };
    },
    mounted() {
        this.fetchBorrowedItems();
    },
    methods: {
        async fetchBorrowedItems() {
            try {
                const response = await axios.get('/api/borrowings');
                this.borrowedItems = response.data.borrowing;
            } catch (error) {
                console.error('Hiba a kölcsönzések lekérésekor:', error);
            }
        },

        // Törlés metódus
        async deleteBorrowing(id) {
            try {
                const response = await axios.delete(`/api/borrowings/${id}`);
                if (response.status === 200) {
                    // Törlés sikeres, frissítjük a kölcsönzött tételek listáját
                    this.borrowedItems = this.borrowedItems.filter(item => item.id !== id);
                    alert('A kölcsönzés sikeresen törölve!');
                }
            } catch (error) {
                console.error('Hiba a törlés során:', error);
                alert('Hiba történt a kölcsönzés törlésekor.');
            }
        }

    }
};
</script>

<style scoped>
.container {
    max-width: 1200px;
}

.bg-white {
    background-color: #ffffff;
}

.shadow-lg {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.rounded-lg {
    border-radius: 0.5rem;
}

.text-gray-700 {
    color: #4a5568;
}
</style>
