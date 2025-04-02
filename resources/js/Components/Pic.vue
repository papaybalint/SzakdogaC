<template>
  <section class="container mx-auto p-6">
  </section>
</template>

<script>
export default {
  name: 'Pic'
}
</script>

<style scoped>
section {
  background-image: url('/storage/original.jpeg');
  background-size: cover;
  background-position: center;
  height: 200px;
}

@media (max-width: 768px) {
  section {
    height: 150px;
  }
}

@media (max-width: 480px) {
  section {
    height: 100px;
  }
}
</style>
