<script setup>
import { ref, onMounted, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useToast } from "vue-toastification";

const props = defineProps({
    departments: Array,
});

const toast = useToast();

const form = useForm({
    pic_name: "",
    email: "",
    phone: "",
    nama_kegiatan: "",
    bidang_kegiatan: "",
    jenis_kegiatan: "",
    department_id: "",
    tanggal_mulai: "",
    tanggal_akhir: "",
    tempat_kegiatan: "",
    jumlah_peserta: "",
    jumlah_panitia: "",
    jumlah_spj: "",
    dana_dipa_polban: "",
    dana_swadaya: "",
    dana_sponsor: "",
    pengisi_acara: "",
    sponsorship: "",
    media_partner: "",
    doc_proposal: null,
    doc_berkegiatan_ketuplak: null,
    doc_ormawa: null,
    doc_sarana_prasarana: null,
    link_surat_izin_ortu: "",
    poster: null,
    caption_poster: "",
});

const submit = () => {
    form.post(route("proposals.store"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            toast.success("Proposal berhasil dibuat");
        },
        onError: () => toast.error("Gagal membuat proposal"),
    });
};

const formatRupiah = (amount) => {
    // Ensure amount is a number and convert to integer
    const numericAmount = parseInt(amount);
    if (isNaN(numericAmount)) return "Rp 0";

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(numericAmount);
};

const formattedDipaPol = ref("");
const formattedSwadaya = ref("");
const formattedSponsor = ref("");

const updateDipaPol = (value) => {
    const numericValue = parseInt(value.replace(/\D/g, ""));
    form.dana_dipa_polban = isNaN(numericValue) ? null : numericValue;
    formattedDipaPol.value = formatRupiah(form.dana_dipa_polban);
};

const updateSwadaya = (value) => {
    const numericValue = parseInt(value.replace(/\D/g, ""));
    form.dana_swadaya = isNaN(numericValue) ? null : numericValue;
    formattedSwadaya.value = formatRupiah(form.dana_swadaya);
};

const updateSponsor = (value) => {
    const numericValue = parseInt(value.replace(/\D/g, ""));
    form.dana_sponsor = isNaN(numericValue) ? null : numericValue;
    formattedSponsor.value = formatRupiah(form.dana_sponsor);
};

// Memastikan format rupiah diterapkan saat nilai berubah
watch(() => form.dana_dipa_polban, (newValue) => {
    formattedDipaPol.value = formatRupiah(newValue);
});

watch(() => form.dana_swadaya, (newValue) => {
    formattedSwadaya.value = formatRupiah(newValue);
});

watch(() => form.dana_sponsor, (newValue) => {
    formattedSponsor.value = formatRupiah(newValue);
});

// Inisialisasi format rupiah saat komponen dimuat
onMounted(() => {
    formattedDipaPol.value = formatRupiah(form.dana_dipa_polban);
    formattedSwadaya.value = formatRupiah(form.dana_swadaya);
    formattedSponsor.value = formatRupiah(form.dana_sponsor);
});
</script>

