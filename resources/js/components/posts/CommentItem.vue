<script setup>
import { nextTick, ref } from "vue";
import { formatDistanceToNow } from "date-fns";
import { useAuthStore } from "../../stores/AuthStore";
import { useGuest } from "../../composables/useGuest";
import api from "../../services/api";
import BaseError from "../ui/BaseError.vue";

const { guestName } = useGuest();
const auth = useAuthStore();

const props = defineProps({ comment: Object });

const emit = defineEmits(["updated", "deleted"]);

const isEditing = ref(false);
const commentInput = ref(null);

// edit form input value
const editBody = ref(props.comment.content);

const startEditing = async () => {
    isEditing.value = true;

    await nextTick();

    commentInput.value?.focus();
};

// update action
const errorMessage = ref(null);
const update = async () => {
    isEditing.value = true;
    errorMessage.value = null;
    try {
        const response = await api.put(`/comments/${props.comment.id}`, {
            guest_name: guestName,
            content: editBody.value,
        });
        editBody.value = response.data.content;
        emit("updated", response.data);
        isEditing.value = false;
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to edit this comment. Please check your connection.";
    }
};

// Cancel editing btn
const cancelEditing = () => {
    isEditing.value = false;

    editBody.value = props.comment.content;
};

// delete action
const deleteLoading = ref(false);
const destroy = async () => {
    if (!confirm("Remove this comment permanently?")) return;

    errorMessage.value = null;
    deleteLoading.value = true;
    try {
        await api.delete(`/comments/${props.comment.id}`, {
            data: { guest_name: guestName },
        });
        emit("deleted", props.comment.id);
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to delete this comment. Please check your connection.";
    } finally {
        deleteLoading.value = false;
    }
};
</script>

<template>
    <div class="space-y-2">
        <BaseError v-if="errorMessage" :error-messages="errorMessage" />

        <div
            class="flex items-start gap-4 p-5 bg-bro-surface border border-bro-border rounded-2xl transition-all duration-200"
        >
            <div
                class="shrink-0 w-9 h-9 rounded-full bg-bro-bg border border-bro-border flex items-center justify-center font-black text-bro-crimson text-sm"
            >
                {{
                    (comment.user?.name || comment.guest_name)
                        ?.charAt(0)
                        ?.toUpperCase()
                }}
            </div>

            <div class="grow min-w-0">
                <div class="flex items-baseline justify-between gap-3 mb-1.5">
                    <span class="font-bold text-sm text-bro-light truncate">
                        {{ comment.user?.name || comment.guest_name }}
                        <span
                            v-if="!comment.user_id"
                            class="text-[10px] uppercase tracking-wide text-bro-muted/40 font-medium ml-1"
                            >(Guest)</span
                        >
                    </span>
                    <span
                        class="text-xs text-bro-muted/40 shrink-0 font-medium"
                    >
                        {{
                            formatDistanceToNow(new Date(comment.created_at), {
                                addSuffix: true,
                            })
                        }}
                    </span>
                </div>

                <div
                    v-if="isEditing"
                    class="flex items-start gap-3 w-full mt-2"
                >
                    <div class="flex-1">
                        <textarea
                            ref="commentInput"
                            v-model="editBody"
                            rows="2"
                            class="w-full px-3 py-2 bg-bro-bg border border-bro-border rounded-xl text-sm text-bro-light placeholder-bro-muted/30 focus:outline-hidden focus:border-bro-crimson resize-none transition-all"
                            placeholder="Edit your comment..."
                        ></textarea>
                    </div>

                    <div class="pt-2 shrink-0">
                        <button
                            @click="cancelEditing"
                            type="button"
                            class="text-xs font-bold text-bro-muted/50 hover:text-red-400 transition-colors cursor-pointer select-none uppercase tracking-wider"
                        >
                            Cancel
                        </button>
                    </div>
                </div>

                <p
                    v-else
                    class="text-sm text-bro-muted whitespace-pre-wrap leading-relaxed font-medium"
                >
                    {{ comment.content }}
                </p>

                <div
                    v-if="auth.user?.id === comment.user_id"
                    class="flex items-center gap-3 text-xs text-bro-muted/50 mt-4 border-t border-bro-border/40 pt-3"
                >
                    <button
                        @click="isEditing ? update() : startEditing()"
                        type="button"
                        class="inline-flex items-center gap-1 hover:text-bro-light transition-colors cursor-pointer font-bold uppercase tracking-wider text-[11px]"
                    >
                        <svg
                            class="w-3.5 h-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.75"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                            />
                        </svg>
                        <span>{{ isEditing ? "Save" : "Edit" }}</span>
                    </button>

                    <span
                        class="text-bro-border select-none text-base font-normal"
                        >•</span
                    >

                    <button
                        @click="destroy"
                        :disabled="deleteLoading"
                        type="button"
                        class="inline-flex items-center gap-1 hover:text-red-400 transition-colors cursor-pointer font-bold uppercase tracking-wider text-[11px] disabled:opacity-40 disabled:cursor-not-allowed"
                    >
                        <svg
                            class="w-3.5 h-3.5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="1.75"
                                d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                            />
                        </svg>
                        <span>{{
                            deleteLoading ? "Removing..." : "Delete"
                        }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
