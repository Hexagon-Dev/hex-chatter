<script setup>
import { ref, defineProps } from 'vue';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const props = defineProps({
  initialWidth: {
    type: Number,
    default: 300,
  },
  minWidth: {
    type: Number,
    default: 100,
  },
  maxWidth: {
    type: Number,
    default: 1000,
  },
});

const isResizing = ref(false);
const startX = ref(0);
const panelWidth = ref(props.initialWidth);

const handleResize = () => {
  if (isResizing.value) {
    const deltaX = event.clientX - startX.value;
    const newWidth = panelWidth.value + deltaX;

    // Ensure the new width is within the specified limits
    panelWidth.value = Math.min(
      Math.max(newWidth, props.minWidth),
      props.maxWidth
    );

    startX.value = event.clientX;
  }
};

const stopResize = () => {
  isResizing.value = false;

  document.removeEventListener('mousemove', handleResize);
  document.removeEventListener('mouseup', stopResize);
};

const startResize = (event) => {
  isResizing.value = true;
  startX.value = event.clientX;

  document.addEventListener('mousemove', handleResize);
  document.addEventListener('mouseup', stopResize);
};
</script>

<template>
  <div :style="{ width: panelWidth + 'px' }" class="relative overflow-hidden">
    <div class="content w-full h-full pr-2">
      <slot></slot>
    </div>
    <div
      @mousedown="startResize"
      class="absolute top-0 right-0 cursor-col-resize dark:bg-surface-800 bg-surface-100 w-2 h-full flex items-center"
    >
      <font-awesome-icon icon="fa-solid fa-grip-lines-vertical" class="text-xs w-full" />
    </div>
  </div>
</template>
