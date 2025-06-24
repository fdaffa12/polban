<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useIntersectionObserver } from "@/composables/useIntersectionObserver";

const props = defineProps({
    categories: Array,
    articles: Array,
    activeCategory: Number,
    pagination: Object,
    popularPosts: Array, // Add this line
});

const activeCategory = ref(props.activeCategory || null);
const displayedCount = ref(5);
const loading = ref(false);

// Computed properties
const displayedArticles = computed(() => {
    return props.articles.slice(0, displayedCount.value);
});

const canLoadMore = computed(() => {
    return displayedCount.value < props.articles.length;
});

const popularPosts = computed(() => props.popularPosts);

// Methods
const filterByCategory = (categoryId) => {
    activeCategory.value = categoryId;
    displayedCount.value = 5; // Reset to initial count
    if (categoryId) {
        router.get(`/news/category/${categoryId}`);
    } else {
        router.get("/news");
    }
};

const goToArticle = (slug) => {
    router.get(`/article/${slug}`);
};

const loadMore = () => {
    loading.value = true;
    setTimeout(() => {
        displayedCount.value += 5;
        loading.value = false;
    }, 500); // Simulate loading delay
};

// Reset displayed count when articles change
onMounted(() => {
    displayedCount.value = 5;
});

useIntersectionObserver();
</script>

