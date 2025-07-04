<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeftIcon, PencilIcon, TrashIcon } from 'lucide-vue-next';

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
        title: 'View',
        href: `/magazine-users/${props.magazineUser.id}`,
    },
];

const deleteMagazineUser = () => {
    if (confirm('Are you sure you want to delete this magazine user?')) {
        router.delete(`/magazine-users/${props.magazineUser.id}`);
    }
};
</script>

<template>
    <Head title="View Magazine User" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Magazine User Details</CardTitle>
                    <div class="flex gap-2">
                        <Link href="/magazine-users">
                            <Button variant="outline">
                                <ArrowLeftIcon class="h-4 w-4 mr-2" />
                                Back to List
                            </Button>
                        </Link>
                        <Link :href="`/magazine-users/${props.magazineUser.id}/edit`">
                            <Button variant="outline">
                                <PencilIcon class="h-4 w-4 mr-2" />
                                Edit
                            </Button>
                        </Link>
                        <Button variant="destructive" @click="deleteMagazineUser">
                            <TrashIcon class="h-4 w-4 mr-2" />
                            Delete
                        </Button>
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Name</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ props.magazineUser.name }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Email</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ props.magazineUser.email }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Created At</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ new Date(props.magazineUser.created_at).toLocaleDateString() }}</p>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Updated At</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ new Date(props.magazineUser.updated_at).toLocaleDateString() }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>