<script setup>
import { ref, computed } from "vue";
import { useForm, Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { Edit, Trash2, ChevronDown } from "lucide-vue-next";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import Multiselect from "@vueform/multiselect";
import "@vueform/multiselect/themes/default.css";

const props = defineProps({
    articles: Array,
    categories: Array,
    tags: Array,
});

const showCategoryModal = ref(false);
const showArticleModal = ref(false);
const editingArticle = ref(null);
const editingCategory = ref(null);
const isCategoriesExpanded = ref(true);
const toast = useToast();

// New refs for content modal
const showContentModal = ref(false);
const selectedContent = ref(null);

// Add these refs for tags management
const newTag = ref("");
const selectedTags = ref([]);

// Modify articleForm to handle tags as strings
const articleForm = useForm({
    title: "",
    content: "",
    category_id: "",
    featured_image: null,
    tags: [], // This will store tag strings
});

const categoryForm = useForm({
    name: "",
});

// Table headers
const categoryHeaders = [
    { text: "Name", value: "name" },
    { text: "Actions", value: "actions", sortable: false },
];

const articleHeaders = [
    { text: "Image", value: "featured_image" },
    { text: "Title", value: "title" },
    { text: "Tags", value: "tags" },
    { text: "Category", value: "category.name" },
    { text: "Content", value: "content" },
    { text: "Actions", value: "actions", sortable: false },
];

const createArticle = () => {
    articleForm.post(route("articles.store"), {
        onSuccess: () => {
            showArticleModal.value = false;
            articleForm.reset();
            toast.success("Article created successfully!");
            updateArticle;
        },
    });
};

const editArticle = (article) => {
    editingArticle.value = article;
    articleForm.title = article.title;
    articleForm.content = article.content;
    articleForm.category_id = article.category_id;
    articleForm.tags = article.tags.map((tag) => tag.name);
    showArticleModal.value = true;
};

const updateArticle = () => {
    articleForm.put(route("articles.update", editingArticle.value.id), {
        onSuccess: () => {
            showArticleModal.value = false;
            articleForm.reset();
            editingArticle.value = null;
            toast.success("Article updated successfully!");
        },
    });
};

const deleteArticle = (article) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("articles.destroy", article.id), {
                onSuccess: () => {
                    toast.success("Article deleted successfully!");
                },
            });
        }
    });
};

const createCategory = () => {
    categoryForm.post(route("categories.store"), {
        onSuccess: () => {
            showCategoryModal.value = false;
            categoryForm.reset();
            toast.success("Category created successfully!");
        },
    });
};

const editCategory = (category) => {
    editingCategory.value = category;
    categoryForm.name = category.name;
    showCategoryModal.value = true;
};

const updateCategory = () => {
    categoryForm.put(route("categories.update", editingCategory.value.id), {
        onSuccess: () => {
            showCategoryModal.value = false;
            categoryForm.reset();
            editingCategory.value = null;
            toast.success("Category updated successfully!");
        },
    });
};

const deleteCategory = (category) => {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("categories.destroy", category.id), {
                onSuccess: () => {
                    toast.success("Category deleted successfully!");
                },
            });
        }
    });
};

const toggleCategories = () => {
    isCategoriesExpanded.value = !isCategoriesExpanded.value;
};

const showFullContent = (content) => {
    selectedContent.value = content;
    showContentModal.value = true;
};

const editorOptions = {
    theme: "snow",
    preserveWhitespace: true,
    modules: {
        toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ header: 1 }, { header: 2 }],
            [{ list: "ordered" }, { list: "bullet" }],
            [{ script: "sub" }, { script: "super" }],
            [{ indent: "-1" }, { indent: "+1" }],
            [{ direction: "rtl" }],
            [{ size: ["small", false, "large", "huge"] }],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            [{ color: [] }, { background: [] }],
            [{ font: [] }],
            [{ align: [] }],
            ["clean"],
            ["link", "image"],
        ],
    },
};

// Add this helper function
const stripHtml = (html) => {
    const tmp = document.createElement("DIV");
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || "";
};

// Add this method
const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        articleForm.featured_image = file;
    }
};

// Add this method to handle tag input
const handleTagInput = (e) => {
    if (e.key === "," || e.key === "Enter") {
        e.preventDefault();
        const tag = newTag.value.trim().toLowerCase();
        if (tag && !articleForm.tags.includes(tag)) {
            articleForm.tags.push(tag);
        }
        newTag.value = "";
    }
};

// Add method to remove tag
const removeTag = (tagToRemove) => {
    articleForm.tags = articleForm.tags.filter((tag) => tag !== tagToRemove);
};

// Add these new refs for search and filters
const articleSearch = ref("");
const categorySearch = ref("");
const selectedCategory = ref("");

