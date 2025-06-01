<script setup>
import { XMarkIcon } from "@heroicons/vue/24/solid";
defineProps(["show"]);
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
  >
    <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-lg relative">
      <button
        class="absolute top-3 right-3 text-gray-600 hover:text-gray-900"
        @click="$emit('close')"
      >
        <XMarkIcon class="w-6 h-6" />
      </button>

      <div>
        <slot />
      </div>
    </div>
  </div>
</template>
