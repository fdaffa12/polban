<template>
    <GuestLayout title="Detail Artikel">
        <!-- Article Header -->
        <section
            class="relative py-20 overflow-hidden"
            :style="{ backgroundColor: 'white' }"
        >
            <div class="container-custom relative z-10">
                <div class="max-w-4xl mx-auto">
                    <nav
                        class="flex items-center space-x-2 text-sm mb-8"
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

                    <div
                        class="inline-block px-4 py-2 rounded-full text-sm font-medium text-white mb-6"
                        :style="{ backgroundColor: 'var(--color-primary)' }"
                    >
                        {{ article.category.name }}
                    </div>

                    <h1
                        class="heading-responsive font-bold mb-6"
                        :style="{ color: 'var(--text-color)' }"
                    >
                        {{ article.title }}
                    </h1>

                    <div
                        class="flex items-center space-x-6 text-sm"
                        :style="{ color: 'var(--light-text)' }"
                    >
                        <div class="flex items-center space-x-2">
                            <svg
                                class="w-4 h-4"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <span>{{ article.author }}</span>
                        </div>
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
                </div>
            </div>
        </section>

        <!-- Article Content -->
        <section
            class="py-16 relative overflow-hidden"
            :style="{
                background: `linear-gradient(to bottom, white 0%, var(--color-background) 100%)`,
            }"
        >
            <div class="container-custom relative z-10">
                <div class="max-w-4xl mx-auto">
                    <!-- Featured Image -->
                    <div v-if="article.featured_image" class="mb-12">
                        <img
                            :src="article.featured_image"
                            :alt="article.title"
                            class="w-full h-96 object-cover rounded-2xl shadow-lg"
                        />
                    </div>

                    <!-- Article Content -->
                    <div
                        class="prose prose-lg max-w-none mb-12"
                        :style="{ color: 'var(--text-color)' }"
                        v-html="article.content"
                    ></div>

                    <!-- Tags -->
                    <div
                        v-if="article.tags && article.tags.length > 0"
                        class="mb-12"
                    >
                        <h3
                            class="text-lg font-semibold mb-4"
                            :style="{ color: 'var(--text-color)' }"
                        >
                            Tags:
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in article.tags"
                                :key="tag.id"
                                class="px-3 py-1 rounded-full text-sm"
                                :style="{
                                    backgroundColor: 'var(--color-secondary)',
                                    color: 'white',
                                }"
                            >
                                {{ tag.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Share Buttons -->
                    <div
                        class="border-t border-b py-8 mb-12"
                        :style="{ borderColor: 'var(--color-secondary)' }"
                    >
                        <h3
                            class="text-lg font-semibold mb-4"
                            :style="{ color: 'var(--text-color)' }"
                        >
                            Bagikan Artikel:
                        </h3>
                        <div class="flex space-x-4">
                            <button
                                @click="shareToFacebook"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors duration-300"
                                :style="{
                                    backgroundColor: '#1877F2',
                                    color: 'white',
                                }"
                            >
                                <span>Facebook</span>
                            </button>
                            <button
                                @click="shareToTwitter"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors duration-300"
                                :style="{
                                    backgroundColor: '#1DA1F2',
                                    color: 'white',
                                }"
                            >
                                <span>Twitter</span>
                            </button>
                            <button
                                @click="shareToWhatsApp"
                                class="flex items-center space-x-2 px-4 py-2 rounded-lg transition-colors duration-300"
                                :style="{
                                    backgroundColor: '#25D366',
                                    color: 'white',
                                }"
                            >
                                <span>WhatsApp</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Related Articles -->
        <section
            v-if="relatedArticles.length > 0"
            class="py-16"
            :style="{ backgroundColor: 'var(--color-background)' }"
        >
            <div class="container-custom">
                <div class="max-w-6xl mx-auto">
                    <h2
                        class="text-3xl font-bold text-center mb-12"
                        :style="{ color: 'var(--text-color)' }"
                    >
                        Artikel Terkait
                    </h2>
                    <div
                        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8"
                    >
                        <article
                            v-for="relatedArticle in relatedArticles"
                            :key="relatedArticle.id"
                            class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden cursor-pointer"
                            @click="goToArticle(relatedArticle.id)"
                        >
                            <div class="relative overflow-hidden">
                                <img
                                    v-if="relatedArticle.featured_image"
                                    :src="relatedArticle.featured_image"
                                    :alt="relatedArticle.title"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                                />
                                <div
                                    v-else
                                    class="w-full h-48 flex items-center justify-center"
                                    :style="{
                                        backgroundColor:
                                            'var(--color-background)',
                                    }"
                                >
                                    <span
                                        :style="{ color: 'var(--light-text)' }"
                                        >No Image</span
                                    >
                                </div>
                            </div>
                            <div class="p-6">
                                <h3
                                    class="text-lg font-bold mb-3 line-clamp-2 group-hover:text-[var(--color-primary)] transition-colors duration-300"
                                    :style="{ color: 'var(--text-color)' }"
                                >
                                    {{ relatedArticle.title }}
                                </h3>
                                <p
                                    class="text-sm mb-4 line-clamp-3"
                                    :style="{ color: 'var(--light-text)' }"
                                >
                                    {{ relatedArticle.content }}
                                </p>
                                <div
                                    class="flex items-center justify-between text-xs"
                                    :style="{ color: 'var(--light-text)' }"
                                >
                                    <span>{{ relatedArticle.author }}</span>
                                    <span>{{ relatedArticle.created_at }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </GuestLayout>
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

const props = defineProps({
    article: Object,
    relatedArticles: Array,
});

const goToArticle = (articleId) => {
    router.get(`/article/${articleId}`);
};

const shareToFacebook = () => {
    const url = encodeURIComponent(window.location.href);
    window.open(
        `https://www.facebook.com/sharer/sharer.php?u=${url}`,
        "_blank"
    );
};

const shareToTwitter = () => {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(props.article.title);
    window.open(
        `https://twitter.com/intent/tweet?url=${url}&text=${text}`,
        "_blank"
    );
};

const shareToWhatsApp = () => {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent(`${props.article.title} - ${url}`);
    window.open(`https://wa.me/?text=${text}`, "_blank");
};
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
</style>
