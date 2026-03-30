<script setup>
defineProps({
  user: Object
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold">Admin Dashboard</h1>
    <p class="mt-2 text-gray-600">
      Welcome back, {{ $page.props.auth.user?.name }}
    </p>
  </div>
</template>
