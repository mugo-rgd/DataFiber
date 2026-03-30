<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'

const form = reactive({
  email: '',
  password: ''
})

function submit() {
  router.post(route('admin.login.submit'), form)
}
</script>

<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md p-6 bg-white rounded shadow">
      <h1 class="text-xl font-bold text-center">Admin Login</h1>

      <form @submit.prevent="submit" class="mt-4 space-y-4">
        <div>
          <label>Email</label>
          <input v-model="form.email" type="email" class="w-full p-2 border rounded" required />
        </div>
        <div>
          <label>Password</label>
          <input v-model="form.password" type="password" class="w-full p-2 border rounded" required />
        </div>
        <button type="submit" class="w-full p-2 text-white bg-blue-600 rounded">
          Login
        </button>
      </form>
    </div>
  </div>
</template>
