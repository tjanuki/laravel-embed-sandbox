<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { TrashIcon, PencilIcon, EyeIcon, PlusIcon } from 'lucide-vue-next';

interface MagazineUser {
    id: number;
    name: string;
    email: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    magazineUsers: {
        data: MagazineUser[];
        links: any[];
        meta: any;
    };
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
];

const deleteMagazineUser = (id: number) => {
    if (confirm('Are you sure you want to delete this magazine user?')) {
        router.delete(`/magazine-users/${id}`);
    }
};
</script>

<template>
    <Head title="Magazine Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between">
                    <CardTitle>Magazine Users</CardTitle>
                    <Link href="/magazine-users/create">
                        <Button>
                            <PlusIcon class="h-4 w-4 mr-2" />
                            Add Magazine User
                        </Button>
                    </Link>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-200 dark:border-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Name</th>
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Email</th>
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Created At</th>
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in props.magazineUsers.data" :key="user.id">
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">{{ user.name }}</td>
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">{{ user.email }}</td>
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">
                                        <div class="flex gap-2">
                                            <Link :href="`/magazine-users/${user.id}`">
                                                <Button size="sm" variant="outline">
                                                    <EyeIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Link :href="`/magazine-users/${user.id}/edit`">
                                                <Button size="sm" variant="outline">
                                                    <PencilIcon class="h-4 w-4" />
                                                </Button>
                                            </Link>
                                            <Button size="sm" variant="destructive" @click="deleteMagazineUser(user.id)">
                                                <TrashIcon class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>