<script setup>
import TheFooter from "./components/TheFooter.vue";
import TheNavbar from "./components/TheNavbar.vue";
</script>

<template>
    <!-- Example if app find url page components store in parents component, it rather rendered that parent components e.g.,: DefaultLayout, then there will rendered it children components set in routes if it have <RouterView/> in parent components = Nested Routes -->
    <RouterView />
</template>
