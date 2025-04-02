<template>
  <header>
    <nav class="navbar">
      <!-- Logo  -->
      <div class="logo">
        <a href="/">Dör Községi Könyvtár</a>
      </div>

      <!-- Hamburger menü mobilhoz -->
      <div class="hamburger" @click="toggleMenu" v-if="isMobile">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>

      <!-- Menü linkek -->
      <ul :class="{ 'active': menuOpen }" class="navbar-links">
        <li v-if="isLoggedIn"> <a href="/items/create" class="borrowed">Tartalom hozzáadása</a></li>
        <li v-if="isLoggedIn"><a href="/borrowed_media" class="borrowed">Kölcsönzéseim</a></li>
        <li v-if="!isLoggedIn"><a href="/login" class="auth-link">Bejelentkezés</a></li>
        <li v-if="!isLoggedIn"><a href="/register" class="auth-link">Regisztráció</a></li>
        <li v-if="isLoggedIn"><a :href="route('profile.edit')" class="auth-link">Profil</a></li>
        <li v-if="isLoggedIn"><a :href="route('logout')" class="logout">Kijelentkezés</a></li>
      </ul>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'NavBar',
  props: ['auth', 'isLoggedIn'],
  data() {
    return {
      isMobile: false,
      menuOpen: false,
    };
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen;
    },
    checkMobile() {
      this.isMobile = window.innerWidth <= 768;
    }
  },
  created() {
    this.checkMobile();
    window.addEventListener('resize', this.checkMobile);
  },
  destroyed() {
    window.removeEventListener('resize', this.checkMobile);
  }
}
</script>

<style scoped>

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
  background-color: grey;
}

.auth-link {
  background-color: orange;
  border-radius: 5px;
}

.logout {
  background-color: red;
  border-radius: 5px;
}

.borrowed {
  background-color: cadetblue;
  border-radius: 5px;

}

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

.navbar-links.active {
  display: block;
}

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

  .hamburger {
    display: flex;
  }

  .navbar-links.active {
    display: flex;
  }
}

img {
  width: 20px;
  height: 20px;
  border-radius: 50%;
}
</style>
