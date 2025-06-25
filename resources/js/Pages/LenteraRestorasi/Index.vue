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
    himpunan: Object,
});

const toast = useToast();

// Vision Management
const showVisionModal = ref(false);
const editingVision = ref(null);
const visionForm = useForm({
    type: "caption",
    vision: "",
    image: null,
});

const openVisionModal = (vision = null) => {
    editingVision.value = vision;
    if (vision) {
        visionForm.type = vision.type;
        visionForm.vision = vision.vision;
        visionForm.image = null;
    } else {
        visionForm.reset();
        visionForm.type = "caption";
    }
    showVisionModal.value = true;
};

const submitVision = () => {
    if (visionForm.type === "image") {
        let formData = new FormData();
        formData.append("type", visionForm.type);
        formData.append("image", visionForm.image);

        visionForm._method = "POST";
        visionForm.transform((data) => {
            let formData = new FormData();
            formData.append("type", data.type);
            if (data.image) {
                formData.append("image", data.image);
            }
            if (data.vision) {
                formData.append("vision", data.vision);
            }
            return formData;
        });
    }

    if (editingVision.value) {
        visionForm.post(
            route("lentera-restorasi.vision.update", editingVision.value.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeVisionModal();
                    toast.success("Vision updated successfully");
                },
                onError: () => {
                    toast.error("Failed to update vision");
                },
            }
        );
    } else {
        visionForm.post(route("lentera-restorasi.vision.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeVisionModal();
                toast.success("Vision added successfully");
            },
            onError: () => {
                toast.error("Failed to add vision");
            },
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
                    preserveScroll: true,
                    onSuccess: () => {
                        props.visions = props.visions.filter(
                            (v) => v.id !== vision.id
                        );
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
const missionForm = useForm({
    type: "caption",
    mission: "",
    image: null,
});

const openMissionModal = (mission = null) => {
    editingMission.value = mission;
    if (mission) {
        missionForm.type = mission.type;
        missionForm.mission = mission.mission;
        missionForm.image = null;
    } else {
        missionForm.reset();
        missionForm.type = "caption";
    }
    showMissionModal.value = true;
};

const submitMission = () => {
    if (missionForm.type === "image") {
        let formData = new FormData();
        formData.append("type", missionForm.type);
        formData.append("image", missionForm.image);

        missionForm._method = "POST";
        missionForm.transform((data) => {
            let formData = new FormData();
            formData.append("type", data.type);
            if (data.image) {
                formData.append("image", data.image);
            }
            if (data.mission) {
                formData.append("mission", data.mission);
            }
            return formData;
        });
    }

    if (editingMission.value) {
        missionForm.post(
            route("lentera-restorasi.mission.update", editingMission.value.id),
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeMissionModal();
                    toast.success("Mission updated successfully");
                },
                onError: () => {
                    toast.error("Failed to update mission");
                },
            }
        );
    } else {
        missionForm.post(route("lentera-restorasi.mission.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeMissionModal();
                toast.success("Mission added successfully");
            },
            onError: () => {
                toast.error("Failed to add mission");
            },
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
                route("lentera-restorasi.mission.destroy", mission.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        props.missions = props.missions.filter(
                            (m) => m.id !== mission.id
                        );
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
        coreValueForm.post(
            route(
                "lentera-restorasi.core-value.update",
                editingCoreValue.value.id
            ),
            {
                preserveScroll: true,
                onSuccess: () => {
                    closeCoreValueModal();
                    toast.success("Core Value updated successfully");
                },
                onError: () => toast.error("Failed to update core value"),
            }
        );
    } else {
        coreValueForm.post(route("lentera-restorasi.core-value.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeCoreValueModal();
                toast.success("Core Value added successfully");
            },
            onError: () => toast.error("Failed to add core value"),
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
                route("lentera-restorasi.core-value.destroy", coreValue.id),
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        props.coreValues = props.coreValues.filter(
                            (cv) => cv.id !== coreValue.id
                        );
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

// Tambahkan state dan method untuk himpunan
const showHimpunanModal = ref(false);
const himpunanForm = useForm({
    name: props.himpunan?.name || "",
    description: props.himpunan?.description || "",
    logo: null,
    yt_link: props.himpunan?.yt_link || "",
});

const openHimpunanModal = () => {
    himpunanForm.name = props.himpunan?.name || "";
    himpunanForm.description = props.himpunan?.description || "";
    himpunanForm.logo = null;
    himpunanForm.yt_link = props.himpunan?.yt_link || "";
    showHimpunanModal.value = true;
};

const closeHimpunanModal = () => {
    showHimpunanModal.value = false;
    himpunanForm.reset();
};

const submitHimpunan = () => {
    himpunanForm.post(route("lentera-restorasi.himpunan.update"), {
        preserveScroll: true,
        onSuccess: () => {
            closeHimpunanModal();
            toast.success("Himpunan updated successfully");
        },
        onError: () => toast.error("Failed to update himpunan"),
    });
};
</script>

<template>
    <AuthenticatedLayout title="Lentera Restorasi">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Himpunan Section -->
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">
                            Himpunan Management
                        </h2>
                        <PrimaryButton @click="openHimpunanModal()">
                            {{ himpunan ? "Edit Himpunan" : "Add Himpunan" }}
                        </PrimaryButton>
                    </div>

                    <div v-if="himpunan" class="border rounded-lg p-4">
                        <div class="flex items-start gap-4">
                            <img
                                v-if="himpunan.logo"
                                :src="`/storage/${himpunan.logo}`"
                                :alt="himpunan.name"
                                class="w-32 h-32 object-cover rounded-lg"
                            />
                            <div class="flex-grow">
                                <h3 class="text-lg font-medium">
                                    {{ himpunan.name }}
                                </h3>
                                <div
                                    class="mt-2"
                                    v-html="himpunan.description"
                                ></div>
                                <div v-if="himpunan.yt_link" class="mt-4">
                                    <a
                                        :href="himpunan.yt_link"
                                        target="_blank"
                                        class="inline-flex items-center text-red-600 hover:text-red-700"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2"
                                            fill="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"
                                            />
                                        </svg>
                                        Watch on YouTube
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500">
                        Belum ada data himpunan
                    </div>
                </div>

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
                                <!-- Tampilkan konten berdasarkan tipe -->
                                <div
                                    v-if="vision.type === 'caption'"
                                    v-html="vision.vision"
                                ></div>
                                <div
                                    v-else-if="vision.type === 'image'"
                                    class="w-full"
                                >
                                    <img
                                        :src="`/storage/${vision.image}`"
                                        :alt="vision.vision || 'Vision Image'"
                                        class="w-full h-48 object-cover rounded-lg"
                                    />
                                </div>
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
                                <!-- Tampilkan konten berdasarkan tipe -->
                                <div
                                    v-if="mission.type === 'caption'"
                                    v-html="mission.mission"
                                ></div>
                                <div
                                    v-else-if="mission.type === 'image'"
                                    class="w-full"
                                >
                                    <img
                                        :src="`/storage/${mission.image}`"
                                        :alt="
                                            mission.mission || 'Mission Image'
                                        "
                                        class="w-full h-48 object-cover rounded-lg"
                                    />
                                </div>
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
                            <form
                                @submit.prevent="submitVision"
                                class="mt-6"
                                enctype="multipart/form-data"
                            >
                                <!-- Tipe Vision -->
                                <div class="mb-4">
                                    <InputLabel for="type" value="Type" />
                                    <select
                                        v-model="visionForm.type"
                                        id="type"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="caption">Caption</option>
                                        <option value="image">Image</option>
                                    </select>
                                    <InputError
                                        :message="visionForm.errors.type"
                                        class="mt-2"
                                    />
                                </div>

                                <!-- Caption Input -->
                                <div v-if="visionForm.type === 'caption'">
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

                                <!-- Image Input -->
                                <div
                                    v-if="visionForm.type === 'image'"
                                    class="mt-4"
                                >
                                    <InputLabel
                                        for="vision_image"
                                        value="Vision Image"
                                    />
                                    <input
                                        type="file"
                                        id="vision_image"
                                        @input="
                                            visionForm.image =
                                                $event.target.files[0]
                                        "
                                        accept="image/*"
                                        class="mt-1 block w-full"
                                        :required="
                                            !editingVision ||
                                            visionForm.type === 'image'
                                        "
                                    />
                                    <InputError
                                        :message="visionForm.errors.image"
                                        class="mt-2"
                                    />

                                    <!-- Preview Image jika ada -->
                                    <div v-if="visionForm.image" class="mt-2">
                                        <img
                                            :src="
                                                URL.createObjectURL(
                                                    visionForm.image
                                                )
                                            "
                                            class="w-full h-48 object-cover rounded-lg"
                                            alt="Preview"
                                        />
                                    </div>
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
                            <form
                                @submit.prevent="submitMission"
                                class="mt-6"
                                enctype="multipart/form-data"
                            >
                                <!-- Tipe Mission -->
                                <div class="mb-4">
                                    <InputLabel for="type" value="Type" />
                                    <select
                                        v-model="missionForm.type"
                                        id="type"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    >
                                        <option value="caption">Caption</option>
                                        <option value="image">Image</option>
                                    </select>
                                </div>

                                <!-- Caption Input -->
                                <div v-if="missionForm.type === 'caption'">
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

                                <!-- Image Input -->
                                <div
                                    v-if="missionForm.type === 'image'"
                                    class="mt-4"
                                >
                                    <InputLabel
                                        for="mission_image"
                                        value="Mission Image"
                                    />
                                    <input
                                        type="file"
                                        id="mission_image"
                                        @input="
                                            missionForm.image =
                                                $event.target.files[0]
                                        "
                                        accept="image/*"
                                        class="mt-1 block w-full"
                                        :required="
                                            !editingMission ||
                                            missionForm.type === 'image'
                                        "
                                    />
                                    <InputError
                                        :message="missionForm.errors.image"
                                        class="mt-2"
                                    />

                                    <!-- Preview gambar yang sudah ada -->
                                    <div
                                        v-if="
                                            editingMission &&
                                            editingMission.image
                                        "
                                        class="mt-2"
                                    >
                                        <p class="text-sm text-gray-600 mb-2">
                                            Current Image:
                                        </p>
                                        <img
                                            :src="`/storage/${editingMission.image}`"
                                            class="w-full h-48 object-cover rounded-lg"
                                            alt="Current Mission Image"
                                        />
                                    </div>

                                    <!-- Preview gambar baru -->
                                    <div v-if="missionForm.image" class="mt-2">
                                        <p class="text-sm text-gray-600 mb-2">
                                            New Image Preview:
                                        </p>
                                        <img
                                            :src="
                                                URL.createObjectURL(
                                                    missionForm.image
                                                )
                                            "
                                            class="w-full h-48 object-cover rounded-lg"
                                            alt="New Mission Image Preview"
                                        />
                                    </div>
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

                    <!-- Himpunan Modal -->
                    <Modal
                        :show="showHimpunanModal"
                        @close="closeHimpunanModal"
                    >
                        <div class="p-6">
                            <h2 class="text-lg font-medium mb-4">
                                {{
                                    himpunan ? "Edit Himpunan" : "Add Himpunan"
                                }}
                            </h2>
                            <form
                                @submit.prevent="submitHimpunan"
                                class="space-y-4"
                            >
                                <div>
                                    <InputLabel for="name" value="Name" />
                                    <TextInput
                                        id="name"
                                        v-model="himpunanForm.name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="himpunanForm.errors.name"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="description"
                                        value="Description"
                                    />
                                    <textarea
                                        id="description"
                                        v-model="himpunanForm.description"
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                        rows="4"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="
                                            himpunanForm.errors.description
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="yt_link"
                                        value="YouTube Link"
                                    />
                                    <TextInput
                                        id="yt_link"
                                        v-model="himpunanForm.yt_link"
                                        type="url"
                                        class="mt-1 block w-full"
                                        placeholder="https://www.youtube.com/watch?v=..."
                                    />
                                    <InputError
                                        :message="himpunanForm.errors.yt_link"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel for="logo" value="Logo" />
                                    <input
                                        type="file"
                                        id="logo"
                                        @input="
                                            himpunanForm.logo =
                                                $event.target.files[0]
                                        "
                                        accept="image/*"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                    />
                                    <InputError
                                        :message="himpunanForm.errors.logo"
                                        class="mt-2"
                                    />
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <PrimaryButton
                                        :disabled="himpunanForm.processing"
                                    >
                                        {{ himpunan ? "Update" : "Save" }}
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
