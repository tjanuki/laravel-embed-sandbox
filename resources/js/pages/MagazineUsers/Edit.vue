<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeftIcon } from 'lucide-vue-next';

interface MagazineUser {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    magazineUser: MagazineUser;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Magazine Users',
        href: '/magazine-users',
    },
    {
        title: 'Edit',
        href: `/magazine-users/${props.magazineUser.id}/edit`,
    },
];

const form = useForm({
    name: props.magazineUser.name,
    email: props.magazineUser.email,
});

const submit = () => {
    form.put(`/magazine-users/${props.magazineUser.id}`);
};
</script>

<template>
    <Head title="Edit Magazine User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Edit Magazine User</CardTitle>
                    <Link href="/magazine-users">
                        <Button variant="outline">
                            <ArrowLeftIcon class="h-4 w-4 mr-2" />
                            Back to List
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <Label for="name">Name</Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                :class="{ 'border-red-500': form.errors.name }"
                                required
                            />
                            <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <div>
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                :class="{ 'border-red-500': form.errors.email }"
                                required
                            />
                            <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                                {{ form.errors.email }}
                            </div>
                        </div>


                        <Button type="submit" :disabled="form.processing">
                            Update Magazine User
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>