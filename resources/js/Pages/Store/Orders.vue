<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
  orders: {
    data: {
      id: number
      order_number: string
      status: string
      payment_status: string
      grand_total: string
      created_at: string
      items: { id: number }[]
    }[]
    links: { url: string | null; label: string; active: boolean }[]
  }
}>()

const statusColor: Record<string, string> = {
  pending:   'bg-amber-100 text-amber-700',
  confirmed: 'bg-blue-100 text-blue-700',
  completed: 'bg-green-100 text-green-700',
  cancelled: 'bg-red-100 text-red-600',
}
const paymentColor: Record<string, string> = {
  pending: 'bg-gray-100 text-gray-600',
  paid:    'bg-green-100 text-green-700',
  failed:  'bg-red-100 text-red-600',
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
          <Link href="/" class="text-gray-600 hover:text-indigo-600">Shop</Link>
          <Link href="/cart" class="text-gray-600 hover:text-indigo-600">Cart</Link>
          <Link href="/logout" method="post" as="button" class="text-gray-500 hover:text-red-500">Sign out</Link>
        </nav>
      </div>
    </header>

    <main class="max-w-4xl mx-auto px-6 py-10">
      <h1 class="text-2xl font-bold text-gray-800 mb-8">My Orders</h1>

      <div v-if="orders.data.length" class="space-y-4">
        <Link
          v-for="order in orders.data" :key="order.id"
          :href="`/orders/${order.id}`"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:shadow-md transition-shadow block"
        >
          <div class="space-y-1">
            <p class="font-bold text-gray-800">{{ order.order_number }}</p>
            <p class="text-xs text-gray-400">{{ new Date(order.created_at).toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' }) }}</p>
            <p class="text-xs text-gray-500">{{ order.items.length }} item{{ order.items.length !== 1 ? 's' : '' }}</p>
          </div>
          <div class="flex items-center gap-3 flex-wrap">
            <span class="text-xs px-2.5 py-1 rounded-full font-medium capitalize" :class="statusColor[order.status] ?? 'bg-gray-100 text-gray-600'">
              {{ order.status }}
            </span>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium capitalize" :class="paymentColor[order.payment_status] ?? 'bg-gray-100 text-gray-600'">
              {{ order.payment_status }}
            </span>
            <span class="font-bold text-indigo-600 text-base">₱{{ Number(order.grand_total).toLocaleString() }}</span>
          </div>
        </Link>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-24 text-gray-400 gap-4">
        <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <p class="text-xl font-semibold">No orders yet</p>
        <Link href="/" class="bg-indigo-600 text-white px-6 py-2.5 rounded-full font-medium hover:bg-indigo-700">Start Shopping</Link>
      </div>
    </main>
  </div>
</template>
