<script setup>
import SideNavBar from "@/components/SideNavBar.vue";
import { ArrowLeftCircleIcon } from "@heroicons/vue/24/solid";
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();

const router = useRouter();
const prescriptionId = route.params.id;
console.log("prescription: ", prescriptionId);

const getUser = async () => {
  try {
    const response = await axios.get(
      `http://127.0.0.1:8000/api/pharmacy/get-user/${prescriptionId}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    const user = response.data.user_id;
    console.log("userId: ", response.data.user_id);
    return user;
  } catch (error) {
    console.error("Error fetching user:", error);
  }
};
const thumbnails = ref([]);
const selectedImage = ref(null);

const fetchPrescriptionImages = async () => {
  try {
    const response = await axios.get(
      `http://127.0.0.1:8000/api/pharmacy/get-images/${prescriptionId}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    thumbnails.value = response.data.map(
      (imgPath) => `http://127.0.0.1:8000/storage/${imgPath}`
    );
    selectedImage.value = thumbnails.value[0] || null;
    console.log(response.data);
  } catch (error) {
    console.error("Error fetching images:", error);
  }
};

onMounted(() => {
  fetchPrescriptionImages();
  getUser();
});

const drugName = ref("");
const drugQuantity = ref("");
const drugList = ref([]);

const addDrug = () => {
  if (!drugName.value || !drugQuantity.value) return;

  const parts = drugQuantity.value.split("x").map((p) => p.trim());
  if (parts.length !== 2 || isNaN(parts[0]) || isNaN(parts[1])) {
    alert("Quantity must be in format: price x quantity (10 x 5)");
    return;
  }

  const price = parseFloat(parts[0]);
  const quantity = parseInt(parts[1]);

  drugList.value.push({
    name: drugName.value,
    quantity: `${price} x ${quantity}`,
    total: price * quantity,
  });

  drugName.value = "";
  drugQuantity.value = "";
};

const totalAmount = computed(() =>
  drugList.value.reduce((sum, item) => sum + item.total, 0)
);
const sendQuotation = async () => {
  if (drugList.value.length === 0) {
    alert("Please add at least one drug before sending the quotation.");
    return;
  }

  try {
    const user = await getUser();
    console.log("user_id:", user);
    const quotationPayload = {
      user_id: user,
      prescription_id: prescriptionId,
      total_amount: totalAmount.value,
      status: "Pending",
      drugs: drugList.value.map((drug) => ({
        drug_name: drug.name,
        quantity: drug.quantity,
        amount: drug.total,
      })),
    };
    console.log("Sending quotationPayload:", quotationPayload);

    const response = await axios.post(
      "http://127.0.0.1:8000/api/pharmacy/create-quotation",
      quotationPayload,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      }
    );
    alert("Quotation sent successfully!");
    drugList.value = [];
    router.push("/pharmacy/view-quotations");
  } catch (error) {
    console.error("Failed to send quotation:", error);
    alert("Failed to send quotation. Please try again.");
  }
};
</script>

<template>
  <div class="flex min-h-screen bg-gray-100">
    <SideNavBar />

    <!-- Main content -->
    <div class="flex-1 p-8">
      <section
        class="bg-white flex rounded-xl shadow-2xl w-full max-w-full p-6 md:p-10 mx-auto"
      >
        <div class="w-1/3 flex flex-col justify-between items-center h-full">
          <div class="w-full flex flex-col items-center">
            <img
              :src="selectedImage"
              alt="Main Drug"
              class="w-full h-80 rounded-lg mb-4 object-cover"
            />
            <div class="flex gap-2 flex-wrap justify-center">
              <img
                v-for="(thumb, index) in thumbnails"
                :key="index"
                :src="thumb"
                alt="Drug Thumbnail"
                class="w-14 h-14 object-cover rounded-md border border-gray-300 cursor-pointer hover:scale-105 transition"
                @click="selectedImage = thumb"
              />
            </div>
          </div>

          <div class="w-full flex justify-start mt-4 text-gray-700">
            <RouterLink
              to="/pharmacy/view-prescriptions"
              class="flex items-center text-blue-600 hover:underline text-sm"
            >
              <ArrowLeftCircleIcon class="h-5 w-5 mr-1" />
              Back
            </RouterLink>
          </div>
        </div>

        <div class="w-2/3 px-6 flex flex-col justify-start overflow-x-auto">
          <div class="overflow-x-auto">
            <table
              class="min-w-full text-sm table-auto border border-gray-200 rounded-lg shadow-sm"
            >
              <thead class="bg-blue text-white">
                <tr>
                  <th class="py-3 px-4 text-left">Drug</th>
                  <th class="py-3 px-4 text-left">Quantity</th>
                  <th class="py-3 px-4 text-left">Amount</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr
                  v-for="(item, index) in drugList"
                  :key="index"
                  class="hover:bg-gray-50 transition"
                >
                  <td class="py-3 px-4">{{ item.name }}</td>
                  <td class="py-3 px-4">Rs. {{ item.quantity }}</td>
                  <td class="py-3 px-4">Rs. {{ item.total.toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="grid grid-cols-3 text-sm font-bold text-gray-700 mt-4">
              <div></div>
              <div class="text-right pr-32">Total:</div>
              <div class="text-left pl-8">Rs. {{ totalAmount.toFixed(2) }}</div>
            </div>
          </div>
          <div class="m-5 p-2 flex justify-end">
            <form @submit.prevent="addDrug" class="w-1/2">
              <div class="mb-4 flex items-center justify-between">
                <label
                  for="drug"
                  class="text-sm font-medium text-gray-700 w-1/3 text-right pr-3"
                >
                  Drug
                </label>
                <input
                  type="text"
                  v-model="drugName"
                  class="w-2/3 border border-gray-300 rounded-md px-3 py-2"
                  required
                />
              </div>

              <div class="mb-4 flex items-center justify-between">
                <label
                  for="quantity"
                  class="text-sm font-medium text-gray-700 w-1/3 text-right pr-3"
                >
                  Quantity
                </label>
                <input
                  type="text"
                  v-model="drugQuantity"
                  class="w-2/3 border border-gray-300 rounded-md px-3 py-2"
                  required
                />
              </div>

              <div class="text-right">
                <button
                  type="submit"
                  class="w-1/3 bg-blue text-white py-2 rounded-md hover:bg-darkBlue transition duration-200"
                >
                  Add
                </button>
              </div>
            </form>
          </div>

          <hr class="text-blue mb-6 border-2" />
          <div class="text-right pr-7">
            <button
              type="button"
              @click="sendQuotation"
              class="w-1/4 bg-blue text-white py-2 rounded-md hover:bg-darkBlue transition duration-200"
            >
              Send Quotation
            </button>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>
