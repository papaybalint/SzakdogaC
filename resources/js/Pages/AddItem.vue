<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import Footer from '@/Components/Footer.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

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

// A form state inicializálása
const form = useForm({
  author: '',
  title: '',
  inventory_number: '',
  barcode: '',
  isbn: '',
  year_of_purchasing: '',
  published_year: '',
  supplier: '',
  categories_id: '',
});

// A form beküldése
const submit = () => {
  form.post(route('items.store'), {
    onSuccess: () => {
    },
    onError: (errors) => {
      console.log("Validációs hibák:", errors);
    },
  });
};


// Aktuális nap lekérése
const today = () => {
  const dtToday = new Date();
  let month = dtToday.getMonth() + 1;
  let day = dtToday.getDate();
  const year = dtToday.getFullYear();
  if (month < 10) month = '0' + month.toString();
  if (day < 10) day = '0' + day.toString();
  
  return `${year}-${month}-${day}`;
};


// Dátum input validálása
const onDateInpit = (event) => {
  if (event.target.value != "") {
    var date = new Date(event.target.value);
    var dtToday = new Date();
    if (date.getTime() > dtToday.getTime()) {
      setTimeout(() => {
        event.target.value = today();
      });
    }
  }
};
</script>

<template>
  <Head title="Tartalom Hozzáadása" />
  <div>
    <header>
      <NavBar :isLoggedIn="nav.isLoggedIn" :auth="auth"/>
    </header>

    <main class="mt-6">
      <div class="container mx-auto p-4">
        <h1 class="text-2xl mb-4">Új Tartalom Hozzáadása</h1>
        <form @submit.prevent="submit">
          <!-- Cím -->
          <div class="mb-4">
            <InputLabel for="title" value="Cím" />
            <TextInput
              id="title"
              v-model="form.title"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError class="mt-2" :message="form.errors.title" />
          </div>

          <!-- Szerző -->
          <div class="mb-4">
            <InputLabel for="author" value="Szerző" />
            <TextInput
              id="author"
              v-model="form.author"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError class="mt-2" :message="form.errors.author" />
          </div>

          <!-- Készlet szám -->
          <div class="mb-4">
            <InputLabel for="inventory_number" value="Leltári szám" />
            <TextInput
              id="inventory_number"
              v-model="form.inventory_number"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError class="mt-2" :message="form.errors.inventory_number" />
          </div>

          <!-- Vonalkód -->
          <div class="mb-4">
            <InputLabel for="barcode" value="Vonalkód" />
            <TextInput
              id="barcode"
              v-model="form.barcode"
              type="number"
              class="mt-1 block w-full"
              required
              min="0"
            />
            <InputError class="mt-2" :message="form.errors.barcode" />
          </div>

          <!-- ISBN -->
          <div class="mb-4">
            <InputLabel for="isbn" value="ISBN" />
            <TextInput
              id="isbn"
              v-model="form.isbn"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError class="mt-2" :message="form.errors.isbn" />
          </div>

          <!-- Beszerzési év -->
          <div class="mb-4">
            <InputLabel for="year_of_purchasing" value="Beszerzési éve" />
            <TextInput
              id="year_of_purchasing"
              v-model="form.year_of_purchasing"
              type="date"
              class="mt-1 block w-full"
              required
              :max="today()"
              @input="onDateInpit($event)"
            />
            <InputError class="mt-2" :message="form.errors.year_of_purchasing" />
          </div>

          <!-- Kiadás éve -->
          <div class="mb-4">
            <InputLabel for="published_year" value="Kiadás éve" />
            <TextInput
              id="published_year"
              v-model="form.published_year"
              type="number"
              class="mt-1 block w-full"
              required
              min="0"
            />
            <InputError class="mt-2" :message="form.errors.published_year" />
          </div>

          <!-- Szállító -->
          <div class="mb-4">
            <InputLabel for="supplier" value="Szállító" />
            <TextInput
              id="supplier"
              v-model="form.supplier"
              type="text"
              class="mt-1 block w-full"
              required
            />
            <InputError class="mt-2" :message="form.errors.supplier" />
          </div>

          <!-- Kategória -->
          <div class="mb-4">
            <InputLabel for="categories_id" value="Kategória" />
            <select
              id="categories_id"
              v-model="form.categories_id"
              class="mt-1 block w-full border-gray-300 rounded-md focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
              required
            >
              <option value="">-- Kategória választása --</option>
              <option v-for="category in nav.categories" :key="category.id" :value="category.id">
                {{ category.name }} <span v-if="category.media_type">- {{ category.media_type }}</span>
              </option>
            </select>
            <InputError class="mt-2" :message="form.errors.categories_id" />
          </div>

          <!-- Submit gomb -->
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
