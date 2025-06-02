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
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import { router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const props = defineProps({
    images: Array,
    visions: Array,
    missions: Array,
    coreValues: Array,
});

const toast = useToast();

// Vision Management
const showVisionModal = ref(false);
const editingVision = ref(null);
const visionForm = useForm({ vision: "" });

const openVisionModal = (vision = null) => {
    editingVision.value = vision;
    if (vision) {
        visionForm.vision = vision.vision;
    } else {
        visionForm.reset();
    }
    showVisionModal.value = true;
};

const submitVision = () => {
    if (editingVision.value) {
        visionForm.put(
            route("lentera-restorasi.vision.update", editingVision.value.id),
            {
                preserveScroll: true,
                onSuccess: closeVisionModal,
            }
        );
    } else {
        visionForm.post(route("lentera-restorasi.vision.store"), {
            preserveScroll: true,
            onSuccess: closeVisionModal,
        });
    }
};

const closeVisionModal = () => {
    showVisionModal.value = false;
    editingVision.value = null;
    visionForm.reset();
};

const deleteVision = (vision) => {
    Swal.fire({
        title: "Delete Vision?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(
                route("lentera-restorasi.vision.destroy", vision.id),
                {
                    onSuccess: () => {
                        toast.success("Vision deleted successfully");
                    },
                    onError: () => {
                        toast.error("Failed to delete vision");
                    },
                }
            );
        }
    });
};

// Mission Management
const showMissionModal = ref(false);
const editingMission = ref(null);
const missionForm = useForm({ mission: "" });

const openMissionModal = (mission = null) => {
    editingMission.value = mission;
    if (mission) {
        missionForm.mission = mission.mission;
    } else {
        missionForm.reset();
    }
    showMissionModal.value = true;
};

const submitMission = () => {
    if (editingMission.value) {
        missionForm.put(
            route("lentera-restorasi.mission.update", editingMission.value.id),
            {
                preserveScroll: true,
                onSuccess: closeMissionModal,
            }
        );
    } else {
        missionForm.post(route("lentera-restorasi.mission.store"), {
            preserveScroll: true,
            onSuccess: closeMissionModal,
        });
    }
};

const closeMissionModal = () => {
    showMissionModal.value = false;
    editingMission.value = null;
    missionForm.reset();
};

const deleteMission = (mission) => {
    Swal.fire({
        title: "Delete Mission?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(
                route("lentera-restorasi.missions.destroy", mission.id),
                {
                    onSuccess: () => {
                        toast.success("Mission deleted successfully");
                    },
                    onError: () => {
                        toast.error("Failed to delete mission");
                    },
                }
            );
        }
    });
};

// Core Values Management
const showCoreValueModal = ref(false);
const editingCoreValue = ref(null);
const coreValueForm = useForm({
    title: "",
    description: "",
});

const openCoreValueModal = (coreValue = null) => {
    editingCoreValue.value = coreValue;
    if (coreValue) {
        coreValueForm.title = coreValue.title;
        coreValueForm.description = coreValue.description;
    } else {
        coreValueForm.reset();
    }
    showCoreValueModal.value = true;
};

const submitCoreValue = () => {
    if (editingCoreValue.value) {
        coreValueForm.put(
            route(
                "lentera-restorasi.core-value.update",
                editingCoreValue.value.id
            ),
            {
                preserveScroll: true,
                onSuccess: closeCoreValueModal,
            }
        );
    } else {
        coreValueForm.post(route("lentera-restorasi.core-value.store"), {
            preserveScroll: true,
            onSuccess: closeCoreValueModal,
        });
    }
};

const closeCoreValueModal = () => {
    showCoreValueModal.value = false;
    editingCoreValue.value = null;
    coreValueForm.reset();
};

const deleteCoreValue = (coreValue) => {
    Swal.fire({
        title: "Delete Core Value?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(
                route("lentera-restorasi.core-values.destroy", coreValue.id),
                {
                    onSuccess: () => {
                        toast.success("Core Value deleted successfully");
                    },
                    onError: () => {
                        toast.error("Failed to delete core value");
                    },
                }
            );
        }
    });
};

const deleteImage = (image) => {
    Swal.fire({
        title: "Delete Image?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("lentera-restorasi.destroy", image.id), {
                preserveScroll: true,
                onSuccess: () => {
                    // Remove the image from the local array
                    props.images = props.images.filter(
                        (img) => img.id !== image.id
                    );
                    toast.success("Image deleted successfully");
                },
                onError: () => {
                    toast.error("Failed to delete image");
                },
            });
        }
    });
};

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
            [{ align: [] }],
            ["clean"],
            ["link", "image"],
        ],
    },
};

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

const closeModal = () => {
    showModal.value = false;
    editingImage.value = null;
    form.reset();
};

const submit = () => {
    if (editingImage.value) {
        form.post(route("lentera-restorasi.update", editingImage.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success("Image updated successfully");
            },
            onError: () => {
                toast.error("Failed to update image");
            },
        });
    } else {
        form.post(route("lentera-restorasi.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                toast.success("Image added successfully");
            },
            onError: () => {
                toast.error("Failed to add image");
            },
        });
    }
};
</script>

