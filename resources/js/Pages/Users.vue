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
  <div>
    <header>
      <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth" />
    </header>

    <main class="mt-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Felhasználók</h1>

        <div v-if="auth.user.role === 'admin'" class="mb-6 flex flex-wrap gap-4">
          <!-- Admin szűrő mezők -->
          <input v-model="searchName" type="text" placeholder="Teljes név"
            class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5"
            style="text-transform: capitalize;" @input="validateNameInput" />
          <input v-model="searchEmail" type="text" placeholder="Email"
            class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5" />
          <input v-model="searchUsername" type="text" placeholder="Felhasználónév"
            class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5" />
          <input v-model="searchPhone" type="text" placeholder="Telefonszám"
            class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5"
            @input="validatePhoneInput" />
          <input v-model="searchBirthPlace" type="text" placeholder="Születési hely"
            class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5"
            style="text-transform: capitalize;" @input="validateBirthPlaceInput" />
          <input type="date" v-model="searchBirthDate" placeholder="Születési Dátum"
            class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5" :max="today()"
            @input="onDateInput" />
          <select v-model="searchRole" class="p-2 border border-gray-300 rounded-lg w-full sm:w-1/2 md:w-1/4 xl:w-1/5">
            <option value="">Szerepkör (mind)</option>
            <option value="admin">Admin</option>
            <option value="user">Sima felhasználó</option>
          </select>
          <button v-if="shouldShowClearButton" @click="clearSearch"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 w-full sm:w-auto">
            Keresés törlése
          </button>
        </div>

        <div v-if="paginatedItems.length > 0"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
          <div v-for="user in paginatedItems" :key="user.id" class="w-full">
            <div class="card bg-white rounded-lg shadow-lg p-4 h-full flex flex-col justify-between" :class="{
              'border-2 border-green-500 bg-green-50': user.id === auth.user.id,
              'border-2 border-red-500 bg-red-50': user.role === 'admin' && user.id !== auth.user.id
            }">
              <div class="card-header">
                <h2 class="text-xl font-bold text-center">
                  {{ user.first_name }} {{ user.last_name }}
                </h2>

                <!-- Jelölések (Saját fiók, Admin) -->
                <div class="flex flex-wrap gap-2 mt-1">
                  <span v-if="user.id === auth.user.id"
                    class="text-sm text-green-700 bg-green-200 px-2 py-1 rounded-full">
                    Saját felhasználó
                  </span>

                  <span v-if="user.role === 'admin'" class="text-sm text-red-700 bg-red-200 px-2 py-1 rounded-full">
                    Admin
                  </span>
                </div>
              </div>
              <div class="card-content">
                <p class="text-gray-700 text-sm"><strong>Felhasználónév:</strong> {{ user.username }}</p>
                <p class="text-gray-700 text-sm"><strong>Email:</strong> {{ user.email }}</p>
                <p class="text-gray-700 text-sm"><strong>Telefonszám:</strong> {{ user.phone }}</p>
                <p class="text-gray-700 text-sm"><strong>Születési hely:</strong> {{ user.birth_place }}</p>
                <p class="text-gray-700 text-sm"><strong>Születési dátum:</strong> {{ user.birth_date }}</p>
              </div>
              <div class="card-footer mt-auto flex justify-end">
                <button v-if="auth.user.role === 'admin' && user.id !== auth.user.id && user.role !== 'admin'"
                  @click="confirmDelete(user.id)"
                  class="mt-4 bg-red-500 text-white py-2 px-5 text-base rounded-lg hover:bg-red-600">
                  Törlés
                </button>
              </div>
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

      </div>
    </main>

    <footer>
      <Footer />
    </footer>

    <!-- Törlés megerősítése ablak -->
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
      searchRole: '',
    };
  },
  computed: {
    filteredUsers() {
      const currentUser = this.auth.user;

      const filtered = this.users.filter(user => {
        const fullName = `${user.first_name} ${user.last_name}`.toLowerCase();
        const email = user.email.toLowerCase();
        const username = user.username.toLowerCase();
        const phone = user.phone ? user.phone.toLowerCase() : '';
        const birthPlace = user.birth_place ? user.birth_place.toLowerCase() : '';
        const birthDate = user.birth_date || '';

        const searchNameMatch = this.normalizeText(fullName).includes(this.normalizeText(this.searchName.toLowerCase()));
        const searchEmailMatch = this.normalizeText(email).includes(this.normalizeText(this.searchEmail.toLowerCase()));
        const searchUsernameMatch = this.normalizeText(username).includes(this.normalizeText(this.searchUsername.toLowerCase()));
        const searchPhoneMatch = this.normalizeText(phone).includes(this.normalizeText(this.searchPhone.toLowerCase()));
        const searchBirthPlaceMatch = this.normalizeText(birthPlace).includes(this.normalizeText(this.searchBirthPlace.toLowerCase()));
        const searchBirthDateMatch = this.normalizeText(birthDate).includes(this.normalizeText(this.searchBirthDate));
        const searchRoleMatch = this.searchRole === '' || user.role === this.searchRole;

        return searchNameMatch && searchEmailMatch && searchUsernameMatch && searchPhoneMatch &&
          searchBirthPlaceMatch && searchBirthDateMatch && searchRoleMatch;
      });

      const currentUserInFiltered = filtered.find(user => user.id === currentUser.id);

      if (currentUserInFiltered) {
        const filteredWithoutCurrentUser = filtered.filter(user => user.id !== currentUser.id);

        const sortedUsers = filteredWithoutCurrentUser.sort((a, b) => {
          const nameA = `${a.first_name} ${a.last_name}`.toLowerCase();
          const nameB = `${b.first_name} ${b.last_name}`.toLowerCase();
          return nameA.localeCompare(nameB);
        });

        filtered.length = 0;
        filtered.push(currentUserInFiltered, ...sortedUsers);
      } else {
        filtered.sort((a, b) => {
          const nameA = `${a.first_name} ${a.last_name}`.toLowerCase();
          const nameB = `${b.first_name} ${b.last_name}`.toLowerCase();
          return nameA.localeCompare(nameB);
        });
      }

      return filtered;
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
        this.searchBirthPlace || this.searchBirthDate || this.searchRole;
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

        const currentUserIndex = this.users.findIndex(user => user.id === this.auth.user.id);
        if (currentUserIndex > -1) {
          const currentUser = this.users.splice(currentUserIndex, 1)[0]; 
          this.users.unshift(currentUser);
        }
      } catch (error) {
        console.error('Hiba a felhasználók lekérésekor:', error);
      }
    },
    validateNameInput(event) {
      this.searchName = event.target.value.replace(/[^a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ\s]/g, '');
    },
    validatePhoneInput(event) {
      this.searchPhone = event.target.value.replace(/[^0-9+]/g, '');
    },
    validateBirthPlaceInput(event) {
      this.searchBirthPlace = event.target.value.replace(/[^a-zA-ZáéíóöőúüűÁÉÍÓÖŐÚÜŰ]/g, '');
    },
    normalizeText(text) {
      return text
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '') 
        .replace(/[^\w\s]/g, ''); 
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
          this.users = this.users.filter(user => user.id !== this.selectedUserId);

          if (this.currentPage > this.totalPages) {
            this.currentPage = this.totalPages;
            this.currentPageInput = this.totalPages;
          }
          if (this.filteredUsers.length === 0 && this.currentPage !== 1) {
            this.currentPage = 1;
            this.currentPageInput = 1;
          }

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
      this.searchRole = '';
      this.goToFirstPage();
    },
    today() {
      var dtToday = new Date();

      var month = dtToday.getMonth() + 1;
      var day = dtToday.getDate();
      var year = dtToday.getFullYear();
      if (month < 10)
        month = '0' + month.toString();
      if (day < 10)
        day = '0' + day.toString();

      var maxDate = year + '-' + month + '-' + day;
      return maxDate;
    },

    onDateInput() {
      const selectedDate = new Date(this.searchBirthDate);
      const today = new Date();

      if (selectedDate.getTime() > today.getTime()) {
        this.searchBirthDate = this.today();
      }
    }
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
    searchRole() {
      this.goToFirstPage();
    },
  },
};
</script>


<style scoped>
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 1rem;
}

.pagination-btn .arrow {
  display: none;
}

@media (min-width: 1024px) {
  .grid {
    grid-template-columns: repeat(5, 1fr);
  }
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

.card-footer button {
  font-size: 1rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
}
</style>
