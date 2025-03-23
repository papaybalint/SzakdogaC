<template>
  <header>
    <nav class="navbar">
      <!-- Logo  -->
      <div class="logo">
        <a href="/">Logo</a>
      </div>

      <!-- Hamburger menü mobilhoz -->
      <div class="hamburger" @click="toggleMenu" v-if="isMobile">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
      </div>

      <!-- Menü linkek -->
      <ul :class="{ 'active': menuOpen }" class="navbar-links">
        <li v-if="isLoggedIn"><a href="/borrowed_media">Kölcsönzéseim</a></li>
        <li v-if="!isLoggedIn"><a href="/login" class="auth-link" >Bejelentkezés</a></li>
        <li v-if="!isLoggedIn"><a href="/register" class="auth-link" >Regisztráció</a></li>
        <li v-if="isLoggedIn"><a :href="route('profile.edit')" class="auth-link" >Profil</a></li>
        <li v-if="isLoggedIn"><a :href="route('logout')" class="auth-link" >Kijelentkezés</a></li>
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
  background-color: #575757;
}

.auth-link {
  background-color: #f1c40f;
  border-radius: 5px;
}

.auth-link:hover {
  background-color: #f39c12;
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
</style>
