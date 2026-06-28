<script setup>
import { ref } from "vue";
import { formatDistanceToNow } from "date-fns";

const props = defineProps({ comment: Object });
</script>

<template>
    <div class="space-y-6">
        <div
            class="flex items-start gap-4 p-5 bg-white border border-slate-50 rounded-2xl"
        >
            <div
                class="shrink-0 w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-emerald-600"
            >
                {{
                    (comment.user?.name || comment.guest_name)
                        ?.charAt(0)
                        ?.toUpperCase()
                }}
            </div>
            <div class="grow">
                <div class="flex items-center justify-between gap-3 mb-2">
                    <span class="font-semibold text-sm text-slate-900">{{
                        comment.user?.name || comment.guest_name
                    }}</span>
                    <span class="text-xs text-slate-400"
                        >{{
                            formatDistanceToNow(new Date(comment.created_at), {
                                suffix: true,
                            })
                        }}
                        ago</span
                    >
                </div>
                <p class="text-sm text-slate-700">
                    {{ comment.content }}
                </p>
            </div>
        </div>
    </div>
</template>
