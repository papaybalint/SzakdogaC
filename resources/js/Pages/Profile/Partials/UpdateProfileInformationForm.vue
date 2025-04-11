<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    username: user.username,
    email: user.email,
    first_name: user.first_name,
    last_name: user.last_name,
    birth_place: user.birth_place,
    birth_date: user.birth_date,
    phone: user.phone,

});

const submit = () => {
    // Első betű nagybetűssé alakítása
    form.first_name = form.first_name.charAt(0).toUpperCase() + form.first_name.slice(1);
    form.last_name = form.last_name.charAt(0).toUpperCase() + form.last_name.slice(1);
    form.birth_place = form.birth_place.charAt(0).toUpperCase() + form.birth_place.slice(1);

    // Frissítés elküldése
    form.patch(route('profile.update'));
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
    setTimeout(() => {
        let input = event.target.value;

        // Csak a + jellel kezdődhet
        if (!input.startsWith('+')) {
            input = '+' + input.replace(/[^\d]/g, '');
        } else {
            input = '+' + input.slice(1).replace(/[^\d]/g, '');
        }

        // Legfeljebb 11 számjegy lehet a + után
        if (input.length > 12) {
            input = input.slice(0, 12);
        }

        event.target.value = input;
        form.phone = input; // frissítjük a v-model értékét is
    });
};

const onDateInput = (event) => {
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
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Profil információk
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Frissítse a fiókjának profilinformációit.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-6">
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
                    readonly
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
                    @input="onDateInput($event)"
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
                    readonly
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>


            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Mentés</PrimaryButton>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                        Mentve.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