// Add computed properties for filtered data
const filteredArticles = computed(() => {
    let filtered = props.articles;

    // Filter by search term
    if (articleSearch.value) {
        const searchTerm = articleSearch.value.toLowerCase();
        filtered = filtered.filter(
            (article) =>
                article.title.toLowerCase().includes(searchTerm) ||
                stripHtml(article.content).toLowerCase().includes(searchTerm) ||
                article.tags.some((tag) =>
                    tag.name.toLowerCase().includes(searchTerm)
                )
        );
    }

    // Filter by category
    if (selectedCategory.value) {
        filtered = filtered.filter(
            (article) => article.category_id === selectedCategory.value
        );
    }

    return filtered;
});

const filteredCategories = computed(() => {
    if (!categorySearch.value) return props.categories;

    const searchTerm = categorySearch.value.toLowerCase();
    return props.categories.filter((category) =>
        category.name.toLowerCase().includes(searchTerm)
    );
});

// Add this to your script setup
const resetFilters = () => {
    articleSearch.value = "";
    categorySearch.value = "";
    selectedCategory.value = "";
};
</script>

<template>
    <AuthenticatedLayout title="Articles Management">
        <Head>
            <title>Articles Management - Laravel</title>
            <meta
                name="description"
                content="Manage your articles and categories"
            />
        </Head>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Articles Management
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Categories Section -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6"
                >
                    <!-- Collapsible Header -->
                    <div
                        @click="toggleCategories"
                        class="p-4 flex items-center justify-between cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
                    >
                        <div class="flex items-center gap-2">
                            <ChevronDown
                                :class="{
                                    'transform rotate-180':
                                        isCategoriesExpanded,
                                }"
                                class="w-5 h-5 transition-transform"
                            />
                            <h2 class="text-xl font-semibold">Categories</h2>
                        </div>
                        <PrimaryButton
                            @click.stop="showCategoryModal = true"
                            class="ml-4"
                        >
                            Add Category
                        </PrimaryButton>
                    </div>

                    <!-- Collapsible Content -->
                    <div
                        v-show="isCategoriesExpanded"
                        class="p-6 border-t border-gray-200 transition-all duration-300"
                    >
                        <!-- Search and Filter -->
                        <div class="mb-4">
                            <div class="flex gap-2">
                                <div class="flex-1">
                                    <InputLabel
                                        for="categorySearch"
                                        value="Search"
                                    />
                                    <TextInput
                                        id="categorySearch"
                                        v-model="categorySearch"
                                        type="text"
                                        class="mt-1 block w-full"
                                        placeholder="Search categories..."
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            v-for="header in categoryHeaders"
                                            :key="header.value"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            {{ header.text }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="category in filteredCategories"
                                        :key="category.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            {{ category.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <button
                                                    @click="
                                                        editCategory(category)
                                                    "
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </button>
                                                <button
                                                    @click="
                                                        deleteCategory(category)
                                                    "
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredCategories.length === 0">
                                        <td
                                            colspan="2"
                                            class="px-6 py-4 text-center text-gray-500"
                                        >
                                            No categories found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Articles Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold">Articles</h2>
                            <PrimaryButton @click="showArticleModal = true">
                                Add Article
                            </PrimaryButton>
                        </div>

                        <!-- Search and Filter -->
                        <div class="mb-4 flex flex-col sm:flex-row gap-4">
                            <TextInput
                                v-model="articleSearch"
                                type="search"
                                placeholder="Search articles..."
                                class="w-full sm:w-64"
                            />

                            <select
                                v-model="selectedCategory"
                                class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            >
                                <option value="">All Categories</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Articles Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            v-for="header in articleHeaders"
                                            :key="header.value"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            {{ header.text }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="article in filteredArticles"
                                        :key="article.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <!-- Image Column -->
                                        <td class="px-6 py-4">
                                            <div class="flex-shrink-0">
                                                <img
                                                    v-if="
                                                        article.featured_image
                                                    "
                                                    :src="`/storage/${article.featured_image}`"
                                                    :alt="article.title"
                                                    class="w-12 h-12 rounded-lg object-cover"
                                                    @error="handleImageError"
                                                />
                                                <div
                                                    v-else
                                                    class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center"
                                                >
                                                    <span class="text-gray-400"
                                                        >No img</span
                                                    >
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Title Column -->
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ article.title }}
                                            </div>
                                        </td>

                                        <!-- Tags Column -->
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1">
                                                <span
                                                    v-for="tag in article.tags"
                                                    :key="tag.id"
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                >
                                                    {{ tag.name }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Category Column -->
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                {{ article.category.name }}
                                            </div>
                                        </td>

                                        <!-- Content Column -->
                                        <td class="px-6 py-4">
                                            <div
                                                @click="
                                                    showFullContent(
                                                        article.content
                                                    )
                                                "
                                                class="text-sm text-gray-500 truncate max-w-md cursor-pointer hover:text-blue-600"
                                                :title="'Click to view full content'"
                                            >
                                                {{
                                                    stripHtml(
                                                        article.content
                                                    ).substring(0, 100) +
                                                    (stripHtml(article.content)
                                                        .length > 100
                                                        ? "..."
                                                        : "")
                                                }}
                                            </div>
                                        </td>

                                        <!-- Actions Column -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <button
                                                    @click="
                                                        editArticle(article)
                                                    "
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </button>
                                                <button
                                                    @click="
                                                        deleteArticle(article)
                                                    "
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="filteredArticles.length === 0">
                                        <td
                                            :colspan="articleHeaders.length"
                                            class="px-6 py-4 text-center text-gray-500"
                                        >
                                            No articles found
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Modal -->
        <Modal :show="showCategoryModal" @close="showCategoryModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ editingCategory ? "Edit Category" : "Create Category" }}
                </h2>

                <form
                    @submit.prevent="
                        editingCategory ? updateCategory() : createCategory()
                    "
                    class="mt-6"
                >
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="categoryForm.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="categoryForm.errors.name"
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <SecondaryButton @click="showCategoryModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="categoryForm.processing">
                            {{ editingCategory ? "Update" : "Create" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Article Modal -->
        <Modal :show="showArticleModal" @close="showArticleModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ editingArticle ? "Edit Article" : "Create Article" }}
                </h2>

                <form
                    @submit.prevent="
                        editingArticle ? updateArticle() : createArticle()
                    "
                    class="mt-6"
                >
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput
                                id="title"
                                v-model="articleForm.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="articleForm.errors.title"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="category" value="Category" />
                            <select
                                id="category"
                                v-model="articleForm.category_id"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Select a category</option>
                                <option
                                    v-for="category in categories"
                                    :key="category.id"
                                    :value="category.id"
                                >
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError
                                :message="articleForm.errors.category_id"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="content" value="Content" />
                            <QuillEditor
                                id="content"
                                v-model:content="articleForm.content"
                                :options="editorOptions"
                                contentType="html"
                                theme="snow"
                                class="mt-1"
                                style="min-height: 200px"
                            />
                            <InputError
                                :message="articleForm.errors.content"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="featured_image"
                                value="Featured Image"
                            />
                            <input
                                type="file"
                                id="featured_image"
                                @change="handleImageUpload"
                                accept="image/*"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="articleForm.errors.featured_image"
                                class="mt-2"
                            />

                            <!-- Preview current image if editing -->
                            <div
                                v-if="
                                    editingArticle &&
                                    editingArticle.featured_image
                                "
                                class="mt-2"
                            >
                                <img
                                    :src="`/storage/${editingArticle.featured_image}`"
                                    class="max-w-xs rounded-lg shadow-md"
                                />
                            </div>
                        </div>

                        <div>
                            <InputLabel for="tags" value="Tags" />
                            <div class="mt-1">
                                <div
                                    class="flex flex-wrap gap-2 p-2 border rounded-md bg-white mb-2"
                                >
                                    <div
                                        v-for="tag in articleForm.tags"
                                        :key="tag"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800"
                                    >
                                        {{ tag }}
                                        <button
                                            @click="removeTag(tag)"
                                            type="button"
                                            class="ml-1 text-blue-600 hover:text-blue-800"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    v-model="newTag"
                                    @keydown="handleTagInput"
                                    placeholder="Type and press comma or enter to add tags"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                />
                                <div class="mt-1 text-sm text-gray-500">
                                    Press comma or enter to add tags
                                </div>
                                <InputError
                                    :message="articleForm.errors.tags"
                                    class="mt-2"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <SecondaryButton @click="showArticleModal = false">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="articleForm.processing">
                            {{ editingArticle ? "Update" : "Create" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Content Modal -->
        <Modal :show="showContentModal" @close="showContentModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Article Content
                </h2>

                <div class="mt-4">
                    <p v-html="selectedContent" class="text-gray-700"></p>
                </div>

                <div class="mt-6 flex justify-end">
                    <PrimaryButton @click="showContentModal = false">
                        Close
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style>
.prose {
    max-width: 65ch;
    color: #374151;
}
.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}
.prose strong {
    font-weight: 600;
    color: #111827;
}
.prose ul {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
    list-style-type: disc;
}
.prose ol {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
    padding-left: 1.625em;
    list-style-type: decimal;
}
.prose blockquote {
    margin-top: 1.6em;
    margin-bottom: 1.6em;
    padding-left: 1em;
    border-left: 0.25em solid #e5e7eb;
    font-style: italic;
}

/* Add these styles */
.tag-input {
    @apply border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm;
}

.tag-item {
    @apply inline-flex items-center px-2 py-1 rounded-full text-sm bg-blue-100 text-blue-800;
}

.tag-remove {
    @apply ml-1 text-blue-600 hover:text-blue-800;
}
</style>
