<template>
  <footer class="bg-gray-800 text-white py-6 mt-6">
    <div class="container mx-auto text-center">
      <p class="font-bold text-lg mb-4">Nyitvatartás:</p>

      <ul class="space-y-2 mx-auto">
        <li>
          <span class="font-semibold">Hétfő:</span> 9:00 - 18:00
        </li>
        <li>
          <span class="font-semibold">Kedd:</span> 9:00 - 18:00
        </li>
        <li>
          <span class="font-semibold">Szerda:</span> 9:00 - 18:00
        </li>
        <li>
          <span class="font-semibold">Csütörtök:</span> 9:00 - 18:00
        </li>
        <li>
          <span class="font-semibold">Péntek:</span> 9:00 - 18:00
        </li>
        <li>
          <span class="font-semibold">Szombat:</span> Zárva
        </li>
        <li>
          <span class="font-semibold">Vasárnap:</span> Zárva
        </li>
      </ul>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'Footer'
}
</script>

<style scoped>
ul {
  list-style-type: none;
  padding-left: 0;
}

li {
  font-size: 1rem;
  line-height: 1.5;
}
</style>