<template>
    <GuestLayout title="Portal Berita">
        <!-- Main Container -->
        <section
            class="relative py-12 float-in-section"
            :style="{ backgroundColor: 'white' }"
        >
            <div class="container-custom">
                <div class="grid-cols-layout">
                    <!-- Left Column (Main Content) -->
                    <div>
                        <!-- Title and Description -->
                        <div class="mb-8 float-in-section delay-100">
                            <h1
                                class="text-3xl lg:text-4xl xl:text-5xl font-bold mb-4"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                Portal Berita
                            </h1>
                            <p
                                class="text-base lg:text-lg mb-6"
                                :style="{ color: 'var(--light-text)' }"
                            >
                                Dapatkan informasi terkini seputar kegiatan dan
                                perkembangan organisasi
                            </p>
                        </div>

                        <!-- Category Navigation -->
                        <div class="mb-8 float-in-section delay-200">
                            <div class="flex flex-wrap gap-2 lg:gap-3">
                                <button
                                    @click="filterByCategory(null)"
                                    class="group relative px-4 lg:px-6 py-2 lg:py-3 rounded-xl transition-all duration-300 text-xs lg:text-sm font-semibold overflow-hidden"
                                    :style="
                                        !activeCategory
                                            ? {
                                                  backgroundColor:
                                                      'var(--color-primary)',
                                                  color: 'white',
                                              }
                                            : {
                                                  backgroundColor: 'white',
                                                  color: 'var(--color-primary)',
                                                  border: '1px solid var(--color-secondary)',
                                              }
                                    "
                                >
                                    <span class="relative z-10"
                                        >Semua Berita</span
                                    >
                                    <div
                                        v-if="activeCategory"
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-secondary)',
                                            opacity: '0.1',
                                        }"
                                    ></div>
                                </button>
                                <button
                                    v-for="category in categories"
                                    :key="category.id"
                                    @click="filterByCategory(category.id)"
                                    class="group relative px-4 lg:px-6 py-2 lg:py-3 rounded-xl transition-all duration-300 text-xs lg:text-sm font-semibold overflow-hidden"
                                    :style="
                                        Number(activeCategory) ===
                                        Number(category.id)
                                            ? {
                                                  backgroundColor:
                                                      'var(--color-primary)',
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
                                        {{ category.name }} ({{
                                            category.articles_count
                                        }})
                                    </span>
                                    <div
                                        v-if="
                                            Number(activeCategory) !==
                                            Number(category.id)
                                        "
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                        :style="{
                                            backgroundColor:
                                                'var(--color-secondary)',
                                            opacity: '0.1',
                                        }"
                                    ></div>
                                </button>
                            </div>
                        </div>

                        <!-- Articles List -->
                        <div class="space-y-4 lg:space-y-6 mb-8">
                            <article
                                v-for="(article, index) in displayedArticles"
                                :key="article.id"
                                class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer float-in-section"
                                :class="`delay-${(index + 3) * 100}`"
                                @click="goToArticle(article.slug)"
                            >
                                <div class="flex flex-col md:flex-row">
                                    <!-- Article Image -->
                                    <div
                                        class="md:w-1/3 relative overflow-hidden"
                                    >
                                        <img
                                            v-if="article.featured_image"
                                            :src="article.featured_image"
                                            :alt="article.title"
                                            class="w-full h-48 md:h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                        <div
                                            v-else
                                            class="w-full h-48 md:h-full flex items-center justify-center"
                                            :style="{
                                                backgroundColor:
                                                    'var(--color-background)',
                                            }"
                                        >
                                            <span
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                                >No Image</span
                                            >
                                        </div>
                                        <div
                                            class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-medium text-white"
                                            :style="{
                                                backgroundColor:
                                                    'var(--color-primary)',
                                            }"
                                        >
                                            {{ article.category.name }}
                                        </div>
                                    </div>

                                    <!-- Article Content -->
                                    <div
                                        class="md:w-2/3 p-4 lg:p-6 flex flex-col justify-between"
                                    >
                                        <div>
                                            <h3
                                                class="text-lg lg:text-xl font-bold mb-2 lg:mb-3 line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300"
                                                :style="{
                                                    color: 'var(--text-color)',
                                                }"
                                            >
                                                {{ article.title }}
                                            </h3>
                                            <p
                                                class="text-sm mb-3 lg:mb-4 line-clamp-3"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                {{ article.content }}
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center justify-between text-xs lg:text-sm"
                                            :style="{
                                                color: 'var(--light-text)',
                                            }"
                                        >
                                            <span>{{ article.author }}</span>
                                            <span>{{
                                                article.created_at
                                            }}</span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>

                        <!-- Load More Button -->
                        <div
                            v-if="canLoadMore"
                            class="text-center float-in-section delay-200"
                        >
                            <button
                                @click="loadMore"
                                :disabled="loading"
                                class="px-6 lg:px-8 py-2 lg:py-3 rounded-xl font-semibold transition-all duration-300 disabled:opacity-50 text-sm lg:text-base"
                                :style="{
                                    backgroundColor: 'var(--color-primary)',
                                    color: 'white',
                                }"
                            >
                                {{ loading ? "Loading..." : "Load More" }}
                            </button>
                        </div>
                    </div>

                    <!-- Right Column (Sidebar) -->
                    <div class="float-in-section delay-300">
                        <div
                            class="bg-white rounded-2xl shadow-lg p-4 lg:p-6 lg:sticky lg:top-8"
                        >
                            <h2
                                class="text-xl lg:text-2xl font-bold mb-4 lg:mb-6"
                                :style="{ color: 'var(--text-color)' }"
                            >
                                Popular Posts
                            </h2>
                            <div class="space-y-3 lg:space-y-4">
                                <article
                                    v-for="(post, index) in popularPosts"
                                    :key="post.id"
                                    class="group cursor-pointer border-b border-gray-100 pb-3 lg:pb-4 last:border-b-0 last:pb-0 float-in-section"
                                    :class="`delay-${(index + 4) * 100}`"
                                    @click="goToArticle(post.slug)"
                                >
                                    <div class="flex gap-3 lg:gap-4">
                                        <div
                                            class="w-16 h-16 lg:w-20 lg:h-20 flex-shrink-0 rounded-lg overflow-hidden"
                                        >
                                            <img
                                                v-if="post.featured_image"
                                                :src="post.featured_image"
                                                :alt="post.title"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            />
                                            <div
                                                v-else
                                                class="w-full h-full flex items-center justify-center"
                                                :style="{
                                                    backgroundColor:
                                                        'var(--color-background)',
                                                }"
                                            >
                                                <span
                                                    class="text-xs"
                                                    :style="{
                                                        color: 'var(--light-text)',
                                                    }"
                                                    >No Image</span
                                                >
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="inline-block px-2 py-1 rounded text-xs font-medium text-white mb-1 lg:mb-2"
                                                :style="{
                                                    backgroundColor:
                                                        'var(--color-primary)',
                                                }"
                                            >
                                                {{ post.category.name }}
                                            </div>
                                            <h3
                                                class="font-semibold text-xs lg:text-sm line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300 mb-1 lg:mb-2"
                                                :style="{
                                                    color: 'var(--text-color)',
                                                }"
                                            >
                                                {{ post.title }}
                                            </h3>
                                            <div
                                                class="flex justify-between items-center text-xs"
                                                :style="{
                                                    color: 'var(--light-text)',
                                                }"
                                            >
                                                <span>{{
                                                    post.created_at
                                                }}</span>
                                                <span
                                                    >{{
                                                        post.viewed ?? 0
                                                    }}
                                                    views</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

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

.container-custom {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (min-width: 640px) {
    .container-custom {
        padding: 0 2rem;
    }
}

/* Memastikan grid 2 kolom di desktop */
@media (min-width: 1024px) {
    .container-custom {
        padding: 0 3rem;
    }
}

/* Tambahkan style untuk float-in animation */
:deep(.float-in-section) {
    will-change: transform, opacity;
}

:deep(.float-in-section.visible) {
    will-change: auto;
}

@keyframes floatIn {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.float-in-section {
    opacity: 0;
    transform: translateY(50px);
}

.float-in-section.visible {
    animation: floatIn 0.8s ease-out forwards;
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}

.delay-300 {
    animation-delay: 0.3s;
}

.delay-400 {
    animation-delay: 0.4s;
}

.delay-500 {
    animation-delay: 0.5s;
}

.delay-600 {
    animation-delay: 0.6s;
}

/* Pastikan animasi berjalan smooth */
* {
    backface-visibility: hidden;
    -webkit-font-smoothing: antialiased;
}

/* Grid layout untuk desktop */
@media (min-width: 1024px) {
    .grid-cols-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }
}
</style>
