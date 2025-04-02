<template>
    <div class="container mx-auto p-4">
      <div v-if="item">
        <!-- Tétel részletes információk -->
        <h2 class="text-2xl font-semibold mb-4">{{ item.title }}</h2>
        <p class="text-sm text-gray-600 mb-2">Szerző: {{ item.author }}</p>
        <p class="text-sm text-gray-600 mb-2">Kiadás dátuma: {{ item.published_year }}</p>
        <p class="text-sm text-gray-600 mb-2">Kategória: {{ categoryName }}</p>

        <!-- Kölcsönzés gomb -->
        <button @click="borrowItem" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
          Kölcsönzés
        </button>

        <!-- Szerkesztés gomb -->
        <button @click="editItem" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 ml-4">
          Szerkesztés
        </button>

        <!-- Törlés gomb -->
        <button @click="deleteItem" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 ml-4">
          Törlés
        </button>
      </div>
      <!-- Hiba esetén üzenet -->
      <div v-else>
        <p>Nem található az adott tétel.</p>
      </div>
    </div>
  </template>

  <script>
  export default {
    data() {
      return {
        item: null,
        categoryName: ''
      };
    },
    methods: {
      // A tétel betöltése
      async loadItem() {
        try {
          const response = await axios.get(`/api/items/${this.$route.params.id}`);
          this.item = response.data;
          this.loadCategory();
        } catch (error) {
          console.error('Hiba a tétel betöltésekor:', error);
        }
      },
      // Kategória betöltése a tételhez
      async loadCategory() {
        try {
          const response = await axios.get(`/api/categories/${this.item.categories_id}`);
          this.categoryName = response.data.name;
        } catch (error) {
          console.error('Hiba a kategória betöltésekor:', error);
        }
      },
      // Kölcsönzés logika
      borrowItem() {
        // A kölcsönzési logika meghívása
        axios.post(`/api/borrowings`, { itemId: this.item.id })
          .then(response => {
            alert('Sikeres kölcsönzés!');
          })
          .catch(error => {
            console.error('Hiba a kölcsönzés során:', error);
          });
      },
      // Szerkesztés logika
      editItem() {
        this.$router.push(`/items/edit/${this.item.id}`);  // ide még kell egy editItem.vue
      },
      // Törlés logika
      deleteItem() {
        if (confirm('Biztosan törli ezt a tételt?')) {
          axios.delete(`/api/items/${this.item.id}`)
            .then(response => {
              this.$router.push('/items');
            })
            .catch(error => {
              console.error('Hiba a törlés során:', error);
            });
        }
      }
    },
    created() {
      this.loadItem();
    }
  };
  </script>

  <style scoped>
  /* Stílusok a gombokhoz és az oldalhoz */
  button {
    transition: all 0.3s ease;
  }

  button {
    width: auto;
    font-size: 0.875rem;
  }

  button:hover {
    transform: scale(1.05);
  }
  </style>