<template>
    <AuthenticatedLayout title="Lentera Restorasi">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Vision Section -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Vision Management</h2>
                        <PrimaryButton @click="openVisionModal()"
                            >Add Vision</PrimaryButton
                        >
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="vision in visions"
                            :key="vision.id"
                            class="border rounded-lg p-4"
                        >
                            <div class="flex justify-between">
                                <div v-html="vision.vision"></div>
                                <div class="flex gap-2">
                                    <button
                                        @click="openVisionModal(vision)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button
                                        @click="deleteVision(vision)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mission Section -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            Mission Management
                        </h2>
                        <PrimaryButton @click="openMissionModal()"
                            >Add Mission</PrimaryButton
                        >
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="mission in missions"
                            :key="mission.id"
                            class="border rounded-lg p-4"
                        >
                            <div class="flex justify-between">
                                <div v-html="mission.mission"></div>
                                <div class="flex gap-2">
                                    <button
                                        @click="openMissionModal(mission)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button
                                        @click="deleteMission(mission)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Core Values Section -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            Core Values Management
                        </h2>
                        <PrimaryButton @click="openCoreValueModal()"
                            >Add Core Value</PrimaryButton
                        >
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="coreValue in coreValues"
                            :key="coreValue.id"
                            class="border rounded-lg p-4"
                        >
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="font-medium">
                                        {{ coreValue.title }}
                                    </h3>
                                    <div
                                        class="mt-2"
                                        v-html="coreValue.description"
                                    ></div>
                                </div>
                                <div class="flex gap-2">
                                    <button
                                        @click="openCoreValueModal(coreValue)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button
                                        @click="deleteCoreValue(coreValue)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Existing Images Section -->
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

                    <!-- Vision Modal -->
                    <Modal :show="showVisionModal" @close="closeVisionModal">
                        <div class="p-6">
                            <h2 class="text-lg font-medium">
                                {{
                                    editingVision
                                        ? "Edit Vision"
                                        : "Add New Vision"
                                }}
                            </h2>
                            <form @submit.prevent="submitVision" class="mt-6">
                                <div>
                                    <InputLabel for="vision" value="Vision" />
                                    <QuillEditor
                                        id="vision"
                                        v-model:content="visionForm.vision"
                                        :options="editorOptions"
                                        contentType="html"
                                        theme="snow"
                                        class="mt-1"
                                        style="min-height: 200px"
                                    />
                                    <InputError
                                        :message="visionForm.errors.vision"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6 flex justify-end gap-4">
                                    <PrimaryButton
                                        :disabled="visionForm.processing"
                                    >
                                        {{ editingVision ? "Update" : "Save" }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </Modal>

                    <!-- Mission Modal -->
                    <Modal :show="showMissionModal" @close="closeMissionModal">
                        <div class="p-6">
                            <h2 class="text-lg font-medium">
                                {{
                                    editingMission
                                        ? "Edit Mission"
                                        : "Add New Mission"
                                }}
                            </h2>
                            <form @submit.prevent="submitMission" class="mt-6">
                                <div>
                                    <InputLabel for="mission" value="Mission" />
                                    <QuillEditor
                                        id="mission"
                                        v-model:content="missionForm.mission"
                                        :options="editorOptions"
                                        contentType="html"
                                        theme="snow"
                                        class="mt-1"
                                        style="min-height: 200px"
                                    />
                                    <InputError
                                        :message="missionForm.errors.mission"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6 flex justify-end gap-4">
                                    <PrimaryButton
                                        :disabled="missionForm.processing"
                                    >
                                        {{ editingMission ? "Update" : "Save" }}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </Modal>

                    <!-- Core Values Modal -->
                    <Modal
                        :show="showCoreValueModal"
                        @close="closeCoreValueModal"
                    >
                        <div class="p-6">
                            <h2 class="text-lg font-medium">
                                {{
                                    editingCoreValue
                                        ? "Edit Core Value"
                                        : "Add New Core Value"
                                }}
                            </h2>
                            <form
                                @submit.prevent="submitCoreValue"
                                class="mt-6"
                            >
                                <div>
                                    <InputLabel
                                        for="core_value_title"
                                        value="Title"
                                    />
                                    <TextInput
                                        id="core_value_title"
                                        v-model="coreValueForm.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="coreValueForm.errors.title"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6">
                                    <InputLabel
                                        for="core_value_description"
                                        value="Description"
                                    />
                                    <QuillEditor
                                        id="core_value_description"
                                        v-model:content="
                                            coreValueForm.description
                                        "
                                        :options="editorOptions"
                                        contentType="html"
                                        theme="snow"
                                        class="mt-1"
                                        style="min-height: 200px"
                                    />
                                    <InputError
                                        :message="
                                            coreValueForm.errors.description
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6 flex justify-end gap-4">
                                    <PrimaryButton
                                        :disabled="coreValueForm.processing"
                                    >
                                        {{
                                            editingCoreValue ? "Update" : "Save"
                                        }}
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
