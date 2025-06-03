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
import { router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const props = defineProps({
    departments: Array,
});

const toast = useToast();

// Department Management
const showDepartmentModal = ref(false);
const editingDepartment = ref(null);
const departmentForm = useForm({
    dept_name: "",
});

const openDepartmentModal = (department = null) => {
    editingDepartment.value = department;
    if (department) {
        departmentForm.dept_name = department.dept_name;
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
</script>

<template>
    <AuthenticatedLayout title="Department Management">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Departments</h2>
                        <PrimaryButton @click="openDepartmentModal()">
                            Add Department
                        </PrimaryButton>
                    </div>

                    <!-- Departments List -->
                    <div class="space-y-6">
                        <div
                            v-for="department in departments"
                            :key="department.id"
                            class="border rounded-lg p-4"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-medium">
                                        {{ department.dept_name }}
                                    </h3>

                                    <!-- Members List -->
                                    <div
                                        class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4"
                                    >
                                        <div
                                            v-for="member in department.members"
                                            :key="member.id"
                                            class="border rounded p-3"
                                        >
                                            <img
                                                :src="`/storage/${member.image}`"
                                                :alt="member.name"
                                                class="w-full h-40 object-cover rounded mb-2"
                                            />
                                            <h4 class="font-medium">
                                                {{ member.name }}
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                {{ member.job_titles }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ member.position }}
                                            </p>
                                            <div class="mt-2 flex gap-2">
                                                <button
                                                    @click="
                                                        openMemberModal(member)
                                                    "
                                                    class="text-blue-600 hover:text-blue-800"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </button>
                                                <button
                                                    @click="
                                                        deleteMember(member)
                                                    "
                                                    class="text-red-600 hover:text-red-800"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <PrimaryButton
                                        @click="
                                            openMemberModal(null, department)
                                        "
                                    >
                                        Add Member
                                    </PrimaryButton>
                                    <button
                                        @click="openDepartmentModal(department)"
                                        class="text-blue-600 hover:text-blue-800"
                                    >
                                        <Edit class="w-5 h-5" />
                                    </button>
                                    <button
                                        @click="deleteDepartment(department)"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <Trash2 class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
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
                    <div>
                        <InputLabel for="dept_name" value="Department Name" />
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

                    <div class="mt-6 flex justify-end gap-4">
                        <PrimaryButton :disabled="departmentForm.processing">
                            {{ editingDepartment ? "Update" : "Save" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Member Modal -->
        <Modal :show="showMemberModal" @close="closeMemberModal">
            <div class="p-6">
                <h2 class="text-lg font-medium">
                    {{ editingMember ? "Edit Member" : "Add New Member" }}
                </h2>
                <form @submit.prevent="submitMember" class="mt-6">
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
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="image" value="Image" />
                        <input
                            type="file"
                            id="image"
                            @input="memberForm.image = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1 block w-full"
                            :required="!editingMember"
                        />
                        <InputError
                            :message="memberForm.errors.image"
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="job_type" value="Job Type" />
                        <select
                            id="job_type"
                            v-model="memberForm.job_type"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            required
                        >
                            <option value="board_of_dept">
                                Board of Department
                            </option>
                            <option value="section_head_dept">
                                Section Head
                            </option>
                            <option value="staff">Staff</option>
                        </select>
                        <InputError
                            :message="memberForm.errors.job_type"
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-4">
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
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="position" value="Position" />
                        <TextInput
                            id="position"
                            v-model="memberForm.position"
                            type="text"
                            class="mt-1 block w-full"
                        />
                        <InputError
                            :message="memberForm.errors.position"
                            class="mt-2"
                        />
                    </div>

                    <div class="mt-6 flex justify-end gap-4">
                        <PrimaryButton :disabled="memberForm.processing">
                            {{ editingMember ? "Update" : "Save" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
