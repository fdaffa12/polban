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

// Tambahkan state untuk mengontrol tampilan
const showProposalTypeSelection = ref(true);
const selectedProposalType = ref(null);

// Fungsi untuk menangani pemilihan jenis proposal
const handleProposalTypeSelection = (type) => {
    selectedProposalType.value = type;
    form.jenis_proposal = type;
    showProposalTypeSelection.value = false;
};

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
    jenis_proposal: "",
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
watch(
    () => form.dana_dipa_polban,
    (newValue) => {
        formattedDipaPol.value = formatRupiah(newValue);
    }
);

watch(
    () => form.dana_swadaya,
    (newValue) => {
        formattedSwadaya.value = formatRupiah(newValue);
    }
);

watch(
    () => form.dana_sponsor,
    (newValue) => {
        formattedSponsor.value = formatRupiah(newValue);
    }
);

// Inisialisasi format rupiah saat komponen dimuat
onMounted(() => {
    formattedDipaPol.value = formatRupiah(form.dana_dipa_polban);
    formattedSwadaya.value = formatRupiah(form.dana_swadaya);
    formattedSponsor.value = formatRupiah(form.dana_sponsor);
});

// Tambahkan ref untuk preview gambar
const previewImage = ref(null);

// Perbaikan method untuk menghapus file
const removeFile = (fieldName) => {
    // Reset nilai form
    form[fieldName] = null;

    // Reset input file dengan mencari element dan mereset nilainya
    const fileInput = document.getElementById(fieldName);
    if (fileInput) {
        fileInput.value = "";
    }

    // Khusus untuk poster, hapus preview image
    if (fieldName === "poster") {
        previewImage.value = null;
    }
};

// Perbaikan method untuk handle poster upload
const handlePosterUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.poster = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        form.poster = null;
        previewImage.value = null;
    }
};
</script>

