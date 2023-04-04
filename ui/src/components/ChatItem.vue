<template>
  <div :class="['flex relative', sender && 'justify-end']">
    <span
      :class="[
        'absolute top-3 right-full w-0 h-0 border-8 border-transparent ',
        !sender ? 'right-full border-r-purple-400' : 'left-full border-l-purple-200',
      ]"
    ></span>

    <div
      :class="[
        'w-10/12 md:w-5/12  p-4 rounded-lg text-xs',
        !sender ? 'bg-purple-400 text-white' : 'border border-purple-200 bg-purple-100 text-purple-500',
      ]"
    >
      <slot />
    </div>
  </div>
</template>

<script lang="ts">
  import { defineComponent } from 'vue';

  export default defineComponent({
    props: {
      sender: {
        type: Boolean,
        default: false,
      },
    },
    setup() {},
  });
</script>
