<template>
    <section class="news-list">
      <h2>Legfrissebb Hírek</h2>
      <div class="news-item" v-for="(news, index) in newsList" :key="index">
        <h3>{{ news.title }}</h3>
        <p>{{ news.description }}</p>
        <p class="date">{{ news.date }}</p>
      </div>
    </section>
  </template>
  
  <script>
  export default {
    name: 'NewsList',
    data() {
      return {
        newsList: [
          {
            title: 'Teszt Elek',
            description: 'Teszt Elek felborította a kukát a falu játszóterén ',
            date: '2025. március 9.',
          },
          {
            title: 'Új könyvek',
            description: 'Könyvtárunknak új könyveket adományozott az orosz kancellár',
            date: '2025. március 8.',
          },
          {
            title: 'Hangoskönyvek',
            description: 'Hangos könyvek érkeztek egyenesen Korda Györgytől',
            date: '2025. március 7.',
          },
        ],
      };
    },
  }
  </script>
  
  <style scoped>
  .news-list {
    padding: 2rem;
    background-color: #f4f4f4;
  }
  
  .news-item {
    background-color: white;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }
  
  .news-item h3 {
    margin: 0 0 0.5rem 0;
  }
  
  .news-item p {
    margin: 0 0 1rem 0;
  }
  
  .date {
    font-size: 0.9rem;
    color: #7f8c8d;
  }
  </style>