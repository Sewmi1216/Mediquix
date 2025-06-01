<script setup>
import { reactive, ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import loginBg from "@/assets/images/login3.jpg";

const router = useRouter();
const errors = ref([]);

const form = reactive({
  name: "",
  email: "",
  password: "",
  address: "",
  dob: "",
  contact_number: "",
  user_type: "customer",
});

const today = new Date().toISOString().split("T")[0];
const maxDate = today;

const registerUser = async (e) => {
  e.preventDefault();

  try {
    const response = await axios.post(
      "http://127.0.0.1:8000/api/register",
      form
    );
    console.log("Registration successful:", response.data);
    router.push("/login");
  } catch (error) {
    console.error("Registration error:", error.response?.data || error.message);
    errors.value.push("Registration failed. Please try again.");
  }
};
</script>

<template>
  <div
    class="min-h-screen bg-cover bg-center flex items-center justify-center px-4 py-8"
    :style="`background-image: url(${loginBg})`"
  >
    <section
      class="backdrop-blur-md bg-white/40 rounded-xl shadow-2xl w-full max-w-2xl p-9 md:p-10"
    >
      <form class="space-y-5" @submit="registerUser">
        <h2
          class="text-xl md:text-2xl font-semibold text-center mb-4 text-gray-800"
        >
          Create an Account
        </h2>

        <!-- Name -->
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1"
            >Name</label
          >
          <input
            v-model="form.name"
            type="text"
            id="name"
            class="w-full border rounded-md px-3 py-2"
            required
          />
        </div>

        <!-- Email -->
        <div>
          <label
            for="email"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Email</label
          >
          <input
            v-model="form.email"
            type="email"
            id="email"
            pattern="^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$"
            class="w-full border rounded-md px-3 py-2"
            required
          />
        </div>

        <!-- Address -->
        <div>
          <label
            for="address"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Address</label
          >
          <textarea
            v-model="form.address"
            id="address"
            rows="3"
            class="w-full border rounded-md px-3 py-2"
            required
          ></textarea>
        </div>

        <!-- Contact Number -->
        <div>
          <label
            for="contact_number"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Contact Number</label
          >
          <input
            v-model="form.contact_number"
            type="text"
            id="contact_number"
            class="w-full border rounded-md px-3 py-2"
            pattern="^\+?[0-9]{7,15}$"
            required
          />
        </div>

        <!-- Date of Birth -->
        <div>
          <label for="dob" class="block text-sm font-medium text-gray-700 mb-1"
            >Date of Birth</label
          >
          <input
            v-model="form.dob"
            type="date"
            id="dob"
            :max="maxDate"
            class="w-full border rounded-md px-3 py-2"
            required
          />
        </div>

        <!-- Password -->
        <div>
          <label
            for="password"
            class="block text-sm font-medium text-gray-700 mb-1"
            >Password</label
          >
          <input
            v-model="form.password"
            type="password"
            pattern=".{6,}"
            id="password"
            class="w-full border rounded-md px-3 py-2"
            required
          />
        </div>

        <!-- Submit Button -->
        <div class="text-center">
          <button
            type="submit"
            class="w-1/4 bg-blue text-white py-2 rounded-md hover:bg-darkBlue transition duration-200"
          >
            Signup
          </button>
        </div>

        <!-- Login Link -->
        <div class="text-center text-sm">
          Already have an account?
          <router-link
            to="/login"
            class="text-darkBlue font-semibold hover:underline ml-1"
            >Login</router-link
          >
        </div>
      </form>
    </section>
  </div>
</template>
