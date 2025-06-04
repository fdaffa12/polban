<script setup>
import { ref, computed, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {
    Edit,
    Trash2,
    Eye,
    XCircle,
    ChevronLeft,
    ChevronRight,
} from "lucide-vue-next";
import { router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import Swal from "sweetalert2";
import { QuillEditor } from "@vueup/vue-quill";
import "@vueup/vue-quill/dist/vue-quill.snow.css";
import {
    format,
    startOfMonth,
    endOfMonth,
    eachDayOfInterval,
    isSameDay,
    isToday,
} from "date-fns";

const props = defineProps({
    events: Array,
    departments: Array,
});

const toast = useToast();
const showModal = ref(false);
const editingEvent = ref(null);
const dates = ref([{ event_date: "", event_time: "" }]);

const eventForm = useForm({
    department_id: "",
    event_name: "",
    event_detail: "",
    fee_type: "free",
    fee_amount: null,
    event_flyer: null,
    event_gallery: [],
    event_doc: null,
    status: "coming_soon",
    dates: dates.value,
});

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

const formatDateForInput = (dateString) => {
    const date = new Date(dateString);
    return date.toISOString().split("T")[0];
};

const formatTimeForInput = (timeString) => {
    return timeString.substring(0, 5); // Get HH:mm format
};

const openModal = (event = null) => {
    editingEvent.value = event;
    if (event) {
        eventForm.department_id = event.department_id;
        eventForm.event_name = event.event_name;
        eventForm.event_detail = event.event_detail;
        eventForm.fee_type = event.fee_type;
        eventForm.fee_amount = event.fee_amount;
        formattedFeeAmount.value = event.fee_amount
            ? formatRupiah(event.fee_amount)
            : "";
        eventForm.status = event.status;
        dates.value = event.dates.map((d) => ({
            event_date: formatDateForInput(d.event_date),
            event_time: formatTimeForInput(d.event_time),
        }));
        eventForm.dates = dates.value;
    } else {
        eventForm.reset();
        formattedFeeAmount.value = "";
        dates.value = [{ event_date: "", event_time: "" }];
        eventForm.dates = dates.value;
    }
    showModal.value = true;
};

const addDate = () => {
    dates.value.push({ event_date: "", event_time: "" });
    eventForm.dates = dates.value;
};

const removeDate = (index) => {
    dates.value.splice(index, 1);
    eventForm.dates = dates.value;
};

const submit = () => {
    if (editingEvent.value) {
        eventForm.post(route("events.update", editingEvent.value.id), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeModal();
                toast.success("Event updated successfully");
            },
            onError: () => toast.error("Failed to update event"),
        });
    } else {
        eventForm.post(route("events.store"), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                closeModal();
                toast.success("Event added successfully");
            },
            onError: () => toast.error("Failed to add event"),
        });
    }
};

const closeModal = () => {
    showModal.value = false;
    editingEvent.value = null;
    eventForm.reset();
    dates.value = [{ event_date: "", event_time: "" }];
};

const deleteEvent = (event) => {
    Swal.fire({
        title: "Delete Event?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("events.destroy", event.id), {
                preserveScroll: true,
                onSuccess: () => {
                    toast.success("Event deleted successfully");
                },
                onError: () => toast.error("Failed to delete event"),
            });
        }
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

const formattedFeeAmount = ref("");

const calculateEventStatus = (eventDates) => {
    if (!eventDates || eventDates.length === 0) return "coming_soon";

    const today = new Date();
    const eventDateObjects = eventDates.map((d) => new Date(d.event_date));
    const earliestDate = new Date(Math.min(...eventDateObjects));
    const latestDate = new Date(Math.max(...eventDateObjects));

    // Set coming_soon if event is more than 7 days away
    if (
        earliestDate > today &&
        earliestDate - today > 7 * 24 * 60 * 60 * 1000
    ) {
        return "coming_soon";
    }
    // Set running if event is happening today or within 7 days
    else if (today >= earliestDate && today <= latestDate) {
        return "running";
    }
    // Set closed if all event dates have passed
    else if (today > latestDate) {
        return "closed";
    }
    // Default to coming_soon for upcoming events within 7 days
    return "coming_soon";
};

