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
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    aboutUs: Object,
});

const toast = useToast();

const form = useForm({
    au_title: props.aboutUs?.au_title ?? "",
    au_desc: props.aboutUs?.au_desc ?? "",
    au_image: null,
    au_values: props.aboutUs?.au_values ?? "",
    history: props.aboutUs?.history ?? "",
});

const imageForm = useForm({
    image: null,
    title: "",
});

const editImageForm = useForm({
    image: null,
    title: "",
    index: null,
});

const showEditImageModal = ref(false);

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

const addImage = () => {
    if (!imageForm.image) {
        toast.error("Please select an image to upload");
        return;
    }
    if (!imageForm.title.trim()) {
        toast.error("Please enter image title");
        return;
    }

    imageForm.post(route("about-us.add-image"), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Tambahkan gambar baru ke array yang ada
            const newImage = {
                path: response?.props?.flash?.images[
                    response?.props?.flash?.images.length - 1
                ].path,
                title: imageForm.title,
            };
            props.aboutUs.au_multiple_image = [
                ...props.aboutUs.au_multiple_image,
                newImage,
            ];

            imageForm.reset();
            document.getElementById("new_image").value = "";
            toast.success("Berhasil menambahkan gambar");
        },
        onError: (errors) => {
            toast.error("Gagal menambahkan gambar");
        },
    });
};

const openEditImageModal = (image, index) => {
    editImageForm.reset();
    editImageForm.title = image.title;
    editImageForm.index = index;
    showEditImageModal.value = true;
};

const updateImage = () => {
    if (!editImageForm.title.trim()) {
        toast.error("Please enter image title");
        return;
    }

    editImageForm.post(route("about-us.update-image", editImageForm.index), {
        preserveScroll: true,
        onSuccess: (response) => {
            // Update gambar yang ada dengan data baru
            const currentImages = [...props.aboutUs.au_multiple_image];
            currentImages[editImageForm.index] = {
                ...currentImages[editImageForm.index],
                title: editImageForm.title,
            };

            // Jika ada file gambar baru yang diupload
            if (
                response?.props?.flash?.images &&
                response?.props?.flash?.images[editImageForm.index]
            ) {
                currentImages[editImageForm.index].path =
                    response.props.flash.images[editImageForm.index].path;
            }

            props.aboutUs.au_multiple_image = currentImages;
            showEditImageModal.value = false;
            editImageForm.reset();
            document.getElementById("edit_image").value = "";
            toast.success("Berhasil mengubah gambar");
        },
        onError: (errors) => {
            toast.error("Gagal mengubah gambar");
        },
    });
};

const removeImage = (index) => {
    if (!confirm("Apakah Anda yakin ingin menghapus gambar ini?")) {
        return;
    }

    router.post(
        route("about-us.remove-image"),
        { index },
        {
            preserveScroll: true,
            onSuccess: (response) => {
                const currentImages = [...props.aboutUs.au_multiple_image];
                currentImages.splice(index, 1);
                props.aboutUs.au_multiple_image = currentImages;
                toast.success("Berhasil menghapus gambar");
            },
            onError: (error) => {
                toast.error("Gagal menghapus gambar");
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

                                <!-- Single Image Upload Form -->
                                <form
                                    @submit.prevent="addImage"
                                    class="mb-4 p-4 border rounded-lg"
                                >
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    >
                                        <div>
                                            <InputLabel
                                                for="image_title"
                                                value="Image Title"
                                            />
                                            <TextInput
                                                id="image_title"
                                                v-model="imageForm.title"
                                                type="text"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <InputError
                                                :message="
                                                    imageForm.errors.title
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel
                                                for="new_image"
                                                value="Upload Image"
                                            />
                                            <input
                                                type="file"
                                                id="new_image"
                                                @change="
                                                    imageForm.image =
                                                        $event.target.files[0]
                                                "
                                                accept="image/*"
                                                class="mt-1 block w-full"
                                                required
                                            />
                                            <InputError
                                                :message="
                                                    imageForm.errors.image
                                                "
                                                class="mt-2"
                                            />
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <PrimaryButton
                                            :disabled="imageForm.processing"
                                        >
                                            Add Image
                                        </PrimaryButton>
                                    </div>
                                </form>

                                <!-- Gallery Preview -->
                                <div
                                    v-if="props.aboutUs?.au_multiple_image"
                                    class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                                >
                                    <div
                                        v-for="(image, index) in props.aboutUs
                                            .au_multiple_image"
                                        :key="index"
                                        class="relative group"
                                    >
                                        <img
                                            :src="`/storage/${image.path}`"
                                            class="rounded-lg shadow-md w-full h-48 object-cover"
                                        />
                                        <div
                                            class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 rounded-b-lg"
                                        >
                                            {{ image.title }}
                                        </div>
                                        <div
                                            class="absolute -top-2 -right-2 flex space-x-2"
                                        >
                                            <button
                                                @click.prevent="
                                                    openEditImageModal(
                                                        image,
                                                        index
                                                    )
                                                "
                                                class="bg-white rounded-full shadow-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:text-blue-500"
                                                title="Edit image"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                                    />
                                                </svg>
                                            </button>
                                            <button
                                                @click.prevent="
                                                    removeImage(index)
                                                "
                                                class="bg-white rounded-full shadow-lg p-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 hover:text-red-500"
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

        <!-- Edit Image Modal -->
        <Modal :show="showEditImageModal" @close="showEditImageModal = false">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-medium text-gray-900">
                        Edit Gallery Image
                    </h2>
                    <button
                        @click="showEditImageModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="updateImage" class="space-y-4">
                    <div>
                        <InputLabel
                            for="edit_image_title"
                            value="Image Title"
                        />
                        <TextInput
                            id="edit_image_title"
                            v-model="editImageForm.title"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="editImageForm.errors.title"
                            class="mt-2"
                        />
                    </div>

                    <div>
                        <InputLabel
                            for="edit_image"
                            value="Change Image (Optional)"
                        />
                        <input
                            type="file"
                            id="edit_image"
                            @change="
                                editImageForm.image = $event.target.files[0]
                            "
                            accept="image/*"
                            class="mt-1 block w-full"
                        />
                        <p class="text-sm text-gray-500 mt-1">
                            Leave empty if you don't want to change the image
                        </p>
                        <InputError
                            :message="editImageForm.errors.image"
                            class="mt-2"
                        />
                    </div>

                    <div class="flex justify-end space-x-2">
                        <SecondaryButton
                            type="button"
                            @click="showEditImageModal = false"
                        >
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="editImageForm.processing"
                        >
                            Update Image
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
