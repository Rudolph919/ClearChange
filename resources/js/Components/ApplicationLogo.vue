<script setup>
defineProps({
    class: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <span :class="['text-lg font-semibold tracking-tight text-gray-800', $props.class]">
        ClearChange
    </span>
</template>
