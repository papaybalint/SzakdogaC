<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
</script>

<template>
    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
            >
                <UpdateProfileInformationForm class="max-w-xl" />
            </div>

            <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
            >
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <!-- <div
                class="bg-white p-4 shadow sm:rounded-lg sm:p-8"
            >
                <DeleteUserForm class="max-w-xl" />
            </div> -->
        </div>
    </div>
</template>
