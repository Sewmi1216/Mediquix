<script setup>
import SideNavBar from "@/components/SideNavBar.vue";
import Modal from "@/components/Modal.vue";
import { ref, onMounted } from "vue";
import { DocumentTextIcon } from "@heroicons/vue/24/solid";

const modalType = ref(null);
const selectedModal = ref(null);
const quotations = ref([]);
const selectedQuotationDetails = ref(null);

async function openModal(type, idObj) {
  modalType.value = type;
  selectedModal.value = idObj;

  if (type === "view" && idObj?.id) {
    await fetchQuotationDetails(idObj.id);
  }
}

function closeModal() {
  modalType.value = null;
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

async function fetchQuotations() {
  try {
    const response = await fetch(
      "http://127.0.0.1:8000/api/pharmacy/quotations",
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    const data = await response.json();
    quotations.value = data.data;
  } catch (error) {
    console.error("Failed to fetch quotations", error);
  }
}
async function fetchQuotationDetails(id) {
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/pharmacy/quotations/${id}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    const data = await response.json();
    console.log(data);
    selectedQuotationDetails.value = data.data;
  } catch (error) {
    console.error("Failed to fetch quotation details", error);
  }
}

onMounted(() => {
  fetchQuotations();
});
</script>

<template>
  <div class="flex min-h-screen bg-gray-100">
    <SideNavBar />

    <!-- Main content -->
    <div class="flex-1 p-8">
      <section
        class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 md:p-10 mx-auto"
      >
        <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">
          View Quotations
        </h2>

        <div class="overflow-x-auto">
          <table
            class="min-w-full text-sm table-auto border border-gray-200 rounded-lg shadow-sm"
          >
            <thead class="bg-blue text-white">
              <tr>
                <th class="py-3 px-4 text-left">#</th>
                <th class="py-3 px-4 text-left">Date / Time</th>
                <th class="py-3 px-4 text-left">Quotation ID</th>
                <th class="py-3 px-4 text-left">Total price</th>
                <th class="py-3 px-4 text-left">View</th>
                <th class="py-3 px-4 text-left">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr
                v-for="(quotation, index) in quotations"
                :key="quotation.quotation_id"
                class="hover:bg-gray-50 transition"
              >
                <td class="py-3 px-4">{{ index + 1 }}</td>
                <td class="py-3 px-4">
                  {{ formatDate(quotation.created_at) }}
                </td>
                <td class="py-3 px-4">{{ quotation.id }}</td>
                <td class="py-3 px-4">Rs. {{ quotation.total_amount }}</td>
                <td class="py-3 px-4">
                  <button
                    @click="openModal('view', { id: quotation.id })"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <DocumentTextIcon class="w-5 h-5" />
                  </button>
                </td>
                <td class="py-3 px-4">
                  <button
                    @click="openModal('approve', { id: quotation.id })"
                    :class="[
                      'px-3 py-1 rounded text-white',
                      quotation.status === 'Pending'
                        ? 'bg-yellow-500 hover:bg-yellow-600'
                        : quotation.status === 'Accepted'
                          ? 'bg-green-500 hover:bg-green-600'
                          : quotation.status === 'Rejected'
                            ? 'bg-red-500 hover:bg-red-600'
                            : 'bg-gray-400',
                    ]"
                  >
                    {{ quotation.status }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>

  <Modal :show="modalType === 'view'" @close="closeModal">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Quotation Details</h3>
    <p class="text-sm">
      ID : {{ selectedQuotationDetails?.id || selectedModal?.id }}
    </p>

    <table
      class="min-w-full text-sm mt-3 table-auto border border-gray-200 rounded-lg shadow-sm"
    >
      <thead class="bg-blue text-white">
        <tr>
          <th class="py-3 px-4 text-left">#</th>
          <th class="py-3 px-4 text-left">Drug</th>
          <th class="py-3 px-4 text-left">Quantity</th>
          <th class="py-3 px-4 text-left">Amount</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 text-gray-700">
        <tr
          v-for="(drug, index) in selectedQuotationDetails?.drugs || []"
          :key="drug.id || index"
          class="hover:bg-gray-50 transition"
        >
          <td class="py-3 px-4">{{ index + 1 }}</td>
          <td class="py-3 px-4">{{ drug.drug_name }}</td>
          <td class="py-3 px-4">Rs. {{ drug.quantity }}</td>
          <td class="py-3 px-4">Rs. {{ drug.amount }}</td>
        </tr>
      </tbody>
    </table>

    <div class="mt-3 text-right text-gray-600 text-sm font-bold">
      <p>
        Total Price : Rs. {{ selectedQuotationDetails?.total_amount || "0.00" }}
      </p>
    </div>

    <div class="mt-3 text-center">
      <button
        @click="closeModal"
        class="bg-blue text-white px-3 text-sm py-2 rounded hover:bg-darkBlue"
      >
        OK
      </button>
    </div>
  </Modal>
</template>
