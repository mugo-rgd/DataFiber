<script setup>
defineProps({
  users: Array
})
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold">Manage Users</h1>
    <table class="w-full mt-4 border">
      <thead class="bg-gray-100">
        <tr>
          <th class="p-2 text-left">ID</th>
          <th class="p-2 text-left">Name</th>
          <th class="p-2 text-left">Email</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id" class="border-t">
          <td class="p-2">{{ user.id }}</td>
          <td class="p-2">{{ user.name }}</td>
          <td class="p-2">{{ user.email }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
