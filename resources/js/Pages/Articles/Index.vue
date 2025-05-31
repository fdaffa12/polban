<script setup>
import { ref } from "vue";
import { useForm, Head, router } from "@inertiajs/vue3"; // Add router import
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

const props = defineProps({
    articles: Array,
    categories: Array,
});

const showCategoryModal = ref(false);
const showArticleModal = ref(false);
const editingArticle = ref(null);
const editingCategory = ref(null);
const isCategoriesExpanded = ref(true);
const toast = useToast();

const articleForm = useForm({
    title: "",
    content: "",
    category_id: "",
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
    { text: "Title", value: "title" },
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
                                        v-for="category in categories"
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
                                        v-for="article in articles"
                                        :key="article.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ article.title }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500">
                                                {{ article.category.name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div
                                                class="text-sm text-gray-500 truncate max-w-md"
                                            >
                                                {{ article.content }}
                                            </div>
                                        </td>
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
                            <textarea
                                id="content"
                                v-model="articleForm.content"
                                rows="4"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            ></textarea>
                            <InputError
                                :message="articleForm.errors.content"
                                class="mt-2"
                            />
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
    </AuthenticatedLayout>
</template>
