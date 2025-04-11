<template>
    <div class="fixed inset-0 flex justify-center items-center bg-gray-800 bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-3/4 md:w-1/2 relative">
            <h2 class="text-2xl font-semibold mb-4">Kölcsönzés</h2>

            <!-- Kereső -->
            <div class="mb-4">
                <input v-model="searchTerm" type="text" placeholder="Keresés név, email vagy telefonszám szerint"
                    class="w-full p-2 border rounded-md" />
            </div>

            <!-- Felhasználói lista -->
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <div v-for="user in filteredUsers" :key="user.id"
                    class="bg-gray-100 p-4 rounded-lg shadow-md flex justify-between items-center">
                    <div>
                        <p><strong>{{ user.full_name }}</strong></p>
                        <p>Email: {{ user.email }}</p>
                        <p>Telefonszám: {{ user.phone }}</p>
                    </div>
                    <button @click="borrowItem(user)"
                        class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                        Kölcsönzés
                    </button>
                </div>
            </div>

            <!-- Bezárás gomb -->
            <button @click="closeModal" class="absolute top-4 right-4 text-2xl text-gray-600 hover:text-gray-900">
                &times;
            </button>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'BorrowingModal',
    props: {
        item: Object, // A szülőtől átadott item objektum
    },
    data() {
        return {
            users: [],
            searchTerm: '', // Keresési kifejezés
        };
    },
    computed: {
        // Felhasználók szűrése a keresési kifejezés alapján
        filteredUsers() {
            const term = this.searchTerm.toLowerCase();
            return this.users.filter(user =>
                user.full_name.toLowerCase().includes(term) ||
                user.email.toLowerCase().includes(term) ||
                user.phone.includes(term)
            );
        },
    },
    mounted() {
        this.fetchUsers(); // A komponens betöltődésekor lekérjük a felhasználókat
    },
    methods: {
        // Felhasználók lekérése az API-ból
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
        // Kölcsönzés logika
        borrowItem(user) {
            if (!this.item || !user) {
                alert('Hiba: Nincs tétel vagy felhasználó!');
                return;
            }

            axios.post('/api/borrowings', {
                itemIds: { id: this.item.id }, // Az aktuális tétel ID-ja
                userId: user.id, // Kiválasztott felhasználó ID-ja
            })
                .then(() => {
                    this.closeModal();
                    alert('Sikeres kölcsönzés!');
                    window.location.reload();
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

        // Modal bezárása
        closeModal() {
            this.$emit('close');
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
}
</style>