watch(
    () => eventForm.fee_type,
    (newValue) => {
        if (newValue === "free") {
            eventForm.fee_amount = null;
            formattedFeeAmount.value = "";
        }
    }
);

watch(
    () => dates.value,
    (newDates) => {
        if (newDates.length > 0 && newDates[0].event_date) {
            eventForm.status = calculateEventStatus(newDates);
        }
    },
    { deep: true }
);

// Update the watch for formattedFeeAmount
watch(formattedFeeAmount, (newValue) => {
    if (eventForm.fee_type === "paid") {
        // Remove all non-numeric characters and convert to number
        const numericValue = parseInt(newValue.replace(/\D/g, ""));
        eventForm.fee_amount = numericValue;
    }
});

const showDetailModal = ref(false);
const selectedEvent = ref(null);

const previewDetail = (event) => {
    selectedEvent.value = event;
    showDetailModal.value = true;
};

const showImagePreview = ref(false);
const previewImage = ref(null);
const currentGalleryIndex = ref(0);
const galleryImages = ref([]);

const openImagePreview = (imagePath, images = null, index = 0) => {
    previewImage.value = `/storage/${imagePath}`;
    showImagePreview.value = true;
    if (images) {
        galleryImages.value = images;
        currentGalleryIndex.value = index;
    } else {
        galleryImages.value = [];
    }
};

const navigateGallery = (direction) => {
    if (galleryImages.value.length === 0) return;

    if (direction === "next") {
        currentGalleryIndex.value =
            (currentGalleryIndex.value + 1) % galleryImages.value.length;
    } else {
        currentGalleryIndex.value =
            currentGalleryIndex.value === 0
                ? galleryImages.value.length - 1
                : currentGalleryIndex.value - 1;
    }
    previewImage.value = `/storage/${
        galleryImages.value[currentGalleryIndex.value]
    }`;
};

const currentMonth = ref(new Date());
const showCalendarView = ref(false);

const calendarDays = computed(() => {
    const start = startOfMonth(currentMonth.value);
    const end = endOfMonth(currentMonth.value);
    return eachDayOfInterval({ start, end });
});

const getEventsForDay = (day) => {
    return props.events.filter((event) =>
        event.dates.some((date) => isSameDay(new Date(date.event_date), day))
    );
};
</script>

