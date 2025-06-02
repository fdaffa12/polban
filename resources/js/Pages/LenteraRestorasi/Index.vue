<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Edit, Trash2 } from "lucide-vue-next";

const props = defineProps({
    images: Array,
});

const showModal = ref(false);
const editingImage = ref(null);

const form = useForm({
    title: "",
    image: null,
});

const openModal = (image = null) => {
    editingImage.value = image;
    if (image) {
        form.title = image.title;
    } else {
        form.reset();
    }
    showModal.value = true;
};

const submit = () => {
    if (editingImage.value) {
        form.post(route("lentera-restorasi.update", editingImage.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("lentera-restorasi.store"), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const closeModal = () => {
    showModal.value = false;
    editingImage.value = null;
    form.reset();
};

const deleteImage = (image) => {
    if (confirm("Are you sure you want to delete this image?")) {
        router.delete(route("lentera-restorasi.destroy", image.id));
    }
};
</script>

<template>
    <AuthenticatedLayout title="Lentera Restorasi Images">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            Lentera Restorasi Images
                        </h2>
                        <PrimaryButton @click="openModal()"
                            >Add Image</PrimaryButton
                        >
                    </div>

                    <!-- Images Grid -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="image in images"
                            :key="image.id"
                            class="relative group"
                        >
                            <img
                                :src="`/storage/${image.image}`"
                                :alt="image.title"
                                class="w-full h-48 object-cover rounded-lg shadow-md"
                            />
                            <div
                                class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-lg flex items-center justify-center"
                            >
                                <div class="text-white text-center p-4">
                                    <h3 class="font-medium">
                                        {{ image.title }}
                                    </h3>
                                    <div class="flex gap-2 mt-2 justify-center">
                                        <button
                                            @click="openModal(image)"
                                            class="p-2 hover:text-blue-400"
                                        >
                                            <Edit class="w-5 h-5" />
                                        </button>
                                        <button
                                            @click="deleteImage(image)"
                                            class="p-2 hover:text-red-400"
                                        >
                                            <Trash2 class="w-5 h-5" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <Modal :show="showModal" @close="closeModal">
                        <div class="p-6">
                            <h2 class="text-lg font-medium">
                                {{
                                    editingImage
                                        ? "Edit Image"
                                        : "Add New Image"
                                }}
                            </h2>
                            <form @submit.prevent="submit" class="mt-6">
                                <div>
                                    <InputLabel for="title" value="Title" />
                                    <TextInput
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.title"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6">
                                    <InputLabel for="image" value="Image" />
                                    <input
                                        type="file"
                                        id="image"
                                        @input="
                                            form.image = $event.target.files[0]
                                        "
                                        accept="image/*"
                                        class="mt-1 block w-full"
                                        :required="!editingImage"
                                    />
                                    <InputError
                                        :message="form.errors.image"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6 flex justify-end gap-4">
                                    <PrimaryButton :disabled="form.processing">
                                        {{ editingImage ? "Update" : "Save" }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </Modal>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
