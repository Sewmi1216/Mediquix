<script setup>
import {
  UserCircleIcon,
  InboxStackIcon,
  DocumentTextIcon,
} from "@heroicons/vue/24/outline";
import { useRouter } from "vue-router";

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
  <div class="flex h-screen">
    <!-- Desktop Sidebar -->
    <aside
      class="hidden md:flex flex-col justify-between w-56 bg-white shadow-md p-6"
    >
      <!-- Top Content -->
      <div>
        <div class="text-darkBlue text-3xl font-extrabold italic mb-10">
          MediQuix
        </div>
        <nav class="space-y-1 text-[15px] font-medium text-gray-700">
          <RouterLink to="/pharmacy/view-prescriptions" v-slot="{ isActive }">
            <div
              :class="[
                'flex items-center gap-2 pl-3 py-2 border-l-4 transition',
                isActive
                  ? 'text-darkBlue font-semibold border-darkBlue bg-gray-100'
                  : 'text-gray-700 border-transparent hover:text-darkBlue hover:border-darkBlue',
              ]"
            >
              <InboxStackIcon class="w-6 h-6" />
              <span>Prescriptions</span>
            </div>
          </RouterLink>

          <RouterLink to="/pharmacy/view-quotations" v-slot="{ isActive }">
            <div
              :class="[
                'flex items-center gap-2 pl-3 py-2 border-l-4 transition',
                isActive
                  ? 'text-darkBlue font-semibold border-darkBlue bg-gray-100'
                  : 'text-gray-700 border-transparent hover:text-darkBlue hover:border-darkBlue',
              ]"
            >
              <DocumentTextIcon class="w-6 h-6" />
              <span>Quotations</span>
            </div>
          </RouterLink>
        </nav>
      </div>

      <div
        @click="logout"
        class="hover:text-darkBlue border-gray-400 border-t-2 p-3 flex items-center space-x-2 cursor-pointer"
      >
        <UserCircleIcon class="w-6 h-6" />
        <span class="text-sm font-medium">Profile</span>
      </div>
    </aside>

    <aside
      class="flex md:hidden flex-col justify-between items-center w-20 bg-white shadow-md p-4"
    >
      <div class="flex flex-col items-center mt-4">
        <div class="text-darkBlue text-sm font-extrabold italic mb-10">
          MediQuix
        </div>
        <RouterLink to="/pharmacy/view-prescriptions" v-slot="{ isActive }">
          <div
            :class="[
              'pl-3 py-2 border-l-4 transition pr-3',
              isActive
                ? 'text-darkBlue font-semibold border-darkBlue bg-gray-100'
                : 'text-gray-700 border-transparent hover:text-darkBlue hover:border-darkBlue',
            ]"
          >
            <InboxStackIcon
              class="w-6 h-6"
              :class="isActive ? 'text-darkBlue' : 'text-gray-600'"
            />
          </div>
        </RouterLink>

        <RouterLink to="/pharmacy/view-quotations" v-slot="{ isActive }">
          <div
            :class="[
              'pl-3 py-2 border-l-4 transition pr-3',
              isActive
                ? 'text-darkBlue font-semibold border-darkBlue bg-gray-100'
                : 'text-gray-700 border-transparent hover:text-darkBlue hover:border-darkBlue',
            ]"
          >
            <DocumentTextIcon
              class="w-6 h-6"
              :class="isActive ? 'text-darkBlue' : 'text-gray-600'"
            />
          </div>
        </RouterLink>
      </div>

      <div class="mb-4 border-t-2 pt-3 border-gray-400" @click="logout">
        <UserCircleIcon class="w-6 h-6 text-gray-600 hover:text-darkBlue" />
      </div>
    </aside>
  </div>
</template>
