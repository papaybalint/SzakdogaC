<template>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-4">Kölcsönzéseim</h1>
        <div class="flex flex-wrap -mx-2">
            <div class="w-full md:w-1/2 lg:w-1/3 px-2 mb-4" v-for="item in borrowedItems" :key="item.id">
                <div class="bg-white rounded-lg shadow-lg p-4">
                    <h2 class="text-xl font-bold mb-2">{{ item.title }}</h2>
                    <p class="text-gray-700">{{ item.author }}</p>
                    <p class="text-gray-700">{{ item.published_year }}</p>
                    <p class="text-gray-700">{{ item.categories_id }}</p>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: 'Borrowed_media',
        data() {
            return {
                borrowedItems: []
            }
        },
        mounted() {
            this.fetchBorrowedItems();
        },
        methods: {
            async fetchBorrowedItems() {
                const response = await fetch('/api/borrowed_media');
                this.borrowedItems = await response.json();
            }
        }
    }
</script>

<style scoped>

</style>



