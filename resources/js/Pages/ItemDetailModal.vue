<template>
  <div class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-3/4 md:w-1/2">
      <h2 class="text-2xl font-semibold mb-4">{{ item.title }}</h2>
      <div class="space-y-4">

        <!-- Szerkesztési mód aktiválása: input mezők -->
        <div v-if="isEditing">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Cím</label>
            <input v-model="editableItem.title" id="title" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="author" class="block text-sm font-medium text-gray-700">Szerző</label>
            <input v-model="editableItem.author" id="author" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="published_year" class="block text-sm font-medium text-gray-700">Kiadás dátuma</label>
            <input v-model="editableItem.published_year" id="published_year" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
            <input v-model="editableItem.isbn" id="isbn" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="inventory_number" class="block text-sm font-medium text-gray-700">Leltári szám</label>
            <input v-model="editableItem.inventory_number" id="inventory_number" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="year_of_purchasing" class="block text-sm font-medium text-gray-700">Beszerzés éve</label>
            <input v-model="editableItem.year_of_purchasing" id="year_of_purchasing" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="supplier" class="block text-sm font-medium text-gray-700">Szállító</label>
            <input v-model="editableItem.supplier" id="supplier" class="w-full p-2 border rounded-md" type="text" />
          </div>
          <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Kategória</label>
            <select v-model="editableItem.categories_id" id="category" class="w-full p-2 border rounded-md">
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
          </div>
        </div>

        <!-- Ha nem szerkesztünk, akkor sima szöveges megjelenítés -->
        <div v-else>
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
                v-if="categories[item.categories_id - 1].media_type">- {{ categories[item.categories_id - 1].media_type }}</span></p>
          </div>
        </div>
      </div>

      <!-- Kölcsönzés, szerkesztés és törlés gombok -->
      <div class="flex flex-wrap justify-start gap-4">
        <button @click="startBorrowing" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
          Kölcsönzés
        </button>
        <button v-if="!isEditing" @click="editItem" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
          Szerkesztés
        </button>
        <button @click="deleteItem" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
          Törlés
        </button>
      </div>

      <!-- Szerkesztési mód kezelése -->
      <div v-if="isEditing" class="mt-4 flex gap-4">
        <button @click="cancelEditing" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
          Mégsem
        </button>
        <button @click="saveChanges" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
          Mentés
        </button>
      </div>

      <button @click="closeModal" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 mt-4">Bezárás</button>
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
  data() {
    return {
      isEditing: false, // Szerkesztési mód aktíválása
      editableItem: { ...this.item }, // A szerkesztéshez használt adatokat másoljuk
    };
  },
  methods: {
    // Kölcsönzés előkészítése
    startBorrowing() {
      this.isBorrowing = true;
    },

    // Kölcsönzés logika
    borrowItem() {
      if (!this.selectedUser) {
        alert('Kérjük, válasszon felhasználót!');
        return;
      }
      
      axios.post(`/api/borrowings`, { itemIds: { id: this.item.id }, userId: this.selectedUser })
        .then(() => {
          alert('Sikeres kölcsönzés!');
          this.closeModal();
        })
        .catch((error) => {
          console.error('Hiba a kölcsönzés során:', error);
        });
    },

    // Szerkesztési mód aktiválása
    editItem() {
      this.isEditing = true;
      this.editableItem = { ...this.item }; // Az eredeti adatokat lemásoljuk, hogy szerkeszthessük
    },

    // Mégsem gomb: a szerkesztési mód megszakítása
    cancelEditing() {
      this.isEditing = false;
      this.editableItem = { ...this.item }; // A változtatásokat töröljük
    },

    // Változások mentése
    saveChanges() {
      axios.put(`/api/items/${this.item.id}`, this.editableItem)
        .then(() => {
          this.isEditing = false;
          alert('A változások mentésre kerültek.');
        })
        .catch((error) => {
          console.error('Hiba a változtatások mentésekor:', error);
        });
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
    },

    // Modal bezárása
    closeModal() {
      this.$emit('close');
    },
  },
};
</script>

<style scoped>
/* Modal stílusok */
button {
  transition: all 0.3s ease;
  width: auto;
  font-size: 0.875rem;
}

button:hover {
  transform: scale(1.05);
}
</style>
