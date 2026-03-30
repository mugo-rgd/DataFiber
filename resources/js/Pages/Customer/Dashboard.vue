<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import type { PageProps } from '../types'

const page = usePage<PageProps>()
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold">Customer Dashboard</h1>
    <p class="mt-2 text-gray-600">
      Hello, {{ page.props.auth.user?.name }} 👋
    </p>
  </div>
</template>
