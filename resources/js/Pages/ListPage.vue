<template>
  <div class="container mx-auto p-4">
    <div class="flex flex-col sm:flex-row items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4 w-full">
      <!-- Kategóriák szűrése -->
      <select v-model="selectedCategory"
        class="p-2 w-full sm:w-1/3 md:w-1/4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <option value="">Minden kategória</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.name }} <span v-if="category.media_type">- {{ category.media_type }}</span>
        </option>
      </select>

      <!-- Cím, Szerző, Kiadás dátumának keresőmezői -->
      <div class="flex w-full sm:w-2/3 md:w-1/2 space-x-4">
        <input v-model="searchTitle" type="text" placeholder="Cím"
          class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <input v-model="searchAuthor" type="text" placeholder="Szerző"
          class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <input v-model="searchYear" type="text" placeholder="Kiadás dátuma"
          class="p-2 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" />
      </div>
    </div>

    <!-- Kártyák -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="item in filteredItems" :key="item.id"
        class="bg-white p-4 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
        <h3 class="text-lg font-semibold mb-2 text-gray-800">{{ item.title }}</h3>
        <p class="text-sm text-gray-600 mb-1">Szerző: {{ item.author }}</p>
        <p class="text-sm text-gray-600 mb-1">Kiadás dátuma: {{ item.published_year }}</p>
        <p class="text-sm text-gray-600 mb-1">Kategória: {{ categories[item.categories_id - 1].name }} <span
            v-if="categories[item.categories_id - 1].media_type">- {{ categories[item.categories_id - 1].media_type
            }}</span></p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    items: Array,
    categories: Array
  },
  data() {
    return {
      searchTitle: '',
      searchAuthor: '',
      searchYear: '',
      selectedCategory: ''
    };
  },
  computed: {
    // Szűrés
    filteredItems() {
      return this.items.filter(item => {
        // Cím, Szerző, Kiadás dátuma van-e
        const hasContent = item.title || item.author || item.published_year;

        // Keresés a címben
        const matchesTitle = item.title.toLowerCase().includes(this.searchTitle.toLowerCase());

        // Keresés: Szerző
        const matchesAuthor = item.author.toLowerCase().includes(this.searchAuthor.toLowerCase());

        // Keresés: Kiadás dátuma
        const matchesYear = item.published_year.toString().includes(this.searchYear);

        // Kategória szűrés
        const matchesCategory = this.selectedCategory
          ? item.categories_id === this.selectedCategory
          : true;

        return hasContent && matchesTitle && matchesAuthor && matchesYear && matchesCategory;
      });
    }
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
</style>