<template>
    <AuthenticatedLayout title="Event Management">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold">Events</h2>
                        <div class="flex gap-4">
                            <button
                                @click="showCalendarView = !showCalendarView"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md transition"
                            >
                                {{
                                    showCalendarView
                                        ? "List View"
                                        : "Calendar View"
                                }}
                            </button>
                            <PrimaryButton @click="openModal()"
                                >Add Event</PrimaryButton
                            >
                        </div>
                    </div>

                    <!-- Events List -->
                    <div
                        v-if="showCalendarView"
                        class="bg-white rounded-lg shadow overflow-hidden"
                    >
                        <!-- Calendar Header -->
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ format(currentMonth, "MMMM yyyy") }}
                            </h3>
                        </div>

                        <!-- Calendar Grid -->
                        <div class="grid grid-cols-7 gap-px bg-gray-200">
                            <!-- Day headers -->
                            <div
                                v-for="day in [
                                    'Sun',
                                    'Mon',
                                    'Tue',
                                    'Wed',
                                    'Thu',
                                    'Fri',
                                    'Sat',
                                ]"
                                :key="day"
                                class="bg-gray-50 py-2 text-center text-xs font-medium text-gray-500"
                            >
                                {{ day }}
                            </div>

                            <!-- Calendar days -->
                            <div
                                v-for="day in calendarDays"
                                :key="day"
                                class="bg-white min-h-[120px] p-2 relative"
                                :class="{
                                    'bg-blue-50': isToday(day),
                                }"
                            >
                                <!-- Date number -->
                                <span
                                    class="text-sm font-medium"
                                    :class="{
                                        'text-blue-600': isToday(day),
                                    }"
                                >
                                    {{ format(day, "d") }}
                                </span>

                                <!-- Events for this day -->
                                <div class="mt-1 space-y-1">
                                    <div
                                        v-for="event in getEventsForDay(day)"
                                        :key="event.id"
                                        @click="previewDetail(event)"
                                        class="text-xs p-1 rounded cursor-pointer truncate"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800':
                                                event.status === 'coming_soon',
                                            'bg-green-100 text-green-800':
                                                event.status === 'running',
                                            'bg-red-100 text-red-800':
                                                event.status === 'closed',
                                        }"
                                    >
                                        {{ event.event_name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Event
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Department
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Status
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Fee
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Dates
                                    </th>
                                    <th
                                        scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="event in events" :key="event.id">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 flex-shrink-0"
                                            >
                                                <img
                                                    class="h-10 w-10 rounded-full object-cover"
                                                    :src="`/storage/${event.event_flyer}`"
                                                    :alt="event.event_name"
                                                />
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-medium text-gray-900"
                                                >
                                                    {{ event.event_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ event.department.dept_name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-semibold rounded-full',
                                                {
                                                    'bg-yellow-100 text-yellow-800':
                                                        event.status ===
                                                        'coming_soon',
                                                    'bg-green-100 text-green-800':
                                                        event.status ===
                                                        'running',
                                                    'bg-red-100 text-red-800':
                                                        event.status ===
                                                        'closed',
                                                },
                                            ]"
                                        >
                                            {{
                                                event.status
                                                    .replace("_", " ")
                                                    .toUpperCase()
                                            }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                    >
                                        {{
                                            event.fee_type === "free"
                                                ? "Free"
                                                : formatRupiah(event.fee_amount)
                                        }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div
                                                v-for="date in event.dates"
                                                :key="date.id"
                                                class="mb-1"
                                            >
                                                {{
                                                    new Date(
                                                        date.event_date
                                                    ).toLocaleDateString()
                                                }}
                                                at {{ date.event_time }}
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                    >
                                        <button
                                            @click="previewDetail(event)"
                                            class="text-green-600 hover:text-green-900 mr-3"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="openModal(event)"
                                            class="text-blue-600 hover:text-blue-900 mr-3"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="deleteEvent(event)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Modal -->
        <Modal :show="showModal" @close="closeModal" max-width="2xl">
            <div class="p-6">
                <h2 class="text-lg font-medium">
                    {{ editingEvent ? "Edit Event" : "Add New Event" }}
                </h2>
                <form @submit.prevent="submit" class="mt-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <InputLabel
                                for="department_id"
                                value="Department"
                            />
                            <select
                                v-model="eventForm.department_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Select Department</option>
                                <option
                                    v-for="dept in departments"
                                    :key="dept.id"
                                    :value="dept.id"
                                >
                                    {{ dept.dept_name }}
                                </option>
                            </select>
                            <InputError
                                :message="eventForm.errors.department_id"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel for="event_name" value="Event Name" />
                            <TextInput
                                id="event_name"
                                v-model="eventForm.event_name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError
                                :message="eventForm.errors.event_name"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <!-- Event Detail with Full Width -->
                    <div class="mb-6">
                        <InputLabel for="event_detail" value="Event Detail" />
                        <div class="mt-1" style="min-height: 200px; z-index: 1">
                            <QuillEditor
                                id="event_detail"
                                v-model:content="eventForm.event_detail"
                                :options="editorOptions"
                                contentType="html"
                                theme="snow"
                                class="bg-white"
                            />
                        </div>
                        <InputError
                            :message="eventForm.errors.event_detail"
                            class="mt-2"
                        />
                    </div>

                    <!-- Fee Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <InputLabel value="Fee Type" />
                            <div class="mt-2 space-x-4">
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="eventForm.fee_type"
                                        value="free"
                                        class="form-radio text-indigo-600"
                                    />
                                    <span class="ml-2">Free</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="eventForm.fee_type"
                                        value="paid"
                                        class="form-radio text-indigo-600"
                                    />
                                    <span class="ml-2">Paid</span>
                                </label>
                            </div>
                        </div>

                        <div v-if="eventForm.fee_type === 'paid'">
                            <InputLabel for="fee_amount" value="Fee Amount" />
                            <TextInput
                                id="fee_amount"
                                v-model="formattedFeeAmount"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                @input="
                                    (e) => {
                                        const value = e.target.value.replace(
                                            /[^0-9]/g,
                                            ''
                                        );
                                        formattedFeeAmount = value
                                            ? formatRupiah(value)
                                            : '';
                                    }
                                "
                            />
                            <InputError
                                :message="eventForm.errors.fee_amount"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <!-- File Uploads -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <InputLabel for="event_flyer" value="Event Flyer" />
                            <input
                                type="file"
                                @input="
                                    eventForm.event_flyer =
                                        $event.target.files[0]
                                "
                                class="mt-1 block w-full"
                                :required="!editingEvent"
                                accept="image/*"
                            />
                            <InputError
                                :message="eventForm.errors.event_flyer"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="event_gallery"
                                value="Event Gallery"
                            />
                            <input
                                type="file"
                                @input="
                                    eventForm.event_gallery = [
                                        ...$event.target.files,
                                    ]
                                "
                                class="mt-1 block w-full"
                                multiple
                                accept="image/*"
                            />
                            <InputError
                                :message="eventForm.errors.event_gallery"
                                class="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                for="event_doc"
                                value="Event Document"
                            />
                            <input
                                type="file"
                                @input="
                                    eventForm.event_doc = $event.target.files[0]
                                "
                                class="mt-1 block w-full"
                                accept=".pdf,.doc,.docx"
                            />
                            <InputError
                                :message="eventForm.errors.event_doc"
                                class="mt-2"
                            />
                        </div>
                    </div>

                    <!-- Event Dates -->
                    <div class="mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <h3 class="font-medium">Event Dates</h3>
                            <button
                                type="button"
                                @click="addDate"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                Add Date
                            </button>
                        </div>
                        <div
                            v-for="(date, index) in dates"
                            :key="index"
                            class="flex gap-4 mt-2"
                        >
                            <div class="flex-1">
                                <InputLabel
                                    :for="`date_${index}`"
                                    value="Date"
                                />
                                <input
                                    :id="`date_${index}`"
                                    v-model="date.event_date"
                                    type="date"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required
                                />
                            </div>
                            <div class="flex-1">
                                <InputLabel
                                    :for="`time_${index}`"
                                    value="Time"
                                />
                                <input
                                    :id="`time_${index}`"
                                    v-model="date.event_time"
                                    type="time"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                    required
                                />
                            </div>
                            <button
                                type="button"
                                @click="removeDate(index)"
                                class="mt-8 text-red-600 hover:text-red-800"
                                v-if="dates.length > 1"
                            >
                                Remove
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <PrimaryButton :disabled="eventForm.processing">
                            {{ editingEvent ? "Update" : "Save" }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Detail Modal -->
        <!-- Detail Modal -->
        <Modal
            :show="showDetailModal"
            @close="showDetailModal = false"
            max-width="4xl"
        >
            <!-- Detail Modal Content -->
            <div class="p-4 sm:p-6" v-if="selectedEvent">
                <!-- Header -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2"
                >
                    <h2 class="text-lg sm:text-xl font-semibold">
                        {{ selectedEvent.event_name }}
                    </h2>
                    <span
                        :class="{
                            'px-2 py-1 text-xs font-semibold rounded-full': true,
                            'bg-yellow-100 text-yellow-800':
                                selectedEvent.status === 'coming_soon',
                            'bg-green-100 text-green-800':
                                selectedEvent.status === 'running',
                            'bg-red-100 text-red-800':
                                selectedEvent.status === 'closed',
                        }"
                    >
                        {{
                            selectedEvent.status.replace("_", " ").toUpperCase()
                        }}
                    </span>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                    <!-- Left Column -->
                    <div class="space-y-4">
                        <!-- Event Flyer -->
                        <div>
                            <h3 class="text-sm font-medium mb-2">
                                Event Flyer
                            </h3>
                            <div
                                class="max-h-[250px] sm:max-h-[300px] overflow-hidden"
                            >
                                <img
                                    :src="`/storage/${selectedEvent.event_flyer}`"
                                    :alt="selectedEvent.event_name"
                                    class="w-full h-auto max-h-[250px] sm:max-h-[300px] object-contain rounded-lg shadow-sm cursor-pointer hover:opacity-90 transition-opacity"
                                    @click="
                                        openImagePreview(
                                            selectedEvent.event_flyer
                                        )
                                    "
                                />
                            </div>
                        </div>

                        <!-- Event Gallery -->
                        <div>
                            <h3 class="text-sm font-medium mb-2">
                                Event Gallery
                            </h3>
                            <div
                                v-if="selectedEvent.event_gallery?.length"
                                class="grid grid-cols-2 sm:grid-cols-3 gap-2"
                            >
                                <div
                                    v-for="(
                                        image, index
                                    ) in selectedEvent.event_gallery"
                                    :key="index"
                                    class="relative aspect-square"
                                >
                                    <img
                                        :src="`/storage/${image}`"
                                        :alt="`Gallery image ${index + 1}`"
                                        class="w-full h-full object-cover rounded-md shadow-sm cursor-pointer hover:opacity-75 transition-opacity"
                                        @click="
                                            openImagePreview(
                                                image,
                                                selectedEvent.event_gallery,
                                                index
                                            )
                                        "
                                    />
                                </div>
                            </div>
                            <p v-else class="text-sm text-gray-500">
                                No gallery images
                            </p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4">
                        <!-- Quick Info Section -->
                        <div class="bg-gray-50 rounded-lg p-3 sm:p-4">
                            <div class="space-y-3">
                                <div>
                                    <h3 class="text-sm font-medium">
                                        Department
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        {{ selectedEvent.department.dept_name }}
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium">Fee</h3>
                                    <p class="text-sm text-gray-600">
                                        {{
                                            selectedEvent.fee_type === "free"
                                                ? "Free"
                                                : formatRupiah(
                                                      selectedEvent.fee_amount
                                                  )
                                        }}
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium">
                                        Event Dates
                                    </h3>
                                    <div class="space-y-1">
                                        <div
                                            v-for="date in selectedEvent.dates"
                                            :key="date.id"
                                            class="flex items-center text-sm text-gray-600"
                                        >
                                            <CalendarDays
                                                class="w-4 h-4 mr-1"
                                            />
                                            {{
                                                new Date(
                                                    date.event_date
                                                ).toLocaleDateString()
                                            }}
                                            at {{ date.event_time }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div>
                            <h3 class="text-sm font-medium mb-2">
                                Event Details
                            </h3>
                            <div class="prose prose-sm max-w-none">
                                <div v-html="selectedEvent.event_detail"></div>
                            </div>
                        </div>

                        <!-- Document Section -->
                        <div v-if="selectedEvent.event_doc">
                            <h3 class="text-sm font-medium mb-2">Documents</h3>
                            <a
                                :href="`/storage/${selectedEvent.event_doc}`"
                                target="_blank"
                                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800"
                            >
                                <FileText class="w-4 h-4 mr-1" />
                                View Document
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Image Preview Modal -->
        <Modal
            :show="showImagePreview"
            @close="showImagePreview = false"
            max-width="6xl"
        >
            <div class="relative bg-gray-900">
                <!-- Close button -->
                <button
                    @click="showImagePreview = false"
                    class="absolute top-2 right-2 z-50 p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-75"
                >
                    <XCircle class="w-6 h-6" />
                </button>

                <!-- Navigation buttons -->
                <div
                    v-if="galleryImages.length > 1"
                    class="absolute inset-y-0 left-0 right-0 flex items-center justify-between px-2 sm:px-4"
                >
                    <button
                        @click="navigateGallery('prev')"
                        class="p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-75"
                    >
                        <ChevronLeft class="w-4 h-4 sm:w-6 sm:h-6" />
                    </button>
                    <button
                        @click="navigateGallery('next')"
                        class="p-1 bg-gray-800 bg-opacity-50 rounded-full text-white hover:bg-opacity-75"
                    >
                        <ChevronRight class="w-4 h-4 sm:w-6 sm:h-6" />
                    </button>
                </div>

                <!-- Image container -->
                <div class="flex items-center justify-center p-2 sm:p-4">
                    <img
                        :src="previewImage"
                        class="max-w-full max-h-[85vh] w-auto h-auto object-contain"
                    />
                </div>

                <!-- Gallery counter -->
                <div
                    v-if="galleryImages.length > 1"
                    class="absolute bottom-4 left-0 right-0 text-center"
                >
                    <span
                        class="px-2 sm:px-3 py-1 bg-gray-800 bg-opacity-50 rounded-full text-white text-xs sm:text-sm"
                    >
                        {{ currentGalleryIndex + 1 }} /
                        {{ galleryImages.length }}
                    </span>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
}
</style>
