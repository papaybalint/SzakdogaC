<template>
    <div id="app">
        <NavBar />
      <div class="filters">
        <!-- Legördülő menü -->
        <select v-model="selectedCategory" class="category-select">
          <option value="">Minden kategória</option>
          <option v-for="category in categories" :key="category" :value="category">
            {{ category }}
          </option>
        </select>

        <!-- Keresősáv -->
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Keresés..."
          class="search-bar"
        />
      </div>

      <!-- Kártyák -->
      <div class="items-container">
        <div v-for="item in filteredItems" :key="item.id" class="item-card">
          <img :src="item.image" alt="Item image" class="item-image" />
          <h3>{{ item.name }}</h3>
          <p>{{ item.description }}</p>
        </div>
      </div>
    </div>
  </template>

  <script>
  import NavBar from '../NavBar.vue';


  export default {
    data() {
      return {
        searchQuery: "",
        selectedCategory: "",
        categories: ["Hús", "Tejtermék", "Zöldség", "Gyümölcs", "Édesség"],
        items: [
          { id: 1, name: "Kolbász", description: "Ez egy leírás a kolbászról", category: "Hús", image: "https://picsum.photos/200/300" },
          { id: 2, name: "Sajt", description: "Ez egy leírás a sajtról", category: "Tejtermék", image: "https://picsum.photos/200/300" },
          { id: 3, name: "Karfiol", description: "Ez egy leírás a karfiolról", category: "Zöldség", image: "https://picsum.photos/200/300" },
          { id: 4, name: "Alma", description: "Ez egy leírás az almáról sajt", category: "Gyümölcs", image: "https://picsum.photos/200/300" },
          { id: 5, name: "Csokoládé", description: "Ez egy leírás a csokoládéról", category: "Édesség", image: "https://picsum.photos/200/300" },
          // Stb
        ],
      };
    },
    computed: {
      filteredItems() {
        let filtered = this.items;

        // Keresés
        if (this.searchQuery) {
          filtered = filtered.filter(item =>
            item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
            item.description.toLowerCase().includes(this.searchQuery.toLowerCase())
          );
        }

        // Kategória szerinti szűrés legördülő menüvel
        if (this.selectedCategory) {
          filtered = filtered.filter(item => item.category === this.selectedCategory);
        }

        return filtered;
      },
    },
  };
  </script>

  <style scoped>
  body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
  }

  /* Szűrőpanelek */
  .filters {
    display: flex;
    justify-content: flex-start;
    padding: 20px;
    background-color: #fff;
    flex-wrap: wrap;
  }

  .category-select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 200px;
    margin-right: 20px;
  }

  .search-bar {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 300px;
  }

  /* Kártyák */
  .items-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-evenly;
    padding: 20px;
  }

  .item-card {
    background-color: #fff;
    border-radius: 8px;
    width: 200px;
    margin: 10px;
    padding: 10px;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .item-image {
    width: 100%;
    border-radius: 8px;
  }

  .item-card h3 {
    font-size: 18px;
    margin: 10px 0;
  }

  .item-card p {
    font-size: 14px;
    color: #555;
  }

  /* Reszponzív beállítások */
  @media (max-width: 768px) {
    .filters {
      flex-direction: column;
      align-items: center;
    }

    .category-select {
      width: 80%;
      margin-bottom: 10px;
    }

    .search-bar {
      width: 80%;
    }

    .items-container {
      flex-direction: column;
      align-items: center;
    }

    .item-card {
      width: 90%;
      margin: 10px 0;
    }
  }
  </style>
