<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  products: {
    data: { id: number; name: string; sku: string; price: string; stock: number; is_active: boolean }[]
  }
}>()
</script>

<template>
  <div class="p-6">
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-2xl font-bold">Products</h1>
      <Link href="/admin/products/create" class="rounded bg-blue-600 px-4 py-2 text-white text-sm">
        Add Product
      </Link>
    </div>

    <table class="w-full text-sm border-collapse">
      <thead>
        <tr class="border-b text-left">
          <th class="py-2 pr-4">Name</th>
          <th class="py-2 pr-4">SKU</th>
          <th class="py-2 pr-4">Price</th>
          <th class="py-2 pr-4">Stock</th>
          <th class="py-2">Active</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="product in products.data" :key="product.id" class="border-b">
          <td class="py-2 pr-4">
            <Link :href="`/admin/products/${product.id}/edit`" class="text-blue-600 hover:underline">
              {{ product.name }}
            </Link>
          </td>
          <td class="py-2 pr-4">{{ product.sku }}</td>
          <td class="py-2 pr-4">{{ product.price }}</td>
          <td class="py-2 pr-4">{{ product.stock }}</td>
          <td class="py-2">{{ product.is_active ? 'Yes' : 'No' }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