<template>
    <AuthenticatedLayout title="Buat Proposal Kegiatan">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-xl font-semibold mb-6">Form Proposal Kegiatan</h2>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Informasi PIC -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Informasi Penanggung Jawab</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="pic_name" value="Nama PIC" />
                                    <TextInput
                                        id="pic_name"
                                        v-model="form.pic_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.pic_name" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="email" value="Email" />
                                    <TextInput
                                        id="email"
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.email" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="phone" value="Nomor Telepon" />
                                    <TextInput
                                        id="phone"
                                        v-model="form.phone"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.phone" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Kegiatan -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Informasi Kegiatan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="nama_kegiatan" value="Nama Kegiatan" />
                                    <TextInput
                                        id="nama_kegiatan"
                                        v-model="form.nama_kegiatan"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.nama_kegiatan" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="bidang_kegiatan" value="Bidang Kegiatan" />
                                    <select
                                        id="bidang_kegiatan"
                                        v-model="form.bidang_kegiatan"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Pilih Bidang Kegiatan</option>
                                        <option value="Program Kerja">Program Kerja</option>
                                        <option value="Pergerakan">Pergerakan</option>
                                    </select>
                                    <InputError :message="form.errors.bidang_kegiatan" class="mt-2" />
                                </div>
                                
                                <div>
                                    <InputLabel for="jenis_kegiatan" value="Jenis Kegiatan" />
                                    <select
                                        id="jenis_kegiatan"
                                        v-model="form.jenis_kegiatan"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Pilih Jenis Kegiatan</option>
                                        <option value="karakter">Karakter</option>
                                        <option value="penalaran">Penalaran</option>
                                        <option value="peminatan">Peminatan</option>
                                        <option value="pengabdian">Pengabdian</option>
                                    </select>
                                    <InputError :message="form.errors.jenis_kegiatan" class="mt-2" />
                                </div>
                                
                                <div>
                                    <InputLabel for="department_id" value="Perangkat" />
                                    <select
                                        id="department_id"
                                        v-model="form.department_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">Pilih Perangkat</option>
                                        <option v-for="department in departments" :key="department.id" :value="department.id">
                                            {{ department.dept_name }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.department_id" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="tanggal_mulai" value="Tanggal Mulai" />
                                    <TextInput
                                        id="tanggal_mulai"
                                        v-model="form.tanggal_mulai"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.tanggal_mulai" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="tanggal_akhir" value="Tanggal Akhir" />
                                    <TextInput
                                        id="tanggal_akhir"
                                        v-model="form.tanggal_akhir"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.tanggal_akhir" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="tempat_kegiatan" value="Tempat Kegiatan" />
                                    <TextInput
                                        id="tempat_kegiatan"
                                        v-model="form.tempat_kegiatan"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.tempat_kegiatan" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="pengisi_acara" value="Pengisi Acara" />
                                    <TextInput
                                        id="pengisi_acara"
                                        v-model="form.pengisi_acara"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.pengisi_acara" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Peserta -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Informasi Peserta & Panitia</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="jumlah_peserta" value="Jumlah Peserta" />
                                    <TextInput
                                        id="jumlah_peserta"
                                        v-model="form.jumlah_peserta"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.jumlah_peserta" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="jumlah_panitia" value="Jumlah Panitia" />
                                    <TextInput
                                        id="jumlah_panitia"
                                        v-model="form.jumlah_panitia"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.jumlah_panitia" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="jumlah_spj" value="Jumlah SPJ" />
                                    <TextInput
                                        id="jumlah_spj"
                                        v-model="form.jumlah_spj"
                                        type="number"
                                        min="0"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.jumlah_spj" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Dana -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Informasi Dana</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel for="dana_dipa_polban" value="Dana DIPA Polban" />
                                    <TextInput
                                        id="dana_dipa_polban"
                                        v-model="formattedDipaPol"
                                        @input="updateDipaPol($event.target.value)"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.dana_dipa_polban" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="dana_swadaya" value="Dana Swadaya" />
                                    <TextInput
                                        id="dana_swadaya"
                                        v-model="formattedSwadaya"
                                        @input="updateSwadaya($event.target.value)"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.dana_swadaya" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="dana_sponsor" value="Dana Sponsor" />
                                    <TextInput
                                        id="dana_sponsor"
                                        v-model="formattedSponsor"
                                        @input="updateSponsor($event.target.value)"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.dana_sponsor" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Sponsor & Media Partner -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Sponsor & Media Partner</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="sponsorship" value="Sponsorship" />
                                    <TextInput
                                        id="sponsorship"
                                        v-model="form.sponsorship"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.sponsorship" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="media_partner" value="Media Partner" />
                                    <TextInput
                                        id="media_partner"
                                        v-model="form.media_partner"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.media_partner" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Dokumen Pendukung</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="doc_proposal" value="Dokumen Proposal (PDF)" />
                                    <input
                                        id="doc_proposal"
                                        type="file"
                                        @input="form.doc_proposal = $event.target.files[0]"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                        accept=".pdf"
                                        required
                                    />
                                    <InputError :message="form.errors.doc_proposal" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="doc_berkegiatan_ketuplak" value="Dokumen Berkegiatan Ketuplak (PDF)" />
                                    <input
                                        id="doc_berkegiatan_ketuplak"
                                        type="file"
                                        @input="form.doc_berkegiatan_ketuplak = $event.target.files[0]"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                        accept=".pdf"
                                        required
                                    />
                                    <InputError :message="form.errors.doc_berkegiatan_ketuplak" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="doc_ormawa" value="Dokumen Ormawa (PDF)" />
                                    <input
                                        id="doc_ormawa"
                                        type="file"
                                        @input="form.doc_ormawa = $event.target.files[0]"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                        accept=".pdf"
                                        required
                                    />
                                    <InputError :message="form.errors.doc_ormawa" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="doc_sarana_prasarana" value="Dokumen Sarana Prasarana (PDF)" />
                                    <input
                                        id="doc_sarana_prasarana"
                                        type="file"
                                        @input="form.doc_sarana_prasarana = $event.target.files[0]"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                        accept=".pdf"
                                        required
                                    />
                                    <InputError :message="form.errors.doc_sarana_prasarana" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="link_surat_izin_ortu" value="Link Surat Izin Orang Tua" />
                                    <TextInput
                                        id="link_surat_izin_ortu"
                                        v-model="form.link_surat_izin_ortu"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.link_surat_izin_ortu" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Poster -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Poster Kegiatan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="poster" value="Poster (Gambar)" />
                                    <input
                                        id="poster"
                                        type="file"
                                        @input="form.poster = $event.target.files[0]"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                        accept="image/*"
                                        required
                                    />
                                    <InputError :message="form.errors.poster" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="caption_poster" value="Caption Poster" />
                                    <textarea
                                        id="caption_poster"
                                        v-model="form.caption_poster"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        rows="4"
                                        required
                                    ></textarea>
                                    <InputError :message="form.errors.caption_poster" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton :disabled="form.processing" class="ml-4">
                                {{ form.processing ? 'Memproses...' : 'Submit Proposal' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>