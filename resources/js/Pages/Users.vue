<template>
    <div class="users-container">
      <h1>Regisztrált Tagok</h1>
      <div class="users-grid">
        <div v-for="user in users" :key="user.id" class="user-card">
          <h3>{{ user.first_name }} {{ user.last_name }}</h3>
          <p><strong>Email:</strong> {{ user.email }}</p>
          <p><strong>Felhasználónév:</strong> {{ user.username }}</p>
          <p><strong>Telefon:</strong> {{ user.phone || 'N/A' }}</p>
          <p><strong>Születési hely:</strong> {{ user.birth_place || 'N/A' }}</p>
          <p><strong>Születési dátum:</strong> {{ user.birth_date || 'N/A' }}</p>
          <button @click="deleteUser(user.id)" class="delete-button">Törlés</button>
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
        users: [],
      };
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
          console.error('Hiba a felhasználók lekérdezésekor:', error);
        }
      },
      async deleteUser(id) {
        if (confirm('Biztosan törölni szeretnéd ezt a felhasználót?')) {
          try {
            await axios.delete(`/api/users/${id}`);
            this.users = this.users.filter(user => user.id !== id);
            alert('Felhasználó sikeresen törölve!');
          } catch (error) {
            console.error('Hiba a felhasználó törlésekor:', error);
            alert('Hiba történt a törlés során.');
          }
        }
      },
    },
  };
  </script>

  <style scoped>
  .users-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }

  .users-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
  }

  .user-card {
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .user-card h3 {
    margin: 0 0 10px;
    font-size: 1.5em;
  }

  .user-card p {
    margin: 5px 0;
  }

  .delete-button {
    background-color: red;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
  }

  .delete-button:hover {
    background-color: darkred;
  }
  </style>
