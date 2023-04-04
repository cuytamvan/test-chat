<template>
  <div
    :class="[
      'py-3 px-5 flex gap-2 border-b last:border-b-0 items-center hover:bg-gray-50 border-l-2',
      'cursor-pointer transition-all duration-200',
      active ? 'border-l-purple-400' : 'border-l-transparent',
    ]"
  >
    <div class="w-10">
      <img src="/user.png" :alt="userName" class="w-8" />
    </div>

    <div class="flex-1">
      <span class="w-36 block text-sm font-medium text-gray-700 capitalize truncate">{{ userName }}</span>
      <p class="truncate overflow-hidden w-36 text-xs mt-1 text-gray-500">
        <span v-if="lastChat.length">{{ lastChat }}</span>
        <i v-else class="text-gray-400">Belum ada percakapan</i>
      </p>
    </div>
  </div>
</template>

<script lang="ts">
  import { defineComponent } from 'vue';

  export default defineComponent({
    props: {
      userId: {
        type: Number,
        required: true,
      },
      userName: {
        type: String,
        required: true,
      },
      lastChat: {
        type: String,
        default: '',
      },
      active: {
        type: Boolean,
        default: false,
      },
    },
    setup() {
      return {};
    },
  });
</script>
