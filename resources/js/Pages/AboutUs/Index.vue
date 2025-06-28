<script setup>
import { ref } from "vue";
import { useForm, Head, router } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { QuillEditor } from "@vueup/vue-quill";
import { useToast } from "vue-toastification";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

const props = defineProps({
    aboutUs: Object,
});

const toast = useToast();

const form = useForm({
    au_title: props.aboutUs?.au_title ?? "",
    au_desc: props.aboutUs?.au_desc ?? "",
    au_image: null,
    au_multiple_image: [],
    au_values: props.aboutUs?.au_values ?? "",
    history: props.aboutUs?.history ?? "",
});

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

const submit = () => {
    form.post(route("about-us.update"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("About Us berhasil diperbarui");
        },
        onError: () => {
            toast.error("Gagal memperbarui About Us");
        },
    });
};

const removeImage = (index) => {
    router.post(
        route("about-us.remove-image"),
        { index },
        {
            preserveScroll: true,
            onSuccess: (response) => {
                if (response?.props?.flash?.success) {
                    props.aboutUs.au_multiple_image =
                        response?.props?.flash?.images || [];
                    toast.success("Image removed successfully");
                }
            },
            onError: () => {
                toast.error("Failed to remove image");
            },
        }
    );
};
</script>

<template>
    <AuthenticatedLayout title="About Us Management">
        <Head>
            <title>AboutUs Management</title>
            <meta name="description" content="Manage your history" />
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Main Content Section -->
                        <div class="space-y-4">
                            <div>
                                <InputLabel for="au_title" value="Title" />
                                <TextInput
                                    id="au_title"
                                    v-model="form.au_title"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError
                                    :message="form.errors.au_title"
                                    class="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel for="au_desc" value="Description" />
                                <QuillEditor
                                    id="au_desc"
                                    v-model:content="form.au_desc"
                                    :options="editorOptions"
                                    contentType="html"
                                    theme="snow"
                                    class="mt-1"
                                    style="min-height: 200px"
                                />
                                <InputError
                                    :message="form.errors.au_desc"
                                    class="mt-2"
                                />
                            </div>

                            <div>
                                <InputLabel for="au_image" value="Main Image" />
                                <input
                                    type="file"
                                    id="au_image"
                                    @change="
                                        form.au_image = $event.target.files[0]
                                    "
                                    accept="image/*"
                                    class="mt-1 block w-full"
                                />
                                <InputError
                                    :message="form.errors.au_image"
                                    class="mt-2"
                                />

                                <!-- Image Preview -->
                                <div
                                    v-if="props.aboutUs?.au_image"
                                    class="mt-2"
                                >
                                    <img
                                        :src="`/storage/${props.aboutUs.au_image}`"
                                        class="max-w-xs rounded-lg shadow-md"
                                    />
                                </div>
                            </div>

                            <div>
                                <InputLabel
                                    for="au_multiple_image"
                                    value="Gallery Images"
                                />
                                <input
                                    type="file"
                                    id="au_multiple_image"
                                    @change="
                                        form.au_multiple_image = Array.from(
                                            $event.target.files
                                        )
                                    "
                                    accept="image/*"
                                    multiple
                                    class="mt-1 block w-full"
                                />
                                <InputError
                                    :message="form.errors.au_multiple_image"
                                    class="mt-2"
                                />

                                <!-- Gallery Preview -->
                                <div
                                    v-if="props.aboutUs?.au_multiple_image"
                                    class="mt-2 grid grid-cols-4 gap-4"
                                >
                                    <div
                                        v-for="(image, index) in props.aboutUs
                                            .au_multiple_image"
                                        :key="image"
                                        class="relative group"
                                    >
                                        <img
                                            :src="`/storage/${image}`"
                                            class="rounded-lg shadow-md w-full h-48 object-cover"
                                        />
                                        <button
                                            @click.prevent="removeImage(index)"
                                            class="absolute -top-2 -right-2 bg-white rounded-full shadow-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:text-red-500"
                                            title="Remove image"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Simplified Company Values Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium">Company Values</h3>
                            <div>
                                <QuillEditor
                                    v-model:content="form.au_values"
                                    :options="editorOptions"
                                    contentType="html"
                                    theme="snow"
                                    class="mt-1"
                                    style="min-height: 200px"
                                />
                                <InputError
                                    :message="form.errors.au_values"
                                    class="mt-2"
                                />
                            </div>
                        </div>

                        <!-- Simplified History Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium">Company History</h3>
                            <div>
                                <QuillEditor
                                    v-model:content="form.history"
                                    :options="editorOptions"
                                    contentType="html"
                                    theme="snow"
                                    class="mt-1"
                                    style="min-height: 200px"
                                />
                                <InputError
                                    :message="form.errors.history"
                                    class="mt-2"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <PrimaryButton :disabled="form.processing">
                                Save Changes
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
