<template>
  <div class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-3/4 md:w-1/2 relative">
      <h2 class="text-2xl font-semibold mb-4">{{ item.title }}</h2>
      <div class="space-y-4">

        <!-- Szerkesztési megjelenítés -->
        <div v-if="isEditing">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Cím</label>
            <input v-model="editableItem.title" id="title" class="w-full p-2 border rounded-md" type="text" />
            <span v-if="errors.title" class="text-red-500 text-sm">{{ errors.title[0] }}</span>
          </div>

          <div>
            <label for="author" class="block text-sm font-medium text-gray-700">Szerző</label>
            <input v-model="editableItem.author" id="author" class="w-full p-2 border rounded-md" type="text" />
            <span v-if="errors.author" class="text-red-500 text-sm">{{ errors.author[0] }}</span>
          </div>

          <div>
            <label for="barcode" class="block text-sm font-medium text-gray-700">Vonalkód</label>
            <input v-model="editableItem.barcode" id="barcode" class="w-full p-2 border rounded-md" type="number" min="0" />
            <span v-if="errors.barcode" class="text-red-500 text-sm">{{ errors.barcode[0] }}</span>
          </div>

          <div>
            <label for="inventory_number" class="block text-sm font-medium text-gray-700">Leltári szám</label>
            <input v-model="editableItem.inventory_number" id="inventory_number" class="w-full p-2 border rounded-md"
              type="text" />
            <span v-if="errors.inventory_number" class="text-red-500 text-sm">{{ errors.inventory_number[0] }}</span>
          </div>

          <div>
            <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
            <input v-model="editableItem.isbn" id="isbn" class="w-full p-2 border rounded-md" type="text" />
            <span v-if="errors.isbn" class="text-red-500 text-sm">{{ errors.isbn[0] }}</span>
          </div>

          <div>
            <label for="year_of_purchasing" class="block text-sm font-medium text-gray-700">Beszerzés éve</label>
            <input v-model="editableItem.year_of_purchasing" id="year_of_purchasing"
              class="w-full p-2 border rounded-md" type="date"  />
            <span v-if="errors.year_of_purchasing" class="text-red-500 text-sm">{{ errors.year_of_purchasing[0]
              }}</span>
          </div>

          <div>
            <label for="published_year" class="block text-sm font-medium text-gray-700">Kiadás éve</label>
            <input v-model="editableItem.published_year" id="published_year" class="w-full p-2 border rounded-md"
              type="number" min="0"/>
            <span v-if="errors.published_year" class="text-red-500 text-sm">{{ errors.published_year[0] }}</span>
          </div>

          <div>
            <label for="supplier" class="block text-sm font-medium text-gray-700">Szállító</label>
            <input v-model="editableItem.supplier" id="supplier" class="w-full p-2 border rounded-md" type="text" />
            <span v-if="errors.supplier" class="text-red-500 text-sm">{{ errors.supplier[0] }}</span>
          </div>

          <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Kategória</label>
            <select v-model="editableItem.categories_id" id="category" class="w-full p-2 border rounded-md">
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}
                <span v-if="category.media_type"> - {{ category.media_type }}</span>
              </option>
            </select>
            <span v-if="errors.categories_id" class="text-red-500 text-sm">{{ errors.categories_id[0] }}</span>
          </div>
        </div>

        <!-- Sima  megjelenítés -->
        <div v-else>
          <div>
            <strong>Szerző:</strong>
            <p>{{ item.author }}</p>
          </div>
          <div>
            <strong>Leltári szám:</strong>
            <p>{{ item.inventory_number }}</p>
          </div>
          <div>
            <strong>Vonalkód:</strong>
            <p>{{ item.barcode }}</p>
          </div>
          <div>
            <strong>ISBN:</strong>
            <p>{{ item.isbn }}</p>
          </div>
          <div>
            <strong>Beszerzés éve:</strong>
            <p>{{ item.year_of_purchasing }}</p>
          </div>
          <div>
            <strong>Kiadás éve:</strong>
            <p>{{ item.published_year }}</p>
          </div>
          <div>
            <strong>Szállító:</strong>
            <p>{{ item.supplier }}</p>
          </div>
          <div>
            <strong>Kategória:</strong>
            <p>{{ categories[item.categories_id - 1].name }} <span
                v-if="categories[item.categories_id - 1].media_type">- {{ categories[item.categories_id - 1].media_type
                }}</span></p>
          </div>
        </div>

        <!-- Kölcsönzés rész -->
        <div v-if="isBorrowing" class="mt-4">
          <div>
            <label for="user" class="block text-sm font-medium text-gray-700">Felhasználó</label>
            <input v-model="searchTerm" id="user" class="w-full p-2 border rounded-md"
              placeholder="Keresés felhasználóban" @input="filterUsers" />
          </div>
          <div class="mt-2">
            <select v-model="selectedUser" class="w-full p-2 border rounded-md">
              <option v-for="user in filteredUsers" :key="user.id" :value="user.id">
                {{ user.name }}
              </option>
            </select>
          </div>
          <div class="mt-4 flex gap-4">
            <button @click="borrowItem" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
              Kölcsönzés
            </button>
            <button @click="cancelBorrowing" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
              Mégsem
            </button>
          </div>
        </div>
      </div>

      <!-- Gombok -->
      <div class="flex flex-wrap justify-start gap-4">
        <!-- Kölcsönzés -->
        <button @click="startBorrowing" v-if="!isEditing && !isBorrowing"
          class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
          Kölcsönzés
        </button>
        <!-- Szerkesztés -->
        <button v-if="!isEditing && !isBorrowing" @click="editItem"
          class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
          Szerkesztés
        </button>
        <!-- Törlés -->
        <button v-if="!isEditing && !isBorrowing" @click="deleteItem"
          class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
          Törlés
        </button>
      </div>

      <!-- Szerkesztési mód -->
      <div v-if="isEditing" class="mt-4 flex gap-4">
        <button @click="saveChanges" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
          Mentés
        </button>
        <button @click="cancelEditing" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
          Mégsem
        </button>
      </div>

      <!-- Bezárás gomb -->
      <button @click="closeModal" class="absolute top-4 right-4 text-2xl text-gray-600 hover:text-gray-900">
        &times;
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    item: Object,
    categories: Array,
    auth: Object,
    users: Array,
  },
  data() {
    return {
      isEditing: false,
      isBorrowing: false,
      editableItem: { ...this.item },
      selectedUser: null,
      searchTerm: "",
      filteredUsers: this.users,
      errors: {}, // Hibák tárolása
    };
  },
  methods: {
    startBorrowing() {
      this.isBorrowing = true;
    },

    // Felhasználók szűrése
    filterUsers() {
      this.filteredUsers = this.users.filter(user => user.name.toLowerCase().includes(this.searchTerm.toLowerCase()));
    },

    // Kölcsönzés
    borrowItem() {
      axios.post(`/api/borrowings`, { itemIds: { id: this.item.id }, userId: this.auth.user.id })
        .then(() => {
          this.$inertia.visit('/borrowed_media');
          alert('Sikeres kölcsönzés!');
        })
        .catch((error) => {
          console.error('Hiba a kölcsönzés során:', error);
          if (error.response && error.response.data.message) {
            alert(error.response.data.message); // Hibás kölcsönzés üzenet kezelése
          } else {
            alert('Hiba történt a kölcsönzés során.');
          }
        });
    },

    // Szerkesztési mód
    editItem() {
      this.isEditing = true;
      this.editableItem = { ...this.item }; // Az eredeti adatokat lemásoljuk, hogy szerkeszthessük
    },

    // Szerkesztés megszakítása
    cancelEditing() {
      this.isEditing = false;
      this.editableItem = { ...this.item }; // A változtatásokat töröljük
      this.errors = {}; // Hibák törlése megszakításkor
    },

    // Változások mentése
    saveChanges() {
      axios.put(`/api/items/${this.item.id}`, this.editableItem)
        .then(() => {
          this.closeModal();
          alert('A változások mentésre kerültek.');
          window.location.reload();
        })
        .catch((error) => {
          console.error('Hiba a változtatások mentésekor:', error);
          if (error.response && error.response.data.errors) {
            // Hibák feldolgozása
            this.errors = error.response.data.errors; // A hibák tárolása
          }
        });
    },


    // Törlés
    deleteItem() {
      if (confirm('Biztosan törli ezt a tételt?')) {
        axios.delete(`/api/items/${this.item.id}`)
          .then(() => {
            this.closeModal();
            this.$inertia.visit('/');
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

    // Kölcsönzés megszakítása
    cancelBorrowing() {
      this.isBorrowing = false;
      this.selectedUser = null;
    },
  },
      // Aktuális dátum
      today() {
      const dtToday = new Date();
      let month = dtToday.getMonth() + 1;
      let day = dtToday.getDate();
      const year = dtToday.getFullYear();
      if (month < 10) month = '0' + month.toString();
      if (day < 10) day = '0' + day.toString();
      return `${year}-${month}-${day}`;
    },

    // Dátum validálás
    onDateInput(event) {
      if (event.target.value != "") {
        const date = new Date(event.target.value);
        const dtToday = new Date();
        if (date.getTime() > dtToday.getTime()) {
          setTimeout(() => {
            event.target.value = this.today(); // Ha jövőbeli dátumot választanak, akkor a mai dátumra állítja
          });
        }
      }
    },
};
</script>

<style scoped>
button {
  transition: all 0.3s ease;
  width: auto;
  font-size: 0.875rem;
}

button:hover {
  transform: scale(1.05);
}

button.absolute {
  font-size: 1.5rem;
  cursor: pointer;
}
</style>
