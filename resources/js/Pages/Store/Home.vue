<script setup lang="ts">
import { Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps<{
  auth: { user: { name: string; email: string } | null }
  cartCount: number
  products: {
    data: {
      id: number
      name: string
      slug: string
      price: string
      stock: number
      category: { id: number; name: string }
    }[]
    links: { url: string | null; label: string; active: boolean }[]
  }
  categories: { id: number; name: string }[]
}>()

const search = ref(new URLSearchParams(window.location.search).get('search') ?? '')
const categoryId = ref(new URLSearchParams(window.location.search).get('category_id') ?? '')

watch([search, categoryId], () => {
  router.get('/', { search: search.value, category_id: categoryId.value }, {
    preserveState: true,
    replace: true,
  })
})

function addToCart(productId: number) {
  if (!props.auth.user) {
    router.visit('/login')
    return
  }
  router.post('/cart', { product_id: productId, quantity: 1 }, { preserveScroll: true })
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex flex-col">

    <!-- Navbar -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between gap-6">
        <!-- Logo -->
        <Link href="/" class="flex items-center gap-2 font-bold text-xl text-indigo-600 shrink-0">
          <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
          </svg>
          ShopLaravel
        </Link>

        <!-- Search bar -->
        <div class="flex-1 max-w-xl hidden sm:block">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input
              v-model="search"
              type="text"
              placeholder="Search products..."
              class="w-full pl-9 pr-4 py-2 text-sm rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"
            />
          </div>
        </div>

        <!-- Nav actions -->
        <nav class="flex items-center gap-4 text-sm shrink-0">
          <template v-if="auth.user">
            <span class="text-gray-500 hidden md:inline">{{ auth.user.name }}</span>
            <Link href="/dashboard" class="text-indigo-600 hover:underline font-medium">Dashboard</Link>
            <Link
              href="/logout" method="post" as="button"
              class="text-gray-500 hover:text-red-500 transition-colors"
            >Sign out</Link>
          </template>
          <template v-else>
            <Link href="/login" class="text-gray-600 hover:text-gray-900 font-medium">Sign in</Link>
            <Link href="/register" class="bg-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium hover:bg-indigo-700 transition-colors">
              Register
            </Link>
          </template>
          <!-- Cart icon -->
          <Link href="/cart" class="relative p-2 text-gray-500 hover:text-indigo-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M10 21a1 1 0 1 1-2 0 1 1 0 0 1 2 0zm9 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
            </svg>
            <span v-if="cartCount > 0" class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-indigo-600 text-white text-xs rounded-full flex items-center justify-center font-bold">{{ cartCount }}</span>
          </Link>
        </nav>
      </div>
    </header>

    <!-- Hero Banner -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
      <div class="max-w-7xl mx-auto px-6 py-16 flex flex-col items-center text-center gap-4">
        <h2 class="text-4xl font-extrabold tracking-tight">Everything you need, delivered.</h2>
        <p class="text-indigo-100 text-lg max-w-xl">Browse our curated collection of products across all categories. Quality guaranteed.</p>
        <div class="flex gap-3 mt-2">
          <a href="#products" class="bg-white text-indigo-600 font-semibold px-6 py-2.5 rounded-full hover:bg-indigo-50 transition-colors">
            Shop Now
          </a>
          <Link v-if="!auth.user" href="/register" class="border border-white text-white font-semibold px-6 py-2.5 rounded-full hover:bg-white/10 transition-colors">
            Join Free
          </Link>
        </div>
      </div>
    </section>

    <main class="max-w-7xl mx-auto px-6 py-10 flex-1 w-full" id="products">

      <!-- Filters row -->
      <div class="flex flex-col sm:flex-row gap-3 mb-8 items-start sm:items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-800">
          {{ categoryId ? categories.find(c => c.id == categoryId)?.name : 'All Products' }}
          <span class="text-sm font-normal text-gray-400 ml-1">({{ products.data.length }} shown)</span>
        </h3>
        <div class="flex gap-2 flex-wrap">
          <button
            @click="categoryId = ''"
            class="px-3 py-1.5 text-xs rounded-full border transition-colors"
            :class="categoryId === '' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400'"
          >All</button>
          <button
            v-for="cat in categories"
            :key="cat.id"
            @click="categoryId = String(cat.id)"
            class="px-3 py-1.5 text-xs rounded-full border transition-colors"
            :class="String(categoryId) === String(cat.id) ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400'"
          >{{ cat.name }}</button>
        </div>
      </div>

      <!-- Mobile search -->
      <div class="sm:hidden mb-6">
        <input
          v-model="search"
          type="text"
          placeholder="Search products..."
          class="w-full px-4 py-2 text-sm rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"
        />
      </div>

      <!-- Product Grid -->
      <div v-if="products.data.length" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
        <div
          v-for="product in products.data"
          :key="product.id"
          class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex flex-col overflow-hidden group"
        >
          <!-- Product image placeholder -->
          <div class="bg-gradient-to-br from-gray-100 to-gray-200 h-44 flex items-center justify-center relative overflow-hidden">
            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span
              v-if="product.stock === 0"
              class="absolute top-2 left-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-medium"
            >Out of stock</span>
            <span
              v-else-if="product.stock <= 5"
              class="absolute top-2 left-2 bg-amber-400 text-white text-xs px-2 py-0.5 rounded-full font-medium"
            >Low stock</span>
          </div>

          <!-- Product info -->
          <div class="p-4 flex flex-col gap-1 flex-1">
            <span class="text-xs text-indigo-500 font-medium uppercase tracking-wide">{{ product.category?.name }}</span>
            <h2 class="text-sm font-semibold text-gray-800 leading-snug line-clamp-2">{{ product.name }}</h2>
            <div class="flex items-center gap-1 mt-1">
              <svg v-for="i in 5" :key="i" class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
              </svg>
              <span class="text-xs text-gray-400 ml-1">4.5</span>
            </div>
            <div class="flex items-center justify-between mt-auto pt-3">
              <span class="text-base font-bold text-gray-900">₱{{ Number(product.price).toLocaleString() }}</span>
            </div>
          </div>

          <!-- Add to cart button -->
          <div class="px-4 pb-4">
            <button
              :disabled="product.stock === 0"
              @click="addToCart(product.id)"
              class="w-full py-2 rounded-xl text-sm font-semibold transition-colors"
              :class="product.stock > 0
                ? 'bg-indigo-600 text-white hover:bg-indigo-700'
                : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
            >
              {{ product.stock > 0 ? 'Add to Cart' : 'Unavailable' }}
            </button>
          </div>
        </div>
      </div>

      <div v-else class="flex flex-col items-center justify-center py-24 text-gray-400 gap-3">
        <svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-lg font-medium">No products found</p>
        <button @click="search = ''; categoryId = ''" class="text-indigo-500 text-sm hover:underline">Clear filters</button>
      </div>

      <!-- Pagination -->
      <div class="flex flex-wrap gap-2 justify-center mt-12">
        <template v-for="link in products.links" :key="link.label">
          <button
            v-if="link.url"
            @click="router.get(link.url)"
            class="px-4 py-2 text-sm rounded-full border font-medium transition-colors"
            :class="link.active
              ? 'bg-indigo-600 text-white border-indigo-600'
              : 'bg-white text-gray-600 border-gray-300 hover:border-indigo-400 hover:text-indigo-600'"
            v-html="link.label"
          />
          <span
            v-else
            class="px-4 py-2 text-sm rounded-full border border-gray-200 text-gray-300"
            v-html="link.label"
          />
        </template>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-16">
      <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm text-gray-500">
        <div class="col-span-2 md:col-span-1">
          <div class="flex items-center gap-2 font-bold text-lg text-indigo-600 mb-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            ShopLaravel
          </div>
          <p class="text-gray-400 text-xs leading-relaxed">Your one-stop shop for quality products at great prices.</p>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700 mb-3">Shop</h4>
          <ul class="space-y-2">
            <li><a href="#" class="hover:text-indigo-600 transition-colors">All Products</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">New Arrivals</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Best Sellers</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700 mb-3">Account</h4>
          <ul class="space-y-2">
            <li><Link href="/login" class="hover:text-indigo-600 transition-colors">Sign In</Link></li>
            <li><Link href="/register" class="hover:text-indigo-600 transition-colors">Register</Link></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">My Orders</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold text-gray-700 mb-3">Support</h4>
          <ul class="space-y-2">
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Help Center</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Contact Us</a></li>
            <li><a href="#" class="hover:text-indigo-600 transition-colors">Returns</a></li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-100 text-center text-xs text-gray-400 py-4">
        © {{ new Date().getFullYear() }} ShopLaravel. Built with Laravel + Inertia + Vue.
      </div>
    </footer>

  </div>
</template>
