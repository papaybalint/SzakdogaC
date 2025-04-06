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
                <input v-model="searchYear" type="text" placeholder="Kiadás dátuma"
                    class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            </div>

            <button v-if="shouldShowClearButton" @click="clearSearch"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Keresés törlése
            </button>
        </div>

        <!-- Kártyák -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            <div v-for="item in paginatedItems" :key="item.id"
                class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col">
                <h3 class="text-lg font-semibold mb-2 text-gray-800">{{ item.title }}</h3>
                <p class="text-sm text-gray-600 mb-1">Szerző: {{ item.author }}</p>
                <p class="text-sm text-gray-600 mb-1">Kiadás dátuma: {{ item.published_year }}</p>
                <p class="text-sm text-gray-600 mb-1">Kategória: {{ categories[item.categories_id - 1].name }} <span
                        v-if="categories[item.categories_id - 1].media_type">- {{ categories[item.categories_id -
                            1].media_type
                        }}</span></p>

                <!-- Részletek Gomb -->
                <div class="mt-auto flex justify-end space-x-2 space-y-2 sm:space-y-0">
                    <button @click="openModal(item)"
                        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-xs sm:text-sm">
                        Részletek
                    </button>
                </div>
            </div>
        </div>

        <!-- Lapozás -->
        <div class="mt-6 flex justify-center items-center space-x-4">
            <!-- Első gomb -->
            <button @click="goToFirstPage"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50"
                :disabled="currentPage === 1">
                Első
            </button>

            <!-- Előző gomb -->
            <button @click="previousPage" :disabled="currentPage === 1"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50">
                Előző
            </button>

            <!-- Oldalszám módosítása -->
            <div class="flex items-center space-x-2">
                <input v-model.number="currentPageInput" type="number" min="1" :max="totalPages"
                    class="w-12 text-center p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @change="onPageInputChange" />
                <span class="text-sm text-gray-600">/ {{ totalPages }}</span>
            </div>

            <!-- Következő gomb -->
            <button @click="nextPage" :disabled="currentPage === totalPages"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50">
                Következő
            </button>

            <!-- Utolsó gomb -->
            <button @click="goToLastPage"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50"
                :disabled="currentPage === totalPages">
                Utolsó
            </button>
        </div>
        <!-- Modal ablak -->
        <ItemDetailModal v-if="modalVisible" :item="modalItem" :categories="categories" :auth="auth"
            @close="closeModal" />
    </div>

</template>

<script>
import ItemDetailModal from './ItemDetailModal.vue';
localStorage.setItem('testKey', 'testValue');

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
            searchTitle: localStorage.getItem('searchTitle') || '',  // Betöltés a localStorage-ból
            searchAuthor: localStorage.getItem('searchAuthor') || '',  // Betöltés a localStorage-ból
            searchYear: localStorage.getItem('searchYear') || '',  // Betöltés a localStorage-ból
            selectedCategory: localStorage.getItem('selectedCategory') || '',  // Betöltés a localStorage-ból
            currentPage: 1,
            currentPageInput: 1,
            pageSize: 15, // Kártyák száma egy oldalra
            modalVisible: false,
            modalItem: null,
            debounceTimeout: null,
        };
    },
    computed: {
        filteredItems() {
            return this.items.filter(item => {
                const matchesTitle = item.title.toLowerCase().includes(this.searchTitle.toLowerCase());
                const matchesAuthor = item.author.toLowerCase().includes(this.searchAuthor.toLowerCase());
                const matchesYear = item.published_year.toString().includes(this.searchYear);
                const matchesCategory = this.selectedCategory
                    ? item.categories_id === this.selectedCategory
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
        saveSearchSettings() {
        if (this.debounceTimeout) clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(() => {
            localStorage.setItem('searchTitle', this.searchTitle);
            localStorage.setItem('searchAuthor', this.searchAuthor);
            localStorage.setItem('searchYear', this.searchYear);
            localStorage.setItem('selectedCategory', this.selectedCategory);
        }, 500); // 500ms késleltetés
    },
        // Lapozás az előző oldalra
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.currentPageInput = this.currentPage;
                this.scrollToTop();
            }
        },
        // Lapozás a következő oldalra
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.currentPageInput = this.currentPage;
                this.scrollToTop();
            }
        },
        // Ugrás az első oldalra
        goToFirstPage() {
            this.currentPage = 1;
            this.currentPageInput = 1;
            this.scrollToTop();
        },
        // Ugrás az utolsó oldalra
        goToLastPage() {
            this.currentPage = this.totalPages;
            this.currentPageInput = this.totalPages;
            this.scrollToTop();
        },
        // Manuális oldalszám beírása
        onPageInputChange() {
            if (this.currentPageInput < 1) {
                this.currentPageInput = 1;
            } else if (this.currentPageInput > this.totalPages) {
                this.currentPageInput = this.totalPages;
            }
            this.currentPage = this.currentPageInput;
            this.scrollToTop();
        },
        // az oldal tetejére csúsztatás
        scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        },
        // Keresés törlése
        clearSearch() {
            this.searchTitle = '';
            this.searchAuthor = '';
            this.searchYear = '';
            this.selectedCategory = '';
            this.saveSearchSettings();
        },
        openModal(item) {
            this.modalItem = { ...item };
            this.modalVisible = true;
        },
        closeModal() {
            this.modalVisible = false;

            // Manuálisan frissítjük a kártyák listáját
            this.updateItems();
        },

        updateItems() {
            this.filteredItems;  // Kényszerítjük, hogy újra számolja a szűrt elemeket
            this.paginatedItems; // Frissítjük a lapozott elemeket
            this.scrollToTop();  // Görgetés a tetejére
        },
    },
    watch: {
        // Figyeljük a kategória változását
        selectedCategory(newCategory) {
            this.currentPage = 1;
            this.currentPageInput = 1; // Az oldalszám frissítése
            this.scrollToTop();  // Görgetés a tetejére
            this.saveSearchSettings();
        },
        // Figyeljük a kereső mezők változását
        searchTitle(newValue) {
            this.currentPage = 1;
            this.currentPageInput = 1;
            this.saveSearchSettings();
        },
        searchAuthor(newValue) {
            this.currentPage = 1;
            this.currentPageInput = 1;
            this.saveSearchSettings();
        },
        searchYear(newValue) {
            this.currentPage = 1;
            this.currentPageInput = 1;
            this.saveSearchSettings();
        },
        modalVisible(newValue) {
            if (!newValue) {
                this.updateItems();  // Ha a modal bezárul, frissítjük a kártyákat
            }
        }
    },
    mounted() {
        // Beállítjuk a szűrőket, ha a localStorage tartalmaz értékeket
        this.searchTitle = localStorage.getItem('searchTitle') || '';
        this.searchAuthor = localStorage.getItem('searchAuthor') || '';
        this.searchYear = localStorage.getItem('searchYear') || '';
        this.selectedCategory = localStorage.getItem('selectedCategory') || '';
    }
};
</script>
<style scoped>
.grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
}

@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
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

@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }

    button {
        width: 100%;
    }
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
