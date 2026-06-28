import api from "../services/api";
import { useGuest } from "./useGuest";

export function useReaction() {
    const { guestId } = useGuest();

    const react = (post, reactionType) => {
        return api.post(`/posts/${post.id}/reactions`, {
            reaction_type: reactionType,
            guest_identifier: guestId ?? null,
        });
    };

    return { react };
}
