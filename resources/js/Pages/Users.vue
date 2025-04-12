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

  <Head title="Felhasználók" />
  <div>
    <header>
      <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth" />
    </header>

    <main class="mt-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Felhasználók</h1>

        <div v-if="auth.user.role === 'admin'" class="mb-6 flex gap-4 items-center flex-wrap">
          <!-- Admin szűrő mezők -->
          <input v-model="searchName" type="text" placeholder="Teljes név"
            class="p-2 border border-gray-300 rounded-lg w-1/5" style="text-transform: capitalize;"
            @input="validateNameInput" />
          <input v-model="searchEmail" type="text" placeholder="Email"
            class="p-2 border border-gray-300 rounded-lg w-1/5" />
          <input v-model="searchUsername" type="text" placeholder="Felhasználónév"
            class="p-2 border border-gray-300 rounded-lg w-1/5" />
          <input v-model="searchPhone" type="text" placeholder="Telefonszám"
            class="p-2 border border-gray-300 rounded-lg w-1/5" @input="validatePhoneInput" />
          <input v-model="searchBirthPlace" type="text" placeholder="Születési hely"
            class="p-2 border border-gray-300 rounded-lg w-1/5" style="text-transform: capitalize;"
            @input="validateBirthPlaceInput" />
          <input v-model="searchBirthDate" type="date" placeholder="Születési dátum"
            class="p-2 border border-gray-300 rounded-lg w-1/5" />
          <button v-if="shouldShowClearButton" @click="clearSearch"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
            Keresés törlése
          </button>
        </div>

        <!-- Felhasználók lista -->
        <div v-if="paginatedItems.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
          <div class="w-full" v-for="user in paginatedItems" :key="user.id">
            <div class="card bg-white rounded-lg shadow-lg p-4 h-full flex flex-col justify-between">
              <div class="card-header">
                <h2 class="text-xl font-bold mb-2">
                  {{ user.first_name }} {{ user.last_name }}
                </h2>
              </div>
              <div class="card-content">
                <p class="text-gray-700 text-sm"><strong>Felhasználónév:</strong> {{ user.username }}</p>
                <p class="text-gray-700 text-sm"><strong>Email:</strong> {{ user.email }}</p>
                <p class="text-gray-700 text-sm"><strong>Telefonszám:</strong> {{ user.phone }}</p>
                <p class="text-gray-700 text-sm"><strong>Születési hely:</strong> {{ user.birth_place }}</p>
                <p class="text-gray-700 text-sm"><strong>Születési dátum:</strong> {{ user.birth_date }}</p>
              </div>
              <div class="card-footer mt-auto flex justify-end">
                <button v-if="auth.user.role === 'admin'" @click="confirmDelete(user.id)"
                  class="mt-4 bg-red-500 text-white py-2 px-5 text-base rounded-lg hover:bg-red-600">
                  Törlés
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Lapozás -->
        <div class="mt-6 flex justify-center items-center space-x-4">
          <button @click="goToFirstPage"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50"
            :disabled="currentPage === 1">
            Első
          </button>

          <button @click="previousPage" :disabled="currentPage === 1"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50">
            Előző
          </button>

          <div class="flex items-center space-x-2">
            <input v-model.number="currentPageInput" type="number" min="1" :max="totalPages"
              class="w-12 text-center p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
              @change="onPageInputChange" />
            <span class="text-sm text-gray-600">/ {{ totalPages }}</span>
          </div>

          <button @click="nextPage" :disabled="currentPage === totalPages"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50">
            Következő
          </button>

          <button @click="goToLastPage"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 disabled:opacity-50"
            :disabled="currentPage === totalPages">
            Utolsó
          </button>
        </div>

      </div>
    </main>

    <footer>
      <Footer />
    </footer>

    <!-- Törlés megerősítő modális ablak -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg">
        <h3 class="text-xl mb-4">Biztosan törölni szeretnéd ezt a felhasználót?</h3>
        <div class="flex justify-between">
          <button @click="deleteUser" class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600">
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
  name: 'Users',
  data() {
    return {
      searchName: '',
      searchEmail: '',
      searchUsername: '',
      searchPhone: '',
      searchBirthPlace: '',
      searchBirthDate: '',
      currentPage: 1,
      currentPageInput: 1,
      users: [],
      pageSize: 15,
      isDeleteModalOpen: false,
      selectedUserId: null,
    };
  },
  computed: {
    filteredUsers() {
      return this.users.filter(user => {
        const fullName = `${user.first_name} ${user.last_name}`.toLowerCase();
        const email = user.email.toLowerCase();
        const username = user.username.toLowerCase();
        const phone = user.phone ? user.phone.toLowerCase() : '';
        const birthPlace = user.birth_place ? user.birth_place.toLowerCase() : '';
        const birthDate = user.birth_date ? user.birth_date : '';  // Date format comparison

        const searchNameMatch = this.normalizeText(fullName).includes(this.normalizeText(this.searchName.toLowerCase()));
        const searchEmailMatch = this.normalizeText(email).includes(this.normalizeText(this.searchEmail.toLowerCase()));
        const searchUsernameMatch = this.normalizeText(username).includes(this.normalizeText(this.searchUsername.toLowerCase()));
        const searchPhoneMatch = this.normalizeText(phone).includes(this.normalizeText(this.searchPhone.toLowerCase()));
        const searchBirthPlaceMatch = this.normalizeText(birthPlace).includes(this.normalizeText(this.searchBirthPlace.toLowerCase()));

        // Dátumoknál is figyeljünk arra, hogy ékezetek nélküli keresést végezzünk
        const searchBirthDateMatch = this.normalizeText(birthDate).includes(this.normalizeText(this.searchBirthDate));

        return searchNameMatch && searchEmailMatch && searchUsernameMatch && searchPhoneMatch &&
          searchBirthPlaceMatch && searchBirthDateMatch;
      });
    },
    paginatedItems() {
      const start = (this.currentPage - 1) * this.pageSize;
      return this.filteredUsers.slice(start, start + this.pageSize);
    },
    totalPages() {
      const pages = Math.ceil(this.filteredUsers.length / this.pageSize);
      return pages > 0 ? pages : 1;
    },
    shouldShowClearButton() {
      return this.searchName || this.searchEmail || this.searchUsername || this.searchPhone ||
        this.searchBirthPlace || this.searchBirthDate;
    }
  },
  mounted() {
    this.fetchUsers();
  },
  methods: {
    async fetchUsers() {
      try {
        const response = await axios.get('/api/users');
        this.users = response.data.users;
      } catch (error) {
        console.error('Hiba a felhasználók lekérésekor:', error);
      }
    },
    validateNameInput(event) {
      // Csak betűk és szóközök engedélyezettek
      this.searchName = event.target.value.replace(/[^a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ\s]/g, '');
    },
    validatePhoneInput(event) {
      // Csak számok és a '+' jel engedélyezett
      this.searchPhone = event.target.value.replace(/[^0-9+]/g, '');
    },
    validateBirthPlaceInput(event) {
      // Csak betűk engedélyezettek, szóköz nem
      this.searchBirthPlace = event.target.value.replace(/[^a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ]/g, '');
    },
    normalizeText(text) {
      return text.normalize("NFD").replace(/[\u0300-\u036f]/g, ""); // Az ékezetek eltávolítása
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
      this.selectedUserId = id;
      this.isDeleteModalOpen = true;
    },
    async deleteUser() {
      try {
        const response = await axios.delete(`/api/users/${this.selectedUserId}`);
        if (response.status === 200) {
          // Frissíti a listát a törlés után
          this.users = this.users.filter(user => user.id !== this.selectedUserId);

          // Ha a törlés után üres marad az oldal, akkor vissza kell navigálni a megfelelő oldalra
          if (this.currentPage > this.totalPages) {
            this.currentPage = this.totalPages;
          }

          alert('A felhasználó sikeresen törölve!');
          this.isDeleteModalOpen = false;
          this.selectedUserId = null;
        }
      } catch (error) {
        console.error('Hiba a felhasználó törlésénél:', error);
        alert('Hiba történt a felhasználó törlésekor.');
        this.isDeleteModalOpen = false;
      }
    },
    cancelDelete() {
      this.isDeleteModalOpen = false;
      this.selectedUserId = null;
    },

    clearSearch() {
      this.searchName = '';
      this.searchEmail = '';
      this.searchUsername = '';
      this.searchPhone = '';
      this.searchBirthPlace = '';
      this.searchBirthDate = '';
      this.goToFirstPage();
    },
  },
  watch: {
    searchName() {
      this.goToFirstPage();
    },
    searchEmail() {
      this.goToFirstPage();
    },
    searchUsername() {
      this.goToFirstPage();
    },
    searchPhone() {
      this.goToFirstPage();
    },
    searchBirthPlace() {
      this.goToFirstPage();
    },
    searchBirthDate() {
      this.goToFirstPage();
    },
  },
};
</script>


<style scoped>
/* Kártyák elrendezése - 5 kártya egy sorban */
.grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 1.5rem;
}

/* Keresőmezők stílusa */
input,
select {
  transition: all 0.3s ease;
}

button {
  transition: all 0.3s ease;
}

input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
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
