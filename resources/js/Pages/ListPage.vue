<template>
    <div class="container mx-auto p-4">

        <div>
            <h1>Könyvtárunkban megtalálható tartalom:</h1>
        </div>
        <br>
        <!-- Szűrők -->
        <div class="flex flex-col sm:flex-row items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4 w-full">
            <select v-model="selectedCategory"
                class="p-2 w-full sm:w-1/3 md:w-1/4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Minden kategória</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }} <span v-if="category.media_type">- {{ category.media_type }}</span>
                </option>
            </select>

            <div class="flex flex-col sm:flex-row w-full sm:w-2/3 md:w-1/2 space-x-4 space-y-4 sm:space-y-0">
                <input v-model="searchTitle" type="text" placeholder="Cím"
                    class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <input v-model="searchAuthor" type="text" placeholder="Szerző"
                    class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <input v-model="searchYear" type="text" placeholder="Kiadás éve"
                    class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @input="validatesearchYearInput" />
            </div>

            <button v-if="shouldShowClearButton" @click="clearSearch"
                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                Keresés törlése
            </button>
        </div>

        <!-- Kártyák -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <div v-for="item in paginatedItems" :key="item.id"
                class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">{{ item.title }}</h3>
                <p class="text-sm text-gray-600 mb-1">Szerző: {{ item.author }}</p>
                <p class="text-sm text-gray-600 mb-1">Kiadás éve: {{ item.published_year }}</p>
                <p class="text-sm text-gray-600 mb-1">Kategória: {{ categories[item.categories_id - 1].name }} <span
                        v-if="categories[item.categories_id - 1].media_type">- {{ categories[item.categories_id -
                            1].media_type
                        }}</span></p>

                <!-- Kikölcsönözve felirat -->
                <div v-if="isBorrowed(item)"
                    class="absolute bottom-4 left-4 bg-red-500 text-white py-1 px-3 rounded-md text-xs">
                    Kikölcsönözve
                </div>

                <!-- Részletek Gomb -->
                <div v-if="auth.user.role === 'admin'"
                    class="mt-auto flex justify-end space-x-2 space-y-2 sm:space-y-0">
                    <button @click="openModal(item)"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-xs sm:text-sm">
                        Részletek
                    </button>
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
        <!-- Modal ablak -->
        <ItemDetailModal v-if="modalVisible" :item="modalItem" :categories="categories" :auth="auth" @close="closeModal"
            @update="handleItemUpdate" />
    </div>

</template>

<script>
import ItemDetailModal from './ItemDetailModal.vue';

export default {
    components: {
        ItemDetailModal,
    },
    props: {
        items: Array,
        categories: Array,
        auth: Object,
    },
    data() {
        return {
            // Betöltés a localStorage-ból
            searchTitle: '',
            searchAuthor: '',
            searchYear: '',
            selectedCategory: '',
            currentPage: 1,
            currentPageInput: 1,
            pageSize: 15, // Kártyák száma egy oldalra
            modalVisible: false,
            modalItem: null,
        };
    },
    computed: {
        filteredItems() {
            return this.items.filter(item => {
                const matchesTitle = item.title.toLowerCase().includes(this.searchTitle.toLowerCase());
                const matchesAuthor = item.author.toLowerCase().includes(this.searchAuthor.toLowerCase());
                const matchesYear = item.published_year.toString().includes(this.searchYear);
                const matchesCategory = this.selectedCategory
                    ? item.categories_id == this.selectedCategory
                    : true;
                return matchesTitle && matchesAuthor && matchesYear && matchesCategory;
            });
        },
        paginatedItems() {
            const start = (this.currentPage - 1) * this.pageSize;
            return this.filteredItems.slice(start, start + this.pageSize);
        },
        totalPages() {
            const pages = Math.ceil(this.filteredItems.length / this.pageSize);
            return pages > 0 ? pages : 1; // Ha nincs találat, akkor legalább 1 oldal legyen
        },
        shouldShowClearButton() {
            return this.searchTitle || this.searchAuthor || this.searchYear || this.selectedCategory !== '';
        }
    },
    methods: {
        // Lapozás az előző oldalra
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.currentPageInput = this.currentPage;
            }
        },
        // Lapozás a következő oldalra
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.currentPageInput = this.currentPage;
            }
        },
        // Ugrás az első oldalra
        goToFirstPage() {
            this.currentPage = 1;
            this.currentPageInput = 1;
        },
        // Ugrás az utolsó oldalra
        goToLastPage() {
            this.currentPage = this.totalPages;
            this.currentPageInput = this.totalPages;
        },
        // Manuális oldalszám beírása
        onPageInputChange() {
            if (this.currentPageInput < 1) {
                this.currentPageInput = 1;
            } else if (this.currentPageInput > this.totalPages) {
                this.currentPageInput = this.totalPages;
            }
            this.currentPage = this.currentPageInput;
        },

        // Keresés törlése
        clearSearch() {
            this.searchTitle = '';
            this.searchAuthor = '';
            this.searchYear = '';
            this.selectedCategory = '';
            this.goToFirstPage();
        },
        openModal(item) {
            this.modalItem = { ...item };
            this.modalVisible = true;
        },
        closeModal() {
            this.modalVisible = false;
        },
        // Frissítés a kártya adatain
        handleItemUpdate(updatedItem) {
            const index = this.items.findIndex(item => item.id === updatedItem.id);
            if (index !== -1) {
                this.items.splice(index, 1, updatedItem);
            }
        },
        isBorrowed(item) {
            // Ellenőrizzük, hogy van-e aktív kölcsönzés, azaz nincs visszaadva
            return item.borrowing_media && item.borrowing_media.some(borrow => !borrow.returned_date);
        },
        validatesearchYearInput(event) {
            this.searchYear = event.target.value.replace(/[^0-9]/g, '');
        },
    },
    watch: {
        selectedCategory() {
            this.goToFirstPage();
        },
        searchTitle() {
            this.goToFirstPage();
        },
        searchAuthor() {
            this.goToFirstPage();
        },
        searchYear() {
            this.goToFirstPage();
        },
    },
};
</script>
<style scoped>
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
</style>
