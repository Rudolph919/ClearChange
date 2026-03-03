<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    title_proposed: '',
    description_proposed: '',
});

const submit = () => {
    form.post(route('change-requests.store'));
};
</script>

<template>
    <Head title="New Change Request" />

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
                    New Change Request
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form
                        class="p-6"
                        @submit.prevent="submit"
                    >
                        <p class="mb-4 text-sm text-gray-600">
                            Enter the changes you want to propose. For new requests, these are the values you're requesting.
                        </p>

                        <div>
                            <InputLabel
                                for="title_proposed"
                                value="Title"
                            />
                            <TextInput
                                id="title_proposed"
                                v-model="form.title_proposed"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                autofocus
                                placeholder="e.g. Update employee salary"
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.title_proposed"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel
                                for="description_proposed"
                                value="Description (optional)"
                            />
                            <textarea
                                id="description_proposed"
                                v-model="form.description_proposed"
                                rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Describe the change you are requesting..."
                            />
                            <InputError
                                class="mt-2"
                                :message="form.errors.description_proposed"
                            />
                        </div>

                        <div class="mt-6 flex items-center gap-4">
                            <Link
                                :href="route('change-requests.index')"
                                class="text-sm text-gray-600 underline hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                            >
                                Create Change Request
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
