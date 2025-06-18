<template>
    <GuestLayout title="Portal Berita">
        <!-- Hero Section -->
        <section class="relative py-20 overflow-hidden" :style="{ backgroundColor: 'white' }">
            <div class="container-custom relative z-10">
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="heading-responsive font-bold mb-6" :style="{ color: 'var(--text-color)' }">
                        Portal Berita
                    </h1>
                    <p class="text-responsive mb-8" :style="{ color: 'var(--light-text)' }">
                        Dapatkan informasi terkini seputar kegiatan dan perkembangan organisasi
                    </p>
                </div>
            </div>
        </section>

        <!-- News Section -->
        <section
            class="py-24 relative overflow-hidden"
            :style="{
                background: `linear-gradient(to bottom, white 0%, var(--color-background) 100%)`
            }"
        >
            <div class="container-custom relative z-10">
                <!-- Category Navigation -->
                <div class="mb-12">
                    <div class="flex flex-wrap gap-3 justify-center max-w-4xl mx-auto">
                        <button
                            @click="filterByCategory(null)"
                            class="group relative px-8 py-4 rounded-2xl transition-all duration-300 text-sm md:text-base font-semibold overflow-hidden"
                            :style="
                                !activeCategory
                                    ? {
                                          backgroundColor: 'var(--color-primary)',
                                          color: 'white',
                                      }
                                    : {
                                          backgroundColor: 'white',
                                          color: 'var(--color-primary)',
                                          border: '1px solid var(--color-secondary)',
                                      }
                            "
                        >
                            <span class="relative z-10">Semua Berita</span>
                            <div
                                v-if="activeCategory"
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    opacity: '0.1',
                                }"
                            ></div>
                        </button>
                        <button
                            v-for="category in categories"
                            :key="category.id"
                            @click="filterByCategory(category.id)"
                            class="group relative px-8 py-4 rounded-2xl transition-all duration-300 text-sm md:text-base font-semibold overflow-hidden"
                            :style="
                                activeCategory === category.id
                                    ? {
                                          backgroundColor: 'var(--color-primary)',
                                          color: 'white',
                                      }
                                    : {
                                          backgroundColor: 'white',
                                          color: 'var(--color-primary)',
                                          border: '1px solid var(--color-secondary)',
                                      }
                            "
                        >
                            <span class="relative z-10">
                                {{ category.name }} ({{ category.articles_count }})
                            </span>
                            <div
                                v-if="activeCategory !== category.id"
                                class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    opacity: '0.1',
                                }"
                            ></div>
                        </button>
                    </div>
                </div>

                <!-- Articles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-12">
                    <article
                        v-for="article in articles"
                        :key="article.id"
                        class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer"
                        @click="goToArticle(article.id)"
                    >
                        <div class="relative overflow-hidden">
                            <img
                                v-if="article.featured_image"
                                :src="article.featured_image"
                                :alt="article.title"
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                            />
                            <div
                                v-else
                                class="w-full h-48 flex items-center justify-center"
                                :style="{ backgroundColor: 'var(--color-background)' }"
                            >
                                <span :style="{ color: 'var(--light-text)' }">No Image</span>
                            </div>
                            <div
                                class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-medium text-white"
                                :style="{ backgroundColor: 'var(--color-primary)' }"
                            >
                                {{ article.category.name }}
                            </div>
                        </div>
                        <div class="p-6">
                            <h3
                                class="text-lg font-bold mb-3 line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                {{ article.title }}
                            </h3>
                            <p
                                class="text-sm mb-4 line-clamp-3"
                                :style="{ color: 'var(--light-text)' }"
                            >
                                {{ article.content }}
                            </p>
                            <div class="flex items-center justify-between text-xs" :style="{ color: 'var(--light-text)' }">
                                <span>{{ article.author }}</span>
                                <span>{{ article.created_at }}</span>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.last_page > 1" class="flex justify-center">
                    <div class="flex items-center space-x-2">
                        <button
                            v-if="pagination.current_page > 1"
                            @click="changePage(pagination.current_page - 1)"
                            class="px-4 py-2 rounded-lg transition-colors duration-300"
                            :style="{
                                backgroundColor: 'white',
                                color: 'var(--color-primary)',
                                border: '1px solid var(--color-secondary)',
                            }"
                        >
                            Previous
                        </button>
                        <span class="px-4 py-2" :style="{ color: 'var(--text-color)' }">
                            {{ pagination.current_page }} / {{ pagination.last_page }}
                        </span>
                        <button
                            v-if="pagination.has_more_pages"
                            @click="changePage(pagination.current_page + 1)"
                            class="px-4 py-2 rounded-lg transition-colors duration-300"
                            :style="{
                                backgroundColor: 'white',
                                color: 'var(--color-primary)',
                                border: '1px solid var(--color-secondary)',
                            }"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const props = defineProps({
    categories: Array,
    articles: Array,
    activeCategory: Number,
    pagination: Object
})

const activeCategory = ref(props.activeCategory || null)

const filterByCategory = (categoryId) => {
    activeCategory.value = categoryId
    if (categoryId) {
        router.get(`/news/category/${categoryId}`)
    } else {
        router.get('/news')
    }
}

const goToArticle = (articleId) => {
    router.get(`/article/${articleId}`)
}

const changePage = (page) => {
    const url = activeCategory.value 
        ? `/news/category/${activeCategory.value}?page=${page}`
        : `/news?page=${page}`
    router.get(url)
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>