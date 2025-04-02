<script setup>
import { Head } from '@inertiajs/vue3'
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

  <Head title="Tartalom Hozzáadása" />

  <div>
    <header>
      <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth" />
    </header>

    <main class="mt-6">
      <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">Új Tartalom Hozzáadása</h1>
        <form @submit.prevent="submitForm">
          <div class="mb-4">
            <label for="author" class="block text-sm font-medium">Szerző</label>
            <input v-model="form.author" type="text" id="author"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="title" class="block text-sm font-medium">Cím</label>
            <input v-model="form.title" type="text" id="title"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="inventory_number" class="block text-sm font-medium">Készlet szám</label>
            <input v-model="form.inventory_number" type="text" id="inventory_number"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="barcode" class="block text-sm font-medium">Vonalkód</label>
            <input v-model="form.barcode" type="number" id="barcode"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="isbn" class="block text-sm font-medium">ISBN</label>
            <input v-model="form.isbn" type="text" id="isbn"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="year_of_purchasing" class="block text-sm font-medium">Beszerzési év</label>
            <input v-model="form.year_of_purchasing" type="date" id="year_of_purchasing"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="published_year" class="block text-sm font-medium">Kiadás éve</label>
            <input v-model="form.published_year" type="number" id="published_year"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="supplier" class="block text-sm font-medium">Szállító</label>
            <input v-model="form.supplier" type="text" id="supplier"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" />
          </div>

          <div class="mb-4">
            <label for="categories_id" class="block text-sm font-medium">Kategória</label>
            <select v-model="form.categories_id" id="categories_id"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
              <option value="">-- Kategória választása --</option>
              <option v-for="category in nav.categories" :key="category.id" :value="category.id">
                {{ category.name }} <span v-if="category.media_type">- {{ category.media_type }}</span>
              </option>
            </select>
          </div>

          <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-md">
            Hozzáadás
          </button>
        </form>
      </div>
    </main>

    <footer>
      <Footer />
    </footer>
  </div>

</template>

<script>
export default {
  data() {
    return {
      form: {
        author: '',
        title: '',
        inventory_number: '',
        barcode: '',
        isbn: '',
        year_of_purchasing: '',
        published_year: '',
        supplier: '',
        categories_id: '',
      },
    };
  },
  methods: {
    async submitForm() {
      console.log("Elküldött adatok:", this.form);

      try {
        // Küldjük el a kérés a szerverhez
        const response = await axios.post('http://localhost:8000/api/items', this.form);

        // Ellenőrizzük a válasz adatokat
        console.log("Szerver válasza:", response.data);

        if (response.data.item) {
          // Ha sikeres, jelezzük
          alert("Sikeres mentés! Az új tétel ID-ja: " + response.data.item.id);
        } else {
          // Ha nincs benne 'item', hiba történt
          alert("Hiba történt a mentés során!");
        }
      } catch (error) {
        console.error("Hiba történt:", error.response?.data || error.message);
        alert("Mentési hiba! Nézd meg a konzolt!");
      }
    }
  }
};
</script>
