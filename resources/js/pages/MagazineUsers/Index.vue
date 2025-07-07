<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { TrashIcon, PencilIcon, EyeIcon, PlusIcon, CodeIcon, CopyIcon } from 'lucide-vue-next';
import { ref } from 'vue';

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

const showEmbedCode = ref(false);
const embedCode = ref('');

const generateEmbedCode = () => {
    const baseUrl = window.location.origin;
    embedCode.value = `<!-- Magazine Subscription Form Widget -->
<div id="magazine-subscription-form"
     data-primary-color="#3b82f6"
     data-success-color="#10b981"
     data-error-color="#ef4444"
     data-border-radius="8px"
     data-font-family="system-ui, -apple-system, sans-serif">
</div>
<script src="${baseUrl}/js/magazine-embed.js"><\/script>
<!-- End Magazine Subscription Form Widget -->`;
    showEmbedCode.value = true;
};


const copyEmbedCode = async () => {
    try {
        await navigator.clipboard.writeText(embedCode.value);
        alert('Embed code copied to clipboard!');
    } catch (err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy embed code. Please copy manually.');
    }
};

</script>

<template>
    <Head title="Magazine Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <!-- Embed Code Generator Card -->
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <CodeIcon class="h-5 w-5" />
                        Embed Code Generator
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Generate an embed code to add a magazine subscription form to any external website.
                        </p>

                        <div class="flex gap-2">
                            <Button @click="generateEmbedCode">
                                <CodeIcon class="h-4 w-4 mr-2" />
                                Generate Embed Code
                            </Button>
                            <Button v-if="showEmbedCode" @click="copyEmbedCode" variant="outline">
                                <CopyIcon class="h-4 w-4 mr-2" />
                                Copy Code
                            </Button>
                        </div>

                        <div v-if="showEmbedCode" class="space-y-2">
                            <label class="text-sm font-medium">Embed Code (Copy and paste into your website):</label>
                            <textarea
                                :value="embedCode"
                                readonly
                                class="w-full h-32 p-3 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 text-sm font-mono resize-none"
                                @click="$event.target && $event.target.select()"
                            />
                            <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                                <p>• This code creates a responsive subscription form</p>
                                <p>• You can customize colors and styling by modifying the data attributes</p>
                                <p>• The form will automatically submit to your application's API</p>
                            </div>
                        </div>

                    </div>
                </CardContent>
            </Card>

            <!-- Magazine Users Table Card -->
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
