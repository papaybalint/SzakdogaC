<template>
  <div class="container mx-auto p-4">
    <div v-if="item">
      <!-- Tétel részletes információk -->
      <h2 class="text-2xl font-semibold mb-4">{{ item.title }}</h2>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div>
          <strong>Szerző:</strong>
          <p>{{ item.author }}</p>
        </div>
        <div>
          <strong>Kiadás dátuma:</strong>
          <p>{{ item.published_year }}</p>
        </div>
        <div>
          <strong>ISBN:</strong>
          <p>{{ item.isbn }}</p>
        </div>
        <div>
          <strong>Leltári szám:</strong>
          <p>{{ item.inventory_number }}</p>
        </div>
        <div>
          <strong>Beszerzés éve:</strong>
          <p>{{ item.year_of_purchasing }}</p>
        </div>
        <div>
          <strong>Szállító:</strong>
          <p>{{ item.supplier }}</p>
        </div>
        <div>
          <strong>Kategória:</strong>
          <p>{{ categories[item.categories_id - 1].name }} <span
                        v-if="categories[item.categories_id - 1].media_type">- {{ categories[item.categories_id -
                            1].media_type
                        }}</span></p>
        </div>
      </div>

      <!-- Kölcsönzés, szerkesztés és törlés gombok -->
      <div class="flex flex-wrap justify-start gap-4">
        <button @click="borrowItem" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
          Kölcsönzés
        </button>
        <button @click="editItem" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
          Szerkesztés
        </button>
        <button @click="deleteItem" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
          Törlés
        </button>
      </div>
    </div>

    <!-- Hiba esetén üzenet -->
    <div v-else>
      <p>Nem található az adott tétel.</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    item: Object,
    categories: Array,
    auth: Object,
  },
  methods: {

    // Kölcsönzés logika
    borrowItem() {
      axios.post(`/api/borrowings`, { itemIds: { id: this.item.id }, userId: this.auth.user.id })
        .then(() => {
          alert('Sikeres kölcsönzés!');
        })
        .catch((error) => {
          console.error('Hiba a kölcsönzés során:', error);
        });
    },

    // Szerkesztés logika
    editItem() {
      this.$router.push(`/items/edit/${this.item.id}`);
    },

    // Törlés logika
    deleteItem() {
      if (confirm('Biztosan törli ezt a tételt?')) {
        axios.delete(`/api/items/${this.item.id}`)
          .then(() => {
            this.$router.push('/items');
          })
          .catch((error) => {
            console.error('Hiba a törlés során:', error);
          });
      }
    }
  },
};
</script>

<style scoped>
/* Reszponzív grid stílus */
.grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 640px) {
  .grid {
    grid-template-columns: 1fr 1fr;
  }
}

@media (min-width: 768px) {
  .grid {
    grid-template-columns: 1fr 1fr 1fr;
  }
}

/* Gombok stílusa */
button {
  transition: all 0.3s ease;
  width: auto;
  font-size: 0.875rem;
}

button:hover {
  transform: scale(1.05);
}
</style>
