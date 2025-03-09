<template>
    <header>
      <nav class="navbar">
        <!-- Logo  -->
        <div class="logo">
          <a href="/">Logo</a>
        </div>

        <!-- Hamburger menü gomb (mobil nézetben) -->
        <div class="hamburger" @click="toggleMenu" v-if="isMobile">
          <span class="bar"></span>
          <span class="bar"></span>
          <span class="bar"></span>
        </div>

        <!-- Menü linkek -->
        <ul :class="{'active': menuOpen}" class="navbar-links">
          <li><a href="/books">Könyvek</a></li>
          <li><a href="/audiobooks">Hangoskönyvek</a></li>
          <li><a href="/dvd">DVD</a></li>
          <li><a href="/cassette">Kazetta</a></li>
          <li><a href="/login" class="auth-link">Bejelentkezés</a></li>
          <li><a href="/register" class="auth-link">Regisztráció</a></li>
        </ul>
      </nav>
    </header>
  </template>

  <script>
  export default {
    name: 'NavBar',
    data() {
      return {
        isMobile: false, // Ellenőrizzük, hogy mobil nézetről van-e szó
        menuOpen: false, // Kezdetben zárva lesz a mobil menü
      };
    },
    methods: {
      // Menü nyitása és zárása a hamburger gombbal
      toggleMenu() {
        this.menuOpen = !this.menuOpen;
      },
      // Ellenőrizzük, hogy mobil nézetről van-e szó
      checkMobile() {
        this.isMobile = window.innerWidth <= 768;
      }
    },
    created() {
      this.checkMobile();
      window.addEventListener('resize', this.checkMobile); // Figyeljük a képernyőméret változását
    },
    destroyed() {
      window.removeEventListener('resize', this.checkMobile); // Tisztítjuk az eseményfigyelőt
    }
  }
  </script>

  <style scoped>
  /* Navigációs sáv alap stílusai */
  .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #333;
    padding: 10px 20px;
  }

  .logo a {
    color: white;
    font-size: 24px;
    text-decoration: none;
  }

  .navbar-links {
    display: flex;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .navbar-links li {
    margin: 0 15px;
  }

  .navbar-links a {
    color: white;
    text-decoration: none;
    padding: 10px 20px;
    display: block;
  }

  .navbar-links a:hover {
    background-color: #575757;
  }

  /* Auth linkek stílusa (Bejelentkezés és Regisztráció) */
  .auth-link {
    background-color: #f1c40f;
    border-radius: 5px;
  }

  .auth-link:hover {
    background-color: #f39c12;
  }

  /* Hamburger menü stílus (mobil nézetben) */
  .hamburger {
    display: none;
    cursor: pointer;
    flex-direction: column;
    gap: 5px;
  }

  .bar {
    width: 25px;
    height: 3px;
    background-color: white;
  }

  /* Menü linkek mobilon */
  .navbar-links.active {
    display: block;
  }

  /* Mobil nézet stílusok */
  @media (max-width: 768px) {
    .navbar-links {
      position: absolute;
      top: 60px;
      left: 0;
      right: 0;
      background-color: #333;
      display: none;
      flex-direction: column;
      align-items: center;
      padding: 20px 0;
    }

    .navbar-links li {
      margin: 10px 0;
    }

    /* Hamburger menü gomb mobil nézetben */
    .hamburger {
      display: flex;
    }

    /* Mobil menü aktiválása */
    .navbar-links.active {
      display: flex;
    }
  }
  </style>
