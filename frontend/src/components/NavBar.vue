<script setup>
import {
  Bars3Icon,
  XMarkIcon,
  UserCircleIcon,
} from "@heroicons/vue/24/outline";
import { ref } from "vue";
import { useRouter } from "vue-router";

const menuOpen = ref(false);
const toggleMenu = () => {
  menuOpen.value = !menuOpen.value;
};

const router = useRouter();

const logout = async () => {
  try {
    const response = await fetch(`http://127.0.0.1:8000/api/logout`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${localStorage.getItem("access_token")}`,
      },
    });

    if (response.ok) {
      alert("Logged out successfully");
      router.push("/login");
    } else {
      const error = await response.json();
      alert("Logout failed: " + (error.message || "Unknown error"));
    }
  } catch (error) {
    alert("Logout error: " + error.message);
  }
};
</script>

<template>
  <!-- Navigation Bar -->
  <nav class="bg-white shadow-md px-6 py-4 flex items-center justify-between">
    <!-- Logo -->
    <div class="text-darkBlue text-2xl font-extrabold italic tracking-wide">
      MediQuix
    </div>

    <!-- Desktop Menu -->
    <ul
      class="hidden md:flex space-x-6 items-center text-sm font-medium text-gray-700"
    >
      <RouterLink to="/view-quotations" v-slot="{ isActive }"
        ><li
          :class="[
            'px-4 py-2 transition-colors duration-200 cursor-pointer',
            isActive
              ? 'bg-blue hover:bg-darkBlue text-white rounded-full'
              : 'hover:border-darkBlue',
          ]"
        >
          Quotations
        </li></RouterLink
      >
      <RouterLink to="/upload-prescription" v-slot="{ isActive }">
        <li
          :class="[
            'px-4 py-2 transition-colors duration-200 cursor-pointer',
            isActive
              ? 'bg-blue hover:bg-darkBlue text-white rounded-full'
              : 'hover:border-darkBlue',
          ]"
        >
          Upload Prescription
        </li></RouterLink
      >

      <div
        :class="[
          'pl-3 py-2 border-l-4 transition pr-3',
          isActive
            ? 'text-darkBlue font-semibold border-darkBlue bg-gray-100'
            : 'text-gray-700 border-transparent hover:text-darkBlue hover:border-darkBlue',
        ]"
      ></div>

      <li
        class="hover:text-darkBlue transition-colors duration-200 cursor-pointer"
        @click="logout"
      >
        <UserCircleIcon class="w-7 h-7" />
      </li>
    </ul>

    <div class="md:hidden">
      <button @click="toggleMenu" class="text-darkBlue">
        <component :is="menuOpen ? XMarkIcon : Bars3Icon" class="w-7 h-7" />
      </button>
    </div>
  </nav>

  <div
    v-if="menuOpen"
    class="md:hidden bg-lightBlue shadow-lg px-6 py-5 space-y-4 text-gray-800 font-medium text-base"
  >
    <div
      class="hover:text-darkBlue border-b border-gray-800 transition-colors cursor-pointer"
    >
      <RouterLink to="/upload-prescription"> Upload Prescription</RouterLink>
    </div>
    <div
      class="hover:text-darkBlue border-b border-gray-800 transition-colors cursor-pointer"
    >
      <RouterLink to="/view-quotations"> Quotations</RouterLink>
    </div>
    <div
      class="hover:text-darkBlue border-b border-gray-800 transition-colors cursor-pointer"
      @click="logout"
    >
      <span>Logout</span>
    </div>
  </div>
</template>
