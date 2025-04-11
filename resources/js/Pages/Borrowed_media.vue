<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
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
    <Head title="Kölcsönzések" />

    <div>
        <header>
            <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth" />
        </header>

        <main class="mt-6">
            <div class="container mx-auto p-6">
                <h1 class="text-3xl font-bold mb-4">Kölcsönzések</h1>

                <div v-if="auth.user.role === 'admin'" class="mb-6 flex gap-4 items-center">
                    <!-- Admin szűrő mezők -->
                    <input v-model="searchTitle" type="text" placeholder="Cím (média)"
                        class="p-2 border border-gray-300 rounded-lg w-1/5" />
                    <input v-model="searchName" type="text" placeholder="Teljes név"
                        class="p-2 border border-gray-300 rounded-lg w-1/5" />
                    <input v-model="searchEmail" type="text" placeholder="Email"
                        class="p-2 border border-gray-300 rounded-lg w-1/5" />
                    <input v-model="searchPhone" type="text" placeholder="Telefonszám"
                        class="p-2 border border-gray-300 rounded-lg w-1/5" />
                    <button v-if="searchName || searchEmail || searchPhone || searchTitle" @click="clearSearchFields"
                        class="p-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                        Keresés törlése
                    </button>
                </div>

                <!-- Kölcsönzési lista -->
                <div v-if="filteredBorrowedItems.length > 0" class="flex flex-wrap -mx-2">
                    <div class="w-full md:w-1/2 lg:w-1/3 px-2 mb-4" v-for="borrowing in filteredBorrowedItems" :key="borrowing.id">
                        <div class="card bg-white rounded-lg shadow-lg p-4">
                            <div class="card-header">
                                <h2 class="text-xl font-bold mb-2">
                                    Kölcsönző: {{ borrowing.user?.first_name ?? 'Nincs adat' }} {{ borrowing.user?.last_name ?? '' }}
                                </h2>
                            </div>
                            <div class="card-content">
                                <p class="text-gray-700 text-sm"><strong>Email:</strong> {{ borrowing.user?.email ?? 'Nincs' }}</p>
                                <p class="text-gray-700 text-sm"><strong>Telefon:</strong> {{ borrowing.user?.phone ?? 'Nincs' }}</p>
                                <h2 class="text-lg font-semibold mt-2">Kölcsönzés azonosító: {{ borrowing.id }}</h2>
                                <p class="text-gray-700">Kölcsönzés dátuma: {{ borrowing.borrowed_date }}</p>
                                <p class="text-gray-700">Esedékesség: {{ borrowing.due_date }}</p>

                                <h3 class="text-lg font-semibold mt-2">Média:</h3>
                                <ul class="list-disc pl-5 text-gray-700">
                                    <li v-for="item in borrowing.items" :key="item.id">
                                        {{ item.title }}
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer">
                                <button v-if="auth.user.role === 'admin'" @click="confirmDelete(borrowing.id)"
                                    class="mt-4 bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">
                                    Törlés
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <p v-else class="text-gray-700">Nincsenek kölcsönzéseid.</p>
            </div>
        </main>

        <footer>
            <Footer />
        </footer>

        <!-- Törlés megerősítése modal -->
        <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex items-center justify-center">
            <div class="bg-white p-6 rounded-lg w-1/3">
                <h3 class="text-xl mb-4">Biztos, hogy törölni szeretnéd?</h3>
                <div class="flex justify-between">
                    <button @click="deleteBorrowing" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                        Törlés
                    </button>
                    <button @click="cancelDelete" class="bg-gray-300 text-black px-4 py-2 rounded-lg hover:bg-gray-400">
                        Mégsem
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'BorrowedMedia',
    data() {
        return {
            borrowedItems: [],
            searchName: '',
            searchEmail: '',
            searchPhone: '',
            searchTitle: '',
            isDeleteModalOpen: false,
            selectedBorrowingId: null,
        };
    },
    computed: {
        filteredBorrowedItems() {
            return this.borrowedItems.filter(b => {
                const isAdmin = this.auth.user.role === 'admin';

                if (!isAdmin && b.user?.id !== this.auth.user.id) {
                    return false;
                }

                const fullName = `${b.user?.first_name ?? ''} ${b.user?.last_name ?? ''}`.toLowerCase();
                const email = b.user?.email?.toLowerCase() ?? '';
                const phone = b.user?.phone?.toLowerCase() ?? '';
                const titleMatch = b.items.some(item =>
                    item.title?.toLowerCase().includes(this.searchTitle.toLowerCase())
                );

                if (isAdmin) {
                    return fullName.includes(this.searchName.toLowerCase()) &&
                        email.includes(this.searchEmail.toLowerCase()) &&
                        phone.includes(this.searchPhone.toLowerCase()) &&
                        (this.searchTitle.trim() === '' || titleMatch);
                }

                return true;
            });
        }
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

        confirmDelete(id) {
            this.selectedBorrowingId = id;
            this.isDeleteModalOpen = true;
        },

        async deleteBorrowing() {
            try {
                const response = await axios.delete(`/api/borrowings/${this.selectedBorrowingId}`);
                if (response.status === 200) {
                    this.borrowedItems = this.borrowedItems.filter(item => item.id !== this.selectedBorrowingId);
                    alert('A kölcsönzés sikeresen törölve!');
                    this.isDeleteModalOpen = false;
                    this.selectedBorrowingId = null;
                }
            } catch (error) {
                console.error('Hiba a törlés során:', error);
                alert('Hiba történt a kölcsönzés törlésekor.');
                this.isDeleteModalOpen = false;
            }
        },

        cancelDelete() {
            this.isDeleteModalOpen = false;
            this.selectedBorrowingId = null;
        },

        clearSearchFields() {
            this.searchName = '';
            this.searchEmail = '';
            this.searchPhone = '';
            this.searchTitle = '';
        }
    }
};
</script>

<style scoped>
/* ... existing styles ... */

.container {
    width: 100%;
    max-width: 1400px;
    margin: 0 auto;
    padding-left: 20px;
    padding-right: 20px;
}

/* Modal styles */
.fixed {
    position: fixed;
}
.bg-opacity-50 {
    background-color: rgba(0, 0, 0, 0.5);
}
.flex {
    display: flex;
}
.items-center {
    align-items: center;
}
.justify-center {
    justify-content: center;
}
.bg-white {
    background-color: #ffffff;
}
.p-6 {
    padding: 1.5rem;
}
.rounded-lg {
    border-radius: 0.5rem;
}
</style>
