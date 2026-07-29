<script setup></script>

<template>
    <!-- Example if app find url page components store in parents component, it rather rendered that parent components e.g.,: DefaultLayout, then there will rendered it children components set in routes if it have <RouterView/> in parent components = Nested Routes, otherwise it render the component outside scope in DefaultLayout -->
    <RouterView />
</template>
