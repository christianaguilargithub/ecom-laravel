<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3'

const props = defineProps<{
  items: {
    id: number
    quantity: number
    product: { id: number; name: string; price: string; stock: number; category: { name: string } }
  }[]
  subtotal: string
}>()

function updateQty(itemId: number, qty: number) {
  router.patch(`/cart/${itemId}`, { quantity: qty }, { preserveScroll: true })
}

function remove(itemId: number) {
  router.delete(`/cart/${itemId}`, { preserveScroll: true })
}

const orderForm = useForm({})
function placeOrder() {
  orderForm.post('/orders')
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar -->
    <header class="bg-white border-b sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        <Link href="/" class="font-bold text-xl text-indigo-600 flex items-center gap-2">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          ShopLaravel
        </Link>
        <nav class="flex items-center gap-4 text-sm">
          <Link href="/" class="text-gray-600 hover:text-indigo-600">Shop</Link>
          <Link href="/orders" class="text-gray-600 hover:text-indigo-600">My Orders</Link>
          <Link href="/logout" method="post" as="button" class="text-gray-500 hover:text-red-500">Sign out</Link>
        </nav>
      </div>
    </header>

    <main class="max-w-5xl mx-auto px-6 py-10">
      <h1 class="text-2xl font-bold text-gray-800 mb-8">Your Cart</h1>

      <div v-if="items.length" class="grid md:grid-cols-3 gap-8">
        <!-- Items -->
        <div class="md:col-span-2 space-y-4">
          <div
            v-for="item in items" :key="item.id"
            class="bg-white rounded-2xl border border-gray-100 p-4 flex gap-4 items-center shadow-sm"
          >
            <div class="bg-gray-100 rounded-xl w-20 h-20 flex items-center justify-center shrink-0">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-xs text-indigo-500 font-medium">{{ item.product.category.name }}</p>
              <p class="font-semibold text-gray-800 text-sm truncate">{{ item.product.name }}</p>
              <p class="text-indigo-600 font-bold mt-0.5">₱{{ Number(item.product.price).toLocaleString() }}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
              <button @click="updateQty(item.id, item.quantity - 1)" :disabled="item.quantity <= 1"
                class="w-7 h-7 rounded-full border text-gray-600 hover:bg-gray-100 disabled:opacity-30 flex items-center justify-center font-bold">−</button>
              <span class="w-6 text-center text-sm font-semibold">{{ item.quantity }}</span>
              <button @click="updateQty(item.id, item.quantity + 1)" :disabled="item.quantity >= item.product.stock"
                class="w-7 h-7 rounded-full border text-gray-600 hover:bg-gray-100 disabled:opacity-30 flex items-center justify-center font-bold">+</button>
            </div>
            <button @click="remove(item.id)" class="text-red-400 hover:text-red-600 ml-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
          </div>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 h-fit space-y-4">
          <h2 class="font-bold text-gray-800 text-lg">Order Summary</h2>
          <div class="flex justify-between text-sm text-gray-600">
            <span>Subtotal</span><span class="font-semibold text-gray-800">₱{{ subtotal }}</span>
          </div>
          <div class="flex justify-between text-sm text-gray-600">
            <span>Shipping</span><span class="text-green-600 font-medium">Free</span>
          </div>
          <div class="border-t pt-4 flex justify-between font-bold text-gray-900">
            <span>Total</span><span class="text-indigo-600 text-lg">₱{{ subtotal }}</span>
          </div>
          <button
            @click="placeOrder"
            :disabled="orderForm.processing"
            class="w-full bg-indigo-600 text-white py-3 rounded-xl font-semibold hover:bg-indigo-700 transition-colors disabled:opacity-50"
          >
            {{ orderForm.processing ? 'Placing order...' : 'Place Order' }}
          </button>
          <Link href="/" class="block text-center text-sm text-indigo-500 hover:underline">Continue Shopping</Link>
        </div>
      </div>

      <!-- Empty cart -->
      <div v-else class="flex flex-col items-center justify-center py-24 text-gray-400 gap-4">
        <svg class="w-20 h-20" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M10 21a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm9 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
        <p class="text-xl font-semibold">Your cart is empty</p>
        <Link href="/" class="bg-indigo-600 text-white px-6 py-2.5 rounded-full font-medium hover:bg-indigo-700">Start Shopping</Link>
      </div>
    </main>
  </div>
</template>
