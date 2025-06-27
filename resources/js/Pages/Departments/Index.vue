<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Edit, Trash2 } from "lucide-vue-next";
import { router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const props = defineProps({
    departments: Array,
    deptLogos: Array,
});

const toast = useToast();
const searchQuery = ref("");

// Department Management
const showDepartmentModal = ref(false);
const editingDepartment = ref(null);
const departmentForm = useForm({
    dept_name: "",
    desc: "",
    image: null,
});

const openDepartmentModal = (department = null) => {
    editingDepartment.value = department;
    if (department) {
        departmentForm.dept_name = department.dept_name;
        departmentForm.desc = department.desc;
        departmentForm.image = null;
    } else {
        departmentForm.reset();
    }
    showDepartmentModal.value = true;
};

const submitDepartment = () => {
    if (editingDepartment.value) {
        departmentForm.post(
            route("departments.update", editingDepartment.value.id),
            {
                preserveScroll: true,
                forceFormData: true,
                onSuccess: () => {
                    closeDepartmentModal();
                    toast.success("Department updated successfully");
                },
                onError: () => toast.error("Failed to update department"),
            }
        );
    } else {
        departmentForm.post(route("departments.store"), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeDepartmentModal();
                toast.success("Department added successfully");
            },
            onError: () => toast.error("Failed to add department"),
        });
    }
};

const closeDepartmentModal = () => {
    showDepartmentModal.value = false;
    editingDepartment.value = null;
    departmentForm.reset();
};

const deleteDepartment = (department) => {
    Swal.fire({
        title: "Delete Department?",
        text: "This will also delete all members in this department!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("departments.destroy", department.id), {
                preserveScroll: true,
                onSuccess: () => {
                    props.departments = props.departments.filter(
                        (d) => d.id !== department.id
                    );
                    toast.success("Department deleted successfully");
                },
                onError: () => toast.error("Failed to delete department"),
            });
        }
    });
};

// Member Management
const showMemberModal = ref(false);
const editingMember = ref(null);
const memberForm = useForm({
    department_id: "",
    name: "",
    image: null,
    job_type: "",
    job_titles: "",
    position: "",
});

const openMemberModal = (member = null, department = null) => {
    editingMember.value = member;
    if (member) {
        memberForm.department_id = member.department_id;
        memberForm.name = member.name;
        memberForm.job_type = member.job_type;
        memberForm.job_titles = member.job_titles;
        memberForm.position = member.position;
    } else if (department) {
        // Set department_id when adding new member
        memberForm.department_id = department.id;
        memberForm.reset("name", "image", "job_type", "job_titles", "position");
    } else {
        memberForm.reset();
    }
    showMemberModal.value = true;
};

const submitMember = () => {
    if (editingMember.value) {
        memberForm.post(route("members.update", editingMember.value.id), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeMemberModal();
                toast.success("Member updated successfully");
            },
            onError: (errors) => {
                console.log(errors);
                toast.error("Failed to update member");
            },
        });
    } else {
        memberForm.post(route("members.store"), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeMemberModal();
                toast.success("Member added successfully");
            },
            onError: (errors) => {
                console.log(errors);
                toast.error("Failed to add member");
            },
        });
    }
};

const closeMemberModal = () => {
    showMemberModal.value = false;
    editingMember.value = null;
    memberForm.reset();
};

const deleteMember = (member) => {
    Swal.fire({
        title: "Delete Member?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("members.destroy", member.id), {
                preserveScroll: true,
                onSuccess: () => {
                    const department = props.departments.find(
                        (d) => d.id === member.department_id
                    );
                    if (department) {
                        department.members = department.members.filter(
                            (m) => m.id !== member.id
                        );
                    }
                    toast.success("Member deleted successfully");
                },
                onError: () => toast.error("Failed to delete member"),
            });
        }
    });
};

const filteredDepartments = computed(() => {
    if (!searchQuery.value) return props.departments;

    return props.departments
        .map((department) => ({
            ...department,
            members: department.members.filter((member) =>
                member.name
                    .toLowerCase()
                    .includes(searchQuery.value.toLowerCase())
            ),
        }))
        .filter((department) => department.members.length > 0);
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = 3;

const paginatedDepartments = computed(() => {
    const filteredDepts = filteredDepartments.value;
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredDepts.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredDepartments.value.length / itemsPerPage);
});

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Department Logo Management
const showDeptLogoModal = ref(false);
const editingDeptLogo = ref(null);
const deptLogoForm = useForm({
    title: "",
    image: null,
});

const openDeptLogoModal = (logo = null) => {
    editingDeptLogo.value = logo;
    if (logo) {
        deptLogoForm.title = logo.title;
        deptLogoForm.image = null;
    } else {
        deptLogoForm.reset();
    }
    showDeptLogoModal.value = true;
};

const closeDeptLogoModal = () => {
    showDeptLogoModal.value = false;
    editingDeptLogo.value = null;
    deptLogoForm.reset();
};

const submitDeptLogo = () => {
    if (editingDeptLogo.value) {
        deptLogoForm.post(
            route("dept-logos.update", editingDeptLogo.value.id),
            {
                preserveScroll: true,
                forceFormData: true,
                onSuccess: () => {
                    closeDeptLogoModal();
                    toast.success("Department logo updated successfully");
                },
                onError: () => toast.error("Failed to update department logo"),
            }
        );
    } else {
        deptLogoForm.post(route("dept-logos.store"), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeDeptLogoModal();
                toast.success("Department logo added successfully");
            },
            onError: () => toast.error("Failed to add department logo"),
        });
    }
};

