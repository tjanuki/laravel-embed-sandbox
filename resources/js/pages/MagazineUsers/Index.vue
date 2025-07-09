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
    source_data?: {
        website?: string;
        url?: string;
        embed_type?: string;
        referrer?: string;
        user_agent?: string;
        ip_address?: string;
        timestamp?: string;
    };
    created_at: string;
    updated_at: string;
}

interface Props {
    magazineUsers: {
        data: MagazineUser[];
        links?: any[];
        meta?: {
            total?: number;
            current_page?: number;
            per_page?: number;
            last_page?: number;
            from?: number;
            to?: number;
        };
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
const embedType = ref('widget'); // 'widget' or 'iframe'

const generateEmbedCode = () => {
    const baseUrl = window.location.origin;
    
    if (embedType.value === 'iframe') {
        embedCode.value = `<!-- Magazine Subscription Form Iframe -->
<iframe src="${baseUrl}/embed/magazine-subscription"
        width="100%"
        height="400"
        frameborder="0"
        style="border: none; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
        title="Magazine Subscription Form">
</iframe>
<!-- End Magazine Subscription Form Iframe -->`;
    } else {
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
    }
    
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

const getEmbedTypeCount = (type: string) => {
    return props.magazineUsers.data.filter(user => 
        user.source_data && user.source_data.embed_type === type
    ).length;
};

const getTopSourceWebsites = () => {
    const websiteStats = props.magazineUsers.data.reduce((acc, user) => {
        if (user.source_data && user.source_data.website) {
            const website = user.source_data.website;
            acc[website] = (acc[website] || 0) + 1;
        }
        return acc;
    }, {} as Record<string, number>);

    return Object.entries(websiteStats)
        .map(([name, count]) => ({ name, count }))
        .sort((a, b) => b.count - a.count);
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

                        <div class="space-y-3">
                            <div class="flex items-center gap-4">
                                <label class="text-sm font-medium">Embed Type:</label>
                                <div class="flex gap-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            v-model="embedType"
                                            value="widget"
                                            class="text-blue-600"
                                        />
                                        <span class="text-sm">Widget</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="radio"
                                            v-model="embedType"
                                            value="iframe"
                                            class="text-blue-600"
                                        />
                                        <span class="text-sm">Iframe</span>
                                    </label>
                                </div>
                            </div>
                            
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
                                <p v-if="embedType === 'widget'">• This code creates a responsive subscription form</p>
                                <p v-if="embedType === 'widget'">• You can customize colors and styling by modifying the data attributes</p>
                                <p v-if="embedType === 'iframe'">• This iframe provides a secure, isolated subscription form</p>
                                <p v-if="embedType === 'iframe'">• You can adjust the width and height attributes as needed</p>
                                <p>• The form will automatically submit to your application's API</p>
                            </div>
                        </div>

                    </div>
                </CardContent>
            </Card>

            <!-- Analytics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">Total Subscriptions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.magazineUsers.meta?.total || props.magazineUsers.data.length }}</div>
                        <p class="text-xs text-gray-500">All time</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">Widget Subscriptions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ getEmbedTypeCount('widget') }}</div>
                        <p class="text-xs text-gray-500">Via embed widget</p>
                    </CardContent>
                </Card>
                
                <Card>
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">Iframe Subscriptions</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ getEmbedTypeCount('iframe') }}</div>
                        <p class="text-xs text-gray-500">Via iframe embed</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Top Source Websites Card -->
            <Card v-if="getTopSourceWebsites().length > 0">
                <CardHeader>
                    <CardTitle>Top Source Websites</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2">
                        <div v-for="(website, index) in getTopSourceWebsites().slice(0, 5)" :key="index" class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">{{ website.name }}</span>
                                <span class="text-xs text-gray-500">({{ website.count }} subscribers)</span>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ Math.round((website.count / (props.magazineUsers.meta?.total || props.magazineUsers.data.length)) * 100) }}%
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
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Source Website</th>
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Embed Type</th>
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Created At</th>
                                    <th class="border border-gray-200 dark:border-gray-700 px-4 py-2 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in props.magazineUsers.data" :key="user.id">
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">{{ user.name }}</td>
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">{{ user.email }}</td>
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">
                                        <span v-if="user.source_data && user.source_data.website" class="text-sm">
                                            {{ user.source_data.website }}
                                        </span>
                                        <span v-else class="text-sm text-gray-500 italic">Direct</span>
                                    </td>
                                    <td class="border border-gray-200 dark:border-gray-700 px-4 py-2">
                                        <span v-if="user.source_data && user.source_data.embed_type" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" :class="{
                                            'bg-blue-100 text-blue-800': user.source_data.embed_type === 'widget',
                                            'bg-purple-100 text-purple-800': user.source_data.embed_type === 'iframe'
                                        }">
                                            {{ user.source_data.embed_type }}
                                        </span>
                                        <span v-else class="text-sm text-gray-500 italic">Manual</span>
                                    </td>
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