<template>
    <AuthenticatedLayout title="Buat Proposal Kegiatan">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Tampilan Pemilihan Jenis Proposal -->
                <div
                    v-if="showProposalTypeSelection"
                    class="flex flex-col items-center justify-center space-y-8"
                >
                    <h2 class="text-2xl font-bold text-gray-900">
                        Pilih Jenis Proposal
                    </h2>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full max-w-2xl"
                    >
                        <!-- Card Pengajuan Pusat -->
                        <button
                            @click="
                                handleProposalTypeSelection('pengajuan_pusat')
                            "
                            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-transparent hover:border-blue-500"
                        >
                            <div
                                class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-blue-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                    />
                                </svg>
                            </div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                Pengajuan Pusat
                            </h3>
                            <p class="text-sm text-gray-600 text-center">
                                Proposal untuk kegiatan yang diajukan ke pusat
                            </p>
                        </button>

                        <!-- Card Pengajuan Umum -->
                        <button
                            @click="
                                handleProposalTypeSelection('pengajuan_umum')
                            "
                            class="flex flex-col items-center p-6 bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border-2 border-transparent hover:border-green-500"
                        >
                            <div
                                class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-8 w-8 text-green-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                            </div>
                            <h3
                                class="text-lg font-semibold text-gray-900 mb-2"
                            >
                                Pengajuan Umum
                            </h3>
                            <p class="text-sm text-gray-600 text-center">
                                Proposal untuk kegiatan umum lainnya
                            </p>
                        </button>
                    </div>
                </div>

                <!-- Form Proposal (ditampilkan setelah memilih jenis) -->
                <div
                    v-else
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <!-- Tombol Kembali ke Pemilihan -->
                    <div class="mb-6">
                        <button
                            @click="showProposalTypeSelection = true"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors duration-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            Kembali
                        </button>
                    </div>

                    <h2 class="text-xl font-semibold mb-6">
                        Form Proposal
                        {{
                            selectedProposalType === "pengajuan_pusat"
                                ? "Pengajuan Pusat"
                                : "Pengajuan Umum"
                        }}
                    </h2>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Informasi PIC -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Informasi Penanggung Jawab
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel
                                        for="pic_name"
                                        value="Nama PIC"
                                    />
                                    <TextInput
                                        id="pic_name"
                                        v-model="form.pic_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.pic_name"
                                        class="mt-2"
                                    />
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
                                    <InputError
                                        :message="form.errors.email"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="phone"
                                        value="Nomor Telepon"
                                    />
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
                            </div>
                        </div>

                        <!-- Informasi Kegiatan -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Informasi Kegiatan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel
                                        for="nama_kegiatan"
                                        value="Nama Kegiatan"
                                    />
                                    <TextInput
                                        id="nama_kegiatan"
                                        v-model="form.nama_kegiatan"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.nama_kegiatan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="bidang_kegiatan"
                                        value="Bidang Kegiatan"
                                    />
                                    <select
                                        id="bidang_kegiatan"
                                        v-model="form.bidang_kegiatan"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">
                                            Pilih Bidang Kegiatan
                                        </option>
                                        <option value="Program Kerja">
                                            Program Kerja
                                        </option>
                                        <option value="Pergerakan">
                                            Pergerakan
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.bidang_kegiatan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jenis_kegiatan"
                                        value="Jenis Kegiatan"
                                    />
                                    <select
                                        id="jenis_kegiatan"
                                        v-model="form.jenis_kegiatan"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">
                                            Pilih Jenis Kegiatan
                                        </option>
                                        <option value="karakter">
                                            Karakter
                                        </option>
                                        <option value="penalaran">
                                            Penalaran
                                        </option>
                                        <option value="peminatan">
                                            Peminatan
                                        </option>
                                        <option value="pengabdian">
                                            Pengabdian
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.jenis_kegiatan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="department_id"
                                        value="Perangkat"
                                    />
                                    <select
                                        id="department_id"
                                        v-model="form.department_id"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="">
                                            Pilih Perangkat
                                        </option>
                                        <option
                                            v-for="department in departments"
                                            :key="department.id"
                                            :value="department.id"
                                        >
                                            {{ department.dept_name }}
                                        </option>
                                    </select>
                                    <InputError
                                        :message="form.errors.department_id"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tanggal_mulai"
                                        value="Tanggal Mulai"
                                    />
                                    <TextInput
                                        id="tanggal_mulai"
                                        v-model="form.tanggal_mulai"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tanggal_mulai"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tanggal_akhir"
                                        value="Tanggal Akhir"
                                    />
                                    <TextInput
                                        id="tanggal_akhir"
                                        v-model="form.tanggal_akhir"
                                        type="date"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tanggal_akhir"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="tempat_kegiatan"
                                        value="Tempat Kegiatan"
                                    />
                                    <TextInput
                                        id="tempat_kegiatan"
                                        v-model="form.tempat_kegiatan"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.tempat_kegiatan"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="pengisi_acara"
                                        value="Pengisi Acara"
                                    />
                                    <TextInput
                                        id="pengisi_acara"
                                        v-model="form.pengisi_acara"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.pengisi_acara"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Peserta -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Informasi Peserta & Panitia
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel
                                        for="jumlah_peserta"
                                        value="Jumlah Peserta"
                                    />
                                    <TextInput
                                        id="jumlah_peserta"
                                        v-model="form.jumlah_peserta"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_peserta"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_panitia"
                                        value="Jumlah Panitia"
                                    />
                                    <TextInput
                                        id="jumlah_panitia"
                                        v-model="form.jumlah_panitia"
                                        type="number"
                                        min="1"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_panitia"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="jumlah_spj"
                                        value="Jumlah SPJ"
                                    />
                                    <TextInput
                                        id="jumlah_spj"
                                        v-model="form.jumlah_spj"
                                        type="number"
                                        min="0"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="form.errors.jumlah_spj"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Dana -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Informasi Dana
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <InputLabel
                                        for="dana_dipa_polban"
                                        value="Dana DIPA Polban"
                                    />
                                    <TextInput
                                        id="dana_dipa_polban"
                                        v-model="formattedDipaPol"
                                        @input="
                                            updateDipaPol($event.target.value)
                                        "
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.dana_dipa_polban"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="dana_swadaya"
                                        value="Dana Swadaya"
                                    />
                                    <TextInput
                                        id="dana_swadaya"
                                        v-model="formattedSwadaya"
                                        @input="
                                            updateSwadaya($event.target.value)
                                        "
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.dana_swadaya"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="dana_sponsor"
                                        value="Dana Sponsor"
                                    />
                                    <TextInput
                                        id="dana_sponsor"
                                        v-model="formattedSponsor"
                                        @input="
                                            updateSponsor($event.target.value)
                                        "
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.dana_sponsor"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Sponsor & Media Partner -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Sponsor & Media Partner
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel
                                        for="sponsorship"
                                        value="Sponsorship"
                                    />
                                    <TextInput
                                        id="sponsorship"
                                        v-model="form.sponsorship"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.sponsorship"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="media_partner"
                                        value="Media Partner"
                                    />
                                    <TextInput
                                        id="media_partner"
                                        v-model="form.media_partner"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError
                                        :message="form.errors.media_partner"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Dokumen -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Dokumen Pendukung
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel
                                        for="doc_proposal"
                                        value="Dokumen Proposal (PDF)"
                                    />
                                    <div class="flex items-center gap-2">
                                        <input
                                            id="doc_proposal"
                                            type="file"
                                            @input="
                                                form.doc_proposal =
                                                    $event.target.files[0]
                                            "
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                            accept=".pdf"
                                            required
                                        />
                                        <button
                                            v-if="form.doc_proposal"
                                            @click.prevent="
                                                removeFile('doc_proposal')
                                            "
                                            type="button"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
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
                                    <InputError
                                        :message="form.errors.doc_proposal"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="doc_berkegiatan_ketuplak"
                                        value="Dokumen Berkegiatan Ketuplak (PDF)"
                                    />
                                    <div class="flex items-center gap-2">
                                        <input
                                            id="doc_berkegiatan_ketuplak"
                                            type="file"
                                            @input="
                                                form.doc_berkegiatan_ketuplak =
                                                    $event.target.files[0]
                                            "
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                            accept=".pdf"
                                            required
                                        />
                                        <button
                                            v-if="form.doc_berkegiatan_ketuplak"
                                            @click.prevent="
                                                removeFile(
                                                    'doc_berkegiatan_ketuplak'
                                                )
                                            "
                                            type="button"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
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
                                    <InputError
                                        :message="
                                            form.errors.doc_berkegiatan_ketuplak
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="doc_ormawa"
                                        value="Dokumen Ormawa (PDF)"
                                    />
                                    <div class="flex items-center gap-2">
                                        <input
                                            id="doc_ormawa"
                                            type="file"
                                            @input="
                                                form.doc_ormawa =
                                                    $event.target.files[0]
                                            "
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                            accept=".pdf"
                                            required
                                        />
                                        <button
                                            v-if="form.doc_ormawa"
                                            @click.prevent="
                                                removeFile('doc_ormawa')
                                            "
                                            type="button"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
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
                                    <InputError
                                        :message="form.errors.doc_ormawa"
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="doc_sarana_prasarana"
                                        value="Dokumen Sarana Prasarana (PDF)"
                                    />
                                    <div class="flex items-center gap-2">
                                        <input
                                            id="doc_sarana_prasarana"
                                            type="file"
                                            @input="
                                                form.doc_sarana_prasarana =
                                                    $event.target.files[0]
                                            "
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                            accept=".pdf"
                                            required
                                        />
                                        <button
                                            v-if="form.doc_sarana_prasarana"
                                            @click.prevent="
                                                removeFile(
                                                    'doc_sarana_prasarana'
                                                )
                                            "
                                            type="button"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
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
                                    <InputError
                                        :message="
                                            form.errors.doc_sarana_prasarana
                                        "
                                        class="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        for="link_surat_izin_ortu"
                                        value="Link Surat Izin Orang Tua"
                                    />
                                    <TextInput
                                        id="link_surat_izin_ortu"
                                        v-model="form.link_surat_izin_ortu"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError
                                        :message="
                                            form.errors.link_surat_izin_ortu
                                        "
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Poster -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">
                                Poster Kegiatan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel
                                        for="poster"
                                        value="Poster (Gambar)"
                                    />
                                    <div class="flex items-center gap-2">
                                        <input
                                            id="poster"
                                            type="file"
                                            @input="handlePosterUpload"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
                                            accept="image/*"
                                            required
                                        />
                                        <button
                                            v-if="form.poster"
                                            @click.prevent="
                                                removeFile('poster')
                                            "
                                            type="button"
                                            class="text-red-600 hover:text-red-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
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
                                    <InputError
                                        :message="form.errors.poster"
                                        class="mt-2"
                                    />

                                    <!-- Preview Gambar -->
                                    <div v-if="previewImage" class="mt-4">
                                        <img
                                            :src="previewImage"
                                            alt="Preview Poster"
                                            class="max-w-full h-auto rounded-lg shadow-lg"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel
                                        for="caption_poster"
                                        value="Caption Poster"
                                    />
                                    <textarea
                                        id="caption_poster"
                                        v-model="form.caption_poster"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        rows="4"
                                        required
                                    ></textarea>
                                    <InputError
                                        :message="form.errors.caption_poster"
                                        class="mt-2"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton
                                :disabled="form.processing"
                                class="ml-4"
                            >
                                {{
                                    form.processing
                                        ? "Memproses..."
                                        : "Submit Proposal"
                                }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
