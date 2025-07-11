<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <img width="42px" :src="'/logo/LogoSintesaOnly.svg'"
        alt="SINTESA">
</template>
