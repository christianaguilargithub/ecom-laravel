<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

defineProps<{
  categories: { id: number; name: string }[]
}>()

const form = useForm({
  name: '',
  sku: '',
  category_id: '',
  description: '',
  price: 0,
  stock: 0,
  is_active: true,
})

function submit() {
  form.post('/admin/products')
}
</script>

<template>
  <form @submit.prevent="submit" class="space-y-6 max-w-xl p-6">
    <div>
      <label class="block text-sm font-medium">Name</label>
      <input v-model="form.name" class="mt-1 w-full rounded border px-3 py-2" />
      <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium">SKU</label>
      <input v-model="form.sku" class="mt-1 w-full rounded border px-3 py-2" />
      <p v-if="form.errors.sku" class="mt-1 text-sm text-red-600">{{ form.errors.sku }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium">Category</label>
      <select v-model="form.category_id" class="mt-1 w-full rounded border px-3 py-2">
        <option value="">Select a category</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600">{{ form.errors.category_id }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium">Description</label>
      <textarea v-model="form.description" rows="3" class="mt-1 w-full rounded border px-3 py-2" />
      <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium">Price</label>
      <input v-model="form.price" type="number" min="0" step="0.01" class="mt-1 w-full rounded border px-3 py-2" />
      <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
    </div>

    <div>
      <label class="block text-sm font-medium">Stock</label>
      <input v-model="form.stock" type="number" min="0" class="mt-1 w-full rounded border px-3 py-2" />
      <p v-if="form.errors.stock" class="mt-1 text-sm text-red-600">{{ form.errors.stock }}</p>
    </div>

    <div class="flex items-center gap-2">
      <input v-model="form.is_active" type="checkbox" id="is_active" />
      <label for="is_active" class="text-sm font-medium">Active</label>
    </div>

    <button
      type="submit"
      :disabled="form.processing"
      class="rounded px-4 py-2 bg-blue-600 text-white disabled:opacity-50"
    >
      {{ form.processing ? 'Saving...' : 'Save Product' }}
    </button>
  </form>
</template>
