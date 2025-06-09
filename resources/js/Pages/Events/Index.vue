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
                                            @click="
                                                router.visit(
                                                    route(
                                                        'events.show',
                                                        event.id
                                                    )
                                                )
                                            "
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
    </AuthenticatedLayout>
</template>

<style scoped>
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, minmax(0, 1fr));
}
</style>
