<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { useIntersectionObserver } from "@/composables/useIntersectionObserver";

const props = defineProps({
    article: Object,
    relatedArticles: Array,
    popularPosts: Array,
});

const goToArticle = (slug) => {
    router.get(`/article/${slug}`);
};

const popularPosts = computed(() => props.popularPosts);

useIntersectionObserver();
</script>

<template>
    <GuestLayout :title="article.title">
        <!-- Article Header -->
        <section
            class="relative py-12 float-in-section"
            :style="{
                background: `linear-gradient(to bottom, white, white 30%, var(--color-background))`,
            }"
        >
            <div class="container-custom">
                <nav
                    class="flex items-center space-x-2 text-sm mb-8 float-in-section delay-100"
                    :style="{ color: 'var(--light-text)' }"
                >
                    <a
                        href="/"
                        class="hover:text-[var(--color-primary)] transition-colors duration-300"
                        >Home</a
                    >
                    <span>/</span>
                    <a
                        href="/news"
                        class="hover:text-[var(--color-primary)] transition-colors duration-300"
                        >News</a
                    >
                    <span>/</span>
                    <span :style="{ color: 'var(--color-primary)' }">{{
                        article.category.name
                    }}</span>
                </nav>

                <!-- Two Column Layout -->
                <div class="grid-cols-layout">
                    <!-- Main Content Column -->
                    <div>
                        <div
                            class="inline-block px-4 py-2 rounded-full text-sm font-medium text-white mb-6 float-in-section delay-200"
                            :style="{ backgroundColor: 'var(--color-primary)' }"
                        >
                            {{ article.category.name }}
                        </div>

                        <h1
                            class="heading-responsive font-bold mb-6 float-in-section delay-300"
                            :style="{ color: 'var(--text-color)' }"
                        >
                            {{ article.title }}
                        </h1>

                        <div
                            class="flex items-center space-x-6 text-sm mb-8 float-in-section delay-400"
                            :style="{ color: 'var(--light-text)' }"
                        >
                            <div class="flex items-center space-x-2"></div>
                            <div class="flex items-center space-x-2">
                                <svg
                                    class="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>{{ article.created_at }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg
                                    class="w-4 h-4"
                                    fill="currentColor"
                                    viewBox="0 0 20 20"
                                >
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                    <path
                                        fill-rule="evenodd"
                                        d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <span>{{ article.viewed ?? 0 }} views</span>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div
                            v-if="article.featured_image"
                            class="mb-8 float-in-section delay-500"
                        >
                            <img
                                :src="article.featured_image"
                                :alt="article.title"
                                class="w-full object-cover rounded-2xl shadow-lg"
                            />
                        </div>

                        <!-- Article Content -->
                        <div
                            class="prose prose-lg max-w-none float-in-section delay-600"
                            :style="{ color: 'var(--text-color)' }"
                            v-html="article.content"
                        ></div>
                    </div>

                    <!-- Sidebar Column -->
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

@media (min-width: 1024px) {
    .container-custom {
        padding: 0 3rem;
    }
}

.grid-cols-layout {
    display: grid;
    gap: 2rem;
    grid-template-columns: 1fr;
}

@media (min-width: 1024px) {
    .grid-cols-layout {
        grid-template-columns: 2fr 1fr;
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    line-height: 1.8;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
    color: var(--text-color);
    font-weight: 600;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose p {
    color: var(--text-color);
    margin-bottom: 1.5rem;
}

.prose strong {
    color: var(--text-color);
    font-weight: 600;
}

.prose a {
    color: var(--color-primary);
    text-decoration: none;
}

.prose a:hover {
    color: var(--color-primary-dark);
}

.prose ul,
.prose ol {
    margin: 1.5rem 0;
    padding-left: 2rem;
}

.prose li {
    margin-bottom: 0.5rem;
    color: var(--text-color);
}

.prose blockquote {
    border-left: 4px solid var(--color-primary);
    padding-left: 1.5rem;
    margin: 2rem 0;
    font-style: italic;
    color: var(--light-text);
}

.heading-responsive {
    font-size: 1.875rem;
    line-height: 2.25rem;
}

@media (min-width: 1024px) {
    .heading-responsive {
        font-size: 2.25rem;
        line-height: 2.5rem;
    }
}

/* Add this new style */
section {
    min-height: 100vh; /* This ensures the gradient covers the full height */
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
</style>
