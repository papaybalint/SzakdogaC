<template>
  <div class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 z-50">
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg relative w-full mx-2 overflow-y-auto max-h-[90vh]" :class="{
      'sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/2': isEditing || isBorrowing,
      'sm:w-3/4 md:w-1/2 lg:w-1/3 xl:w-1/4': !isEditing && !isBorrowing
    }">
      <h2 class="text-2xl font-semibold mb-4 pr-10">
        {{ item.title }}
        <span v-if="isBorrowed(item)" class="borrowed-badge">
          Kikölcsönözve
        </span>
      </h2>
      <div class="space-y-4">

        <!-- Szerkesztési nézet -->
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
            <input v-model="editableItem.barcode" id="barcode" class="w-full p-2 border rounded-md" type="text"
              @input="onBarcodeInput" />
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
              class="w-full p-2 border rounded-md" type="date" :max="today()" @input="onDateInput" />
            <span v-if="errors.year_of_purchasing" class="text-red-500 text-sm">{{ errors.year_of_purchasing[0]
            }}</span>
          </div>

          <div>
            <label for="published_year" class="block text-sm font-medium text-gray-700">Kiadás éve</label>
            <input v-model="editableItem.published_year" id="published_year" class="w-full p-2 border rounded-md"
              type="text" @input="onPublishedYearInput" />
            <span v-if="errors.published_year" class="text-red-500 text-sm">{{ errors.published_year[0] }}</span>
          </div>

          <div>
            <label for="supplier" class="block text-sm font-medium text-gray-700">Szállító</label>
            <input v-model="editableItem.supplier" id="supplier" class="w-full p-2 border rounded-md" type="text"
              style="text-transform: capitalize;" />
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

        <!-- Alap nézet -->
        <div v-if="!isBorrowing && !isEditing">
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

        <!-- Kölcsönzés nézet-->
        <div v-if="isBorrowing" class="mt-4">

          <!-- Kereső mező -->
          <div class="mb-4">
            <input v-model="searchTerm" type="text" placeholder="Keresés név, email vagy telefonszám szerint"
              class="w-full p-2 border rounded-md" />
          </div>

          <!-- Felhasználók lista -->
          <div class="space-y-4 max-h-96 overflow-y-auto">
            <div v-for="user in filteredUsers" :key="user.id" :class="[
              'p-4 rounded-lg shadow-md flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4',
              user.id === auth.user.id ? 'bg-green-100 border border-green-400' : 'bg-gray-100'
            ]">
              <div>
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-base">{{ user.full_name }}</span>
                  <span v-if="user.id === auth.user.id" class="text-sm bg-green-500 text-white px-2 py-1 rounded-full">
                    Aktív felhasználó
                  </span>
                </div>
                <p>Email: {{ user.email }}</p>
                <p>Telefonszám: {{ user.phone }}</p>
              </div>
              <button @click="borrowItem(user)"
                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 w-full sm:w-auto">
                Kölcsönzés
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Gombok -->
      <div v-if="!isBorrowing && !isEditing" class="flex flex-wrap justify-center gap-4 mt-8">
        <button @click="startBorrowing" v-if="!isEditing && !isBorrowing" :disabled="isBorrowed(item)"
          :class="{ 'opacity-25': isBorrowed(item) }"
          class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 w-full sm:w-auto">
          Kölcsönzés
        </button>
        <button v-if="!isEditing && !isBorrowing" @click="editItem" :disabled="isBorrowed(item)"
          :class="{ 'opacity-25': isBorrowed(item) }"
          class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 w-full sm:w-auto">
          Szerkesztés
        </button>
        <button v-if="!isEditing && !isBorrowing" @click="deleteItem" :disabled="isBorrowed(item)"
          :class="{ 'opacity-25': isBorrowed(item) }"
          class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full sm:w-auto">
          Törlés
        </button>
      </div>


      <!-- Szerkesztési nézet gombok -->
      <div v-if="isEditing" class="mt-4 flex gap-4 justify-center">
        <button @click="saveChanges"
          class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 w-full sm:w-auto">
          Mentés
        </button>
        <button @click="cancelEditing"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 w-full sm:w-auto">
          Mégsem
        </button>
      </div>

      <!-- Kölcsönzés nézet gombok -->
      <div v-if="isBorrowing" class="mt-4 flex gap-4 justify-center">
        <button @click="cancelBorrowing"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 w-full sm:w-auto">
          Mégsem
        </button>
      </div>

      <!-- Bezárás gomb -->
      <button @click="closeModal"
        class="absolute top-4 right-4 text-white text-2xl w-8 h-8 bg-red-600 hover:bg-red-700 flex items-center justify-center rounded-lg z-10">
        &times;
      </button>
    </div>

  </div>
  <!-- Törlés megerősítése gomb -->
  <div class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 z-50" v-if="isDeleteModalOpen">
    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg relative w-full mx-2 max-w-sm">
      <h2 class="text-2xl font-semibold mb-4">Törlés megerősítése</h2>
      <p>Biztosan törölni szeretnéd ezt a tételt?</p>
      <div class="flex justify-between mt-4">
        <button @click="confirmDelete"
          class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full sm:w-auto">
          Törlés
        </button>
        <button @click="closeDeleteModal"
          class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 w-full sm:w-auto">
          Mégsem
        </button>
      </div>
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
      isEditing: false,
      isBorrowing: false,
      editableItem: { ...this.item },
      errors: {},
      searchTerm: '',
      users: [],
      isDeleteModalOpen: false,
    };
  },
  computed: {
    filteredUsers() {
      const term = this.searchTerm.toLowerCase();

      const filtered = this.users.filter(user =>
        user.full_name.toLowerCase().includes(term) ||
        user.email.toLowerCase().includes(term) ||
        user.phone.includes(term)
      );

      return filtered.sort((a, b) => {
        const isCurrentUserA = a.id === this.auth.user.id;
        const isCurrentUserB = b.id === this.auth.user.id;

        if (isCurrentUserA && !isCurrentUserB) return -1;
        if (!isCurrentUserA && isCurrentUserB) return 1;

        return a.full_name.localeCompare(b.full_name, 'hu', { sensitivity: 'base' });
      });
    },
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      try {
        const response = await axios.get('/api/users');
        this.users = response.data.users.map(user => ({
          ...user,
          full_name: `${user.first_name} ${user.last_name}`,
        }));
      } catch (error) {
        console.error('Hiba a felhasználók lekérésekor:', error);
      }
    },

    borrowItem(user) {
      if (!this.item || !user) {
        alert('Hiba: Nincs tétel vagy felhasználó!');
        return;
      }

      axios.post('/api/borrowings', {
        itemIds: { id: this.item.id },
        userId: user.id, 
      })
        .then(() => {
          // alert('Sikeres kölcsönzés!');
          this.item.borrowing = true;
          this.$emit('update', this.item);
          this.closeModal();
        })
        .catch((error) => {
          console.error('Hiba a kölcsönzés során:', error);
          if (error.response && error.response.data.message) {
            alert(error.response.data.message);
          } else {
            alert('Hiba történt a kölcsönzés során.');
          }
        });
    },
    isBorrowed(item) {
      return item.borrowing;
    },

    startBorrowing() {
      this.isBorrowing = true; 
    },

    editItem() {
      this.isEditing = true;
      this.editableItem = { ...this.item }; 
      this.editableItem.year_of_purchasing = this.item.year_of_purchasing.replaceAll(".", "-");
    },

    cancelEditing() {
      this.isEditing = false;
      this.editableItem = { ...this.item }; 
      this.errors = {}; 
    },

    saveChanges() {
      this.editableItem.title = this.editableItem.title.charAt(0).toUpperCase() + this.editableItem.title.slice(1);
      this.editableItem.author = this.editableItem.author.charAt(0).toUpperCase() + this.editableItem.author.slice(1);
      this.editableItem.supplier = this.editableItem.supplier.charAt(0).toUpperCase() + this.editableItem.supplier.slice(1);
      axios.put(`/api/items/${this.item.id}`, this.editableItem)
        .then(() => {
          this.$emit('update', this.editableItem);
          this.closeModal();
          // alert('A változások mentésre kerültek.');
        })
        .catch((error) => {
          console.error('Hiba a változtatások mentésekor:', error);
          if (error.response && error.response.data.errors) {
            this.errors = error.response.data.errors; 
          }
        });
    },

    deleteItem() {
      this.isDeleteModalOpen = true;
    },

    confirmDelete() {
      axios.get(`/api/items/${this.item.id}`)
        .then(response => {
          const updatedItem = response.data;

          if (updatedItem.is_borrowed) {
            alert('A könyv jelenleg ki van kölcsönözve, nem törölhető!');
            return;
          }

          axios.delete(`/api/items/${this.item.id}`)
            .then(() => {
              this.$emit('update', this.item.id);
              this.closeModal();
            })
            .catch((error) => {
              console.error('Hiba a törlés során:', error);
              alert('Hiba történt a törlés során.');
            });
        })
        .catch(error => {
          console.error('Hiba az adatlekérés során:', error);
          alert('Hiba történt az adat lekérése közben.');
        });

      this.isDeleteModalOpen = false;
    },

    closeDeleteModal() {
      this.isDeleteModalOpen = false;
    },

    closeModal() {
      this.$emit('close');
    },

    cancelBorrowing() {
      this.isBorrowing = false;
      this.selectedUser = null;
    },

    today() {
      const dtToday = new Date();
      let month = dtToday.getMonth() + 1;
      let day = dtToday.getDate();
      const year = dtToday.getFullYear();
      if (month < 10) month = '0' + month.toString();
      if (day < 10) day = '0' + day.toString();
      return `${year}-${month}-${day}`;
    },

    onDateInput() {
      const selectedDate = new Date(this.editableItem.year_of_purchasing); 
      const today = new Date();

      if (selectedDate.getTime() > today.getTime()) {
        this.editableItem.year_of_purchasing = this.today();
      }
    },
    onPublishedYearInput() {
      this.editableItem.published_year = this.editableItem.published_year.replace(/[^0-9]/g, '');
    },
    onBarcodeInput() {
      this.editableItem.barcode = this.editableItem.barcode.replace(/[^0-9]/g, '');
    },
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
  width: 2rem;
  height: 2rem;
  background-color: #f87171;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s;
  border-radius: 0.375rem;
}

button.absolute:hover {
  background-color: #ef4444;
}

.borrowed-badge {
  display: block;
  background-color: red;
  color: white;
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 14px;
  margin-top: 5px;
  width: 150px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
