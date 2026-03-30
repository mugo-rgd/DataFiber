<script setup>
defineProps({
  user: Object
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold">Shared Dashboard - Customer View</h1>
    <p class="mt-2">Welcome, {{ user.name }} (Customer)</p>
  </div>
</template>
