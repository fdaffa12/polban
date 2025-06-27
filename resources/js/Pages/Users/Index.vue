<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Link, router, Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";

const props = defineProps({
    users: Array,
    roles: Object,
});

const toast = useToast();
const showUserModal = ref(false);
const editingUser = ref(null);

const userForm = useForm({
    name: "",
    email: "",
    password: "",
    role: "",
});

const createUser = () => {
    userForm.post(route("users.store"), {
        onSuccess: () => {
            showUserModal.value = false;
            userForm.reset();
            toast.success("Pengguna berhasil dibuat!");
        },
    });
};

const editUser = (user) => {
    editingUser.value = user;
    userForm.name = user.name;
    userForm.email = user.email;
    userForm.role = user.role;
    userForm.password = ""; // Reset password field
    showUserModal.value = true;
};

const updateUser = () => {
    userForm.put(route("users.update", editingUser.value.id), {
        onSuccess: () => {
            showUserModal.value = false;
            userForm.reset();
            editingUser.value = null;
            toast.success("Pengguna berhasil diperbarui!");
        },
    });
};

const deleteUser = (user) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Data pengguna akan dihapus permanen!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("users.destroy", user.id), {
                onSuccess: () => {
                    toast.success("Pengguna berhasil dihapus!");
                },
                preserveScroll: true,
            });
        }
    });
};
</script>

<template>
    <AuthenticatedLayout title="User Management">
        <Head>
            <title>User Management</title>
            <meta name="description" content="Manage your users" />
        </Head>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Users</h2>
                    <PrimaryButton @click="showUserModal = true">
                        Tambah User
                    </PrimaryButton>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Nama
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users" :key="user.id">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ user.role_label }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button
                                        @click="editUser(user)"
                                        class="text-blue-600 hover:text-blue-900 mr-4"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="deleteUser(user)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- User Modal -->
        <Modal :show="showUserModal" @close="showUserModal = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ editingUser ? "Edit User" : "Create User" }}
                </h2>

                <form
                    @submit.prevent="editingUser ? updateUser() : createUser()"
                    class="mt-6"
                >
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Nama" />
                            <TextInput
                                id="name"
                                v-model="userForm.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="userForm.errors.name"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="userForm.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="userForm.errors.email"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="password" value="Password" />
                            <TextInput
                                id="password"
                                v-model="userForm.password"
                                type="password"
                                class="mt-1 block w-full"
                                :required="!editingUser"
                            />
                            <InputError
                                :message="userForm.errors.password"
                                class="mt-2"
                            />
                            <span
                                v-if="editingUser"
                                class="text-sm text-gray-500"
                            >
                                Biarkan kosong jika tidak ingin mengubah
                                password
                            </span>
                        </div>

                        <div>
                            <InputLabel for="role" value="Role" />
                            <select
                                id="role"
                                v-model="userForm.role"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Pilih Role</option>
                                <option
                                    v-for="(label, value) in roles"
                                    :key="value"
                                    :value="value"
                                >
                                    {{ label }}
                                </option>
                            </select>
                            <InputError
                                :message="userForm.errors.role"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        <SecondaryButton @click="showUserModal = false">
                            Batal
                        </SecondaryButton>
                        <PrimaryButton :disabled="userForm.processing">
                            {{ editingUser ? "Update" : "Simpan" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
