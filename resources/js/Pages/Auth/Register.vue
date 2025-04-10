<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    first_name:'',
    last_name:'',
    birth_place:'',
    birth_date:'',
    phone:'',

});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const today = () => {
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    
    var maxDate = year + '-' + month + '-' + day;
    return maxDate;
}

const onPhoneInput = (event) => {
    setTimeout(()=>{    // ??? valamiért enélkül nem megy
        event.target.value = String(event.target.value).replace(
        /[^\+\d\s]+/g,  // ami nem szám, szóköz vagy plusz azt törli
        ""
    );
    })
};

const onDateInpit = (event) => {
    if(event.target.value != ""){
        var date = new Date(event.target.value);
        var dtToday = new Date();
        if(date.getTime() > dtToday.getTime()){
            setTimeout(()=>{
            event.target.value = today();
            })
        }
    }
}
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="username" value="Felhasználónév" />

                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.username"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <div class="mt-4">
                <InputLabel for="first_name" value="Vezetéknév" />

                <TextInput
                    id="first_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.first_name"
                    required
                    autocomplete="first_name"
                    style="text-transform: capitalize;"
                />

                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="last_name" value="Keresztnév" />

                <TextInput
                    id="last_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.last_name"
                    required
                    autocomplete="last_name"
                    style="text-transform: capitalize;"
                />

                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>

            <div class="mt-4">
                <InputLabel for="birth_place" value="Születési hely" />

                <TextInput
                    id="birth_place"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.birth_place"
                    required
                    autocomplete="birth_place"
                    style="text-transform: capitalize;"
                />

                <InputError class="mt-2" :message="form.errors.birth_place" />
            </div>

            <div class="mt-4">
                <InputLabel for="birth_date" value="Születési idő" />

                <TextInput
                    id="birth_date"
                    type="date"
                    class="mt-1 block w-full"
                    v-model="form.birth_date"
                    required
                    autocomplete="birth_date"
                    :max="today()"
                    @input="onDateInpit($event)"
                />

                <InputError class="mt-2" :message="form.errors.birth_date" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Telefonszám" />

                <TextInput
                    id="phone"
                    type="tel"
                    inputmode="numeric"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    placeholder="például: +36207367812"
                    required
                    autocomplete="phone"
                    @input="onPhoneInput($event)"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Jelszó" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Jelszó megerősítése"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Van már fiókja?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Regisztráció
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
