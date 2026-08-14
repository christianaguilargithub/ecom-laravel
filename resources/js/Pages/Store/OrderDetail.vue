<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  order: {
    id: number
    order_number: string
    status: string
    payment_status: string
    fulfillment_status: string
    subtotal: string
    shipping_total: string
    grand_total: string
    created_at: string
    items: {
      id: number
      product_name: string
      sku: string
      unit_price: string
      quantity: number
      subtotal: string
    }[]
  }
}>()

const statusColor: Record<string, string> = {
  pending:   'bg-amber-100 text-amber-700',
  confirmed: 'bg-blue-100 text-blue-700',
  completed: 'bg-green-100 text-green-700',
  cancelled: 'bg-red-100 text-red-600',
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <header class="bg-white border-b sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <Link href="/" class="font-bold text-xl text-indigo-600 flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          ShopLaravel
        </Link>
        <nav class="flex items-center gap-4 text-sm">
          <Link href="/orders" class="text-gray-600 hover:text-indigo-600">← My Orders</Link>
          <Link href="/logout" method="post" as="button" class="text-gray-500 hover:text-red-500">Sign out</Link>
        </nav>
      </div>
    </header>

    <main class="max-w-3xl mx-auto px-6 py-10 space-y-6">
      <!-- Header -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div>
            <h1 class="text-xl font-bold text-gray-800">{{ order.order_number }}</h1>
            <p class="text-sm text-gray-400 mt-0.5">
              Placed on {{ new Date(order.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' }) }}
            </p>
          </div>
          <div class="flex gap-2 flex-wrap">
            <span class="text-xs px-3 py-1 rounded-full font-medium capitalize" :class="statusColor[order.status] ?? 'bg-gray-100 text-gray-600'">
              {{ order.status }}
            </span>
            <span class="text-xs px-3 py-1 rounded-full font-medium capitalize bg-gray-100 text-gray-600">
              {{ order.payment_status }}
            </span>
            <span class="text-xs px-3 py-1 rounded-full font-medium capitalize bg-purple-100 text-purple-700">
              {{ order.fulfillment_status }}
            </span>
          </div>
        </div>
      </div>

      <!-- Items -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h2 class="font-bold text-gray-800 mb-4">Items</h2>
        <div class="divide-y divide-gray-100">
          <div v-for="item in order.items" :key="item.id" class="py-3 flex items-center justify-between gap-4">
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-800 text-sm truncate">{{ item.product_name }}</p>
              <p class="text-xs text-gray-400">SKU: {{ item.sku }}</p>
            </div>
            <div class="text-right shrink-0">
              <p class="text-sm text-gray-600">₱{{ Number(item.unit_price).toLocaleString() }} × {{ item.quantity }}</p>
              <p class="font-semibold text-gray-800">₱{{ Number(item.subtotal).toLocaleString() }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Totals -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
        <h2 class="font-bold text-gray-800 mb-2">Summary</h2>
        <div class="flex justify-between text-sm text-gray-600">
          <span>Subtotal</span><span>₱{{ Number(order.subtotal).toLocaleString() }}</span>
        </div>
        <div class="flex justify-between text-sm text-gray-600">
          <span>Shipping</span><span>{{ Number(order.shipping_total) === 0 ? 'Free' : '₱' + Number(order.shipping_total).toLocaleString() }}</span>
        </div>
        <div class="border-t pt-3 flex justify-between font-bold text-gray-900 text-base">
          <span>Total</span><span class="text-indigo-600">₱{{ Number(order.grand_total).toLocaleString() }}</span>
        </div>
      </div>
    </main>
  </div>
</template>
