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
    <div class="space-y-6">
        <BaseError v-if="errorMessage" :error-messages="errorMessage" />
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

                <div v-if="isEditing" class="flex items-start gap-3 w-full">
                    <!-- Left Side: Textarea occupies all remaining room -->
                    <div class="flex-1">
                        <textarea
                            ref="commentInput"
                            v-model="editBody"
                            rows="3"
                            class="w-full px-3 py-2 border border-slate-300 rounded-xl shadow-xs text-sm text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 resize-none transition-all duration-150 bg-white"
                            placeholder="Edit your comment..."
                        ></textarea>
                    </div>

                    <!-- Right Side: Cancel link aligned right next to it -->
                    <div class="pt-6 shrink-0">
                        <button
                            @click="cancelEditing"
                            type="button"
                            class="text-sm font-medium text-slate-400 hover:text-rose-500 transition-colors duration-150 cursor-pointer select-none"
                        >
                            Cancel
                        </button>
                    </div>
                </div>

                <p v-else class="text-sm text-slate-700">
                    {{ comment.content }}
                </p>
            </div>

            <div
                v-if="auth.user?.id === comment.user_id"
                class="flex items-center gap-4 text-sm text-slate-500"
            >
                <!-- Edit Button -->
                <button
                    @click="isEditing ? update() : startEditing()"
                    type="button"
                    class="inline-flex items-center gap-1.5 hover:text-indigo-600 transition-colors duration-150 cursor-pointer select-none font-medium"
                >
                    <svg
                        class="w-4 h-4 stroke-[1.75]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                        />
                    </svg>
                    <span>{{ isEditing ? "Update" : "Edit" }}</span>
                </button>

                <!-- Tiny Divider Dot -->
                <span class="text-slate-300 select-none">•</span>

                <!-- Delete Button -->
                <button
                    @click="destroy"
                    :disabled="deleteLoading"
                    type="button"
                    class="inline-flex items-center gap-1.5 hover:text-rose-600 transition-colors duration-150 cursor-pointer select-none font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg
                        class="w-4 h-4 stroke-[1.75]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m14.74 9-.346 9m-4.788 0L9 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                        />
                    </svg>
                    <span>{{ deleteLoading ? "Deleting" : "Delete" }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
