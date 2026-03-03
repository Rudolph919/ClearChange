<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    changeRequest: Object,
});

function getValuesFromItems() {
    const items = props.changeRequest.items || [];
    const titleItem = items.find((i) => i.field_name === 'title');
    const descItem = items.find((i) => i.field_name === 'description');

    const proposedTitle = titleItem?.new_value ?? props.changeRequest.title ?? '';
    const proposedDesc = descItem?.new_value ?? props.changeRequest.description ?? '';

    // Current = what we're changing from (old_value). For new CRs with no old_value, show the
    // existing proposal as "current" so the user sees their draft content; Proposed = where to revise.
    return {
        title_current: titleItem?.old_value ?? proposedTitle,
        title_proposed: proposedTitle,
        description_current: descItem?.old_value ?? proposedDesc,
        description_proposed: proposedDesc,
    };
}

const form = useForm(getValuesFromItems());

const submit = () => {
    form.put(route('change-requests.update', props.changeRequest.id));
};
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
                    <form
                        class="p-6"
                        @submit.prevent="submit"
                    >
                        <div>
                            <InputLabel
                                for="title_current"
                                value="Title"
                            />
                            <p class="text-sm text-gray-500">
                                Current (what exists) → Proposed (what you want)
                            </p>
                            <div class="mt-2 flex gap-4">
                                <TextInput
                                    id="title_current"
                                    v-model="form.title_current"
                                    type="text"
                                    class="block flex-1"
                                    placeholder="Current value (optional)"
                                    autofocus
                                />
                                <span class="self-center text-gray-400">→</span>
                                <TextInput
                                    id="title_proposed"
                                    v-model="form.title_proposed"
                                    type="text"
                                    class="block flex-1"
                                    placeholder="Proposed value"
                                />
                            </div>
                            <InputError
                                class="mt-2"
                                :message="form.errors.title_proposed"
                            />
                        </div>

                        <div class="mt-4">
                            <InputLabel
                                for="description_current"
                                value="Description"
                            />
                            <p class="text-sm text-gray-500">
                                Current (what exists) → Proposed (what you want)
                            </p>
                            <div class="mt-2 flex flex-col gap-2 sm:flex-row sm:gap-4">
                                <textarea
                                    id="description_current"
                                    v-model="form.description_current"
                                    rows="3"
                                    class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Current (optional)"
                                />
                                <span class="hidden self-center text-gray-400 sm:inline">→</span>
                                <textarea
                                    id="description_proposed"
                                    v-model="form.description_proposed"
                                    rows="3"
                                    class="block flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Proposed (optional)"
                                />
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                At least one proposed value is required
                            </p>
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
                                Update Change Request
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
