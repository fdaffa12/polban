<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";

const props = defineProps({
    aboutUs: Object,
});

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
    modules: {
        toolbar: [
            ["bold", "italic", "underline", "strike"],
            ["blockquote", "code-block"],
            [{ header: 1 }, { header: 2 }],
            [{ list: "ordered" }, { list: "bullet" }],
            [{ script: "sub" }, { script: "super" }],
            [{ indent: "-1" }, { indent: "+1" }],
            [{ size: ["small", false, "large", "huge"] }],
            [{ header: [1, 2, 3, 4, 5, 6, false] }],
            [{ color: [] }, { background: [] }],
            ["clean"],
        ],
    },
};

const submit = () => {
    form.post(route("about-us.update"), {
        preserveScroll: true,
        onSuccess: () => {
            // Show success message
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="About Us Management">
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
                                    contentType="html"
                                    theme="snow"
                                    class="mt-1"
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
                                    <img
                                        v-for="image in props.aboutUs
                                            .au_multiple_image"
                                        :key="image"
                                        :src="`/storage/${image}`"
                                        class="rounded-lg shadow-md"
                                    />
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
