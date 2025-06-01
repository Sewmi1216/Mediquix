<script setup>
import SideNavBar from "@/components/SideNavBar.vue";
import Modal from "@/components/Modal.vue";
import { ref, onMounted } from "vue";
import {
  BookOpenIcon,
  CloudIcon,
  DocumentTextIcon,
  XMarkIcon,
  ClockIcon,
  MapPinIcon,
} from "@heroicons/vue/24/solid";
import { RouterLink } from "vue-router";

const modalType = ref(null);
const selectedData = ref(null);
const prescriptions = ref([]);

function openModal(type, data) {
  modalType.value = type;
  selectedData.value = data;
}

function closeModal() {
  modalType.value = null;
  selectedData.value = null;
}
function formatDate(datetime) {
  const date = new Date(datetime);
  return date
    .toLocaleString("en-GB", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
    })
    .replace(",", "");
}

async function fetchPrescriptions() {
  try {
    const response = await fetch(
      "http://127.0.0.1:8000/api/pharmacy/prescriptions",
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    const data = await response.json();
    prescriptions.value = data.data;
  } catch (error) {
    console.error("Failed to fetch prescriptions", error);
  }
}

onMounted(() => {
  fetchPrescriptions();
});
</script>

<template>
  <div class="flex min-h-screen bg-gray-100">
    <SideNavBar />

    <!-- Main content -->
    <div class="flex-1 p-8">
      <section
        class="bg-white rounded-xl shadow-2xl w-full max-w-5xl p-6 md:p-10 mx-auto"
      >
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
          View Prescriptions
        </h2>

        <div class="overflow-x-auto">
          <table
            class="min-w-full text-sm table-auto border border-gray-200 rounded-lg shadow-sm"
          >
            <thead class="bg-blue text-white">
              <tr>
                <th class="py-3 px-4 text-left">#</th>
                <th class="py-3 px-4 text-left">Date / Time</th>
                <th class="py-3 px-4 text-left">Prescription ID</th>
                <th class="py-3 px-4 text-left">Delivery Date</th>
                <th class="py-3 px-4 text-left">Delivery Time</th>
                <th class="py-3 px-4 text-left">Delivery Address</th>
                <th class="py-3 px-4 text-left">Note</th>
                <th class="py-3 px-4 text-left">View</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr
                v-for="(prescription, index) in prescriptions"
                :key="prescription.prescription_id"
                class="hover:bg-gray-50 transition"
              >
                <td class="py-3 px-4">{{ index + 1 }}</td>
                <td class="py-3 px-4">
                  {{ formatDate(prescription.created_at) }}
                </td>
                <td class="py-3 px-4">{{ prescription.id }}</td>
                <td class="py-3 px-4">{{ prescription.delivery_date }}</td>
                <td class="py-3 px-4">{{ prescription.delivery_time }}</td>
                <td class="py-3 px-4">
                  <button
                    @click="openModal('address', prescription.delivery_address)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <MapPinIcon class="w-5 h-5" />
                  </button>
                </td>
                <td class="py-3 px-4">
                  <button
                    @click="openModal('note', prescription.note)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <BookOpenIcon class="w-5 h-5" />
                  </button>
                </td>
                <td class="py-3 px-4">
                  <RouterLink
                    :to="`/pharmacy/create-quotation/${prescription.id}`"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <DocumentTextIcon class="w-5 h-5" />
                  </RouterLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>

  <!-- Modals -->
  <Modal :show="modalType === 'address'" @close="closeModal">
    <p class="text-base font-medium">Delivery Address:</p>
    <p class="mt-2 text-gray-700">{{ selectedData }}</p>
    <div class="mt-4 text-center">
      <button
        @click="closeModal"
        class="bg-blue text-white px-3 py-2 rounded hover:bg-darkBlue text-sm"
      >
        OK
      </button>
    </div>
  </Modal>

  <Modal :show="modalType === 'note'" @close="closeModal">
    <p class="text-base font-medium">Note:</p>
    <p class="mt-2 text-gray-700">{{ selectedData }}</p>
    <div class="mt-4 text-center">
      <button
        @click="closeModal"
        class="bg-blue text-white px-3 py-2 rounded hover:bg-darkBlue text-sm"
      >
        OK
      </button>
    </div>
  </Modal>
</template>
