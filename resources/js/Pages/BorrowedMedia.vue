<script setup>
import { ref, watch } from 'vue';
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

                <div v-if="auth.user.role === 'admin'" class="mb-6 flex flex-wrap gap-4">
                    <!-- Admin szűrő mezők -->
                    <input v-model="searchTitle" type="text" placeholder="Cím (média)"
                        class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5" />
                    <input v-model="searchName" type="text" placeholder="Teljes név"
                        class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5"
                        style="text-transform: capitalize;" @input="validateNameInput" />
                    <input v-model="searchEmail" type="text" placeholder="Email"
                        class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5" />
                    <input v-model="searchPhone" type="text" placeholder="Telefonszám"
                        class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5"
                        @input="validatePhoneInput" />
                    <button v-if="shouldShowClearButton" @click="clearSearch"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full sm:w-auto">
                        Keresés törlése
                    </button>
                </div>
                <!-- Csak sima felhasználóknak (Cím keresés) -->
                <div v-if="auth.user.role !== 'admin'" class="mb-6 flex flex-wrap gap-4">
                    <input v-model="searchTitle" type="text" placeholder="Cím (média)"
                        class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5" />
                    <button v-if="shouldShowClearButton" @click="clearSearch"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full sm:w-auto">
                        Keresés törlése
                    </button>
                </div>
                <!-- Kölcsönzési lista -->
                <div v-if="paginatedItems.length > 0" class="flex flex-wrap -mx-2">
                    <div class="w-full md:w-1/2 lg:w-1/3 px-2 mb-4" v-for="borrowing in paginatedItems"
                        :key="borrowing.id">
                        <div class="card bg-white rounded-lg shadow-lg p-4 h-full flex flex-col justify-between" :class="{
                            'border-2 border-green-500 bg-green-50': auth.user.role === 'admin' && borrowing.user.id === auth.user.id
                        }">
                            <div class="card-header">
                                <h2 class="text-xl font-bold mb-2">
                                    Kölcsönző: {{ borrowing.user?.first_name ?? 'Nincs adat' }} {{
                                    borrowing.user?.last_name ?? '' }}
                                </h2>
                                <!-- Jelölések (Saját fiók, csak ha admin és saját) -->
                                <div class="flex flex-wrap gap-2 mt-1">
                                    <span v-if="auth.user.role === 'admin' && borrowing.user.id === auth.user.id"
                                        class="text-sm text-green-700 bg-green-200 px-2 py-1 rounded-full">
                                        Saját Kölcsönzés
                                    </span>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="text-gray-700 text-sm"><strong>Email:</strong> {{ borrowing.user?.email ??
                                    'Nincs' }}</p>
                                <p class="text-gray-700 text-sm"><strong>Telefon:</strong> {{ borrowing.user?.phone ??
                                    'Nincs' }}</p>
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
                            <div class="card-footer mt-auto flex justify-end">
                                <button v-if="auth.user.role === 'admin'" @click="confirmDelete(borrowing.id)"
                                    class="mt-4 bg-red-500 text-white py-2 px-5 text-base rounded-lg hover:bg-red-600">
                                    Törlés
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-center items-center gap-3 sm:gap-4">
                    <!-- Lapozás -->
                    <button @click="goToFirstPage"
                        class="pagination-btn px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 w-full sm:w-auto text-sm"
                        :disabled="currentPage === 1">
                        <span class="text">Első</span>
                        <span class="arrow">
                            << </span>
                    </button>

                    <button @click="previousPage" :disabled="currentPage === 1"
                        class="pagination-btn px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 w-full sm:w-auto text-sm">
                        <span class="text">Előző</span>
                        <span class="arrow">
                            < </span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <input v-model.number="currentPageInput" type="number" min="1" :max="totalPages"
                            class="w-16 sm:w-12 text-center p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            @change="onPageInputChange" />
                        <span class="text-sm text-gray-600">/ {{ totalPages }}</span>
                    </div>

                    <button @click="nextPage" :disabled="currentPage === totalPages"
                        class="pagination-btn px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 w-full sm:w-auto text-sm">
                        <span class="text">Következő</span>
                        <span class="arrow">> </span>
                    </button>

                    <button @click="goToLastPage"
                        class="pagination-btn px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50 w-full sm:w-auto text-sm"
                        :disabled="currentPage === totalPages">
                        <span class="text">Utolsó</span>
                        <span class="arrow">>> </span>
                    </button>
                </div>
            </div>
        </main>

        <footer>
            <Footer />
        </footer>

        <!-- Törlés megerősítő modális ablak -->
        <div v-if="isDeleteModalOpen"
            class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-xl mb-4">Biztosan törölni szeretnéd a kölcsönzést?</h3>
                <div class="flex justify-between">
                    <button @click="deleteBorrowing"
                        class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">
                        Törlés
                    </button>
                    <button @click="cancelDelete" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600">
                        Mégse
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
            searchTitle: '',
            searchName: '',
            searchEmail: '',
            searchPhone: '',
            currentPage: 1,
            currentPageInput: 1,
            borrowedItems: [],
            pageSize: 9,
            isDeleteModalOpen: false,
            selectedBorrowingId: null,
        };
    },
    computed: {
        filteredBorrowedItems() {
            return this.borrowedItems
                .sort((a, b) => {
                    // Külön kezeljük a felhasználó kölcsönzéseit
                    const isUserBorrowedA = a.user?.id === this.auth.user.id ? 1 : 0;
                    const isUserBorrowedB = b.user?.id === this.auth.user.id ? 1 : 0;

                    // Azokat, amik a felhasználó kölcsönzései, előre tesszük
                    return isUserBorrowedB - isUserBorrowedA;
                })
                .filter(b => {
                    const isAdmin = this.auth.user.role === 'admin';

                    if (!isAdmin && b.user?.id !== this.auth.user.id) {
                        return false;
                    }

                    const normalizedSearchTitle = this.normalizeText(this.searchTitle.toLowerCase());
                    const titleMatch = b.items.some(item =>
                        this.normalizeText(item.title.toLowerCase()).includes(normalizedSearchTitle)
                    );

                    if (this.auth.user.role !== 'admin') {
                        return titleMatch;  // Csak a cím alapján szűrünk
                    }

                    const fullName = `${b.user?.first_name ?? ''} ${b.user?.last_name ?? ''}`.toLowerCase();
                    const email = b.user?.email?.toLowerCase() ?? '';
                    const phone = b.user?.phone?.toLowerCase() ?? '';

                    const normalizedFullName = this.normalizeText(fullName);
                    const normalizedEmail = this.normalizeText(email);
                    const normalizedPhone = this.normalizeText(phone);
                    const normalizedSearchName = this.normalizeText(this.searchName.toLowerCase());
                    const normalizedSearchEmail = this.normalizeText(this.searchEmail.toLowerCase());
                    const normalizedSearchPhone = this.normalizeText(this.searchPhone.toLowerCase());

                    const searchNameParts = normalizedSearchName.split(' ');

                    const nameMatch = searchNameParts.every(part =>
                        normalizedFullName.includes(part)
                    );

                    return nameMatch &&
                        normalizedEmail.includes(normalizedSearchEmail) &&
                        normalizedPhone.includes(normalizedSearchPhone) &&
                        (normalizedSearchTitle.trim() === '' || titleMatch);
                });
        },


        paginatedItems() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredBorrowedItems.slice(start, start + this.pageSize);
        },
        totalPages() {
            const pages = Math.ceil(this.filteredBorrowedItems.length / this.pageSize);
            return pages > 0 ? pages : 1; // Ha nincs találat, akkor legalább 1 oldal legyen
        },
        shouldShowClearButton() {
            return this.searchTitle || this.searchName || this.searchEmail || this.searchPhone !== '';
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
        validatePhoneInput(event) {
            // Csak számok és a '+' jel engedélyezett
            this.searchPhone = event.target.value.replace(/[^0-9+]/g, '');
        },
        validateNameInput(event) {
            // Csak betűk és szóközök engedélyezettek
            this.searchName = event.target.value.replace(/[^a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ\s]/g, '');
        },
        normalizeText(text) {
            return text
                .normalize('NFD')  // Normalizáljuk az ékezetek nélküli karakterekre
                .replace(/[\u0300-\u036f]/g, '')  // Az ékezetek eltávolítása
                .replace(/[^\w\s]/g, '');  // A nem szó karakterek (pl. : , ;) eltávolítása
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.currentPageInput = this.currentPage;
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.currentPageInput = this.currentPage;
            }
        },
        goToFirstPage() {
            this.currentPage = 1;
            this.currentPageInput = 1;
        },
        goToLastPage() {
            this.currentPage = this.totalPages;
            this.currentPageInput = this.totalPages;
        },
        onPageInputChange() {
            if (this.currentPageInput < 1) {
                this.currentPageInput = 1;
            } else if (this.currentPageInput > this.totalPages) {
                this.currentPageInput = this.totalPages;
            }
            this.currentPage = this.currentPageInput;
        },

        confirmDelete(id) {
            this.selectedBorrowingId = id;
            this.isDeleteModalOpen = true;
        },
        async deleteBorrowing() {
            try {
                // Küldjünk egy törlési kérést az API-nak
                const response = await axios.delete(`/api/borrowings/${this.selectedBorrowingId}`);
                if (response.status === 200) {
                    // Frissítsd a kölcsönzési listát a törlés után
                    this.borrowedItems = this.borrowedItems.filter(item => item.id !== this.selectedBorrowingId);

                    // Ha az aktuális oldal több, mint a maximális oldal szám (mert töröltünk egy elemet), állítsuk be az oldalt a legutolsó elérhető oldalra
                    if (this.currentPage > this.totalPages) {
                        this.currentPage = this.totalPages;
                        this.currentPageInput = this.totalPages;
                    }

                    // Ha a lista üres és nem az első oldalon vagyunk, állítsuk be az oldalt 1-re
                    if (this.filteredBorrowedItems.length === 0 && this.currentPage !== 1) {
                        this.currentPage = 1;
                        this.currentPageInput = 1;
                    }

                    // A törlés sikeres volt, értesítjük a felhasználót
                    alert('A kölcsönzés sikeresen törölve!');
                    this.isDeleteModalOpen = false;
                    this.selectedBorrowingId = null;
                }
            } catch (error) {
                // Hibakezelés
                console.error('Hiba a törlés során:', error);
                alert('Hiba történt a kölcsönzés törlésekor.');
                this.isDeleteModalOpen = false;
            }
        },
        cancelDelete() {
            this.isDeleteModalOpen = false;
            this.selectedBorrowingId = null;
        },

        clearSearch() {
            this.searchTitle = '';
            this.searchName = '';
            this.searchEmail = '';
            this.searchPhone = '';
            this.goToFirstPage();
        },
    },
    watch: {
        // Ha bármelyik szűrő változik, állítsuk be az oldalt az első oldalra
        searchTitle() {
            this.goToFirstPage();
        },
        searchName() {
            this.goToFirstPage();
        },
        searchEmail() {
            this.goToFirstPage();
        },
        searchPhone() {
            this.goToFirstPage();
        }
    },
};
</script>

<style scoped>
/* Grid and Pagination Styles */
.grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

/* Alapértelmezett stílus: asztali nézetben a nyilak el vannak rejtve */
.pagination-btn .arrow {
    display: none;
    /* Elrejtjük alapértelmezetten */
}

/* Mobil nézetben a szövegeket elrejtjük és a nyilakat mutatjuk */
@media (max-width: 640px) {
    .pagination-btn .text {
        display: none;
        /* Elrejtjük a szöveget mobilon */
    }

    .pagination-btn .arrow {
        display: inline-block;
        /* Megjelenítjük a nyilakat mobilon */
    }
}

input,
select {
    transition: all 0.3s ease;
}

input:focus,
select:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 5px rgba(79, 70, 229, 0.5);
}

button {
    transition: all 0.3s ease;
}

button {
    width: auto;
    font-size: 0.875rem;
}


input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

h1 {
    text-align: center;
    font-weight: bold;
    font-size: 2rem;
}

.card-footer button {
    font-size: 1rem;
    /* Kisebb betűméret */
    padding: 0.5rem 1rem;
    /* Kisebb padding */
    border-radius: 0.375rem;
    /* Finomított sarkok */
}
</style>
