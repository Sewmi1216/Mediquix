<script setup>
import NavBar from "@/components/NavBar.vue";
import { DocumentTextIcon, XMarkIcon } from "@heroicons/vue/24/outline";
import { ref, onMounted } from "vue";

const selectedQuotation = ref(null);
const showModal = ref(false);
const selectedQuotationDetails = ref(null);
const quotations = ref([]);

const openModal = async (quotation) => {
  selectedQuotation.value = quotation;
  await fetchQuotationDetails(quotation.id);
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  selectedQuotation.value = null;
  selectedQuotationDetails.value = null;
};

const showApproveModal = ref(false);
const openApproveModal = (quotation) => {
  selectedQuotation.value = quotation;
  showApproveModal.value = true;
};
const closeApproveModal = () => {
  showApproveModal.value = false;
  selectedQuotation.value = null;
};
const acceptQuotation = async (id) => {
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/quotations/${id}/accept`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    if (response.ok) {
      console.log("Quotation accepted");
      await fetchQuotations();
    } else {
      console.error("Failed to accept quotation");
    }
  } catch (error) {
    console.error("Error accepting quotation", error);
  } finally {
    closeApproveModal();
  }
};
const rejectQuotation = async (id) => {
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/quotations/${id}/reject`,
      {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    if (response.ok) {
      console.log("Quotation rejected");
      await fetchQuotations();
    } else {
      console.error("Failed to reject quotation");
    }
  } catch (error) {
    console.error("Error rejecting quotation", error);
  } finally {
    closeApproveModal();
  }
};
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

async function fetchQuotations(quotationId) {
  try {
    const response = await fetch("http://127.0.0.1:8000/api/quotations", {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("access_token")}`,
      },
    });
    const data = await response.json();
    quotations.value = data.data;
  } catch (error) {
    console.error("Failed to fetch quotations", error);
  }
}
async function fetchQuotationDetails(quotationId) {
  try {
    const response = await fetch(
      `http://127.0.0.1:8000/api/quotations/${quotationId}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    const data = await response.json();
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
  <NavBar />
  <div class="flex items-center justify-center px-4 py-10 bg-gray-50">
    <section
      class="bg-white rounded-xl shadow-2xl w-full max-w-3xl p-6 md:p-10"
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
              <th class="py-3 px-4 text-left">Quotation ID</th>
              <th class="py-3 px-4 text-left">Total Price</th>
              <th class="py-3 px-4 text-left">View</th>
              <th class="py-3 px-4 text-left">Accept / Reject</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-gray-700">
            <tr
              v-for="(quotation, index) in quotations"
              :key="quotation.quotation_id"
              class="hover:bg-gray-50 transition"
            >
              <td class="py-3 px-4">{{ index + 1 }}</td>
              <td class="py-3 px-4">{{ quotation.id }}</td>
              <td class="py-3 px-4">Rs. {{ quotation.total_amount }}</td>
              <td class="py-3 px-4">
                <button
                  @click="openModal(quotation)"
                  class="text-blue-600 hover:text-blue-800"
                >
                  <DocumentTextIcon class="w-5 h-5" />
                </button>
              </td>
              <td class="py-3 px-4">
                <button
                  @click="openApproveModal(quotation)"
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
  <!-- Modal -->
  <div
    v-if="showModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg relative">
      <button
        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900"
        @click="closeModal"
      >
        <XMarkIcon class="w-6 h-6" />
      </button>
      <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Quotation Details
      </h3>
      <p class="text-sm">ID : {{ selectedQuotation?.id }}</p>

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
          Total Price : Rs.
          {{ selectedQuotationDetails?.total_amount || "0.00" }}
        </p>
      </div>
      <div class="mt-3 text-center">
        <button
          @click="closeModal"
          class="bg-blue text-white px-3 text-sm py-2 rounded hover:bg-darkBlue"
        >
          Close
        </button>
      </div>
    </div>
  </div>
  <div
    v-if="showApproveModal"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
  >
    <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg relative">
      <button
        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900"
        @click="closeApproveModal"
      >
        <XMarkIcon class="w-6 h-6" />
      </button>

      <p class="text-base">Do you want to accept or reject this quotation?</p>

      <div class="mt-3 text-center flex justify-center space-x-4">
        <button
          @click="acceptQuotation(selectedQuotation?.id)"
          class="bg-green-500 text-white px-3 text-sm py-2 rounded hover:bg-green-800"
        >
          Accept
        </button>
        <button
          @click="rejectQuotation(selectedQuotation?.id)"
          class="bg-red-500 text-white px-3 text-sm py-2 rounded hover:bg-red-800"
        >
          Reject
        </button>
      </div>
    </div>
  </div>
</template>
