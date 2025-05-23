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

            <div class="flex flex-col sm:flex-row w-full sm:w-2/3 md:w-1/2 space-y-4 sm:space-y-0 sm:space-x-4">
                <input v-model="searchTitle" type="text" placeholder="Cím"
                    class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                <input v-model="searchAuthor" type="text" placeholder="Szerző"
                    class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    style="text-transform: capitalize;" @input="validateAuthorInput" />
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
                class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col relative"
                :class="{ 'pb-10': isBorrowed(item) && auth.user.role !== 'admin' }">
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

        <!-- Lapozás -->
        <div class="mt-6 flex flex-wrap justify-center items-center gap-3 sm:gap-4">
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

        <!-- Részletek modal ablaka -->
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
            searchTitle: '',
            searchAuthor: '',
            searchYear: '',
            selectedCategory: '',
            currentPage: 1,
            currentPageInput: 1,
            pageSize: 15,
            modalVisible: false,
            modalItem: null,
            itemList: this.items,
        };
    },
    computed: {
        filteredItems() {
            return this.itemList.filter(item => {
                const normalizedTitle = this.normalizeText(item.title.toLowerCase());
                const normalizedAuthor = this.normalizeText(item.author.toLowerCase());
                const normalizedSearchTitle = this.normalizeText(this.searchTitle.toLowerCase());
                const normalizedSearchAuthor = this.normalizeText(this.searchAuthor.toLowerCase());
                const normalizedSearchYear = this.searchYear.trim();

                const matchesTitle = normalizedTitle.includes(normalizedSearchTitle);
                const matchesAuthor = normalizedAuthor.includes(normalizedSearchAuthor);
                const matchesYear = item.published_year.toString().includes(normalizedSearchYear);
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
            return pages > 0 ? pages : 1;
        },
        shouldShowClearButton() {
            return this.searchTitle || this.searchAuthor || this.searchYear || this.selectedCategory !== '';
        }
    },
    methods: {
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
            document.querySelector("body").style.overflow = "hidden";
        },
        closeModal() {
            this.modalVisible = false;
            document.querySelector("body").style.overflow = "";
        },
        handleItemUpdate(updatedItemOrId) {
            if (typeof updatedItemOrId === 'object') {
                const index = this.itemList.findIndex(item => item.id === updatedItemOrId.id);
                if (index !== -1) {
                    this.itemList = [
                        ...this.itemList.slice(0, index),
                        updatedItemOrId,
                        ...this.itemList.slice(index + 1)
                    ];
                }
            } else if (typeof updatedItemOrId === 'number') {
                this.itemList = this.itemList.filter(item => item.id !== updatedItemOrId);
            }
        },

        isBorrowed(item) {
            return item.borrowing;
        },
        validatesearchYearInput(event) {
            this.searchYear = event.target.value.replace(/[^0-9]/g, '');
        },
        validateAuthorInput(event) {
            this.searchAuthor = event.target.value.replace(/[^a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ\s]/g, '');
        },
        normalizeText(text) {
            return text
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^\w\s]/g, '');
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

.pagination-btn .arrow {
    display: none;
}

@media (max-width: 640px) {
    .pagination-btn .text {
        display: none;
    }

    .pagination-btn .arrow {
        display: inline-block;
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
