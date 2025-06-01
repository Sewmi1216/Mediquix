<script setup>
import { ref } from "vue";
import NavBar from "@/components/NavBar.vue";

const deliveryAddress = ref("");
const deliveryDate = ref("");
const deliveryTime = ref("");
const note = ref("");
const images = ref([]);

const errorMessages = ref(null);
const successMessage = ref(null);

const today = new Date().toISOString().split("T")[0];
const minDate = today;

const handleFileUpload = (event) => {
  const files = event.target.files;
  if (files.length > 5) {
    alert("You can only upload up to 5 images.");
    event.target.value = null;
    images.value = [];
    return;
  }
  images.value = Array.from(files);
};

const handleSubmit = async (e) => {
  e.preventDefault();

  errorMessages.value = null;
  successMessage.value = null;

  const formData = new FormData();
  formData.append("delivery_address", deliveryAddress.value);
  formData.append("delivery_date", deliveryDate.value);
  formData.append("delivery_time", deliveryTime.value);
  formData.append("note", note.value || "");

  images.value.forEach((file, idx) => {
    formData.append("images[]", file);
  });

  try {
    const token = localStorage.getItem("access_token");
    console.log("Token from localStorage:", token);

    const response = await fetch(
      "http://127.0.0.1:8000/api/add-prescriptions",
      {
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
        body: formData,
      }
    );

    const data = await response.json();

    if (!response.ok) {
      errorMessages.value = data.errors || { form: ["Unknown error"] };
    } else {
      successMessage.value =
        data.message || "Prescription uploaded successfully.";
      deliveryAddress.value = "";
      deliveryDate.value = "";
      deliveryTime.value = "";
      note.value = "";
      images.value = [];

      document.getElementById("images").value = null;
    }
  } catch (error) {
    errorMessages.value = { form: ["Network error or server unavailable."] };
  }
};
</script>

<template>
  <NavBar />

  <div
    class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4 py-10 bg-gray-50"
  >
    <section
      class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6 md:p-10"
    >
      <form
        class="space-y-6"
        @submit="handleSubmit"
        enctype="multipart/form-data"
        novalidate
      >
        <h2 class="text-2xl font-semibold text-center text-gray-800">
          Upload Prescription
        </h2>

        <div v-if="errorMessages" class="mb-4 text-red-600">
          <ul>
            <li v-for="(msgs, key) in errorMessages" :key="key">
              <span v-if="Array.isArray(msgs)">
                <span v-for="msg in msgs" :key="msg">{{ msg }}</span>
              </span>
              <span v-else>{{ msgs }}</span>
            </li>
          </ul>
        </div>

        <!-- Delivery Address -->
        <div>
          <label
            for="deliveryAddress"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Delivery address
          </label>
          <textarea
            id="deliveryAddress"
            rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            v-model="deliveryAddress"
          ></textarea>
        </div>

        <!-- Delivery Date -->
        <div>
          <label
            for="deliveryDate"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Delivery date
          </label>
          <input
            type="date"
            id="deliveryDate"
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            v-model="deliveryDate"
            :min="minDate"
          />
        </div>

        <!-- Delivery Time -->
        <div>
          <label
            for="timeSlot"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Delivery time
          </label>
          <select
            id="timeSlot"
            class="w-full border text-sm border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
            v-model="deliveryTime"
          >
            <option value="" disabled>Select a time slot</option>
            <option>08:00-10:00</option>
            <option>10:00-12:00</option>
            <option>12:00-14:00</option>
            <option>14:00-16:00</option>
            <option>16:00-18:00</option>
            <option>18:00-20:00</option>
          </select>
        </div>

        <!-- File Upload -->
        <div>
          <label
            for="images"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Upload up to 5 prescription images
          </label>
          <input
            type="file"
            id="images"
            name="images[]"
            accept="image/*"
            multiple
            class="w-full cursor-pointer text-sm border border-gray-300 rounded-md px-3 py-2 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-blue-600 file:text-gray-400 hover:file:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="handleFileUpload"
          />
          <p class="text-xs text-gray-500 mt-1">
            Max 5 images. Only image files allowed.
          </p>
        </div>

        <!-- Note -->
        <div>
          <label
            for="note"
            class="block text-sm font-medium text-gray-700 mb-1"
          >
            Note
          </label>
          <textarea
            id="note"
            rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            v-model="note"
          ></textarea>
        </div>
        <div
          v-if="successMessage"
          class="mb-4 text-green-600 font-semibold text-center"
        >
          {{ successMessage }}
        </div>
        <!-- Submit -->
        <div class="text-center">
          <button
            type="submit"
            class="w-1/4 bg-blue text-white py-2 rounded-md hover:bg-darkBlue transition duration-200"
          >
            Upload
          </button>
        </div>
      </form>
    </section>
  </div>
</template>