const deleteDeptLogo = (logo) => {
    Swal.fire({
        title: "Delete Department Logo?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("dept-logos.destroy", logo.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("Department logo deleted successfully");
                },
                onError: () => toast.error("Failed to delete department logo"),
            });
        }
    });
};

// Update searchQuery watcher
watch(searchQuery, () => {
    currentPage.value = 1; // Reset ke halaman pertama ketika melakukan pencarian
});
</script>

<template>
    <AuthenticatedLayout title="Department Management">
        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6"
                >
                    <!-- Tambahkan section baru setelah Departments section -->
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 sm:p-6 mt-6"
                    >
                        <!-- Header Section -->
                        <div
                            class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6"
                        >
                            <div>
                                <h2 class="text-xl font-semibold">
                                    Department Logos
                                </h2>
                                <p class="text-sm text-gray-500">
                                    These logos will be displayed on the front
                                    page of the website
                                </p>
                            </div>
                            <PrimaryButton @click="openDeptLogoModal()">
                                Add Department Logo
                            </PrimaryButton>
                        </div>

                        <!-- Department Logos Grid -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                        >
                            <div
                                v-for="logo in deptLogos"
                                :key="logo.id"
                                class="border rounded-lg p-4 bg-gray-50"
                            >
                                <div class="aspect-w-4 aspect-h-3 mb-3">
                                    <img
                                        :src="`/storage/${logo.image}`"
                                        :alt="logo.title"
                                        class="w-full h-full object-cover rounded-md"
                                    />
                                </div>
                                <h4 class="font-medium text-gray-900">
                                    {{ logo.title }}
                                </h4>
                                <div class="mt-3 flex justify-end gap-2">
                                    <button
                                        @click="openDeptLogoModal(logo)"
                                        class="p-1.5 text-blue-600 hover:text-blue-800 rounded hover:bg-blue-50"
                                    >
                                        <Edit class="w-4 h-4" />
                                    </button>
                                    <button
                                        @click="deleteDeptLogo(logo)"
                                        class="p-1.5 text-red-600 hover:text-red-800 rounded hover:bg-red-50"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Header Section -->
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6"
                    >
                        <h2 class="text-xl font-semibold">Departments</h2>
                        <PrimaryButton @click="openDepartmentModal()">
                            Add Department
                        </PrimaryButton>
                    </div>

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <InputLabel for="search" value="Search Members" />
                        <TextInput
                            id="search"
                            v-model="searchQuery"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="Search by member name..."
                        />
                    </div>

                    <!-- Departments List -->
                    <div class="space-y-6">
                        <div
                            v-for="department in paginatedDepartments"
                            :key="department.id"
                            class="border rounded-lg p-4"
                        >
                            <!-- Department Header -->
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start gap-4"
                            >
                                <div class="flex-1">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-16 h-16 rounded-lg overflow-hidden"
                                        >
                                            <img
                                                v-if="department.image"
                                                :src="`/storage/${department.image}`"
                                                :alt="department.dept_name"
                                                class="w-full h-full object-cover"
                                            />
                                            <div
                                                v-else
                                                class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400"
                                            >
                                                No Image
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-medium">
                                            {{ department.dept_name }}
                                        </h3>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <PrimaryButton
                                        @click="
                                            openMemberModal(null, department)
                                        "
                                        class="text-sm"
                                    >
                                        Add Member
                                    </PrimaryButton>
                                    <button
                                        @click="openDepartmentModal(department)"
                                        class="inline-flex items-center justify-center p-2 text-blue-600 hover:text-blue-800 rounded-md hover:bg-blue-50"
                                    >
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button
                                        @click="deleteDepartment(department)"
                                        class="inline-flex items-center justify-center p-2 text-red-600 hover:text-red-800 rounded-md hover:bg-red-50"
                                    >
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>

                            <!-- Members Grid -->
                            <div class="mt-4">
                                <div
                                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
                                >
                                    <div
                                        v-for="member in department.members"
                                        :key="member.id"
                                        class="border rounded-lg p-3 bg-gray-50 hover:bg-gray-100 transition-colors"
                                    >
                                        <!-- Member Image -->
                                        <div class="aspect-w-4 aspect-h-3 mb-3">
                                            <img
                                                :src="`/storage/${member.image}`"
                                                :alt="member.name"
                                                class="w-full h-full object-cover rounded-md"
                                            />
                                        </div>

                                        <!-- Member Info -->
                                        <div class="space-y-1">
                                            <h4
                                                class="font-medium text-gray-900"
                                            >
                                                {{ member.name }}
                                            </h4>
                                            <p
                                                class="text-sm font-medium text-gray-700"
                                            >
                                                {{ member.job_titles }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                {{ member.position }}
                                            </p>
                                        </div>

                                        <!-- Member Actions -->
                                        <div
                                            class="mt-3 flex justify-end gap-2"
                                        >
                                            <button
                                                @click="openMemberModal(member)"
                                                class="p-1.5 text-blue-600 hover:text-blue-800 rounded hover:bg-blue-50"
                                            >
                                                <Edit class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="deleteMember(member)"
                                                class="p-1.5 text-red-600 hover:text-red-800 rounded hover:bg-red-50"
                                            >
                                                <Trash2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination Controls -->
                        <div
                            v-if="totalPages > 1"
                            class="mt-6 flex justify-center items-center gap-4"
                        >
                            <button
                                @click="prevPage"
                                :disabled="currentPage === 1"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Previous
                            </button>
                            <span class="text-sm text-gray-600">
                                Page {{ currentPage }} of {{ totalPages }}
                            </span>
                            <button
                                @click="nextPage"
                                :disabled="currentPage === totalPages"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Modal -->
        <Modal :show="showDepartmentModal" @close="closeDepartmentModal">
            <div class="p-6">
                <h2 class="text-lg font-medium">
                    {{
                        editingDepartment
                            ? "Edit Department"
                            : "Add New Department"
                    }}
                </h2>
                <form @submit.prevent="submitDepartment" class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel
                                for="dept_name"
                                value="Department Name"
                            />
                            <TextInput
                                id="dept_name"
                                v-model="departmentForm.dept_name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="departmentForm.errors.dept_name"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="desc" value="Description" />
                            <textarea
                                id="desc"
                                v-model="departmentForm.desc"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Enter department description..."
                            ></textarea>
                            <InputError
                                :message="departmentForm.errors.desc"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="dept_image"
                                value="Department Image"
                            />
                            <input
                                type="file"
                                id="dept_image"
                                @input="
                                    departmentForm.image =
                                        $event.target.files[0]
                                "
                                accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                :required="!editingDepartment"
                            />
                            <InputError
                                :message="departmentForm.errors.image"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-4">
                        <PrimaryButton :disabled="departmentForm.processing">
                            {{ editingDepartment ? "Update" : "Save" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Member Modal -->
        <Modal :show="showMemberModal" @close="closeMemberModal" max-width="xl">
            <div class="p-4 sm:p-6">
                <h2 class="text-lg font-medium mb-6">
                    {{ editingMember ? "Edit Member" : "Add New Member" }}
                </h2>
                <form @submit.prevent="submitMember" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Name Field -->
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                v-model="memberForm.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="memberForm.errors.name"
                                class="mt-1"
                            />
                        </div>

                        <!-- Job Type Field -->
                        <div>
                            <InputLabel for="job_type" value="Job Type" />
                            <select
                                id="job_type"
                                v-model="memberForm.job_type"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="board_of_dept">
                                    Ketua Departemen
                                </option>
                                <option value="vice_board_of_dept">
                                    Wakil Ketua Departemen
                                </option>
                                <option value="section_head_dept">
                                    Kepala Divisi
                                </option>
                                <option value="staff">Staff</option>
                            </select>
                            <InputError
                                :message="memberForm.errors.job_type"
                                class="mt-1"
                            />
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div>
                        <InputLabel for="image" value="Profile Image" />
                        <input
                            type="file"
                            id="image"
                            @input="memberForm.image = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            :required="!editingMember"
                        />
                        <InputError
                            :message="memberForm.errors.image"
                            class="mt-1"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Job Title Field -->
                        <div>
                            <InputLabel for="job_titles" value="Job Title" />
                            <TextInput
                                id="job_titles"
                                v-model="memberForm.job_titles"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="memberForm.errors.job_titles"
                                class="mt-1"
                            />
                        </div>

                        <!-- Position Field -->
                        <div>
                            <InputLabel for="position" value="Position" />
                            <TextInput
                                id="position"
                                v-model="memberForm.position"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError
                                :message="memberForm.errors.position"
                                class="mt-1"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <PrimaryButton :disabled="memberForm.processing">
                            {{ editingMember ? "Update" : "Save" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Department Logo Modal -->
        <Modal :show="showDeptLogoModal" @close="closeDeptLogoModal">
            <div class="p-6">
                <h2 class="text-lg font-medium">
                    {{
                        editingDeptLogo
                            ? "Edit Department Logo"
                            : "Add New Department Logo"
                    }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    This logo will be displayed on the front page of the website
                </p>
                <form @submit.prevent="submitDeptLogo" class="mt-6">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput
                                id="title"
                                v-model="deptLogoForm.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="deptLogoForm.errors.title"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="logo_image" value="Logo Image" />
                            <input
                                type="file"
                                id="logo_image"
                                @input="
                                    deptLogoForm.image = $event.target.files[0]
                                "
                                accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                :required="!editingDeptLogo"
                            />
                            <InputError
                                :message="deptLogoForm.errors.image"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-4">
                        <PrimaryButton :disabled="deptLogoForm.processing">
                            {{ editingDeptLogo ? "Update" : "Save" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
