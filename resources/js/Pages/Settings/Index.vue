<script setup>
import { ref } from "vue";
import { useForm, Head } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Plus, Trash2 } from "lucide-vue-next";
import { useToast } from "vue-toastification";

const props = defineProps({
    setting: Object,
});

const toast = useToast();

const form = useForm({
    nama: props.setting?.nama || "",
    logo: null,
    email: props.setting?.email || "",
    phone: props.setting?.phone || "",
    address: props.setting?.address || "",
    social_media: props.setting?.social_media || [],
});

const addSocialMedia = () => {
    form.social_media.push({
        name: "",
        url: "",
    });
};

const removeSocialMedia = (index) => {
    form.social_media.splice(index, 1);
};

const submit = () => {
    form.post(route("settings.update"), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success("Pengaturan berhasil diperbarui");
        },
        onError: () => {
            toast.error("Gagal memperbarui pengaturan");
        },
    });
};
</script>

<template>
    <AuthenticatedLayout title="Pengaturan Website">
        <Head>
            <title>Pengaturan Website</title>
            <meta name="description" content="Manage your website settings" />
        </Head>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit">
                        <!-- Nama Website -->
                        <div class="mb-6">
                            <InputLabel for="nama" value="Nama Website" />
                            <TextInput
                                id="nama"
                                v-model="form.nama"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.nama"
                                class="mt-2"
                            />
                        </div>

                        <!-- Logo -->
                        <div class="mb-6">
                            <InputLabel for="logo" value="Logo" />
                            <div class="flex items-center gap-4">
                                <img
                                    v-if="setting.logo"
                                    :src="`/storage/${setting.logo}`"
                                    alt="Logo"
                                    class="h-20 w-auto"
                                />
                                <input
                                    type="file"
                                    id="logo"
                                    @input="form.logo = $event.target.files[0]"
                                    accept="image/*"
                                    class="mt-1"
                                />
                            </div>
                            <InputError
                                :message="form.errors.logo"
                                class="mt-2"
                            />
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.email"
                                class="mt-2"
                            />
                        </div>

                        <!-- Phone -->
                        <div class="mb-6">
                            <InputLabel for="phone" value="Nomor Telepon" />
                            <TextInput
                                id="phone"
                                v-model="form.phone"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="form.errors.phone"
                                class="mt-2"
                            />
                        </div>

                        <!-- Address -->
                        <div class="mb-6">
                            <InputLabel for="address" value="Alamat" />
                            <textarea
                                id="address"
                                v-model="form.address"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"
                                required
                            ></textarea>
                            <InputError
                                :message="form.errors.address"
                                class="mt-2"
                            />
                        </div>

                        <!-- Social Media -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <InputLabel value="Media Sosial" />
                                <button
                                    type="button"
                                    @click="addSocialMedia"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700"
                                >
                                    <Plus class="w-4 h-4 mr-2" />
                                    Tambah Media Sosial
                                </button>
                            </div>

                            <div
                                v-for="(social, index) in form.social_media"
                                :key="index"
                                class="flex gap-4 mb-4"
                            >
                                <div class="flex-1">
                                    <TextInput
                                        v-model="social.name"
                                        type="text"
                                        class="w-full"
                                        placeholder="Nama Media Sosial"
                                        required
                                    />
                                </div>
                                <div class="flex-1">
                                    <TextInput
                                        v-model="social.url"
                                        type="url"
                                        class="w-full"
                                        placeholder="URL Media Sosial"
                                        required
                                    />
                                </div>
                                <button
                                    type="button"
                                    @click="removeSocialMedia(index)"
                                    class="text-red-600 hover:text-red-800"
                                >
                                    <Trash2 class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <PrimaryButton :disabled="form.processing">
                                Simpan Pengaturan
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
