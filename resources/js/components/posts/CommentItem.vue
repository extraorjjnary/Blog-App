<script setup>
import { nextTick, ref } from "vue";
import { useAuthStore } from "../../stores/AuthStore";
import { useGuest } from "../../composables/useGuest";
import api from "../../services/api";
import BaseError from "../ui/BaseError.vue";
import dayjs from "../../../utils/dayjs.js";
import { Check, SquarePen, Trash2 } from "@lucide/vue";

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
                        {{ dayjs(comment.created_at).fromNow() }}
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
                    class="text-sm text-bro-muted whitespace-pre-wrap leading-relaxed wrap-break-word font-medium"
                >
                    {{ comment.content }}
                </p>

                <div
                    v-if="auth.user?.id === comment.user_id"
                    class="flex items-center gap-3 text-xs text-bro-muted/50 mt-4 border-t border-bro-border/40 pt-3 select-none"
                >
                    <!-- Edit / Save Toggle Control Trigger -->
                    <button
                        @click="isEditing ? update() : startEditing()"
                        type="button"
                        class="inline-flex items-center gap-1.5 hover:text-bro-light transition-colors cursor-pointer font-bold uppercase tracking-wider text-[11px] group"
                    >
                        <!-- Conditional Icon State Swap based on inline active edit mode -->
                        <template v-if="isEditing">
                            <Check
                                class="w-3 h-3 text-emerald-500 stroke-[2.5]"
                            />
                        </template>
                        <template v-else>
                            <SquarePen
                                class="w-3 h-3 text-bro-muted group-hover:text-bro-crimson-hover transition-colors stroke-2"
                            />
                        </template>

                        <span>{{ isEditing ? "Save" : "Edit" }}</span>
                    </button>

                    <!-- Minimal Separator Dot -->
                    <span
                        class="text-bro-border/60 select-none text-sm font-normal"
                        >•</span
                    >

                    <!-- Destructive Delete Action Trigger -->
                    <button
                        @click="destroy"
                        :disabled="deleteLoading"
                        type="button"
                        class="inline-flex items-center gap-1.5 text-bro-muted hover:text-rose-500 disabled:text-bro-border transition-colors cursor-pointer font-bold uppercase tracking-wider text-[11px] disabled:cursor-not-allowed group"
                    >
                        <Trash2
                            class="w-3 h-3 group-hover:text-rose-500 disabled:text-bro-border transition-colors stroke-2"
                        />

                        <span>{{
                            deleteLoading ? "Removing..." : "Delete"
                        }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
