<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    changeRequest: Object,
});
</script>

<template>
    <Head title="Edit Change Request" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('change-requests.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ← Back
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Change Request
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <p class="text-gray-500">Form placeholder.</p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
