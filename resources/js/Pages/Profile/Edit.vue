<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
</script>

<template>
<div class="py-12">
    <div class="mx-auto max-w-2xl space-y-6 sm:px-6 lg:px-8">
        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-6 mx-auto">
            <UpdateProfileInformationForm class="w-full" />
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-6 mx-auto">
            <UpdatePasswordForm class="w-full" />
        </div>

        <div class="bg-white p-4 shadow sm:rounded-lg sm:p-6 mx-auto">
            <DeleteUserForm class="w-full" />
        </div>
    </div>
</div>
</template>
