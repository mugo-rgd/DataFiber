<script setup>
defineProps({
  customer: Object
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold">My Profile</h1>
    <div class="mt-4">
      <p><strong>Name:</strong> {{ customer.name }}</p>
      <p><strong>Email:</strong> {{ customer.email }}</p>
    </div>
  </div>
</template>
